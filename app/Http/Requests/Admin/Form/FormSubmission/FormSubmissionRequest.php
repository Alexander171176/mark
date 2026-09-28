<?php

namespace App\Http\Requests\Admin\Form\FormSubmission;

use App\Models\Admin\Form\FormSubmission\FormSubmission;
use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use Illuminate\Validation\Rule;

class FormSubmissionRequest extends BaseFormRequest
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
        $this->merge([
            /*
            |--------------------------------------------------------------------------
            | Статус заявки
            |--------------------------------------------------------------------------
            |
            | Не подставляем STATUS_NEW автоматически.
            |
            | Административный Request используется для обновления
            | существующей заявки, поэтому отсутствие status должно
            | приводить к ошибке валидации, а не к неожиданному
            | сбросу текущего статуса в "new".
            |
            */

            'status' => $this->normalizeNullableString(
                $this->input('status')
            ),

            /*
            |--------------------------------------------------------------------------
            | Ответственный сотрудник
            |--------------------------------------------------------------------------
            |
            | null означает, что заявка не назначена сотруднику.
            |
            | Некорректное значение специально не приводим к 0,
            | чтобы Laravel корректно вернул ошибку правила integer.
            |
            */

            'assigned_user_id' =>
                $this->normalizeNullableInteger(
                    $this->input(
                        'assigned_user_id'
                    )
                ),
        ]);
    }

    /**
     * Правила валидации.
     */
    public function rules(): array
    {
        return [
            /*
            |--------------------------------------------------------------------------
            | Статус заявки
            |--------------------------------------------------------------------------
            */

            'status' => [
                'required',
                'string',
                'max:50',

                Rule::in(
                    $this->allowedStatuses()
                ),
            ],

            /*
            |--------------------------------------------------------------------------
            | Ответственный сотрудник
            |--------------------------------------------------------------------------
            */

            'assigned_user_id' => [
                'nullable',
                'integer',

                Rule::exists(
                    'users',
                    'id'
                ),
            ],
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
            | Статус заявки
            |--------------------------------------------------------------------------
            */

            'status.required' =>
                'Необходимо указать статус заявки.',

            'status.string' =>
                'Статус заявки должен быть строкой.',

            'status.max' =>
                'Статус заявки не должен превышать 50 символов.',

            'status.in' =>
                'Указан недопустимый статус заявки.',

            /*
            |--------------------------------------------------------------------------
            | Ответственный сотрудник
            |--------------------------------------------------------------------------
            */

            'assigned_user_id.integer' =>
                'ID ответственного сотрудника должен быть числом.',

            'assigned_user_id.exists' =>
                'Указанный ответственный сотрудник не найден.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Status helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Получить список допустимых статусов заявки.
     *
     * Основным источником является config/forms.php.
     *
     * Константы модели используются как безопасный fallback,
     * если конфигурация по какой-либо причине отсутствует.
     *
     * @return array<int, string>
     */
    protected function allowedStatuses(): array
    {
        $statuses = array_keys(
            config(
                'forms.submission_statuses',
                []
            )
        );

        if ($statuses !== []) {
            return $statuses;
        }

        return [
            FormSubmission::STATUS_NEW,
            FormSubmission::STATUS_PROCESSING,
            FormSubmission::STATUS_COMPLETED,
            FormSubmission::STATUS_CANCELLED,
            FormSubmission::STATUS_SPAM,
        ];
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
     * Нормализация nullable integer.
     *
     * Важно:
     *
     * null / ""  -> null
     * 15         -> 15
     * "15"       -> 15
     * "abc"      -> "abc"
     *
     * Некорректное значение оставляем как есть,
     * чтобы Laravel вернул ошибку правила integer,
     * а не преобразовал его в 0.
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
}
