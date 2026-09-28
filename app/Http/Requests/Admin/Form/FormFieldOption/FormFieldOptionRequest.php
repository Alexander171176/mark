<?php

namespace App\Http\Requests\Admin\Form\FormFieldOption;

use App\Models\Admin\Form\FormField\FormField;
use App\Models\Admin\Form\FormFieldOption\FormFieldOption;
use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class FormFieldOptionRequest extends BaseFormRequest
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
                    Arr::get(
                        $translation,
                        'label'
                    )
                ),

                'description' => $this->normalizeNullableText(
                    Arr::get(
                        $translation,
                        'description'
                    )
                ),
            ];
        }

        $this->merge([
            /*
            |--------------------------------------------------------------------------
            | Основные данные
            |--------------------------------------------------------------------------
            */

            'form_field_id' => $this->normalizeNullableInteger(
                $this->input('form_field_id')
            ),

            'value' => $this->normalizeNullableString(
                $this->input('value')
            ),

            /*
            |--------------------------------------------------------------------------
            | Состояние варианта
            |--------------------------------------------------------------------------
            */

            'activity' => $this->toBoolean(
                $this->input(
                    'activity',
                    true
                ),
                true
            ),

            'is_default' => $this->toBoolean(
                $this->input(
                    'is_default',
                    false
                ),
                false
            ),

            'sort' => $this->filled('sort')
                ? (int) $this->input('sort')
                : 100,

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
        $optionId = $this->resolveOptionId();

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

                'form_field_id' => [
                    'required',
                    'integer',

                    Rule::exists(
                        'form_fields',
                        'id'
                    ),
                ],

                'value' => [
                    'required',
                    'string',
                    'max:255',

                    Rule::unique(
                        'form_field_options',
                        'value'
                    )
                        ->where(
                            fn ($query) => $query->where(
                                'form_field_id',
                                $this->input('form_field_id')
                            )
                        )
                        ->ignore(
                            $optionId
                        ),
                ],

                /*
                |--------------------------------------------------------------------------
                | Состояние варианта
                |--------------------------------------------------------------------------
                */

                'activity' => [
                    'required',
                    'boolean',
                ],

                'is_default' => [
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

                'translations.*.label' => [
                    'required',
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
     * Дополнительная бизнес-валидация.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                /*
                |--------------------------------------------------------------------------
                | Если form_field_id уже не прошёл базовую валидацию,
                | выполнять дополнительный запрос к БД нет необходимости.
                |--------------------------------------------------------------------------
                */

                if (
                    $validator
                        ->errors()
                        ->has('form_field_id')
                ) {
                    return;
                }

                $this->validateFieldSupportsOptions(
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

            'form_field_id.required' =>
                'Необходимо указать поле формы.',

            'form_field_id.integer' =>
                'ID поля формы должен быть числом.',

            'form_field_id.exists' =>
                'Указанное поле формы не найдено.',

            'value.required' =>
                'Системное значение варианта обязательно для заполнения.',

            'value.string' =>
                'Системное значение варианта должно быть строкой.',

            'value.max' =>
                'Системное значение варианта не должно превышать 255 символов.',

            'value.unique' =>
                'Вариант с таким системным значением уже существует для этого поля.',

            /*
            |--------------------------------------------------------------------------
            | Состояние варианта
            |--------------------------------------------------------------------------
            */

            'activity.boolean' =>
                'Поле активности должно быть логическим значением.',

            'is_default.boolean' =>
                'Настройка значения по умолчанию должна быть логическим значением.',

            'sort.integer' =>
                'Поле сортировки должно быть числом.',

            'sort.min' =>
                'Поле сортировки не может быть меньше 0.',

            /*
            |--------------------------------------------------------------------------
            | Дополнительные настройки
            |--------------------------------------------------------------------------
            */

            'settings.array' =>
                'Дополнительные настройки варианта должны быть массивом или корректным JSON-объектом.',

            /*
            |--------------------------------------------------------------------------
            | Переводы
            |--------------------------------------------------------------------------
            */

            'translations.required' =>
                'Необходимо добавить хотя бы один перевод варианта.',

            'translations.array' =>
                'Поле переводов должно быть массивом.',

            'translations.min' =>
                'Необходимо добавить хотя бы одну локаль перевода.',

            'translations.*.label.required' =>
                'Название варианта обязательно для каждой добавленной локали.',

            'translations.*.label.string' =>
                'Название варианта должно быть строкой.',

            'translations.*.label.max' =>
                'Название варианта не должно превышать 255 символов.',

            'translations.*.description.string' =>
                'Описание варианта должно быть строкой.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Business validation
    |--------------------------------------------------------------------------
    */

    /**
     * Проверить, поддерживает ли выбранное поле
     * варианты значений.
     *
     * Варианты разрешены только для типов,
     * у которых:
     *
     * has_options = true
     */
    protected function validateFieldSupportsOptions(
        Validator $validator
    ): void {
        $fieldId = $this->input(
            'form_field_id'
        );

        if (
            !is_int($fieldId)
            && !(
                is_string($fieldId)
                && ctype_digit($fieldId)
            )
        ) {
            return;
        }

        $field = FormField::query()
            ->select([
                'id',
                'type',
            ])
            ->find(
                (int) $fieldId
            );

        if (!$field) {
            return;
        }

        $typeConfig = config(
            "forms.field_types.{$field->type}"
        );

        if (
            !is_array($typeConfig)
            || !(
                $typeConfig['has_options']
                ?? false
            )
        ) {
            $validator
                ->errors()
                ->add(
                    'form_field_id',
                    'Выбранный тип поля не поддерживает варианты значений.'
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Route helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Получить ID редактируемого варианта
     * из параметров текущего маршрута.
     *
     * Поддерживает:
     * - route model binding: FormFieldOption $formFieldOption;
     * - числовой параметр {formFieldOption};
     * - резервный параметр {id}.
     */
    protected function resolveOptionId(): ?int
    {
        $routeOption = $this->route(
            'formFieldOption'
        );

        if (
            $routeOption instanceof FormFieldOption
        ) {
            return (int) $routeOption->id;
        }

        if (
            is_int($routeOption)
            || (
                is_string($routeOption)
                && ctype_digit($routeOption)
            )
        ) {
            return (int) $routeOption;
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
     * Нормализация nullable integer.
     *
     * Важно:
     * - null / пустая строка -> null;
     * - корректное целое число -> int;
     * - некорректное значение остаётся как есть,
     *   чтобы Laravel вернул ошибку правила integer.
     */
    protected function normalizeNullableInteger(
        mixed $value
    ): mixed {
        if (
            is_null($value)
            || $value === ''
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
                json_last_error()
                === JSON_ERROR_NONE
                && is_array($decoded)
            ) {
                return $decoded;
            }

            /*
            |--------------------------------------------------------------------------
            | Некорректный JSON
            |--------------------------------------------------------------------------
            |
            | Не превращаем значение в null.
            |
            | Оставляем исходную строку, чтобы правило:
            |
            | settings => nullable|array
            |
            | вернуло ошибку валидации.
            |
            */

            return $value;
        }

        return $value;
    }
}
