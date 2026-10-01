<?php

namespace App\Http\Controllers\Admin\Form\FormField;

use App\Http\Controllers\Admin\Form\BaseFormAdminController;
use App\Http\Requests\Admin\Form\FormField\FormFieldRequest;
use App\Http\Resources\Admin\Form\Form\FormSharedResource;
use App\Http\Resources\Admin\Form\FormField\FormFieldResource;
use App\Http\Resources\Admin\Form\FormField\FormFieldSharedResource;
use App\Models\Admin\Form\Form\Form;
use App\Models\Admin\Form\FormField\FormField;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use App\Traits\Admin\Form\HasFormActivityTrait;
use App\Traits\Admin\Form\HasFormSortingTrait;
use App\Traits\Admin\Form\HasFormTranslationsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class FormFieldController extends BaseFormAdminController
{
    use HasFormTranslationsTrait;
    use HasFormActivityTrait;
    use HasFormSortingTrait;

    /**
     * Основная модель контроллера.
     */
    protected string $modelClass = FormField::class;

    /**
     * Название сущности для сообщений.
     */
    protected string $entityLabel = 'полей формы';

    /**
     * У form_fields нет собственного user_id.
     *
     * Владелец определяется через:
     *
     * form_fields.form_id
     *      -> forms.user_id
     */
    protected bool $ownerScoped = false;

    /**
     * Поля переводов поля формы.
     */
    protected array $translationFields = [
        'label',
        'placeholder',
        'description',
    ];

    /*
    |--------------------------------------------------------------------------
    | CRUD
    |--------------------------------------------------------------------------
    */

    /**
     * Список полей форм.
     *
     * Поддерживаются режимы:
     * - frontend;
     * - server;
     * - auto.
     *
     * Также поддерживается фильтрация:
     *
     * /admin/form-fields?form_id=5
     */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);

        /*
        |--------------------------------------------------------------------------
        | Настройки списка
        |--------------------------------------------------------------------------
        */

        $perPage = $settings->int(
            'adminFormFieldsPerPage',
            12
        );

        $defaultSort = $settings->string(
            'adminFormFieldsDefaultSort',
            'sortAsc'
        );

        $sortParam = (string) $request->query(
            'sort',
            $defaultSort
        );

        $search = trim(
            (string) $request->query(
                'search',
                ''
            )
        );

        $processingMode = $settings->string(
            'adminFormFieldsProcessingMode',
            'auto'
        );

        /*
        |--------------------------------------------------------------------------
        | Фильтр по форме
        |--------------------------------------------------------------------------
        */

        $formId = $request->filled('form_id')
            ? (int) $request->query('form_id')
            : null;

        $form = null;

        /*
         * Если передан form_id, проверяем:
         * - существует ли форма;
         * - доступна ли она текущему пользователю.
         */
        if ($formId) {
            $form = $this->formQuery($currentLocale)
                ->withCount([
                    'fields',
                    'submissions',
                ])
                ->findOrFail($formId);
        }

        /*
        |--------------------------------------------------------------------------
        | Определение режима обработки
        |--------------------------------------------------------------------------
        */

        $fieldsCount = $this->applyFormFilter(
            $this->baseQuery(),
            $formId
        )->count();

        $useServerProcessing = app(
            ProcessingModeService::class
        )->shouldUseServer(
            $processingMode,
            $fieldsCount,
            300
        );

        try {
            $fields = $this->getIndexFields(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
                formId: $formId
            );

            return Inertia::render(
                'Admin/Form/FormFields/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    /*
                    |--------------------------------------------------------------------------
                    | Настройки
                    |--------------------------------------------------------------------------
                    */

                    'adminFormFieldsPerPage' =>
                        $perPage,

                    'adminFormFieldsDefaultSort' =>
                        $defaultSort,

                    'adminFormFieldsProcessingMode' =>
                        $processingMode,

                    /*
                    |--------------------------------------------------------------------------
                    | Данные
                    |--------------------------------------------------------------------------
                    */

                    'fields' => FormFieldSharedResource::collection(
                        $fields
                    ),

                    'fieldsCount' =>
                        $fieldsCount,

                    /*
                    |--------------------------------------------------------------------------
                    | Родительская форма
                    |--------------------------------------------------------------------------
                    */

                    'form' => $form
                        ? new FormSharedResource($form)
                        : null,

                    'formId' =>
                        $formId,

                    /*
                    |--------------------------------------------------------------------------
                    | Текущее состояние списка
                    |--------------------------------------------------------------------------
                    */

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    /*
                    |--------------------------------------------------------------------------
                    | Справочники
                    |--------------------------------------------------------------------------
                    */

                    'fieldTypes' => config(
                        'forms.field_types',
                        []
                    ),
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки списка полей форм: '
                . $e->getMessage(),
                [
                    'form_id' =>
                        $formId,

                    'exception' =>
                        $e,
                ]
            );

            return Inertia::render(
                'Admin/Form/FormFields/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminFormFieldsPerPage' =>
                        $perPage,

                    'adminFormFieldsDefaultSort' =>
                        $defaultSort,

                    'adminFormFieldsProcessingMode' =>
                        $processingMode,

                    'fields' => [],

                    'fieldsCount' => 0,

                    'form' => $form
                        ? new FormSharedResource($form)
                        : null,

                    'formId' =>
                        $formId,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'fieldTypes' => config(
                        'forms.field_types',
                        []
                    ),

                    'error' =>
                        'Ошибка загрузки полей форм.',
                ]
            );
        }
    }

    /** Редирект на страницу редактирования */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route('admin.formFields.edit', $id);
    }

    /**
     * Страница редактирования отдельного поля формы.
     *
     * Это упрощённый интерфейс для работы
     * с одним конкретным полем:
     * - основные настройки;
     * - переводы;
     * - варианты значений;
     * - переводы вариантов.
     */
    public function edit(
        int $formField,
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale($request);

        $formField = $this->fullFieldQuery()
            ->findOrFail($formField);

        /*
        |--------------------------------------------------------------------------
        | Доступные формы
        |--------------------------------------------------------------------------
        |
        | Backend допускает изменение form_id.
        | Поэтому Edit получает только те формы,
        | к которым текущий пользователь имеет доступ.
        |
        */

        $forms = $this->formsForSelect(
            $currentLocale
        );

        return Inertia::render(
            'Admin/Form/FormFields/Edit',
            [
                'field' => new FormFieldResource(
                    $formField
                ),

                'form' => $formField->form
                    ? new FormSharedResource(
                        $formField->form
                    )
                    : null,

                'forms' => FormSharedResource::collection(
                    $forms
                ),

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'fieldTypes' => config(
                    'forms.field_types',
                    []
                ),

                'fieldWidths' => config(
                    'forms.field_widths',
                    []
                ),

                'validationRules' => config(
                    'forms.validation_rules',
                    []
                ),

                'fileSettings' => config(
                    'forms.files',
                    []
                ),
            ]
        );
    }

    /**
     * Обновление отдельного поля формы.
     *
     * В одной транзакции обновляются:
     * - основные данные поля;
     * - переводы поля;
     * - варианты значений;
     * - переводы вариантов.
     */
    public function update(
        FormFieldRequest $request,
        int $formField
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | Проверяем доступ к текущему полю
        |--------------------------------------------------------------------------
        */

        $formField = $this->baseQuery()
            ->findOrFail($formField);

        $validated = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Проверяем доступ к родительской форме
        |--------------------------------------------------------------------------
        |
        | form_id может быть изменён.
        | Поэтому необходимо проверить форму,
        | в которую переносится поле.
        |
        */

        $this->accessibleFormsQuery()
            ->findOrFail(
                (int) $validated['form_id']
            );

        try {
            DB::transaction(
                function () use (
                    $formField,
                    $validated
                ) {
                    /*
                    |--------------------------------------------------------------------------
                    | Вложенные данные
                    |--------------------------------------------------------------------------
                    */

                    $translations = $validated['translations']
                        ?? [];

                    $options = $validated['options']
                        ?? [];

                    /*
                    |--------------------------------------------------------------------------
                    | Основные данные поля
                    |--------------------------------------------------------------------------
                    */

                    $formField->update(
                        $this->fieldData(
                            $validated
                        )
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Переводы поля
                    |--------------------------------------------------------------------------
                    */

                    $this->syncTranslations(
                        $formField,
                        $translations,
                        [
                            'label',
                            'placeholder',
                            'description',
                        ]
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Варианты значений
                    |--------------------------------------------------------------------------
                    */

                    $this->syncFormFieldOptions(
                        $formField,
                        $options
                    );
                }
            );

            return redirect()
                ->route(
                    'admin.formFields.index',
                    [
                        'formField' =>
                            $formField->id,
                    ]
                )
                ->with(
                    'success',
                    'Поле формы успешно обновлено.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при обновлении поля формы ID '
                . $formField->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ошибка при обновлении поля формы.'
                );
        }
    }

    /**
     * Удаление поля формы.
     */
    public function destroy(
        int $formField
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | Проверяем доступ к полю
        |--------------------------------------------------------------------------
        */

        $formField = $this->baseQuery()
            ->findOrFail($formField);

        $formId = (int) $formField->form_id;

        try {
            DB::transaction(
                function () use ($formField) {
                    /*
                    |--------------------------------------------------------------------------
                    | Удаление поля
                    |--------------------------------------------------------------------------
                    |
                    | Исторические значения и файлы заявок
                    | сохраняются.
                    |
                    | Их form_field_id становится NULL согласно
                    | внешним ключам БД, а snapshot-поля:
                    |
                    | field_name
                    | field_type
                    | field_label
                    |
                    | продолжают хранить исторические данные.
                    |
                    */

                    $formField->delete();
                }
            );

            return redirect()
                ->route(
                    'admin.formFields.index',
                    [
                        'form_id' =>
                            $formId,
                    ]
                )
                ->with(
                    'success',
                    'Поле формы успешно удалено.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при удалении поля формы ID '
                . $formField->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при удалении поля формы.'
            );
        }
    }

    /**
     * Массовое удаление полей форм.
     */
    public function bulkDestroy(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'ids' => [
                'required',
                'array',
            ],

            'ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:form_fields,id',
            ],
        ]);

        $ids = $validated['ids'];

        /*
        |--------------------------------------------------------------------------
        | Проверяем доступность всех выбранных полей
        |--------------------------------------------------------------------------
        */

        $allowedIds = $this->baseQuery()
            ->whereIn(
                'form_fields.id',
                $ids
            )
            ->pluck(
                'form_fields.id'
            )
            ->toArray();

        if (
            count($allowedIds)
            !== count($ids)
        ) {
            return back()->with(
                'error',
                'Часть полей формы недоступна для удаления.'
            );
        }

        try {
            DB::transaction(
                function () use ($allowedIds) {
                    /*
                    |--------------------------------------------------------------------------
                    | Массовое удаление
                    |--------------------------------------------------------------------------
                    |
                    | Исторические submission values/files
                    | не удаляются.
                    |
                    | Их form_field_id становится NULL,
                    | snapshot-данные сохраняются.
                    |
                    */

                    FormField::query()
                        ->whereIn(
                            'form_fields.id',
                            $allowedIds
                        )
                        ->delete();
                }
            );

            return back()->with(
                'success',
                'Выбранные поля формы успешно удалены.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка bulkDestroy form fields: '
                . $e->getMessage(),
                [
                    'ids' =>
                        $allowedIds,

                    'exception' =>
                        $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при массовом удалении полей формы.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Base queries
    |--------------------------------------------------------------------------
    */

    /**
     * Базовый запрос полей форм.
     *
     * FormField не имеет собственного user_id,
     * поэтому доступ обычного пользователя
     * ограничивается владельцем родительской формы.
     *
     * Администратор видит все поля.
     */
    protected function baseQuery(): Builder
    {
        $query = FormField::query();

        $user = auth()->user();

        if (
            $user
            && method_exists(
                $user,
                'hasRole'
            )
            && !$user->hasRole('admin')
        ) {
            $query->whereHas(
                'form',
                function (Builder $formQuery) use ($user) {
                    $formQuery->where(
                        'forms.user_id',
                        $user->id
                    );
                }
            );
        }

        return $query;
    }

    /**
     * Формы, доступные текущему пользователю.
     *
     * Используется при:
     * - Index;
     * - Edit;
     * - Update;
     * - смене form_id.
     */
    private function accessibleFormsQuery(): Builder
    {
        $query = Form::query();

        $user = auth()->user();

        if (
            $user
            && method_exists(
                $user,
                'hasRole'
            )
            && !$user->hasRole('admin')
        ) {
            $query->where(
                'forms.user_id',
                $user->id
            );
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | Index helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Базовый запрос Admin Index.
     *
     * Загружаются:
     * - current + fallback переводы поля;
     * - родительская форма;
     * - current + fallback переводы формы;
     * - владелец формы;
     * - количество вариантов поля;
     * - количество исторических значений;
     * - количество исторических файлов.
     *
     * Resource не должен выполнять
     * дополнительные SQL-запросы.
     */
    private function indexQuery(
        string $locale,
        ?int $formId = null
    ): Builder {
        $locales = $this->resourceLocales(
            $locale
        );

        $query = $this->baseQuery()
            ->with([
                'translations' => fn ($query) => $query
                    ->whereIn(
                        'locale',
                        $locales
                    ),

                'form' => function ($query) use ($locales) {
                    $query->with([
                        'translations' => fn ($translationQuery) =>
                        $translationQuery->whereIn(
                            'locale',
                            $locales
                        ),

                        'user:id,name,email,profile_photo_path',
                    ]);
                },
            ])
            ->withCount([
                'options',
                'submissionValues',
                'submissionFiles',
            ]);

        return $this->applyFormFilter(
            $query,
            $formId
        );
    }

    /**
     * Применить фильтр по родительской форме.
     */
    private function applyFormFilter(
        Builder $query,
        ?int $formId
    ): Builder {
        if ($formId) {
            $query->where(
                'form_fields.form_id',
                $formId
            );
        }

        return $query;
    }

    /**
     * Получение списка полей для Index.
     *
     * Server:
     * - поиск выполняется в БД;
     * - сортировка выполняется в БД;
     * - используется серверная пагинация.
     *
     * Frontend:
     * - сервер отдаёт весь список;
     * - поиск, сортировка и пагинация
     *   выполняются Vue.
     */
    private function getIndexFields(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = '',
        ?int $formId = null
    ) {
        $query = $this->indexQuery(
            $locale,
            $formId
        );

        if ($useServerProcessing) {
            return $query
                ->search(
                    $search,
                    $locale
                )
                ->sortByParam(
                    $sort,
                    $locale
                )
                ->paginate(
                    $perPage
                )
                ->withQueryString();
        }

        return $query
            ->ordered()
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Full resource helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Полный запрос поля формы
     * для Show / Edit.
     */
    private function fullFieldQuery(): Builder
    {
        return $this->baseQuery()
            ->with([
                /*
                |--------------------------------------------------------------------------
                | Все переводы поля
                |--------------------------------------------------------------------------
                */

                'translations',

                /*
                |--------------------------------------------------------------------------
                | Родительская форма
                |--------------------------------------------------------------------------
                */

                'form.translations',

                'form.user:id,name,email,profile_photo_path',

                /*
                |--------------------------------------------------------------------------
                | Варианты значений
                |--------------------------------------------------------------------------
                |
                | Полный FormFieldResource отдаёт
                | FormFieldOptionResource.
                |
                */

                'options.translations',
            ])
            ->withCount([
                'options',
                'submissionValues',
                'submissionFiles',
            ]);
    }

    /**
     * Подготовить конкретную родительскую форму
     * для FormSharedResource.
     *
     * Используется в Index
     * при фильтрации по form_id.
     */
    private function formQuery(
        string $locale
    ): Builder {
        $locales = $this->resourceLocales(
            $locale
        );

        return $this->accessibleFormsQuery()
            ->with([
                'translations' => fn ($query) => $query
                    ->whereIn(
                        'locale',
                        $locales
                    ),

                'user:id,name,email,profile_photo_path',
            ]);
    }

    /**
     * Получить доступные формы
     * для выбора родительской формы в Edit.
     *
     * Загружаются только данные,
     * необходимые FormSharedResource.
     */
    private function formsForSelect(
        string $locale
    ) {
        $locales = $this->resourceLocales(
            $locale
        );

        return $this->accessibleFormsQuery()
            ->with([
                'translations' => fn ($query) => $query
                    ->whereIn(
                        'locale',
                        $locales
                    ),

                'user:id,name,email,profile_photo_path',
            ])
            ->withCount([
                'fields',
                'submissions',
            ])
            ->ordered()
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Form field options helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Синхронизация вариантов значений поля.
     *
     * Порядок:
     * 1. DELETE;
     * 2. UPDATE;
     * 3. CREATE.
     *
     * Отсутствие существующего варианта
     * в массиве options не считается удалением.
     *
     * Для удаления используется _delete=true.
     */
    private function syncFormFieldOptions(
        FormField $field,
        array $options
    ): void {
        /*
        |--------------------------------------------------------------------------
        | DELETE
        |--------------------------------------------------------------------------
        */

        foreach ($options as $optionData) {
            if (
                !is_array($optionData)
                || empty($optionData['_delete'])
            ) {
                continue;
            }

            $optionId = isset($optionData['id'])
            && is_numeric($optionData['id'])
                ? (int) $optionData['id']
                : null;

            if ($optionId === null) {
                continue;
            }

            /*
             * Вариант ищется строго внутри
             * текущего поля.
             */
            $option = $field
                ->options()
                ->whereKey($optionId)
                ->firstOrFail();

            $option->delete();
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        foreach ($options as $optionData) {
            if (
                !is_array($optionData)
                || !empty($optionData['_delete'])
            ) {
                continue;
            }

            $optionId = isset($optionData['id'])
            && is_numeric($optionData['id'])
                ? (int) $optionData['id']
                : null;

            if ($optionId === null) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Вложенные данные
            |--------------------------------------------------------------------------
            */

            $translations = $optionData['translations']
                ?? [];

            unset(
                $optionData['id'],
                $optionData['_delete'],
                $optionData['translations']
            );

            /*
            |--------------------------------------------------------------------------
            | Существующий вариант
            |--------------------------------------------------------------------------
            */

            $option = $field
                ->options()
                ->whereKey($optionId)
                ->firstOrFail();

            /*
            |--------------------------------------------------------------------------
            | Значение по умолчанию
            |--------------------------------------------------------------------------
            */

            $this->prepareDefaultFieldOption(
                $field,
                !empty($optionData['is_default']),
                $option->id
            );

            /*
            |--------------------------------------------------------------------------
            | Обновление
            |--------------------------------------------------------------------------
            */

            $option->update(
                $optionData
            );

            /*
            |--------------------------------------------------------------------------
            | Переводы
            |--------------------------------------------------------------------------
            */

            $this->syncTranslations(
                $option,
                $translations,
                [
                    'label',
                    'description',
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
        */

        foreach ($options as $optionData) {
            if (
                !is_array($optionData)
                || !empty($optionData['_delete'])
            ) {
                continue;
            }

            $optionId = isset($optionData['id'])
            && is_numeric($optionData['id'])
                ? (int) $optionData['id']
                : null;

            if ($optionId !== null) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Вложенные данные
            |--------------------------------------------------------------------------
            */

            $translations = $optionData['translations']
                ?? [];

            unset(
                $optionData['id'],
                $optionData['_delete'],
                $optionData['translations']
            );

            /*
            |--------------------------------------------------------------------------
            | Значение по умолчанию
            |--------------------------------------------------------------------------
            */

            $this->prepareDefaultFieldOption(
                $field,
                !empty($optionData['is_default'])
            );

            /*
            |--------------------------------------------------------------------------
            | Создание варианта
            |--------------------------------------------------------------------------
            */

            $option = $field
                ->options()
                ->create(
                    $optionData
                );

            /*
            |--------------------------------------------------------------------------
            | Переводы
            |--------------------------------------------------------------------------
            */

            $this->syncTranslations(
                $option,
                $translations,
                [
                    'label',
                    'description',
                ]
            );
        }
    }

    /**
     * Подготовка варианта,
     * выбранного по умолчанию.
     *
     * Для supports_multiple=true
     * допускается несколько is_default=true.
     *
     * Для остальных типов предыдущий
     * вариант по умолчанию сбрасывается.
     */
    private function prepareDefaultFieldOption(
        FormField $field,
        bool $isDefault,
        ?int $exceptOptionId = null
    ): void {
        if (!$isDefault) {
            return;
        }

        $typeConfig = config(
            "forms.field_types.{$field->type}",
            []
        );

        if (!is_array($typeConfig)) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Поле должно поддерживать варианты
        |--------------------------------------------------------------------------
        */

        if (!(
            $typeConfig['has_options']
            ?? false
        )) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Несколько значений по умолчанию разрешены
        |--------------------------------------------------------------------------
        */

        if (
            (bool) (
                $typeConfig['supports_multiple']
                ?? false
            )
        ) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Сбрасываем предыдущий default
        |--------------------------------------------------------------------------
        */

        $query = $field
            ->options()
            ->where(
                'is_default',
                true
            );

        if ($exceptOptionId !== null) {
            $query->where(
                'id',
                '!=',
                $exceptOptionId
            );
        }

        $query->update([
            'is_default' => false,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Data helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Подготовить основные данные поля
     * без переводов и вариантов.
     */
    protected function fieldData(
        array $validated
    ): array {
        return [
            'form_id' =>
                $validated['form_id'],

            'name' =>
                $validated['name'],

            'type' =>
                $validated['type'],

            /*
            |--------------------------------------------------------------------------
            | Состояние
            |--------------------------------------------------------------------------
            */

            'activity' =>
                $validated['activity'],

            'required' =>
                $validated['required'],

            'readonly' =>
                $validated['readonly'],

            'disabled' =>
                $validated['disabled'],

            /*
            |--------------------------------------------------------------------------
            | Порядок
            |--------------------------------------------------------------------------
            */

            'sort' =>
                $validated['sort'],

            /*
            |--------------------------------------------------------------------------
            | Значение и валидация
            |--------------------------------------------------------------------------
            */

            'default_value' =>
                $validated['default_value']
                ?? null,

            'validation' =>
                $validated['validation']
                ?? null,

            /*
            |--------------------------------------------------------------------------
            | Отображение
            |--------------------------------------------------------------------------
            */

            'width' =>
                $validated['width'],

            /*
            |--------------------------------------------------------------------------
            | Дополнительные настройки
            |--------------------------------------------------------------------------
            */

            'settings' =>
                $validated['settings']
                ?? null,
        ];
    }
}
