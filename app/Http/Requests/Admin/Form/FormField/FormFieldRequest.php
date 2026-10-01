<?php

namespace App\Http\Requests\Admin\Form\FormField;

use App\Models\Admin\Form\FormField\FormField;
use App\Models\Admin\Form\FormFieldOption\FormFieldOption;
use App\Support\Admin\Form\FormFieldData;
use App\Support\Admin\Form\FormFieldOptionData;
use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
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
     *
     * form_id относится только к самостоятельному CRUD поля.
     * Остальные данные, включая options, нормализуются
     * через общий FormFieldData.
     *
     * Служебный флаг _delete вариантов восстанавливается
     * отдельно, поскольку он относится к механизму
     * редактирования, а не к FormFieldOptionData.
     */
    protected function prepareForValidation(): void
    {
        $supportedLocales = config(
            'app.available_locales',
            ['ru']
        );

        $originalOptions = $this->input(
            'options',
            []
        );

        if (!is_array($originalOptions)) {
            $originalOptions = [];
        }

        $fieldData = FormFieldData::prepare(
            $this->all(),
            $supportedLocales
        );

        $preparedOptions = $fieldData['options'] ?? [];

        foreach ($preparedOptions as $index => &$option) {
            $originalOption = $originalOptions[$index] ?? [];

            $option['_delete'] = is_array($originalOption)
                ? $this->toBoolean(
                    $originalOption['_delete'] ?? false
                )
                : false;
        }

        unset($option);

        $fieldData['options'] = $preparedOptions;

        $this->replace([
            ...$this->all(),

            'form_id' => $this->filled('form_id')
                ? (int) $this->input('form_id')
                : null,

            ...$fieldData,
        ]);
    }

    /**
     * Правила валидации.
     *
     * Основной контракт поля берётся из FormFieldData.
     * Варианты значения валидируются отдельно через
     * FormFieldOptionData.
     */
    public function rules(): array
    {
        $formId = $this->filled('form_id')
            ? (int) $this->input('form_id')
            : null;

        $fieldId = $this->resolveFieldId();

        $rules = [
                'form_id' => [
                    'required',
                    'integer',
                    Rule::exists(
                        'forms',
                        'id'
                    ),
                ],
            ] + FormFieldData::rules(
                formId: $formId,
                fieldId: $fieldId,
                type: $this->input('type')
            );

        /*
        |--------------------------------------------------------------------------
        | Варианты значений
        |--------------------------------------------------------------------------
        */

        $rules['options'] = [
            'nullable',
            'array',
        ];

        $rules['options.*'] = [
            'required',
            'array',
        ];

        $options = $this->input(
            'options',
            []
        );

        if (is_array($options)) {
            foreach ($options as $index => $option) {
                if (!is_array($option)) {
                    continue;
                }

                $optionId = isset($option['id'])
                && is_numeric($option['id'])
                    ? (int) $option['id']
                    : null;

                $delete = (bool) (
                    $option['_delete']
                    ?? false
                );

                $prefix = "options.$index";

                /*
                |--------------------------------------------------------------------------
                | Служебный флаг удаления
                |--------------------------------------------------------------------------
                */

                $rules["$prefix._delete"] = [
                    'required',
                    'boolean',
                ];

                /*
                |--------------------------------------------------------------------------
                | Удаление существующего варианта
                |--------------------------------------------------------------------------
                */

                if ($delete) {
                    $rules["$prefix.id"] = [
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
                    prefix: $prefix,
                    formFieldId: $fieldId,
                    optionId: $optionId
                );
            }
        }

        return $rules + $this->localeRules(
                config(
                    'app.available_locales',
                    ['ru']
                )
            );
    }

    /**
     * Дополнительная проверка данных поля
     * и его вариантов значений.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                /*
                |--------------------------------------------------------------------------
                | Поле
                |--------------------------------------------------------------------------
                */

                FormFieldData::validate(
                    $validator,
                    $this->all()
                );

                /*
                |--------------------------------------------------------------------------
                | Варианты значений
                |--------------------------------------------------------------------------
                */

                $this->validateFieldOptions(
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
                'form_id.required' =>
                    'Необходимо указать форму, которой принадлежит поле.',

                'form_id.integer' =>
                    'ID формы должен быть числом.',

                'form_id.exists' =>
                    'Указанная форма не найдена.',

                'options.array' =>
                    'Варианты значений поля должны быть массивом.',

                'options.*.array' =>
                    'Некорректный формат варианта значения.',

                'options.*._delete.required' =>
                    'Не передан признак удаления варианта.',

                'options.*._delete.boolean' =>
                    'Признак удаления варианта должен быть логическим значением.',

                'options.*.id.required' =>
                    'Для удаления варианта необходимо указать его ID.',

                'options.*.id.integer' =>
                    'ID варианта должен быть числом.',

                'options.*.id.exists' =>
                    'Указанный вариант значения не найден.',
            ]
            + FormFieldData::messages()
            + FormFieldOptionData::messages('options.*');
    }

    /*
    |--------------------------------------------------------------------------
    | Option validation
    |--------------------------------------------------------------------------
    */

    /**
     * Проверка вариантов значений текущего поля.
     */
    protected function validateFieldOptions(
        Validator $validator
    ): void {
        $options = $this->input(
            'options',
            []
        );

        if (!is_array($options)) {
            return;
        }

        $type = $this->input('type');

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
        | Проверяем наличие актуальных вариантов
        |--------------------------------------------------------------------------
        |
        | Варианты с _delete=true не считаются актуальными,
        | поскольку они являются командами удаления.
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

        /*
        |--------------------------------------------------------------------------
        | Тип поля не поддерживает options
        |--------------------------------------------------------------------------
        */

        if (
            !$supportsOptions
            && $hasActualOptions
        ) {
            $validator->errors()->add(
                'options',
                'Выбранный тип поля не поддерживает варианты значений.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Бизнес-валидация каждого варианта
        |--------------------------------------------------------------------------
        */

        foreach ($options as $index => $option) {
            if (
                !is_array($option)
                || !empty($option['_delete'])
            ) {
                continue;
            }

            FormFieldOptionData::validate(
                $validator,
                $option,
                "options.$index"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Проверки коллекции
        |--------------------------------------------------------------------------
        */

        $this->validateUniqueOptionValues(
            $validator,
            $options
        );

        $this->validateDefaultOptions(
            $validator,
            $options
        );

        $this->validateOptionOwnership(
            $validator,
            $options
        );
    }

    /**
     * Проверить уникальность value
     * внутри вариантов текущего поля.
     *
     * SQL unique не обнаружит два одинаковых
     * новых варианта до их сохранения.
     */
    protected function validateUniqueOptionValues(
        Validator $validator,
        array $options
    ): void {
        $values = [];

        foreach ($options as $index => $option) {
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
                    "options.$index.value",
                    'Вариант с таким системным значением уже добавлен в это поле.'
                );

                continue;
            }

            $values[$value] = $index;
        }
    }

    /**
     * Проверить количество вариантов
     * со значением is_default=true.
     *
     * select / radio допускают только один default.
     * checkbox_group и другие типы с
     * supports_multiple=true могут иметь несколько.
     */
    protected function validateDefaultOptions(
        Validator $validator,
        array $options
    ): void {
        $type = $this->input('type');

        $typeConfig = is_string($type)
            ? config(
                "forms.field_types.$type",
                []
            )
            : [];

        $supportsMultiple = is_array($typeConfig)
            && (bool) (
                $typeConfig['supports_multiple']
                ?? false
            );

        if ($supportsMultiple) {
            return;
        }

        $defaultIndexes = [];

        foreach ($options as $index => $option) {
            if (
                !is_array($option)
                || !empty($option['_delete'])
                || empty($option['is_default'])
            ) {
                continue;
            }

            $defaultIndexes[] = $index;
        }

        if (count($defaultIndexes) <= 1) {
            return;
        }

        foreach (
            array_slice(
                $defaultIndexes,
                1
            ) as $index
        ) {
            $validator->errors()->add(
                "options.$index.is_default",
                'Для этого типа поля можно выбрать только один вариант по умолчанию.'
            );
        }
    }

    /**
     * Проверить принадлежность существующих
     * вариантов редактируемому полю.
     *
     * Нельзя передать ID варианта другого поля
     * и изменить или удалить его через этот Request.
     */
    protected function validateOptionOwnership(
        Validator $validator,
        array $options
    ): void {
        $fieldId = $this->resolveFieldId();

        /*
         * При создании самостоятельного поля
         * существующих вариантов быть не должно.
         *
         * После удаления standalone Create этот сценарий
         * фактически останется только защитным.
         */
        if ($fieldId === null) {
            foreach ($options as $index => $option) {
                if (
                    is_array($option)
                    && !empty($option['id'])
                ) {
                    $validator->errors()->add(
                        "options.$index.id",
                        'Нельзя использовать существующий вариант при создании нового поля.'
                    );
                }
            }

            return;
        }

        foreach ($options as $index => $option) {
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
                    "options.$index.id",
                    'Указанный вариант не принадлежит редактируемому полю.'
                );
            }
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
    | Value helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Приведение значения к boolean.
     */
    protected function toBoolean(
        mixed $value,
        bool $default = false
    ): bool {
        if ($value === null) {
            return $default;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            return $value === 1;
        }

        if (is_string($value)) {
            $normalized = strtolower(
                trim($value)
            );

            if (in_array(
                $normalized,
                ['1', 'true', 'on', 'yes'],
                true
            )) {
                return true;
            }

            if (in_array(
                $normalized,
                ['0', 'false', 'off', 'no', ''],
                true
            )) {
                return false;
            }
        }

        return (bool) $value;
    }
}
