<?php

namespace App\Http\Requests\Admin\School\Certificate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['number', 'verification_code', 'name_on_certificate', 'status'] as $field) {
            if ($this->filled($field) && is_string($this->input($field))) {
                $data[$field] = trim($this->input($field));
            }
        }

        if (!empty($data['status'])) {
            $data['status'] = strtolower($data['status']);
        }

        foreach (['user_id', 'school_course_id', 'school_enrollment_id', 'score'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        if ($this->filled('hours')) {
            $data['hours'] = is_string($this->input('hours'))
                ? str_replace(',', '.', trim($this->input('hours')))
                : $this->input('hours');
        }

        if ($this->filled('meta') && is_string($this->input('meta'))) {
            $decoded = json_decode($this->input('meta'), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $data['meta'] = $decoded;
            }
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $certificate = $this->route('school_certificate')
            ?? $this->route('certificate')
            ?? $this->route('schoolCertificate');

        $id = is_object($certificate)
            ? $certificate->id
            : ($certificate ? (int) $certificate : null);

        return [
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],

            'school_course_id' => [
                'required',
                'integer',
                Rule::exists('school_courses', 'id'),
                Rule::unique('school_certificates', 'school_course_id')
                    ->where(fn ($q) => $q->where('user_id', $this->input('user_id')))
                    ->ignore($id),
            ],

            'school_enrollment_id' => ['nullable', 'integer', Rule::exists('school_enrollments', 'id')],

            'number' => [
                'required',
                'string',
                'max:32',
                Rule::unique('school_certificates', 'number')->ignore($id),
            ],

            'verification_code' => [
                'required',
                'string',
                'max:64',
                Rule::unique('school_certificates', 'verification_code')->ignore($id),
            ],

            'issued_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:issued_at'],
            'revoked_at' => ['nullable', 'date'],

            'score' => ['nullable', 'integer', 'between:0,100'],
            'hours' => ['nullable', 'numeric', 'min:0'],

            'status' => ['required', Rule::in(['draft', 'issued', 'revoked', 'expired'])],

            'name_on_certificate' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'meta' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Укажите пользователя.',
            'user_id.exists' => 'Пользователь не найден.',

            'school_course_id.required' => 'Укажите курс.',
            'school_course_id.exists' => 'Курс не найден.',
            'school_course_id.unique' => 'Для этого пользователя сертификат по выбранному курсу уже существует.',

            'school_enrollment_id.exists' => 'Зачисление не найдено.',

            'number.required' => 'Укажите номер сертификата.',
            'number.unique' => 'Такой номер сертификата уже существует.',

            'verification_code.required' => 'Укажите код проверки.',
            'verification_code.unique' => 'Такой код проверки уже существует.',

            'expires_at.after_or_equal' => 'Дата истечения не может быть раньше даты выдачи.',

            'score.between' => 'Оценка должна быть от 0 до 100.',
            'hours.min' => 'Количество часов не может быть отрицательным.',

            'status.required' => 'Укажите статус сертификата.',
            'status.in' => 'Недопустимый статус сертификата.',

            'meta.array' => 'Поле meta должно быть объектом/массивом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'Пользователь',
            'school_course_id' => 'Курс',
            'school_enrollment_id' => 'Зачисление',
            'number' => 'Номер сертификата',
            'verification_code' => 'Код проверки',
            'issued_at' => 'Дата выдачи',
            'expires_at' => 'Дата истечения',
            'revoked_at' => 'Дата отзыва',
            'score' => 'Итоговая оценка',
            'hours' => 'Академические часы',
            'status' => 'Статус',
            'name_on_certificate' => 'Имя на сертификате',
            'notes' => 'Заметки',
            'meta' => 'Метаданные',
        ];
    }
}
