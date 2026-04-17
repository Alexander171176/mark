<?php

namespace App\Http\Requests\Admin\School\Certificate;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach (['number','verification_code','name_on_certificate'] as $field) {
            if ($this->filled($field)) {
                $merge[$field] = trim((string) $this->input($field));
            }
        }
        if ($this->filled('status')) {
            $merge['status'] = strtolower((string) $this->input('status'));
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    /**
     * @return array<string,ValidationRule|array|string>
     */
    public function rules(): array
    {
        $id = $this->route('certificate')?->id ?? $this->route('id');

        $statusEnum = ['draft','issued','revoked','expired'];

        return [
            'user_id'           => ['required','integer','exists:users,id'],
            'course_id'         => [
                'required','integer','exists:courses,id',
                // Один сертификат на пользователя по одному курсу
                Rule::unique('certificates')
                    ->where(fn ($q) => $q->where('user_id', $this->input('user_id')))
                    ->ignore($id),
            ],
            'enrollment_id'     => ['nullable','integer','exists:enrollments,id'],

            'number'            => [
                'required','string','max:32',
                Rule::unique('certificates', 'number')->ignore($id),
            ],
            'verification_code' => [
                'required','string','max:64',
                Rule::unique('certificates', 'verification_code')->ignore($id),
            ],

            'issued_at'         => ['nullable','date'],
            'expires_at'        => ['nullable','date','after_or_equal:issued_at'],
            'revoked_at'        => ['nullable','date'],

            'score'             => ['nullable','integer','min:0','max:100'],
            'hours'             => ['nullable','numeric','min:0'],

            'status'            => ['required', Rule::in($statusEnum)],

            'name_on_certificate' => ['nullable','string','max:255'],
            'notes'               => ['nullable','string'],
            'meta'                => ['nullable','array'],
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id'             => 'пользователь',
            'course_id'           => 'курс',
            'enrollment_id'       => 'зачисление',
            'number'              => 'номер сертификата',
            'verification_code'   => 'код проверки',
            'issued_at'           => 'дата выдачи',
            'expires_at'          => 'дата истечения',
            'revoked_at'          => 'дата отзыва',
            'score'               => 'итоговый балл',
            'hours'               => 'академические часы',
            'status'              => 'статус',
            'name_on_certificate' => 'имя на сертификате',
            'notes'               => 'примечания',
        ];
    }

    public function messages(): array
    {
        return [
            'course_id.unique' => 'Для этого пользователя сертификат по выбранному курсу уже существует.',
        ];
    }
}
