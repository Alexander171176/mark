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
     */
    public static function prepare(
        array $data,
        array $supportedLocales
    ): array {
        $translations = Arr::get(
            $data,
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

        return [
            /*
            |--------------------------------------------------------------------------
            | ID
            |--------------------------------------------------------------------------
            |
            | Используется только конструктором формы при Edit.
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
        ];
    }

    /**
     * Правила валидации одного поля.
     *
     * $prefix:
     * - пустой для FormFieldRequest;
     * - fields.0 / fields.1 / ... для FormRequest.
     *
     * $formId необходим для проверки уникальности name
     * внутри существующей формы.
     *
     * $fieldId необходим при обновлении существующего поля.
     */
    public static function rules(
        string $prefix = '',
        ?int $formId = null,
        ?int $fieldId = null
    ): array {
        $key = static fn (string $name): string =>
        $prefix !== ''
            ? "{$prefix}.{$name}"
            : $name;

        $nameRule = Rule::unique(
            'form_fields',
            'name'
        );

        if ($formId) {
            $nameRule->where(
                fn ($query) => $query->where(
                    'form_id',
                    $formId
                )
            );
        }

        if ($fieldId) {
            $nameRule->ignore(
                $fieldId
            );
        }

        return [
            $key('id') => [
                'nullable',
                'integer',
                'exists:form_fields,id',
            ],

            $key('name') => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9]+(?:_[a-z0-9]+)*$/',
                $nameRule,
            ],

            $key('type') => [
                'required',
                'string',
                Rule::in(
                    array_keys(
                        config(
                            'forms.field_types',
                            []
                        )
                    )
                ),
            ],

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

            $key('default_value') => [
                'nullable',
                'string',
            ],

            $key('validation') => [
                'nullable',
                'array',
            ],

            $key('width') => [
                'required',
                'string',
                Rule::in(
                    array_keys(
                        config(
                            'forms.field_widths',
                            []
                        )
                    )
                ),
            ],

            $key('settings') => [
                'nullable',
                'array',
            ],

            $key('translations') => [
                'required',
                'array',
                'min:1',
            ],

            $key('translations.*') => [
                'required',
                'array',
            ],

            $key('translations.*.label') => [
                'required',
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
     * Дополнительная проверка логики поля.
     *
     * Здесь размещаются правила, которые зависят
     * одновременно от нескольких значений поля.
     */
    public static function validate(
        Validator $validator,
        array $field,
        string $prefix = ''
    ): void {
        $key = static fn (string $name): string =>
        $prefix !== ''
            ? "{$prefix}.{$name}"
            : $name;

        $type = $field['type'] ?? null;

        if (!$type) {
            return;
        }

        $fieldTypes = config(
            'forms.field_types',
            []
        );

        $typeConfig = $fieldTypes[$type]
            ?? [];

        /*
        |--------------------------------------------------------------------------
        | Multiple
        |--------------------------------------------------------------------------
        */

        $supportsMultiple = (bool) (
            $typeConfig['supports_multiple']
            ?? false
        );

        $multiple = (bool) (
            $field['settings']['multiple']
            ?? false
        );

        if (
            $multiple
            && !$supportsMultiple
        ) {
            $validator->errors()->add(
                $key('settings.multiple'),
                'Выбранный тип поля не поддерживает множественный выбор.'
            );
        }
    }

    /**
     * Сообщения валидации поля.
     *
     * Используем wildcard, поэтому сообщения подходят
     * как для самостоятельного CRUD, так и для fields.*.
     */
    public static function messages(
        string $prefix = ''
    ): array {
        $key = static fn (string $name): string =>
        $prefix !== ''
            ? "{$prefix}.{$name}"
            : $name;

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
                'Системное имя может содержать только строчные латинские буквы, цифры и символ подчёркивания.',

            $key('name.unique') =>
                'Поле с таким системным именем уже существует в форме.',

            $key('type.required') =>
                'Необходимо выбрать тип поля.',

            $key('type.in') =>
                'Выбран недопустимый тип поля.',

            $key('activity.boolean') =>
                'Активность поля должна быть логическим значением.',

            $key('required.boolean') =>
                'Настройка обязательности поля должна быть логическим значением.',

            $key('readonly.boolean') =>
                'Настройка только для чтения должна быть логическим значением.',

            $key('disabled.boolean') =>
                'Настройка отключения поля должна быть логическим значением.',

            $key('sort.integer') =>
                'Сортировка поля должна быть числом.',

            $key('sort.min') =>
                'Сортировка поля не может быть меньше 0.',

            $key('default_value.string') =>
                'Значение по умолчанию должно быть строкой.',

            $key('validation.array') =>
                'Настройки валидации поля должны быть массивом.',

            $key('width.required') =>
                'Необходимо указать ширину поля.',

            $key('width.in') =>
                'Выбрана недопустимая ширина поля.',

            $key('settings.array') =>
                'Дополнительные настройки поля должны быть массивом.',

            $key('translations.required') =>
                'Необходимо добавить хотя бы один перевод поля.',

            $key('translations.array') =>
                'Переводы поля должны быть массивом.',

            $key('translations.min') =>
                'Необходимо добавить хотя бы одну локаль перевода.',

            $key('translations.*.label.required') =>
                'Название поля обязательно для каждой добавленной локали.',

            $key('translations.*.label.string') =>
                'Название поля должно быть строкой.',

            $key('translations.*.label.max') =>
                'Название поля не должно превышать 255 символов.',

            $key('translations.*.placeholder.max') =>
                'Placeholder поля не должен превышать 255 символов.',
        ];
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
     */
    private static function normalizeNullableInteger(
        mixed $value
    ): ?int {
        if (
            $value === ''
            || $value === null
        ) {
            return null;
        }

        if (
            is_int($value)
            || (
                is_string($value)
                && ctype_digit($value)
            )
        ) {
            return (int) $value;
        }

        return null;
    }

    /**
     * Подготовка array / JSON значения.
     */
    private static function prepareArrayValue(
        mixed $value
    ): ?array {
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

            return is_array($decoded)
                ? $decoded
                : null;
        }

        return null;
    }

    /**
     * Приведение значения к boolean.
     */
    private static function toBoolean(
        mixed $value,
        bool $default = false
    ): bool {
        if (
            $value === null
            || $value === ''
        ) {
            return $default;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (
            is_int($value)
            || is_float($value)
        ) {
            return (bool) $value;
        }

        if (is_string($value)) {
            return match (
            strtolower(
                trim($value)
            )
            ) {
                '1',
                'true',
                'yes',
                'on' => true,

                '0',
                'false',
                'no',
                'off' => false,

                default => $default,
            };
        }

        return $default;
    }
}
