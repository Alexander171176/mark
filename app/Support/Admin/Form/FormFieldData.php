<?php

namespace App\Support\Admin\Form;

use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class FormFieldData
{
    /**
     * Нормализация данных одного поля формы.
     *
     * Используется:
     * - FormFieldRequest для самостоятельного CRUD поля;
     * - FormRequest для вложенных fields.* конструктора формы.
     *
     * Варианты значения options также подготавливаются здесь,
     * но служебный флаг _delete относится только к FormRequest.
     */
    public static function prepare(
        array $data,
        array $supportedLocales
    ): array {
        /*
        |--------------------------------------------------------------------------
        | Переводы
        |--------------------------------------------------------------------------
        */

        $translations = Arr::get($data, 'translations', []);

        if (!is_array($translations)) {
            $translations = [];
        }

        $preparedTranslations = [];

        foreach ($translations as $locale => $translation) {
            if (!in_array($locale, $supportedLocales, true)) {
                continue;
            }

            if (!is_array($translation)) {
                $translation = [];
            }

            $preparedTranslations[$locale] = [
                'label' => self::normalizeNullableString(
                    Arr::get($translation, 'label')
                ),

                'placeholder' => self::normalizeNullableString(
                    Arr::get($translation, 'placeholder')
                ),

                'description' => self::normalizeNullableText(
                    Arr::get($translation, 'description')
                ),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Варианты значений
        |--------------------------------------------------------------------------
        */

        $options = Arr::get($data, 'options', []);

        if (!is_array($options)) {
            $options = [];
        }

        $preparedOptions = [];

        foreach ($options as $option) {
            if (!is_array($option)) {
                continue;
            }

            $preparedOptions[] = FormFieldOptionData::prepare(
                $option,
                $supportedLocales
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Результат
        |--------------------------------------------------------------------------
        */

        return [
            /*
            |--------------------------------------------------------------------------
            | ID
            |--------------------------------------------------------------------------
            |
            | Используется конструктором формы при Edit.
            | Для нового поля значение null.
            |
            */

            'id' => self::normalizeNullableInteger(
                Arr::get($data, 'id')
            ),

            /*
            |--------------------------------------------------------------------------
            | Основные данные
            |--------------------------------------------------------------------------
            */

            'name' => self::normalizeNullableName(
                Arr::get($data, 'name')
            ),

            'type' => self::normalizeNullableString(
                Arr::get($data, 'type')
            ) ?: 'text',

            /*
            |--------------------------------------------------------------------------
            | Состояние поля
            |--------------------------------------------------------------------------
            */

            'activity' => self::toBoolean(
                Arr::get($data, 'activity', true),
                true
            ),

            'required' => self::toBoolean(
                Arr::get($data, 'required', false),
                false
            ),

            'readonly' => self::toBoolean(
                Arr::get($data, 'readonly', false),
                false
            ),

            'disabled' => self::toBoolean(
                Arr::get($data, 'disabled', false),
                false
            ),

            'sort' => self::normalizeInteger(
                Arr::get($data, 'sort'),
                100
            ),

            /*
            |--------------------------------------------------------------------------
            | Значение и валидация
            |--------------------------------------------------------------------------
            */

            'default_value' => self::normalizeNullableText(
                Arr::get($data, 'default_value')
            ),

            'validation' => self::prepareArrayValue(
                Arr::get($data, 'validation')
            ),

            /*
            |--------------------------------------------------------------------------
            | Отображение
            |--------------------------------------------------------------------------
            */

            'width' => self::normalizeNullableString(
                Arr::get($data, 'width')
            ) ?: 'full',

            /*
            |--------------------------------------------------------------------------
            | Дополнительные настройки
            |--------------------------------------------------------------------------
            */

            'settings' => self::prepareArrayValue(
                Arr::get($data, 'settings')
            ),

            /*
            |--------------------------------------------------------------------------
            | Переводы
            |--------------------------------------------------------------------------
            */

            'translations' => $preparedTranslations,

            /*
            |--------------------------------------------------------------------------
            | Варианты значений
            |--------------------------------------------------------------------------
            */

            'options' => $preparedOptions,
        ];
    }

    /**
     * Правила валидации одного поля.
     *
     * $prefix:
     * - пустой для FormFieldRequest;
     * - fields.0 / fields.1 / ... для FormRequest.
     *
     * Options здесь не валидируются:
     * коллекцией fields.*.options.* управляет FormRequest,
     * используя FormFieldOptionData.
     */
    public static function rules(
        string $prefix = '',
        ?int $formId = null,
        ?int $fieldId = null,
        ?string $type = null
    ): array {
        $key = self::keyResolver($prefix);

        $nameRule = Rule::unique(
            'form_fields',
            'name'
        );

        if ($formId !== null) {
            $nameRule->where(
                fn ($query) => $query->where(
                    'form_id',
                    $formId
                )
            );
        }

        if ($fieldId !== null) {
            $nameRule->ignore($fieldId);
        }

        return [
            /*
            |--------------------------------------------------------------------------
            | ID
            |--------------------------------------------------------------------------
            */

            $key('id') => [
                'nullable',
                'integer',
                'exists:form_fields,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Основные данные
            |--------------------------------------------------------------------------
            */

            $key('name') => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z][a-z0-9_]*$/',
                $nameRule,
            ],

            $key('type') => [
                'required',
                'string',
                'max:50',

                Rule::in(
                    array_keys(
                        config('forms.field_types', [])
                    )
                ),
            ],

            /*
            |--------------------------------------------------------------------------
            | Состояние поля
            |--------------------------------------------------------------------------
            */

            $key('activity') => [
                'required',
                'boolean',
            ],

            $key('required') => [
                'required',
                'boolean',
            ],

            $key('readonly') => [
                'required',
                'boolean',
            ],

            $key('disabled') => [
                'required',
                'boolean',
            ],

            $key('sort') => [
                'required',
                'integer',
                'min:0',
            ],

            /*
            |--------------------------------------------------------------------------
            | Значение и валидация
            |--------------------------------------------------------------------------
            */

            $key('default_value') => [
                'nullable',
                'string',
            ],

            $key('validation') => [
                'nullable',
                'array',
            ],

            $key('validation.*') => [
                'nullable',
            ],

            /*
            |--------------------------------------------------------------------------
            | Отображение
            |--------------------------------------------------------------------------
            */

            $key('width') => [
                'required',
                'string',
                'max:20',

                Rule::in(
                    array_keys(
                        config('forms.field_widths', [])
                    )
                ),
            ],

            /*
            |--------------------------------------------------------------------------
            | Дополнительные настройки
            |--------------------------------------------------------------------------
            */

            $key('settings') => [
                'nullable',
                'array',
            ],

            /*
            |--------------------------------------------------------------------------
            | Переводы
            |--------------------------------------------------------------------------
            */

            $key('translations') => [
                'required',
                'array',
                'min:1',
            ],

            $key('translations.*') => [
                'required',
                'array',
            ],

            /*
            |--------------------------------------------------------------------------
            | Label
            |--------------------------------------------------------------------------
            |
            | Для hidden-поля label не требуется.
            |
            */

            $key('translations.*.label') => [
                Rule::requiredIf(
                    $type !== 'hidden'
                ),
                'nullable',
                'string',
                'max:255',
            ],

            $key('translations.*.placeholder') => [
                'nullable',
                'string',
                'max:255',
            ],

            $key('translations.*.description') => [
                'nullable',
                'string',
            ],
        ];
    }

    /**
     * Дополнительная проверка данных поля.
     *
     * Проверяются:
     * - динамические Laravel validation rules;
     * - настройки конкретных типов;
     * - логическая совместимость параметров.
     *
     * Options отдельно валидируются через
     * FormFieldOptionData в FormRequest.
     */
    public static function validate(
        Validator $validator,
        array $field,
        string $prefix = ''
    ): void {
        self::validateDynamicRules(
            $validator,
            $field,
            $prefix
        );

        self::validateFieldSettings(
            $validator,
            $field,
            $prefix
        );

        self::validateFieldBehavior(
            $validator,
            $field,
            $prefix
        );
    }

    /**
     * Сообщения валидации поля.
     */
    public static function messages(
        string $prefix = ''
    ): array {
        $key = self::keyResolver($prefix);

        return [
            $key('id.integer') =>
                'ID поля должен быть числом.',

            $key('id.exists') =>
                'Указанное поле формы не найдено.',

            $key('name.required') =>
                'Системное имя поля обязательно для заполнения.',

            $key('name.string') =>
                'Системное имя поля должно быть строкой.',

            $key('name.max') =>
                'Системное имя поля не должно превышать 100 символов.',

            $key('name.regex') =>
                'Системное имя поля должно начинаться с латинской буквы и может содержать только строчные латинские буквы, цифры и символ подчёркивания.',

            $key('name.unique') =>
                'Поле с таким системным именем уже существует в этой форме.',

            $key('type.required') =>
                'Необходимо указать тип поля.',

            $key('type.string') =>
                'Тип поля должен быть строкой.',

            $key('type.max') =>
                'Тип поля не должен превышать 50 символов.',

            $key('type.in') =>
                'Указан недопустимый тип поля.',

            $key('activity.boolean') =>
                'Поле активности должно быть логическим значением.',

            $key('required.boolean') =>
                'Настройка обязательности должна быть логическим значением.',

            $key('readonly.boolean') =>
                'Настройка только для чтения должна быть логическим значением.',

            $key('disabled.boolean') =>
                'Настройка отключения поля должна быть логическим значением.',

            $key('sort.integer') =>
                'Поле сортировки должно быть числом.',

            $key('sort.min') =>
                'Поле сортировки не может быть меньше 0.',

            $key('default_value.string') =>
                'Значение по умолчанию должно быть строкой.',

            $key('validation.array') =>
                'Правила валидации должны быть массивом или корректным JSON-массивом.',

            $key('width.required') =>
                'Необходимо указать ширину поля.',

            $key('width.string') =>
                'Ширина поля должна быть строкой.',

            $key('width.max') =>
                'Значение ширины поля не должно превышать 20 символов.',

            $key('width.in') =>
                'Указано недопустимое значение ширины поля.',

            $key('settings.array') =>
                'Дополнительные настройки поля должны быть массивом или корректным JSON-объектом.',

            $key('translations.required') =>
                'Необходимо добавить хотя бы один перевод поля.',

            $key('translations.array') =>
                'Поле переводов должно быть массивом.',

            $key('translations.min') =>
                'Необходимо добавить хотя бы одну локаль перевода.',

            $key('translations.*.label.required') =>
                'Название поля обязательно для каждой добавленной локали.',

            $key('translations.*.label.string') =>
                'Название поля должно быть строкой.',

            $key('translations.*.label.max') =>
                'Название поля не должно превышать 255 символов.',

            $key('translations.*.placeholder.string') =>
                'Placeholder поля должен быть строкой.',

            $key('translations.*.placeholder.max') =>
                'Placeholder поля не должен превышать 255 символов.',

            $key('translations.*.description.string') =>
                'Описание поля должно быть строкой.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Dynamic validation
    |--------------------------------------------------------------------------
    */

    /**
     * Проверить динамические Laravel validation rules.
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
    private static function validateDynamicRules(
        Validator $validator,
        array $field,
        string $prefix
    ): void {
        $validation = $field['validation'] ?? null;

        if (!is_array($validation)) {
            return;
        }

        $allowedRules = config(
            'forms.validation_rules',
            []
        );

        foreach ($validation as $key => $value) {
            $ruleName = is_int($key)
                ? self::extractRuleName($value)
                : strtolower(
                    trim((string) $key)
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
                $validator->errors()->add(
                    self::key($prefix, 'validation'),
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
    private static function validateFieldSettings(
        Validator $validator,
        array $field,
        string $prefix
    ): void {
        $settings = $field['settings'] ?? null;

        if (!is_array($settings)) {
            return;
        }

        if (($field['type'] ?? null) === 'file') {
            self::validateFileSettings(
                $validator,
                $settings,
                $prefix
            );
        }
    }

    /**
     * Проверить настройки файлового поля.
     */
    private static function validateFileSettings(
        Validator $validator,
        array $settings,
        string $prefix
    ): void {
        /*
        |--------------------------------------------------------------------------
        | multiple
        |--------------------------------------------------------------------------
        */

        if (
            array_key_exists('multiple', $settings)
            && !is_bool($settings['multiple'])
        ) {
            $validator->errors()->add(
                self::key($prefix, 'settings.multiple'),
                'Настройка multiple должна быть логическим значением.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | max_files
        |--------------------------------------------------------------------------
        */

        if (
            array_key_exists('max_files', $settings)
            && (
                !is_numeric($settings['max_files'])
                || (int) $settings['max_files'] < 1
                || (int) $settings['max_files'] > (int) config(
                    'forms.files.max_files',
                    10
                )
            )
        ) {
            $validator->errors()->add(
                self::key($prefix, 'settings.max_files'),
                'Недопустимое максимальное количество файлов.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | max_size
        |--------------------------------------------------------------------------
        */

        if (
            array_key_exists('max_size', $settings)
            && (
                !is_numeric($settings['max_size'])
                || (int) $settings['max_size'] < 1
                || (int) $settings['max_size'] > (int) config(
                    'forms.files.max_size',
                    10 * 1024 * 1024
                )
            )
        ) {
            $validator->errors()->add(
                self::key($prefix, 'settings.max_size'),
                'Недопустимый максимальный размер файла.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | extensions
        |--------------------------------------------------------------------------
        */

        if (
            array_key_exists('extensions', $settings)
            && !is_array($settings['extensions'])
        ) {
            $validator->errors()->add(
                self::key($prefix, 'settings.extensions'),
                'Список расширений файлов должен быть массивом.'
            );

            return;
        }

        if (!array_key_exists('extensions', $settings)) {
            return;
        }

        $allowedExtensions = array_map(
            'strtolower',
            config('forms.files.extensions', [])
        );

        foreach ($settings['extensions'] as $extension) {
            if (
                !is_string($extension)
                || !in_array(
                    strtolower(trim($extension)),
                    $allowedExtensions,
                    true
                )
            ) {
                $validator->errors()->add(
                    self::key($prefix, 'settings.extensions'),
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
    private static function validateFieldBehavior(
        Validator $validator,
        array $field,
        string $prefix
    ): void {
        $type = $field['type'] ?? null;
        $defaultValue = $field['default_value'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | File
        |--------------------------------------------------------------------------
        */

        if (
            $type === 'file'
            && $defaultValue !== null
        ) {
            $validator->errors()->add(
                self::key($prefix, 'default_value'),
                'Для файлового поля нельзя задавать значение по умолчанию.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Checkbox group
        |--------------------------------------------------------------------------
        */

        if (
            $type === 'checkbox_group'
            && $defaultValue !== null
        ) {
            $validator->errors()->add(
                self::key($prefix, 'default_value'),
                'Для группы checkbox значения по умолчанию задаются через варианты выбора.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Select / Radio
        |--------------------------------------------------------------------------
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
            && $defaultValue !== null
        ) {
            $validator->errors()->add(
                self::key($prefix, 'default_value'),
                'Для поля с вариантами выбора значение по умолчанию задаётся через вариант выбора.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Получить полный ключ поля.
     */
    private static function key(
        string $prefix,
        string $name
    ): string {
        return $prefix !== ''
            ? "{$prefix}.{$name}"
            : $name;
    }

    /**
     * Resolver ключей для rules/messages.
     */
    private static function keyResolver(
        string $prefix
    ): callable {
        return static fn (string $name): string =>
        self::key(
            $prefix,
            $name
        );
    }

    /**
     * Получить имя Laravel validation rule
     * из строки вида "max:255".
     */
    private static function extractRuleName(
        mixed $rule
    ): ?string {
        if (!is_string($rule)) {
            return null;
        }

        $rule = trim($rule);

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

    /**
     * Нормализация nullable строки.
     */
    private static function normalizeNullableString(
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
    private static function normalizeNullableText(
        mixed $value
    ): ?string {
        return self::normalizeNullableString(
            $value
        );
    }

    /**
     * Нормализация системного имени поля.
     */
    private static function normalizeNullableName(
        mixed $value
    ): ?string {
        $value = self::normalizeNullableString(
            $value
        );

        return $value !== null
            ? strtolower($value)
            : null;
    }

    /**
     * Нормализация целого числа.
     */
    private static function normalizeInteger(
        mixed $value,
        int $default = 0
    ): int {
        if (
            $value === ''
            || $value === null
        ) {
            return $default;
        }

        return (int) $value;
    }

    /**
     * Нормализация nullable ID.
     *
     * Некорректное значение сохраняется как есть,
     * чтобы Laravel validation смог вернуть
     * корректную ошибку integer.
     */
    private static function normalizeNullableInteger(
        mixed $value
    ): mixed {
        if (
            $value === ''
            || $value === null
        ) {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        if (
            is_string($value)
            && ctype_digit($value)
        ) {
            return (int) $value;
        }

        return $value;
    }

    /**
     * Подготовка array / JSON значения.
     *
     * Важно:
     * некорректное значение не превращаем в null,
     * иначе правило array не сможет вернуть ошибку.
     */
    private static function prepareArrayValue(
        mixed $value
    ): mixed {
        if (
            $value === null
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

            return $value;
        }

        return $value;
    }

    /**
     * Приведение значения к boolean.
     */
    private static function toBoolean(
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
}
