<?php

namespace App\Http\Requests\Admin\Form\FormFieldOption;

use App\Models\Admin\Form\FormField\FormField;
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
                    Arr::get($translation, 'label')
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

            'form_field_id' => $this->filled('form_field_id')
                ? (int) $this->input('form_field_id')
                : null,

            'value' => $this->normalizeNullableString(
                $this->input('value')
            ),

            /*
            |--------------------------------------------------------------------------
            | Состояние варианта
            |--------------------------------------------------------------------------
            */

            'activity' => $this->toBoolean(
                $this->input('activity', true),
                true
            ),

            'is_default' => $this->toBoolean(
                $this->input('is_default', false),
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
        $optionId = $this->route('formFieldOption')?->id
            ?? $this->route('formFieldOption')
            ?? $this->route('id');

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
                        ->ignore($optionId),
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
                if ($validator->errors()->has('form_field_id')) {
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
                'Дополнительные настройки варианта должны быть массивом.',

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

    /**
     * Проверить, поддерживает ли выбранное поле
     * варианты значений.
     *
     * Варианты разрешены только для типов,
     * у которых has_options = true.
     */
    protected function validateFieldSupportsOptions(
        Validator $validator
    ): void {
        $fieldId = $this->input(
            'form_field_id'
        );

        if (!$fieldId) {
            return;
        }

        $field = FormField::query()
            ->select([
                'id',
                'type',
            ])
            ->find($fieldId);

        if (!$field) {
            return;
        }

        $typeConfig = config(
            "forms.field_types.{$field->type}"
        );

        if (
            !is_array($typeConfig)
            || !($typeConfig['has_options'] ?? false)
        ) {
            $validator
                ->errors()
                ->add(
                    'form_field_id',
                    'Выбранный тип поля не поддерживает варианты значений.'
                );
        }
    }

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
     * Подготовить JSON-массив.
     *
     * Принимает как массив, так и JSON-строку.
     */
    protected function prepareArrayValue(
        mixed $value
    ): ?array {
        if (is_null($value) || $value === '') {
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
}
