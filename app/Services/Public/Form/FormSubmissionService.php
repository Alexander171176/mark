<?php

namespace App\Services\Public\Form;

use App\Models\Admin\Form\Form\Form;
use App\Models\Admin\Form\FormField\FormField;
use App\Models\Admin\Form\FormSubmission\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class FormSubmissionService
{
    /**
     * Диск для хранения файлов заявок.
     *
     * Используем private local storage.
     */
    protected string $disk = 'local';

    /**
     * Физически сохранённые файлы.
     *
     * Используются для очистки файловой системы,
     * если транзакция БД завершится ошибкой.
     *
     * @var array<int, array{disk: string, path: string}>
     */
    protected array $storedFiles = [];

    /**
     * Создать новую заявку.
     *
     * Входные данные уже должны быть
     * проверены FormSubmissionValidationService.
     *
     * @throws Throwable
     */
    public function create(
        Form $form,
        array $validated,
        Request $request
    ): FormSubmission {
        $this->storedFiles = [];

        try {
            $submission = DB::transaction(
                function () use (
                    $form,
                    $validated,
                    $request
                ): FormSubmission {
                    /**
                     * Создаём основную заявку.
                     */
                    $submission = $this->createSubmission(
                        form: $form,
                        request: $request
                    );

                    /**
                     * Сохраняем snapshot значений
                     * и приложенные файлы.
                     */
                    $this->storeFields(
                        submission: $submission,
                        form: $form,
                        validated: $validated
                    );

                    /**
                     * Первоначальная запись
                     * истории статуса:
                     *
                     * null -> new
                     */
                    $this->createInitialStatusHistory(
                        submission: $submission
                    );

                    return $submission;
                }
            );

            /**
             * После успешного commit очищаем
             * технический список сохранённых файлов.
             */
            $this->storedFiles = [];

            return $submission->load([
                'values',
                'files',
                'statusHistory',
            ]);
        } catch (Throwable $e) {
            /**
             * Транзакция БД не умеет откатывать
             * физически сохранённые файлы.
             *
             * Поэтому удаляем их вручную.
             */
            $this->cleanupStoredFiles();

            throw $e;
        }
    }

    /**
     * Создать основную запись заявки.
     */
    protected function createSubmission(
        Form $form,
        Request $request
    ): FormSubmission {
        return FormSubmission::create([
            'form_id' => $form->id,

            'user_id' => $request->user()?->id,

            'status' => FormSubmission::STATUS_NEW,

            'source' => $this->resolveSource(
                $request
            ),

            'page_url' => $this->resolvePageUrl(
                $request
            ),

            'locale' => app()->getLocale(),

            'context' => $this->resolveContext(
                $request
            ),

            'utm_source' => $this->stringOrNull(
                $request->input('utm_source')
            ),

            'utm_medium' => $this->stringOrNull(
                $request->input('utm_medium')
            ),

            'utm_campaign' => $this->stringOrNull(
                $request->input('utm_campaign')
            ),

            'utm_content' => $this->stringOrNull(
                $request->input('utm_content')
            ),

            'utm_term' => $this->stringOrNull(
                $request->input('utm_term')
            ),

            'ip' => $request->ip(),

            'user_agent' => $this->limitString(
                $request->userAgent(),
                1000
            ),

            'session_id' => $request->hasSession()
                ? $request->session()->getId()
                : null,

            'assigned_user_id' => null,

            'processed_at' => null,
            'completed_at' => null,

            'submitted_at' => now(),
        ]);
    }

    /**
     * Сохранить значения всех полей формы.
     */
    protected function storeFields(
        FormSubmission $submission,
        Form $form,
        array $validated
    ): void {
        /**
         * Контроллер уже должен загрузить
         * activeFields.translations и
         * activeFields.activeOptions.translations.
         *
         * Если relation отсутствует,
         * загрузим её здесь.
         */
        $form->loadMissing([
            'activeFields.translations',
            'activeFields.activeOptions.translations',
        ]);

        foreach ($form->activeFields as $field) {
            /**
             * Disabled-поля не являются
             * пользовательскими входными данными.
             */
            if ($field->disabled) {
                continue;
            }

            /**
             * Если валидатор не вернул поле,
             * сохранять его не нужно.
             */
            if (!array_key_exists(
                $field->name,
                $validated
            )) {
                continue;
            }

            $value = $validated[$field->name];

            /**
             * Файлы хранятся отдельно
             * в form_submission_files.
             */
            if ($field->isFile()) {
                $this->storeFiles(
                    submission: $submission,
                    field: $field,
                    value: $value
                );

                continue;
            }

            $this->storeValue(
                submission: $submission,
                field: $field,
                value: $value
            );
        }
    }

    /**
     * Сохранить snapshot обычного значения поля.
     */
    protected function storeValue(
        FormSubmission $submission,
        FormField $field,
        mixed $value
    ): void {
        $structured = is_array($value);

        $submission->values()->create([
            'form_field_id' => $field->id,

            'field_name' => $field->name,
            'field_type' => $field->type,
            'field_label' => $this->resolveFieldLabel(
                $field
            ),

            'value' => $structured
                ? null
                : $this->normalizeScalarValue(
                    $value
                ),

            'value_json' => $structured
                ? array_values($value)
                : null,

            'display_value' => $this->resolveDisplayValue(
                field: $field,
                value: $value
            ),
        ]);
    }

    /**
     * Сохранить файл или несколько файлов поля.
     */
    protected function storeFiles(
        FormSubmission $submission,
        FormField $field,
        mixed $value
    ): void {
        if ($value === null) {
            return;
        }

        $files = is_array($value)
            ? $value
            : [$value];

        foreach ($files as $index => $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $this->storeFile(
                submission: $submission,
                field: $field,
                file: $file,
                sort: $index + 1
            );
        }
    }

    /**
     * Сохранить один физический файл
     * и его snapshot в БД.
     */
    protected function storeFile(
        FormSubmission $submission,
        FormField $field,
        UploadedFile $file,
        int $sort
    ): void {
        $extension = strtolower(
            $file->getClientOriginalExtension()
                ?: $file->extension()
                ?: ''
        );

        $fileName = (string) Str::uuid();

        if ($extension !== '') {
            $fileName .= '.' . $extension;
        }

        /**
         * Каждый submission получает
         * собственную директорию.
         */
        $directory = sprintf(
            'forms/submissions/%d/%s',
            $submission->id,
            $field->name
        );

        $path = $file->storeAs(
            $directory,
            $fileName,
            $this->disk
        );

        if (!$path) {
            throw new RuntimeException(
                sprintf(
                    'Не удалось сохранить файл поля "%s".',
                    $field->name
                )
            );
        }

        /**
         * Запоминаем физический файл сразу.
         *
         * Если последующая операция БД упадёт,
         * файл будет удалён в catch create().
         */
        $this->storedFiles[] = [
            'disk' => $this->disk,
            'path' => $path,
        ];

        $submission->files()->create([
            'form_field_id' => $field->id,

            'field_name' => $field->name,
            'field_label' => $this->resolveFieldLabel(
                $field
            ),

            'original_name' => $file->getClientOriginalName(),
            'file_name' => $fileName,
            'path' => $path,
            'disk' => $this->disk,

            'mime_type' => $file->getMimeType(),

            'extension' => $extension !== ''
                ? $extension
                : null,

            /**
             * Размер сохраняем в байтах.
             *
             * Это соответствует helper-методам
             * FormSubmissionFile::getSizeInKb()
             * и getSizeInMb().
             */
            'size' => $file->getSize(),

            'sort' => $sort,

            'metadata' => [
                'client_mime_type' => $file->getClientMimeType(),
            ],
        ]);
    }

    /**
     * Создать первоначальную историю статуса.
     */
    protected function createInitialStatusHistory(
        FormSubmission $submission
    ): void {
        $submission->statusHistory()->create([
            'user_id' => null,

            'from_status' => null,
            'to_status' => FormSubmission::STATUS_NEW,

            'source' => 'public_form',

            'comment' => null,

            'metadata' => [
                'initial' => true,
            ],

            'changed_at' => now(),
        ]);
    }

    /**
     * Получить snapshot label поля
     * для текущей локали.
     */
    protected function resolveFieldLabel(
        FormField $field
    ): string {
        $translation = $field
            ->loadedTranslationOrFallback();

        return $translation?->label
            ?: $field->name;
    }

    /**
     * Получить человекочитаемое snapshot-значение.
     *
     * Для select/radio/checkbox_group сохраняем
     * переведённые label выбранных options.
     *
     * Благодаря этому последующее изменение
     * Form Builder не изменит старую заявку.
     */
    protected function resolveDisplayValue(
        FormField $field,
        mixed $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        if ($field->isCheckbox()) {
            return $this->normalizeBoolean($value)
                ? '1'
                : '0';
        }

        if (
            $field->type === 'select'
            || $field->type === 'radio'
        ) {
            return $this->resolveOptionLabels(
                field: $field,
                values: [$value]
            );
        }

        if ($field->isCheckboxGroup()) {
            return $this->resolveOptionLabels(
                field: $field,
                values: is_array($value)
                    ? $value
                    : [$value]
            );
        }

        if (is_array($value)) {
            return implode(
                ', ',
                array_map(
                    static fn ($item): string => (string) $item,
                    $value
                )
            );
        }

        return $this->normalizeScalarValue(
            $value
        );
    }

    /**
     * Получить snapshot label выбранных options.
     */
    protected function resolveOptionLabels(
        FormField $field,
        array $values
    ): ?string {
        if ($values === []) {
            return null;
        }

        $values = array_map(
            static fn ($value): string => (string) $value,
            $values
        );

        $labels = [];

        foreach ($values as $value) {
            $option = $field
                ->activeOptions
                ->first(
                    static fn ($option): bool =>
                        (string) $option->value === $value
                );

            if (!$option) {
                /**
                 * Значение уже прошло серверную
                 * динамическую валидацию.
                 *
                 * Fallback оставляем на случай,
                 * если option был изменён между
                 * validation и сохранением.
                 */
                $labels[] = $value;

                continue;
            }

            $translation = $option
                ->loadedTranslationOrFallback();

            $labels[] = $translation?->label
                ?: $option->value;
        }

        return $labels !== []
            ? implode(', ', $labels)
            : null;
    }

    /**
     * Нормализовать scalar-значение
     * для текстового поля БД.
     */
    protected function normalizeScalarValue(
        mixed $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value
                ? '1'
                : '0';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return null;
    }

    /**
     * Привести значение к boolean.
     */
    protected function normalizeBoolean(
        mixed $value
    ): bool {
        return in_array(
            $value,
            [
                true,
                1,
                '1',
                'true',
                'on',
                'yes',
            ],
            true
        );
    }

    /**
     * Определить источник заявки.
     */
    protected function resolveSource(
        Request $request
    ): string {
        return 'public_form';
    }

    /**
     * Определить URL страницы,
     * с которой отправлена форма.
     */
    protected function resolvePageUrl(
        Request $request
    ): ?string {
        $pageUrl = $request->input(
            '_page_url'
        );

        if (is_string($pageUrl) && $pageUrl !== '') {
            return $this->limitString(
                $pageUrl,
                2048
            );
        }

        $referer = $request->headers->get(
            'referer'
        );

        return $this->limitString(
            $referer,
            2048
        );
    }

    /**
     * Дополнительный контекст заявки.
     *
     * Сюда позже можно добавить:
     * - route;
     * - modal;
     * - block;
     * - campaign;
     * - frontend component;
     * - другие системные данные.
     */
    protected function resolveContext(
        Request $request
    ): array {
        return [
            'form_code' => $request->route(
                'formCode'
            ),
        ];
    }

    /**
     * Вернуть непустую строку или null.
     */
    protected function stringOrNull(
        mixed $value
    ): ?string {
        if (!is_scalar($value)) {
            return null;
        }

        $value = trim(
            (string) $value
        );

        return $value !== ''
            ? $value
            : null;
    }

    /**
     * Ограничить длину строки.
     */
    protected function limitString(
        ?string $value,
        int $length
    ): ?string {
        if ($value === null) {
            return null;
        }

        return Str::limit(
            $value,
            $length,
            ''
        );
    }

    /**
     * Удалить физические файлы,
     * сохранённые до ошибки транзакции.
     */
    protected function cleanupStoredFiles(): void
    {
        foreach ($this->storedFiles as $storedFile) {
            try {
                Storage::disk(
                    $storedFile['disk']
                )->delete(
                    $storedFile['path']
                );
            } catch (Throwable) {
                /**
                 * Здесь намеренно не перебиваем
                 * исходное исключение.
                 *
                 * Ошибка основной операции
                 * должна остаться первичной.
                 */
            }
        }

        $this->storedFiles = [];
    }
}
