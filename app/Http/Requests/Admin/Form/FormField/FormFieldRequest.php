<?php

namespace App\Http\Requests\Admin\Form\FormField;

use App\Models\Admin\Form\FormField\FormField;
use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class FormFieldRequest extends BaseFormRequest
{
    /**
     * Определяет, авторизован ли пользователь
     * выполнять данный запрос.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Подготовка данных перед валидацией.
     */
    protected function prepareForValidation(): void
    {
        $supportedLocales = config(
            'app.available_locales',
            ['ru']
        );

        $translations = $this->input(
            'translations',
            []
        );

        if (!is_array($translations)) {
            $translations = [];
        }

        $preparedTranslations = [];

        foreach ($translations as $locale => $translation) {
            if (!in_array(
                $locale,
                $supportedLocales,
                true
            )) {
                continue;
            }

            if (!is_array($translation)) {
                $translation = [];
            }

            $preparedTranslations[$locale] = [
                'label' => $this->normalizeNullableString(
                    Arr::get($translation, 'label')
                ),

                'placeholder' => $this->normalizeNullableString(
                    Arr::get($translation, 'placeholder')
                ),

                'description' => $this->normalizeNullableText(
                    Arr::get($translation, 'description')
                ),
            ];
        }

        $this->merge([
            /*
            |--------------------------------------------------------------------------
            | Основные данные
            |--------------------------------------------------------------------------
            */

            'form_id' => $this->filled('form_id')
                ? (int) $this->input('form_id')
                : null,

            'name' => $this->normalizeNullableName(
                $this->input('name')
            ),

            'type' => $this->normalizeNullableString(
                $this->input('type')
            ) ?: 'text',

            /*
            |--------------------------------------------------------------------------
            | Состояние поля
            |--------------------------------------------------------------------------
            */

            'activity' => $this->toBoolean(
                $this->input('activity', true),
                true
            ),

            'required' => $this->toBoolean(
                $this->input('required', false),
                false
            ),

            'readonly' => $this->toBoolean(
                $this->input('readonly', false),
                false
            ),

            'disabled' => $this->toBoolean(
                $this->input('disabled', false),
                false
            ),

            'sort' => $this->filled('sort')
                ? (int) $this->input('sort')
                : 100,

            /*
            |--------------------------------------------------------------------------
            | Значение и валидация
            |--------------------------------------------------------------------------
            */

            'default_value' => $this->normalizeNullableText(
                $this->input('default_value')
            ),

            'validation' => $this->prepareArrayValue(
                $this->input('validation')
            ),

            /*
            |--------------------------------------------------------------------------
            | Отображение
            |--------------------------------------------------------------------------
            */

            'width' => $this->normalizeNullableString(
                $this->input('width')
            ) ?: 'full',

            /*
            |--------------------------------------------------------------------------
            | Дополнительные настройки
            |--------------------------------------------------------------------------
            */

            'settings' => $this->prepareArrayValue(
                $this->input('settings')
            ),

            /*
            |--------------------------------------------------------------------------
            | Переводы
            |--------------------------------------------------------------------------
            */

            'translations' => $preparedTranslations,
        ]);
    }

    /**
     * Правила валидации.
     */
    public function rules(): array
    {
        $fieldId = $this->resolveFieldId();

        $availableLocales = config(
            'app.available_locales',
            ['ru']
        );

        return [
                /*
                |--------------------------------------------------------------------------
                | Основные данные
                |--------------------------------------------------------------------------
                */

                'form_id' => [
                    'required',
                    'integer',
                    Rule::exists(
                        'forms',
                        'id'
                    ),
                ],

                'name' => [
                    'required',
                    'string',
                    'max:100',
                    'regex:/^[a-z][a-z0-9_]*$/',

                    Rule::unique(
                        'form_fields',
                        'name'
                    )
                        ->where(
                            fn ($query) => $query->where(
                                'form_id',
                                $this->input('form_id')
                            )
                        )
                        ->ignore($fieldId),
                ],

                'type' => [
                    'required',
                    'string',
                    'max:50',

                    Rule::in(
                        array_keys(
                            config(
                                'forms.field_types',
                                []
                            )
                        )
                    ),
                ],

                /*
                |--------------------------------------------------------------------------
                | Состояние поля
                |--------------------------------------------------------------------------
                */

                'activity' => [
                    'required',
                    'boolean',
                ],

                'required' => [
                    'required',
                    'boolean',
                ],

                'readonly' => [
                    'required',
                    'boolean',
                ],

                'disabled' => [
                    'required',
                    'boolean',
                ],

                'sort' => [
                    'required',
                    'integer',
                    'min:0',
                ],

                /*
                |--------------------------------------------------------------------------
                | Значение и правила валидации
                |--------------------------------------------------------------------------
                */

                'default_value' => [
                    'nullable',
                    'string',
                ],

                'validation' => [
                    'nullable',
                    'array',
                ],

                'validation.*' => [
                    'nullable',
                ],

                /*
                |--------------------------------------------------------------------------
                | Отображение
                |--------------------------------------------------------------------------
                */

                'width' => [
                    'required',
                    'string',
                    'max:20',

                    Rule::in(
                        array_keys(
                            config(
                                'forms.field_widths',
                                []
                            )
                        )
                    ),
                ],

                /*
                |--------------------------------------------------------------------------
                | Дополнительные настройки
                |--------------------------------------------------------------------------
                */

                'settings' => [
                    'nullable',
                    'array',
                ],

                /*
                |--------------------------------------------------------------------------
                | Переводы
                |--------------------------------------------------------------------------
                */

                'translations' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'translations.*' => [
                    'required',
                    'array',
                ],

                /*
                |--------------------------------------------------------------------------
                | Label
                |--------------------------------------------------------------------------
                |
                | Для визуальных полей label обязателен.
                |
                | Для hidden-поля label не требуется,
                | поскольку оно не отображается пользователю.
                |
                */

                'translations.*.label' => [
                    Rule::requiredIf(
                        fn () => $this->input('type') !== 'hidden'
                    ),

                    'nullable',
                    'string',
                    'max:255',
                ],

                'translations.*.placeholder' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'translations.*.description' => [
                    'nullable',
                    'string',
                ],
            ] + $this->localeRules(
                $availableLocales
            );
    }

    /**
     * Дополнительная проверка данных.
     *
     * Здесь проверяются:
     * - динамические Laravel validation rules;
     * - настройки конкретных типов полей;
     * - логическая совместимость параметров поля.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                /*
                |--------------------------------------------------------------------------
                | Если базовая структура данных уже невалидна,
                | дополнительные проверки всё равно безопасны:
                | каждый validator ниже проверяет тип входных данных.
                |--------------------------------------------------------------------------
                */

                $this->validateDynamicRules(
                    $validator
                );

                $this->validateFieldSettings(
                    $validator
                );

                $this->validateFieldBehavior(
                    $validator
                );
            },
        ];
    }

    /**
     * Сообщения валидации.
     */
    public function messages(): array
    {
        return [
            /*
            |--------------------------------------------------------------------------
            | Основные данные
            |--------------------------------------------------------------------------
            */

            'form_id.required' =>
                'Необходимо указать форму, которой принадлежит поле.',

            'form_id.integer' =>
                'ID формы должен быть числом.',

            'form_id.exists' =>
                'Указанная форма не найдена.',

            'name.required' =>
                'Системное имя поля обязательно для заполнения.',

            'name.string' =>
                'Системное имя поля должно быть строкой.',

            'name.max' =>
                'Системное имя поля не должно превышать 100 символов.',

            'name.regex' =>
                'Системное имя поля должно начинаться с латинской буквы и может содержать только строчные латинские буквы, цифры и символ подчёркивания.',

            'name.unique' =>
                'Поле с таким системным именем уже существует в этой форме.',

            'type.required' =>
                'Необходимо указать тип поля.',

            'type.string' =>
                'Тип поля должен быть строкой.',

            'type.max' =>
                'Тип поля не должен превышать 50 символов.',

            'type.in' =>
                'Указан недопустимый тип поля.',

            /*
            |--------------------------------------------------------------------------
            | Состояние поля
            |--------------------------------------------------------------------------
            */

            'activity.boolean' =>
                'Поле активности должно быть логическим значением.',

            'required.boolean' =>
                'Настройка обязательности должна быть логическим значением.',

            'readonly.boolean' =>
                'Настройка только для чтения должна быть логическим значением.',

            'disabled.boolean' =>
                'Настройка отключения поля должна быть логическим значением.',

            'sort.integer' =>
                'Поле сортировки должно быть числом.',

            'sort.min' =>
                'Поле сортировки не может быть меньше 0.',

            /*
            |--------------------------------------------------------------------------
            | Значение и валидация
            |--------------------------------------------------------------------------
            */

            'default_value.string' =>
                'Значение по умолчанию должно быть строкой.',

            'validation.array' =>
                'Правила валидации должны быть массивом или корректным JSON-массивом.',

            /*
            |--------------------------------------------------------------------------
            | Отображение
            |--------------------------------------------------------------------------
            */

            'width.required' =>
                'Необходимо указать ширину поля.',

            'width.string' =>
                'Ширина поля должна быть строкой.',

            'width.max' =>
                'Значение ширины поля слишком длинное.',

            'width.in' =>
                'Указано недопустимое значение ширины поля.',

            /*
            |--------------------------------------------------------------------------
            | Дополнительные настройки
            |--------------------------------------------------------------------------
            */

            'settings.array' =>
                'Дополнительные настройки поля должны быть массивом или корректным JSON-объектом.',

            /*
            |--------------------------------------------------------------------------
            | Переводы
            |--------------------------------------------------------------------------
            */

            'translations.required' =>
                'Необходимо добавить хотя бы один перевод поля.',

            'translations.array' =>
                'Поле переводов должно быть массивом.',

            'translations.min' =>
                'Необходимо добавить хотя бы одну локаль перевода.',

            'translations.*.label.required' =>
                'Название поля обязательно для каждой добавленной локали.',

            'translations.*.label.string' =>
                'Название поля должно быть строкой.',

            'translations.*.label.max' =>
                'Название поля не должно превышать 255 символов.',

            'translations.*.placeholder.string' =>
                'Placeholder поля должен быть строкой.',

            'translations.*.placeholder.max' =>
                'Placeholder поля не должен превышать 255 символов.',

            'translations.*.description.string' =>
                'Описание поля должно быть строкой.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Dynamic validation
    |--------------------------------------------------------------------------
    */

    /**
     * Проверить динамические правила валидации.
     *
     * Допускаются только правила, явно разрешённые
     * в config/forms.php.
     *
     * Поддерживаются форматы:
     *
     * [
     *     'string',
     *     'max:255',
     *     'min:3',
     * ]
     *
     * и:
     *
     * [
     *     'string' => true,
     *     'max' => 255,
     *     'min' => 3,
     * ]
     */
    protected function validateDynamicRules(
        Validator $validator
    ): void {
        $validation = $this->input(
            'validation'
        );

        if (!is_array($validation)) {
            return;
        }

        $allowedRules = config(
            'forms.validation_rules',
            []
        );

        foreach ($validation as $key => $value) {
            $ruleName = is_int($key)
                ? $this->extractRuleName($value)
                : strtolower(
                    trim(
                        (string) $key
                    )
                );

            if (
                $ruleName === null
                || $ruleName === ''
                || !in_array(
                    $ruleName,
                    $allowedRules,
                    true
                )
            ) {
                $validator
                    ->errors()
                    ->add(
                        'validation',
                        sprintf(
                            'Правило валидации "%s" не разрешено.',
                            $ruleName
                                ?: (
                            is_scalar($value)
                                ? (string) $value
                                : ''
                            )
                        )
                    );
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Field settings
    |--------------------------------------------------------------------------
    */

    /**
     * Дополнительная проверка настроек поля.
     */
    protected function validateFieldSettings(
        Validator $validator
    ): void {
        $settings = $this->input(
            'settings'
        );

        if (!is_array($settings)) {
            return;
        }

        $type = $this->input('type');

        /*
        |--------------------------------------------------------------------------
        | Настройки file
        |--------------------------------------------------------------------------
        */

        if ($type === 'file') {
            $this->validateFileSettings(
                $validator,
                $settings
            );
        }
    }

    /**
     * Проверить настройки файлового поля.
     */
    protected function validateFileSettings(
        Validator $validator,
        array $settings
    ): void {
        /*
        |--------------------------------------------------------------------------
        | multiple
        |--------------------------------------------------------------------------
        */

        if (
            array_key_exists(
                'multiple',
                $settings
            )
            && !is_bool(
                $settings['multiple']
            )
        ) {
            $validator
                ->errors()
                ->add(
                    'settings.multiple',
                    'Настройка multiple должна быть логическим значением.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | max_files
        |--------------------------------------------------------------------------
        */

        if (
            array_key_exists(
                'max_files',
                $settings
            )
            && (
                !is_numeric(
                    $settings['max_files']
                )
                || (int) $settings['max_files'] < 1
                || (int) $settings['max_files']
                > (int) config(
                    'forms.files.max_files',
                    10
                )
            )
        ) {
            $validator
                ->errors()
                ->add(
                    'settings.max_files',
                    'Недопустимое максимальное количество файлов.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | max_size
        |--------------------------------------------------------------------------
        */

        if (
            array_key_exists(
                'max_size',
                $settings
            )
            && (
                !is_numeric(
                    $settings['max_size']
                )
                || (int) $settings['max_size'] < 1
                || (int) $settings['max_size']
                > (int) config(
                    'forms.files.max_size',
                    10 * 1024 * 1024
                )
            )
        ) {
            $validator
                ->errors()
                ->add(
                    'settings.max_size',
                    'Недопустимый максимальный размер файла.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | extensions
        |--------------------------------------------------------------------------
        */

        if (
            array_key_exists(
                'extensions',
                $settings
            )
            && !is_array(
                $settings['extensions']
            )
        ) {
            $validator
                ->errors()
                ->add(
                    'settings.extensions',
                    'Список расширений файлов должен быть массивом.'
                );

            return;
        }

        if (!array_key_exists(
            'extensions',
            $settings
        )) {
            return;
        }

        $allowedExtensions = array_map(
            'strtolower',
            config(
                'forms.files.extensions',
                []
            )
        );

        foreach (
            $settings['extensions']
            as $extension
        ) {
            if (
                !is_string($extension)
                || !in_array(
                    strtolower(
                        trim($extension)
                    ),
                    $allowedExtensions,
                    true
                )
            ) {
                $validator
                    ->errors()
                    ->add(
                        'settings.extensions',
                        sprintf(
                            'Расширение файла "%s" не разрешено.',
                            is_scalar($extension)
                                ? (string) $extension
                                : ''
                        )
                    );
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Field behavior
    |--------------------------------------------------------------------------
    */

    /**
     * Проверить логическую совместимость
     * отдельных настроек поля.
     */
    protected function validateFieldBehavior(
        Validator $validator
    ): void {
        $type = $this->input('type');

        /*
        |--------------------------------------------------------------------------
        | File
        |--------------------------------------------------------------------------
        |
        | Значение файла по умолчанию не имеет смысла.
        | Файл должен быть загружен пользователем.
        |
        */

        if (
            $type === 'file'
            && $this->input('default_value') !== null
        ) {
            $validator
                ->errors()
                ->add(
                    'default_value',
                    'Для файлового поля нельзя задавать значение по умолчанию.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Checkbox group
        |--------------------------------------------------------------------------
        |
        | checkbox_group поддерживает несколько значений.
        | В текущей структуре default_value является строкой,
        | поэтому множественные значения по умолчанию
        | должны задаваться через is_default у options.
        |
        */

        if (
            $type === 'checkbox_group'
            && $this->input('default_value') !== null
        ) {
            $validator
                ->errors()
                ->add(
                    'default_value',
                    'Для группы checkbox значения по умолчанию задаются через варианты выбора.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Select / Radio
        |--------------------------------------------------------------------------
        |
        | Для полей с вариантами выбора значение по умолчанию
        | также должно задаваться через is_default у option.
        |
        */

        if (
            in_array(
                $type,
                [
                    'select',
                    'radio',
                ],
                true
            )
            && $this->input('default_value') !== null
        ) {
            $validator
                ->errors()
                ->add(
                    'default_value',
                    'Для поля с вариантами выбора значение по умолчанию задаётся через вариант выбора.'
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Route helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Получить ID редактируемого поля
     * из параметров текущего маршрута.
     *
     * Поддерживает:
     * - route model binding: FormField $formField;
     * - числовой параметр {formField};
     * - резервный параметр {id}.
     */
    protected function resolveFieldId(): ?int
    {
        $routeField = $this->route(
            'formField'
        );

        if ($routeField instanceof FormField) {
            return (int) $routeField->id;
        }

        if (
            is_int($routeField)
            || (
                is_string($routeField)
                && ctype_digit($routeField)
            )
        ) {
            return (int) $routeField;
        }

        $routeId = $this->route(
            'id'
        );

        if (
            is_int($routeId)
            || (
                is_string($routeId)
                && ctype_digit($routeId)
            )
        ) {
            return (int) $routeId;
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Locale helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Правила для разрешённых локалей приложения.
     */
    protected function localeRules(
        array $availableLocales
    ): array {
        $rules = [];

        foreach ($availableLocales as $locale) {
            $rules["translations.$locale"] = [
                'sometimes',
                'array',
            ];
        }

        return $rules;
    }

    /*
    |--------------------------------------------------------------------------
    | Normalize helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Нормализация nullable строки.
     */
    protected function normalizeNullableString(
        mixed $value
    ): ?string {
        if (is_null($value)) {
            return null;
        }

        $value = trim(
            (string) $value
        );

        return $value === ''
            ? null
            : $value;
    }

    /**
     * Нормализация nullable текста.
     */
    protected function normalizeNullableText(
        mixed $value
    ): ?string {
        if (is_null($value)) {
            return null;
        }

        $value = trim(
            (string) $value
        );

        return $value === ''
            ? null
            : $value;
    }

    /**
     * Нормализация системного имени поля.
     *
     * Например:
     *
     * Customer Phone
     *
     * ->
     *
     * customer_phone
     */
    protected function normalizeNullableName(
        mixed $value
    ): ?string {
        $value = $this->normalizeNullableString(
            $value
        );

        if ($value === null) {
            return null;
        }

        $value = strtolower(
            $value
        );

        $value = preg_replace(
            '/[^a-z0-9]+/',
            '_',
            $value
        );

        $value = trim(
            (string) $value,
            '_'
        );

        return $value === ''
            ? null
            : $value;
    }

    /**
     * Приведение значения к boolean.
     */
    protected function toBoolean(
        mixed $value,
        bool $default = false
    ): bool {
        $result = filter_var(
            $value,
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );

        return $result ?? $default;
    }

    /**
     * Подготовить массив из массива или JSON-строки.
     *
     * Важно:
     * - null / пустая строка -> null;
     * - массив -> массив;
     * - корректный JSON-массив/объект -> массив;
     * - некорректный JSON остаётся исходным значением,
     *   чтобы правило "array" вернуло ошибку валидации.
     */
    protected function prepareArrayValue(
        mixed $value
    ): mixed {
        if (
            is_null($value)
            || $value === ''
        ) {
            return null;
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode(
                $value,
                true
            );

            if (
                json_last_error() === JSON_ERROR_NONE
                && is_array($decoded)
            ) {
                return $decoded;
            }

            /*
            |--------------------------------------------------------------------------
            | Некорректный JSON
            |--------------------------------------------------------------------------
            |
            | Не превращаем его в null.
            |
            | Оставляем исходную строку, чтобы:
            |
            | validation => nullable|array
            | settings   => nullable|array
            |
            | вернули нормальную ошибку валидации.
            |
            */

            return $value;
        }

        return $value;
    }

    /**
     * Получить имя Laravel validation rule
     * из строки вида:
     *
     * max:255
     *
     * ->
     *
     * max
     */
    protected function extractRuleName(
        mixed $rule
    ): ?string {
        if (!is_string($rule)) {
            return null;
        }

        $rule = trim(
            $rule
        );

        if ($rule === '') {
            return null;
        }

        return strtolower(
            explode(
                ':',
                $rule,
                2
            )[0]
        );
    }
}
