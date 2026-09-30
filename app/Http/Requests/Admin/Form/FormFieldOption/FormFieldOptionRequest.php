<?php

namespace App\Http\Requests\Admin\Form\FormFieldOption;

use App\Models\Admin\Form\FormField\FormField;
use App\Models\Admin\Form\FormFieldOption\FormFieldOption;
use App\Support\Admin\Form\FormFieldOptionData;
use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
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
     *
     * form_field_id относится только к самостоятельному CRUD варианта.
     * Остальные данные нормализуются через общий FormFieldOptionData.
     */
    protected function prepareForValidation(): void
    {
        $supportedLocales = config(
            'app.available_locales',
            ['ru']
        );

        $optionData = FormFieldOptionData::prepare(
            $this->all(),
            $supportedLocales
        );

        $this->replace([
            ...$this->all(),

            'form_field_id' => $this->filled('form_field_id')
                ? (int) $this->input('form_field_id')
                : null,

            ...$optionData,
        ]);
    }

    /**
     * Правила валидации.
     *
     * form_field_id проверяется самим Request,
     * поскольку во вложенном варианте FormRequest
     * form_field_id отсутствует.
     *
     * Остальной контракт варианта берётся
     * из общего FormFieldOptionData.
     */
    public function rules(): array
    {
        $formFieldId = $this->filled('form_field_id')
            ? (int) $this->input('form_field_id')
            : null;

        $optionId = $this->resolveOptionId();

        return [
                'form_field_id' => [
                    'required',
                    'integer',
                    Rule::exists(
                        'form_fields',
                        'id'
                    ),
                ],
            ] + FormFieldOptionData::rules(
                formFieldId: $formFieldId,
                optionId: $optionId
            ) + $this->localeRules(
                config(
                    'app.available_locales',
                    ['ru']
                )
            );
    }

    /**
     * Дополнительная проверка данных варианта.
     *
     * Общая бизнес-валидация находится
     * в FormFieldOptionData.
     *
     * Проверка поддержки вариантов родительским полем
     * относится только к самостоятельному CRUD,
     * поэтому остаётся в Request.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                FormFieldOptionData::validate(
                    $validator,
                    $this->all()
                );

                /*
                |--------------------------------------------------------------------------
                | Проверка родительского поля
                |--------------------------------------------------------------------------
                |
                | Если form_field_id уже не прошёл базовую валидацию,
                | выполнять дополнительный запрос к БД нет необходимости.
                |
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
                'form_field_id.required' =>
                    'Необходимо указать поле формы.',

                'form_field_id.integer' =>
                    'ID поля формы должен быть числом.',

                'form_field_id.exists' =>
                    'Указанное поле формы не найдено.',
            ] + FormFieldOptionData::messages();
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

        if ($routeOption instanceof FormFieldOption) {
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
}
