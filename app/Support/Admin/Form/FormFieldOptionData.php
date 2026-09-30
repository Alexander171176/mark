<?php

namespace App\Support\Admin\Form;

use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class FormFieldOptionData
{
    /**
     * Нормализация данных одного варианта поля формы.
     *
     * Используется:
     * - FormFieldOptionRequest для самостоятельного CRUD варианта;
     * - FormRequest для вложенных fields.*.options.* конструктора формы.
     */
    public static function prepare(
        array $data,
        array $supportedLocales
    ): array {
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
            | Используется конструктором формы при Edit.
            | Для нового варианта значение null.
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

            'value' => self::normalizeNullableString(
                Arr::get($data, 'value')
            ),

            /*
            |--------------------------------------------------------------------------
            | Состояние варианта
            |--------------------------------------------------------------------------
            */

            'activity' => self::toBoolean(
                Arr::get($data, 'activity', true),
                true
            ),

            'is_default' => self::toBoolean(
                Arr::get($data, 'is_default', false),
                false
            ),

            'sort' => self::normalizeInteger(
                Arr::get($data, 'sort'),
                100
            ),

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
     * Правила валидации одного варианта поля.
     *
     * $prefix:
     * - пустой для FormFieldOptionRequest;
     * - fields.0.options.0 / ... для FormRequest.
     *
     * $formFieldId:
     * - используется самостоятельным CRUD;
     * - для нового вложенного поля может быть null.
     */
    public static function rules(
        string $prefix = '',
        ?int $formFieldId = null,
        ?int $optionId = null
    ): array {
        $key = self::keyResolver($prefix);

        $valueRule = Rule::unique(
            'form_field_options',
            'value'
        );

        if ($formFieldId !== null) {
            $valueRule->where(
                fn ($query) => $query->where(
                    'form_field_id',
                    $formFieldId
                )
            );
        }

        if ($optionId !== null) {
            $valueRule->ignore($optionId);
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
                'exists:form_field_options,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Основные данные
            |--------------------------------------------------------------------------
            */

            $key('value') => [
                'required',
                'string',
                'max:255',
                $valueRule,
            ],

            /*
            |--------------------------------------------------------------------------
            | Состояние варианта
            |--------------------------------------------------------------------------
            */

            $key('activity') => [
                'required',
                'boolean',
            ],

            $key('is_default') => [
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

            $key('translations.*.label') => [
                'required',
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
     * Дополнительная бизнес-валидация варианта.
     *
     * Основные Laravel rules находятся в rules().
     * Здесь оставляем проверки, которые невозможно
     * или неудобно выразить обычными rules.
     */
    public static function validate(
        Validator $validator,
        array $option,
        string $prefix = ''
    ): void {
        self::validateOptionSettings(
            $validator,
            $option,
            $prefix
        );
    }

    /**
     * Сообщения валидации варианта.
     */
    public static function messages(
        string $prefix = ''
    ): array {
        $key = self::keyResolver($prefix);

        return [
            $key('id.integer') =>
                'ID варианта поля должен быть числом.',

            $key('id.exists') =>
                'Указанный вариант поля не найден.',

            $key('value.required') =>
                'Системное значение варианта обязательно для заполнения.',

            $key('value.string') =>
                'Системное значение варианта должно быть строкой.',

            $key('value.max') =>
                'Системное значение варианта не должно превышать 255 символов.',

            $key('value.unique') =>
                'Вариант с таким системным значением уже существует в этом поле.',

            $key('activity.boolean') =>
                'Поле активности варианта должно быть логическим значением.',

            $key('is_default.boolean') =>
                'Настройка варианта по умолчанию должна быть логическим значением.',

            $key('sort.integer') =>
                'Поле сортировки варианта должно быть числом.',

            $key('sort.min') =>
                'Поле сортировки варианта не может быть меньше 0.',

            $key('settings.array') =>
                'Дополнительные настройки варианта должны быть массивом или корректным JSON-объектом.',

            $key('translations.required') =>
                'Необходимо добавить хотя бы один перевод варианта.',

            $key('translations.array') =>
                'Поле переводов варианта должно быть массивом.',

            $key('translations.min') =>
                'Необходимо добавить хотя бы одну локаль перевода варианта.',

            $key('translations.*.label.required') =>
                'Название варианта обязательно для каждой добавленной локали.',

            $key('translations.*.label.string') =>
                'Название варианта должно быть строкой.',

            $key('translations.*.label.max') =>
                'Название варианта не должно превышать 255 символов.',

            $key('translations.*.description.string') =>
                'Описание варианта должно быть строкой.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Option settings
    |--------------------------------------------------------------------------
    */

    /**
     * Дополнительная проверка настроек варианта.
     *
     * Сейчас специальных settings ещё нет.
     * Метод оставляем как точку расширения,
     * чтобы контракт FormFieldOptionData не пришлось
     * менять при появлении новых настроек.
     */
    private static function validateOptionSettings(
        Validator $validator,
        array $option,
        string $prefix
    ): void {
        $settings = $option['settings'] ?? null;

        if ($settings === null) {
            return;
        }

        if (!is_array($settings)) {
            return;
        }

        /*
         * Здесь в будущем можно добавить проверки:
         *
         * settings.icon
         * settings.color
         * settings.image
         * settings.meta
         * и других параметров варианта.
         */
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
     * Важно:
     * - null / пустая строка -> null;
     * - корректное целое число -> int;
     * - некорректное значение оставляем как есть,
     *   чтобы Laravel вернул ошибку правила integer.
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
