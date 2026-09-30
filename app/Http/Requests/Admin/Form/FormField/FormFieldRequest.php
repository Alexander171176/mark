<?php

namespace App\Http\Requests\Admin\Form\FormField;

use App\Models\Admin\Form\FormField\FormField;
use App\Support\Admin\Form\FormFieldData;
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
     * Остальные данные нормализуются через общий FormFieldData.
     */
    protected function prepareForValidation(): void
    {
        $supportedLocales = config(
            'app.available_locales',
            ['ru']
        );

        $fieldData = FormFieldData::prepare(
            $this->all(),
            $supportedLocales
        );

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
     * form_id проверяется самим Request,
     * поскольку во вложенном поле FormRequest
     * form_id отсутствует.
     *
     * Остальной контракт поля берётся
     * из общего FormFieldData.
     */
    public function rules(): array
    {
        $formId = $this->filled('form_id')
            ? (int) $this->input('form_id')
            : null;

        $fieldId = $this->resolveFieldId();

        return [
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
            ) + $this->localeRules(
                config(
                    'app.available_locales',
                    ['ru']
                )
            );
    }

    /**
     * Дополнительная проверка данных поля.
     *
     * Вся логика:
     * - динамических validation rules;
     * - настроек типов полей;
     * - логической совместимости параметров
     *
     * находится в общем FormFieldData.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                FormFieldData::validate(
                    $validator,
                    $this->all()
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
            ] + FormFieldData::messages();
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
}
