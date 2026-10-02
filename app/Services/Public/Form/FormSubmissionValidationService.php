<?php

namespace App\Services\Public\Form;

use App\Models\Admin\Form\Form\Form;
use App\Models\Admin\Form\FormField\FormField;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FormSubmissionValidationService
{
    /**
     * Выполнить динамическую валидацию публичной формы.
     *
     * Правила строятся исключительно по актуальной
     * конфигурации формы и её полей из базы данных.
     *
     * @throws ValidationException
     */
    public function validate(
        Request $request,
        Form $form
    ): array {
        $this->loadFormRelations($form);

        $rules = [];
        $attributes = [];

        foreach ($form->activeFields as $field) {
            /*
            |--------------------------------------------------------------------------
            | Disabled
            |--------------------------------------------------------------------------
            |
            | Отключённое поле может отображаться на frontend,
            | но принимать значение от пользователя не должно.
            |
            */

            if ($field->disabled) {
                continue;
            }

            $fieldRules = $this->buildFieldRules($field);

            if ($fieldRules !== []) {
                $rules[$field->name] = $fieldRules;
            }

            if (
                $field->isFile()
                && $field->allowsMultipleFiles()
            ) {
                $rules["{$field->name}.*"] =
                    $this->buildFileItemRules($field);
            }

            $attributes[$field->name] =
                $this->getFieldLabel($field);
        }

        $validator = Validator::make(
            $request->all(),
            $rules,
            [],
            $attributes
        );

        return $validator->validate();
    }

    /**
     * Загрузить необходимые связи формы.
     */
    private function loadFormRelations(
        Form $form
    ): void {
        $form->loadMissing([
            'activeFields.translations',
            'activeFields.activeOptions.translations',
        ]);
    }

    /**
     * Построить правила конкретного поля.
     */
    private function buildFieldRules(
        FormField $field
    ): array {
        $rules = [];

        /*
        |--------------------------------------------------------------------------
        | Required / Nullable
        |--------------------------------------------------------------------------
        */

        $rules[] = $field->required
            ? 'required'
            : 'nullable';

        /*
        |--------------------------------------------------------------------------
        | Базовые правила типа
        |--------------------------------------------------------------------------
        */

        $rules = array_merge(
            $rules,
            $this->buildTypeRules($field)
        );

        /*
        |--------------------------------------------------------------------------
        | Пользовательские правила Form Builder
        |--------------------------------------------------------------------------
        */

        $rules = array_merge(
            $rules,
            $this->buildConfiguredRules($field)
        );

        return array_values(
            array_unique($rules, SORT_REGULAR)
        );
    }

    /**
     * Базовые правила в зависимости от типа поля.
     */
    private function buildTypeRules(
        FormField $field
    ): array {
        return match ($field->type) {
            'text',
            'textarea',
            'tel',
            'hidden' => [
                'string',
            ],

            'email' => [
                'string',
                'email',
            ],

            'url' => [
                'string',
                'url',
            ],

            'number',
            'range' => [
                'numeric',
            ],

            'date' => [
                'date',
            ],

            'datetime' => [
                'date',
            ],

            'checkbox' => [
                'boolean',
            ],

            'select',
            'radio' => $this->buildSingleOptionRules(
                $field
            ),

            'checkbox_group' =>
            $this->buildMultipleOptionRules(
                $field
            ),

            'file' => $this->buildFileRules(
                $field
            ),

            default => [
                'string',
            ],
        };
    }

    /**
     * Проверка одиночного варианта:
     * select / radio.
     */
    private function buildSingleOptionRules(
        FormField $field
    ): array {
        return [
            'string',
            Rule::in(
                $this->getAllowedOptionValues($field)
            ),
        ];
    }

    /**
     * Проверка множественных вариантов:
     * checkbox_group.
     */
    private function buildMultipleOptionRules(
        FormField $field
    ): array {
        $allowedValues =
            $this->getAllowedOptionValues($field);

        return [
            'array',
            Rule::forEach(
                fn () => [
                    'string',
                    Rule::in($allowedValues),
                ]
            ),
        ];
    }

    /**
     * Правила контейнера файлового поля.
     */
    private function buildFileRules(
        FormField $field
    ): array {
        if (!$field->allowsMultipleFiles()) {
            return $this->buildFileItemRules(
                $field
            );
        }

        $rules = [
            'array',
        ];

        $maxFiles = (int) $field->getSetting(
            'max_files',
            config(
                'forms.files.max_files',
                10
            )
        );

        if ($maxFiles > 0) {
            $rules[] = 'max:' . $maxFiles;
        }

        return $rules;
    }

    /**
     * Правила одного загруженного файла.
     */
    private function buildFileItemRules(
        FormField $field
    ): array {
        $rules = [
            'file',
        ];

        /*
        |--------------------------------------------------------------------------
        | Размер
        |--------------------------------------------------------------------------
        |
        | Form Builder хранит max_size в байтах,
        | а Laravel rule "max" для файла ожидает KiB.
        |
        */

        $maxSizeBytes = (int) $field->getSetting(
            'max_size',
            config(
                'forms.files.max_size',
                10 * 1024 * 1024
            )
        );

        if ($maxSizeBytes > 0) {
            $maxSizeKb = (int) ceil(
                $maxSizeBytes / 1024
            );

            $rules[] = 'max:' . $maxSizeKb;
        }

        /*
        |--------------------------------------------------------------------------
        | Расширения
        |--------------------------------------------------------------------------
        */

        $extensions = $field->getSetting(
            'extensions',
            config(
                'forms.files.extensions',
                []
            )
        );

        if (
            is_array($extensions)
            && $extensions !== []
        ) {
            $extensions = array_values(
                array_unique(
                    array_filter(
                        array_map(
                            static fn ($extension) =>
                            strtolower(
                                trim(
                                    (string) $extension
                                )
                            ),
                            $extensions
                        )
                    )
                )
            );

            if ($extensions !== []) {
                $rules[] =
                    'extensions:' .
                    implode(',', $extensions);
            }
        }

        return $rules;
    }

    /**
     * Получить разрешённые значения options.
     */
    private function getAllowedOptionValues(
        FormField $field
    ): array {
        if (!$field->supportsOptions()) {
            return [];
        }

        return $field->activeOptions
            ->pluck('value')
            ->filter(
                fn ($value) =>
                    $value !== null
                    && $value !== ''
            )
            ->map(
                fn ($value) =>
                (string) $value
            )
            ->values()
            ->all();
    }

    /**
     * Получить дополнительные Laravel rules,
     * заданные через Form Builder.
     *
     * Используются только правила,
     * разрешённые config/forms.php.
     */
    private function buildConfiguredRules(
        FormField $field
    ): array {
        $validation = $field->validation ?? [];

        if (!is_array($validation)) {
            return [];
        }

        $allowedRules = array_map(
            'strtolower',
            config(
                'forms.validation_rules',
                []
            )
        );

        $rules = [];

        foreach ($validation as $key => $value) {
            $rule = $this->normalizeConfiguredRule(
                $key,
                $value
            );

            if ($rule === null) {
                continue;
            }

            $ruleName = $this->extractRuleName(
                $rule
            );

            if (
                $ruleName === null
                || !in_array(
                    $ruleName,
                    $allowedRules,
                    true
                )
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Системные правила
            |--------------------------------------------------------------------------
            |
            | Эти правила определяются структурой Form Builder,
            | поэтому не позволяем JSON-конфигурации
            | переопределять их поведение.
            |
            */

            if (
                in_array(
                    $ruleName,
                    [
                        'required',
                        'nullable',
                        'file',
                        'array',
                    ],
                    true
                )
            ) {
                continue;
            }

            $rules[] = $rule;
        }

        return $rules;
    }

    /**
     * Нормализовать правило из validation JSON.
     *
     * Поддерживаются:
     *
     * [
     *     'string',
     *     'max:255',
     * ]
     *
     * и:
     *
     * [
     *     'string' => true,
     *     'max' => 255,
     * ]
     */
    private function normalizeConfiguredRule(
        int|string $key,
        mixed $value
    ): ?string {
        if (is_int($key)) {
            if (!is_string($value)) {
                return null;
            }

            $value = trim($value);

            return $value !== ''
                ? $value
                : null;
        }

        $ruleName = strtolower(
            trim((string) $key)
        );

        if ($ruleName === '') {
            return null;
        }

        if (
            $value === false
            || $value === null
        ) {
            return null;
        }

        if ($value === true) {
            return $ruleName;
        }

        if (is_array($value)) {
            $parameters = implode(
                ',',
                array_map(
                    static fn ($item) =>
                    (string) $item,
                    $value
                )
            );

            return $parameters !== ''
                ? "{$ruleName}:{$parameters}"
                : $ruleName;
        }

        if (is_scalar($value)) {
            return "{$ruleName}:{$value}";
        }

        return null;
    }

    /**
     * Получить имя Laravel validation rule.
     */
    private function extractRuleName(
        string $rule
    ): ?string {
        $rule = trim($rule);

        if ($rule === '') {
            return null;
        }

        return strtolower(
            trim(
                (string) Arr::first(
                    explode(':', $rule)
                )
            )
        );
    }

    /**
     * Название поля для сообщений Laravel.
     */
    private function getFieldLabel(
        FormField $field
    ): string {
        $translation =
            $field->loadedTranslationOrFallback();

        return $translation?->label
            ?: $field->name;
    }
}
