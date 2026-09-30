<?php

namespace App\Http\Requests\Admin\Form\Form;

use App\Models\Admin\Form\Form\Form;
use App\Models\Admin\Form\FormField\FormField;
use App\Models\Admin\Form\FormFieldOption\FormFieldOption;
use App\Support\Admin\Form\FormFieldData;
use App\Support\Admin\Form\FormFieldOptionData;
use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class FormRequest extends BaseFormRequest
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

        /*
        |--------------------------------------------------------------------------
        | Переводы формы
        |--------------------------------------------------------------------------
        */

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
                'title' => $this->normalizeNullableString(
                    Arr::get($translation, 'title')
                ),

                'subtitle' => $this->normalizeNullableString(
                    Arr::get($translation, 'subtitle')
                ),

                'description' => $this->normalizeNullableText(
                    Arr::get($translation, 'description')
                ),

                'submit_text' => $this->normalizeNullableString(
                    Arr::get($translation, 'submit_text')
                ),

                'success_message' => $this->normalizeNullableText(
                    Arr::get($translation, 'success_message')
                ),

                'error_message' => $this->normalizeNullableText(
                    Arr::get($translation, 'error_message')
                ),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Владелец
        |--------------------------------------------------------------------------
        */

        $userId = $this->input('user_id');

        if (
            $userId === ''
            || $userId === null
        ) {
            $userId = null;
        } elseif (
            is_int($userId)
            || (
                is_string($userId)
                && ctype_digit($userId)
            )
        ) {
            $userId = (int) $userId;
        }

        /*
        |--------------------------------------------------------------------------
        | Поля формы
        |--------------------------------------------------------------------------
        */

        $fields = $this->input(
            'fields',
            []
        );

        if (!is_array($fields)) {
            $fields = [];
        }

        $preparedFields = [];

        foreach ($fields as $field) {
            if (!is_array($field)) {
                $field = [];
            }

            $preparedField = FormFieldData::prepare(
                $field,
                $supportedLocales
            );

            /*
            |--------------------------------------------------------------------------
            | Явное удаление поля
            |--------------------------------------------------------------------------
            |
            | _delete относится к конструктору формы,
            | а не к самой модели FormField.
            |
            */

            $preparedField['_delete'] = $this->toBoolean(
                Arr::get($field, '_delete', false),
                false
            );

            /*
            |--------------------------------------------------------------------------
            | Явное удаление вариантов поля
            |--------------------------------------------------------------------------
            |
            | FormFieldData подготавливает сами options через
            | FormFieldOptionData, но служебный _delete
            | принадлежит только конструктору формы.
            |
            */

            $originalOptions = Arr::get(
                $field,
                'options',
                []
            );

            if (!is_array($originalOptions)) {
                $originalOptions = [];
            }

            $preparedOptions = $preparedField['options'] ?? [];

            foreach ($preparedOptions as $optionIndex => $preparedOption) {
                $originalOption = $originalOptions[$optionIndex] ?? [];

                if (!is_array($originalOption)) {
                    $originalOption = [];
                }

                $preparedOption['_delete'] = $this->toBoolean(
                    Arr::get(
                        $originalOption,
                        '_delete',
                        false
                    ),
                    false
                );

                $preparedOptions[$optionIndex] = $preparedOption;
            }

            $preparedField['options'] = $preparedOptions;

            $preparedFields[] = $preparedField;
        }

        /*
        |--------------------------------------------------------------------------
        | Merge
        |--------------------------------------------------------------------------
        */

        $this->merge([
            'user_id' => $userId,

            'code' => $this->normalizeNullableCode(
                $this->input('code')
            ),

            'status' => $this->normalizeNullableString(
                $this->input('status')
            ) ?: Form::STATUS_DRAFT,

            'activity' => $this->toBoolean(
                $this->input('activity', true),
                true
            ),

            'sort' => $this->filled('sort')
                ? (int) $this->input('sort')
                : 100,

            /*
            |--------------------------------------------------------------------------
            | Защита от спама
            |--------------------------------------------------------------------------
            */

            'spam_protection' => $this->toBoolean(
                $this->input('spam_protection', true),
                true
            ),

            'honeypot_enabled' => $this->toBoolean(
                $this->input('honeypot_enabled', true),
                true
            ),

            'min_submit_seconds' => $this->filled('min_submit_seconds')
                ? (int) $this->input('min_submit_seconds')
                : (int) config(
                    'forms.spam.min_submit_seconds',
                    3
                ),

            'rate_limit' => $this->filled('rate_limit')
                ? (int) $this->input('rate_limit')
                : (int) config(
                    'forms.spam.rate_limit',
                    5
                ),

            'rate_limit_minutes' => $this->filled('rate_limit_minutes')
                ? (int) $this->input('rate_limit_minutes')
                : (int) config(
                    'forms.spam.rate_limit_minutes',
                    10
                ),

            'captcha_enabled' => $this->toBoolean(
                $this->input('captcha_enabled', false),
                false
            ),

            /*
            |--------------------------------------------------------------------------
            | Поведение формы
            |--------------------------------------------------------------------------
            */

            'auth_required' => $this->toBoolean(
                $this->input('auth_required', false),
                false
            ),

            /*
            |--------------------------------------------------------------------------
            | Дополнительные настройки
            |--------------------------------------------------------------------------
            */

            'settings' => $this->prepareSettings(
                $this->input('settings')
            ),

            /*
            |--------------------------------------------------------------------------
            | Переводы и поля
            |--------------------------------------------------------------------------
            */

            'translations' => $preparedTranslations,
            'fields' => $preparedFields,
        ]);
    }

    /**
     * Правила валидации.
     */
    public function rules(): array
    {
        $formId = $this->resolveFormId();

        $availableLocales = config(
            'app.available_locales',
            ['ru']
        );

        $rules = [
            /*
            |--------------------------------------------------------------------------
            | Основные данные
            |--------------------------------------------------------------------------
            */

            'user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'code' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9]+(?:_[a-z0-9]+)*$/',

                Rule::unique(
                    'forms',
                    'code'
                )->ignore($formId),
            ],

            'status' => [
                'required',
                'string',
                'max:50',

                Rule::in(
                    array_keys(
                        config(
                            'forms.statuses',
                            []
                        )
                    )
                ),
            ],

            'activity' => [
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
            | Защита от спама
            |--------------------------------------------------------------------------
            */

            'spam_protection' => [
                'required',
                'boolean',
            ],

            'honeypot_enabled' => [
                'required',
                'boolean',
            ],

            'min_submit_seconds' => [
                'required',
                'integer',
                'min:0',
                'max:65535',
            ],

            'rate_limit' => [
                'required',
                'integer',
                'min:1',
                'max:65535',
            ],

            'rate_limit_minutes' => [
                'required',
                'integer',
                'min:1',
                'max:65535',
            ],

            'captcha_enabled' => [
                'required',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | Поведение формы
            |--------------------------------------------------------------------------
            */

            'auth_required' => [
                'required',
                'boolean',
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
            | Переводы формы
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

            'translations.*.title' => [
                'required',
                'string',
                'max:255',
            ],

            'translations.*.subtitle' => [
                'nullable',
                'string',
                'max:255',
            ],

            'translations.*.description' => [
                'nullable',
                'string',
            ],

            'translations.*.submit_text' => [
                'nullable',
                'string',
                'max:255',
            ],

            'translations.*.success_message' => [
                'nullable',
                'string',
            ],

            'translations.*.error_message' => [
                'nullable',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | Поля формы
            |--------------------------------------------------------------------------
            |
            | Форма может существовать без полей.
            |
            */

            'fields' => [
                'nullable',
                'array',
            ],

            'fields.*' => [
                'required',
                'array',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Правила вложенных полей
        |--------------------------------------------------------------------------
        */

        $fields = $this->input(
            'fields',
            []
        );

        if (is_array($fields)) {
            foreach ($fields as $index => $field) {
                if (!is_array($field)) {
                    continue;
                }

                $fieldId = isset($field['id'])
                && is_numeric($field['id'])
                    ? (int) $field['id']
                    : null;

                $delete = (bool) (
                    $field['_delete']
                    ?? false
                );

                /*
                |--------------------------------------------------------------------------
                | Служебный флаг конструктора
                |--------------------------------------------------------------------------
                */

                $rules["fields.$index._delete"] = [
                    'required',
                    'boolean',
                ];

                /*
                |--------------------------------------------------------------------------
                | Удаляемое поле
                |--------------------------------------------------------------------------
                |
                | Для удаления нужен только ID существующего поля.
                | Options удалятся каскадно вместе с FormField.
                |
                */

                if ($delete) {
                    $rules["fields.$index.id"] = [
                        'required',
                        'integer',
                        'exists:form_fields,id',
                    ];

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Создание / обновление поля
                |--------------------------------------------------------------------------
                */

                $rules += FormFieldData::rules(
                    prefix: "fields.$index",
                    formId: $formId,
                    fieldId: $fieldId,
                    type: $field['type'] ?? null
                );

                /*
                |--------------------------------------------------------------------------
                | Варианты значения поля
                |--------------------------------------------------------------------------
                */

                $rules["fields.$index.options"] = [
                    'nullable',
                    'array',
                ];

                $rules["fields.$index.options.*"] = [
                    'required',
                    'array',
                ];

                $options = $field['options'] ?? [];

                if (!is_array($options)) {
                    continue;
                }

                foreach ($options as $optionIndex => $option) {
                    if (!is_array($option)) {
                        continue;
                    }

                    $optionId = isset($option['id'])
                    && is_numeric($option['id'])
                        ? (int) $option['id']
                        : null;

                    $optionDelete = (bool) (
                        $option['_delete']
                        ?? false
                    );

                    $optionPrefix =
                        "fields.$index.options.$optionIndex";

                    /*
                    |--------------------------------------------------------------------------
                    | Служебный флаг варианта
                    |--------------------------------------------------------------------------
                    */

                    $rules["$optionPrefix._delete"] = [
                        'required',
                        'boolean',
                    ];

                    /*
                    |--------------------------------------------------------------------------
                    | Удаляемый вариант
                    |--------------------------------------------------------------------------
                    */

                    if ($optionDelete) {
                        $rules["$optionPrefix.id"] = [
                            'required',
                            'integer',
                            'exists:form_field_options,id',
                        ];

                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Создание / обновление варианта
                    |--------------------------------------------------------------------------
                    */

                    $rules += FormFieldOptionData::rules(
                        prefix: $optionPrefix,
                        formFieldId: $fieldId,
                        optionId: $optionId
                    );
                }
            }
        }

        return $rules + $this->localeRules(
                $availableLocales
            );
    }

    /**
     * Дополнительная проверка вложенных
     * полей и вариантов значений.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $fields = $this->input(
                    'fields',
                    []
                );

                if (!is_array($fields)) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Бизнес-валидация полей и options
                |--------------------------------------------------------------------------
                */

                foreach ($fields as $index => $field) {
                    if (
                        !is_array($field)
                        || !empty($field['_delete'])
                    ) {
                        continue;
                    }

                    FormFieldData::validate(
                        $validator,
                        $field,
                        "fields.$index"
                    );

                    $this->validateFieldOptions(
                        $validator,
                        $field,
                        $index
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Уникальность имён внутри текущего массива
                |--------------------------------------------------------------------------
                */

                $this->validateUniqueFieldNames(
                    $validator,
                    $fields
                );

                /*
                |--------------------------------------------------------------------------
                | Принадлежность существующих полей форме
                |--------------------------------------------------------------------------
                */

                $this->validateFieldOwnership(
                    $validator,
                    $fields
                );
            },
        ];
    }

    /**
     * Сообщения валидации.
     */
    public function messages(): array
    {
        $messages = [
            /*
            |--------------------------------------------------------------------------
            | Основные данные
            |--------------------------------------------------------------------------
            */

            'user_id.integer' =>
                'ID владельца формы должен быть числом.',

            'user_id.exists' =>
                'Указанный пользователь не найден.',

            'code.required' =>
                'Системный код формы обязателен для заполнения.',

            'code.string' =>
                'Системный код формы должен быть строкой.',

            'code.max' =>
                'Системный код формы не должен превышать 100 символов.',

            'code.regex' =>
                'Системный код может содержать только строчные латинские буквы, цифры и символ подчёркивания.',

            'code.unique' =>
                'Форма с таким системным кодом уже существует.',

            'status.required' =>
                'Необходимо указать статус формы.',

            'status.in' =>
                'Недопустимое значение статуса формы.',

            'activity.boolean' =>
                'Поле активности должно быть логическим значением.',

            'sort.integer' =>
                'Поле сортировки должно быть числом.',

            'sort.min' =>
                'Поле сортировки не может быть меньше 0.',

            /*
            |--------------------------------------------------------------------------
            | Защита от спама
            |--------------------------------------------------------------------------
            */

            'spam_protection.boolean' =>
                'Настройка защиты от спама должна быть логическим значением.',

            'honeypot_enabled.boolean' =>
                'Настройка honeypot должна быть логическим значением.',

            'min_submit_seconds.integer' =>
                'Минимальное время заполнения должно быть числом.',

            'min_submit_seconds.min' =>
                'Минимальное время заполнения не может быть меньше 0 секунд.',

            'min_submit_seconds.max' =>
                'Минимальное время заполнения не может превышать 65535 секунд.',

            'rate_limit.integer' =>
                'Лимит отправок должен быть числом.',

            'rate_limit.min' =>
                'Лимит отправок должен быть не меньше 1.',

            'rate_limit.max' =>
                'Лимит отправок не может превышать 65535.',

            'rate_limit_minutes.integer' =>
                'Период ограничения должен быть числом.',

            'rate_limit_minutes.min' =>
                'Период ограничения должен быть не меньше 1 минуты.',

            'rate_limit_minutes.max' =>
                'Период ограничения не может превышать 65535 минут.',

            'captcha_enabled.boolean' =>
                'Настройка CAPTCHA должна быть логическим значением.',

            /*
            |--------------------------------------------------------------------------
            | Поведение формы
            |--------------------------------------------------------------------------
            */

            'auth_required.boolean' =>
                'Настройка авторизации должна быть логическим значением.',

            /*
            |--------------------------------------------------------------------------
            | Дополнительные настройки
            |--------------------------------------------------------------------------
            */

            'settings.array' =>
                'Дополнительные настройки формы должны быть массивом или корректным JSON-объектом.',

            /*
            |--------------------------------------------------------------------------
            | Переводы формы
            |--------------------------------------------------------------------------
            */

            'translations.required' =>
                'Необходимо добавить хотя бы один перевод формы.',

            'translations.array' =>
                'Поле переводов должно быть массивом.',

            'translations.min' =>
                'Необходимо добавить хотя бы одну локаль перевода.',

            'translations.*.title.required' =>
                'Название формы обязательно для каждой добавленной локали.',

            'translations.*.title.string' =>
                'Название формы должно быть строкой.',

            'translations.*.title.max' =>
                'Название формы не должно превышать 255 символов.',

            'translations.*.subtitle.max' =>
                'Подзаголовок формы не должен превышать 255 символов.',

            'translations.*.submit_text.max' =>
                'Текст кнопки отправки не должен превышать 255 символов.',

            /*
            |--------------------------------------------------------------------------
            | Поля формы
            |--------------------------------------------------------------------------
            */

            'fields.array' =>
                'Поля формы должны быть массивом.',

            'fields.*.array' =>
                'Некорректная структура поля формы.',

            'fields.*._delete.required' =>
                'Не удалось определить состояние поля формы.',

            'fields.*._delete.boolean' =>
                'Некорректное значение признака удаления поля.',

            'fields.*.id.required' =>
                'Для удаления необходимо указать ID поля.',

            'fields.*.id.integer' =>
                'ID поля должен быть числом.',

            'fields.*.id.exists' =>
                'Указанное поле формы не найдено.',

            /*
            |--------------------------------------------------------------------------
            | Варианты значений
            |--------------------------------------------------------------------------
            */

            'fields.*.options.array' =>
                'Варианты поля должны быть массивом.',

            'fields.*.options.*.array' =>
                'Некорректная структура варианта поля.',

            'fields.*.options.*._delete.required' =>
                'Не удалось определить состояние варианта поля.',

            'fields.*.options.*._delete.boolean' =>
                'Некорректное значение признака удаления варианта.',

            'fields.*.options.*.id.required' =>
                'Для удаления варианта необходимо указать его ID.',

            'fields.*.options.*.id.integer' =>
                'ID варианта поля должен быть числом.',

            'fields.*.options.*.id.exists' =>
                'Указанный вариант поля не найден.',
        ];

        /*
        |--------------------------------------------------------------------------
        | Сообщения вложенных полей и options
        |--------------------------------------------------------------------------
        */

        $fields = $this->input(
            'fields',
            []
        );

        if (is_array($fields)) {
            foreach ($fields as $index => $field) {
                $messages += FormFieldData::messages(
                    "fields.$index"
                );

                if (!is_array($field)) {
                    continue;
                }

                $options = $field['options'] ?? [];

                if (!is_array($options)) {
                    continue;
                }

                foreach (array_keys($options) as $optionIndex) {
                    $messages += FormFieldOptionData::messages(
                        "fields.$index.options.$optionIndex"
                    );
                }
            }
        }

        return $messages;
    }

    /*
    |--------------------------------------------------------------------------
    | Field validation helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Проверить уникальность системных имён
     * непосредственно внутри массива fields.
     *
     * SQL unique не может обнаружить два одинаковых
     * новых поля до их сохранения в БД.
     */
    protected function validateUniqueFieldNames(
        Validator $validator,
        array $fields
    ): void {
        $names = [];

        foreach ($fields as $index => $field) {
            if (
                !is_array($field)
                || !empty($field['_delete'])
            ) {
                continue;
            }

            $name = $field['name'] ?? null;

            if (
                !is_string($name)
                || $name === ''
            ) {
                continue;
            }

            if (isset($names[$name])) {
                $validator->errors()->add(
                    "fields.$index.name",
                    'Поле с таким системным именем уже добавлено в форму.'
                );

                continue;
            }

            $names[$name] = $index;
        }
    }

    /**
     * Проверить принадлежность существующих полей
     * редактируемой форме.
     *
     * Защищает Edit от передачи ID поля,
     * принадлежащего другой форме.
     */
    protected function validateFieldOwnership(
        Validator $validator,
        array $fields
    ): void {
        $formId = $this->resolveFormId();

        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        |
        | При создании формы существующих полей быть не должно.
        |
        */

        if ($formId === null) {
            foreach ($fields as $index => $field) {
                if (!is_array($field)) {
                    continue;
                }

                if (!empty($field['id'])) {
                    $validator->errors()->add(
                        "fields.$index.id",
                        'При создании формы нельзя использовать существующее поле.'
                    );
                }

                if (!empty($field['_delete'])) {
                    $validator->errors()->add(
                        "fields.$index._delete",
                        'При создании формы нельзя удалять существующие поля.'
                    );
                }
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Edit
        |--------------------------------------------------------------------------
        */

        foreach ($fields as $index => $field) {
            if (
                !is_array($field)
                || empty($field['id'])
            ) {
                continue;
            }

            $fieldId = (int) $field['id'];

            $belongsToForm = FormField::query()
                ->whereKey($fieldId)
                ->where(
                    'form_id',
                    $formId
                )
                ->exists();

            if (!$belongsToForm) {
                $validator->errors()->add(
                    "fields.$index.id",
                    'Указанное поле не принадлежит редактируемой форме.'
                );
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Option validation helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Проверить options одного поля.
     */
    protected function validateFieldOptions(
        Validator $validator,
        array $field,
        int|string $fieldIndex
    ): void {
        $options = $field['options'] ?? [];

        if (!is_array($options)) {
            return;
        }

        $type = $field['type'] ?? null;

        $typeConfig = is_string($type)
            ? config(
                "forms.field_types.$type",
                []
            )
            : [];

        $supportsOptions = is_array($typeConfig)
            && (bool) (
                $typeConfig['has_options']
                ?? false
            );

        /*
        |--------------------------------------------------------------------------
        | Тип поля не поддерживает options
        |--------------------------------------------------------------------------
        |
        | Удаляемые существующие options разрешаем передать:
        | они являются командой удаления, а не актуальным вариантом поля.
        |
        */

        $hasActualOptions = false;

        foreach ($options as $option) {
            if (
                is_array($option)
                && empty($option['_delete'])
            ) {
                $hasActualOptions = true;

                break;
            }
        }

        if (
            !$supportsOptions
            && $hasActualOptions
        ) {
            $validator->errors()->add(
                "fields.$fieldIndex.options",
                'Выбранный тип поля не поддерживает варианты значений.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Бизнес-валидация каждого option
        |--------------------------------------------------------------------------
        */

        foreach ($options as $optionIndex => $option) {
            if (
                !is_array($option)
                || !empty($option['_delete'])
            ) {
                continue;
            }

            FormFieldOptionData::validate(
                $validator,
                $option,
                "fields.$fieldIndex.options.$optionIndex"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Коллекционные проверки
        |--------------------------------------------------------------------------
        */

        $this->validateUniqueOptionValues(
            $validator,
            $options,
            $fieldIndex
        );

        $this->validateDefaultOptions(
            $validator,
            $field,
            $options,
            $fieldIndex
        );

        $this->validateOptionOwnership(
            $validator,
            $field,
            $options,
            $fieldIndex
        );
    }

    /**
     * Проверить уникальность value
     * непосредственно внутри options одного поля.
     *
     * SQL unique не может обнаружить два одинаковых
     * новых варианта до их сохранения в БД.
     */
    protected function validateUniqueOptionValues(
        Validator $validator,
        array $options,
        int|string $fieldIndex
    ): void {
        $values = [];

        foreach ($options as $optionIndex => $option) {
            if (
                !is_array($option)
                || !empty($option['_delete'])
            ) {
                continue;
            }

            $value = $option['value'] ?? null;

            if (
                !is_string($value)
                || $value === ''
            ) {
                continue;
            }

            if (isset($values[$value])) {
                $validator->errors()->add(
                    "fields.$fieldIndex.options.$optionIndex.value",
                    'Вариант с таким системным значением уже добавлен в это поле.'
                );

                continue;
            }

            $values[$value] = $optionIndex;
        }
    }

    /**
     * Проверить количество вариантов,
     * выбранных по умолчанию.
     *
     * Для типов с supports_multiple=true
     * допускается несколько вариантов по умолчанию.
     *
     * Для остальных option-полей допускается
     * только один вариант по умолчанию.
     */
    protected function validateDefaultOptions(
        Validator $validator,
        array $field,
        array $options,
        int|string $fieldIndex
    ): void {
        $type = $field['type'] ?? null;

        if (!is_string($type)) {
            return;
        }

        $typeConfig = config(
            "forms.field_types.$type",
            []
        );

        if (
            !is_array($typeConfig)
            || !(
                $typeConfig['has_options']
                ?? false
            )
        ) {
            return;
        }

        if (
            (bool) (
                $typeConfig['supports_multiple']
                ?? false
            )
        ) {
            return;
        }

        $defaultFound = false;

        foreach ($options as $optionIndex => $option) {
            if (
                !is_array($option)
                || !empty($option['_delete'])
                || empty($option['is_default'])
            ) {
                continue;
            }

            if ($defaultFound) {
                $validator->errors()->add(
                    "fields.$fieldIndex.options.$optionIndex.is_default",
                    'Для этого типа поля можно выбрать только один вариант по умолчанию.'
                );

                continue;
            }

            $defaultFound = true;
        }
    }

    /**
     * Проверить принадлежность существующих options
     * текущему FormField.
     *
     * Новое поле не может использовать существующие
     * варианты из базы данных.
     */
    protected function validateOptionOwnership(
        Validator $validator,
        array $field,
        array $options,
        int|string $fieldIndex
    ): void {
        $fieldId = isset($field['id'])
        && is_numeric($field['id'])
            ? (int) $field['id']
            : null;

        /*
        |--------------------------------------------------------------------------
        | Новое поле
        |--------------------------------------------------------------------------
        */

        if ($fieldId === null) {
            foreach ($options as $optionIndex => $option) {
                if (!is_array($option)) {
                    continue;
                }

                if (!empty($option['id'])) {
                    $validator->errors()->add(
                        "fields.$fieldIndex.options.$optionIndex.id",
                        'Для нового поля нельзя использовать существующий вариант.'
                    );
                }

                if (!empty($option['_delete'])) {
                    $validator->errors()->add(
                        "fields.$fieldIndex.options.$optionIndex._delete",
                        'Для нового поля нельзя удалять существующие варианты.'
                    );
                }
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Существующее поле
        |--------------------------------------------------------------------------
        */

        foreach ($options as $optionIndex => $option) {
            if (
                !is_array($option)
                || empty($option['id'])
            ) {
                continue;
            }

            $optionId = (int) $option['id'];

            $belongsToField = FormFieldOption::query()
                ->whereKey($optionId)
                ->where(
                    'form_field_id',
                    $fieldId
                )
                ->exists();

            if (!$belongsToField) {
                $validator->errors()->add(
                    "fields.$fieldIndex.options.$optionIndex.id",
                    'Указанный вариант не принадлежит редактируемому полю.'
                );
            }
        }
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
    | Route helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Получить ID редактируемой формы
     * из параметров текущего маршрута.
     */
    protected function resolveFormId(): ?int
    {
        $routeForm = $this->route(
            'form'
        );

        if ($routeForm instanceof Form) {
            return (int) $routeForm->id;
        }

        if (
            is_int($routeForm)
            || (
                is_string($routeForm)
                && ctype_digit($routeForm)
            )
        ) {
            return (int) $routeForm;
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
        return $this->normalizeNullableString(
            $value
        );
    }

    /**
     * Нормализация системного кода формы.
     */
    protected function normalizeNullableCode(
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
     * Подготовка JSON-настроек формы.
     *
     * Некорректный JSON оставляется исходной строкой,
     * чтобы правило array вернуло ошибку валидации.
     */
    protected function prepareSettings(
        mixed $settings
    ): mixed {
        if (
            is_null($settings)
            || $settings === ''
        ) {
            return null;
        }

        if (is_array($settings)) {
            return $settings;
        }

        if (is_string($settings)) {
            $decoded = json_decode(
                $settings,
                true
            );

            if (
                json_last_error() === JSON_ERROR_NONE
                && is_array($decoded)
            ) {
                return $decoded;
            }

            return $settings;
        }

        return $settings;
    }
}
