<?php

namespace App\Http\Requests\Admin\Form\FormSubmission;

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
            */

            'status' => $this->normalizeNullableString(
                $this->input('status')
            ) ?: 'new',

            /*
            |--------------------------------------------------------------------------
            | Ответственный сотрудник
            |--------------------------------------------------------------------------
            */

            'assigned_user_id' => $this->filled(
                'assigned_user_id'
            )
                ? (int) $this->input(
                    'assigned_user_id'
                )
                : null,
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
                    array_keys(
                        config(
                            'forms.submission_statuses',
                            []
                        )
                    )
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
}
