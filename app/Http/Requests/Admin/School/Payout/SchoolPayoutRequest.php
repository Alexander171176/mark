<?php

namespace App\Http\Requests\Admin\School\Payout;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SchoolPayoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['number', 'status', 'method', 'currency'] as $field) {
            if ($this->filled($field) && is_string($this->input($field))) {
                $data[$field] = trim($this->input($field));
            }
        }

        if (!empty($data['currency'])) {
            $data['currency'] = strtoupper($data['currency']);
        }

        foreach ([
                     'school_instructor_profile_id',
                     'school_provider_account_id',
                     'created_by',
                     'updated_by',
                 ] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        foreach (['amount_gross', 'fee_total', 'tax_total', 'amount_net'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = is_string($this->input($field))
                    ? str_replace(',', '.', trim($this->input($field)))
                    : $this->input($field);
            }
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
        $payout = $this->route('school_payout')
            ?? $this->route('payout')
            ?? $this->route('schoolPayout');

        $id = is_object($payout)
            ? $payout->id
            : ($payout ? (int) $payout : null);

        return [
            'school_instructor_profile_id' => [
                'required',
                'integer',
                Rule::exists('school_instructor_profiles', 'id'),
            ],

            'school_provider_account_id' => [
                'nullable',
                'integer',
                Rule::exists('school_provider_accounts', 'id'),
            ],

            'number' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:32',
                Rule::unique('school_payouts', 'number')->ignore($id),
            ],

            'period_start' => ['nullable', 'date'],
            'period_end' => ['nullable', 'date', 'after_or_equal:period_start'],

            'currency' => ['required', 'string', 'size:3', 'alpha'],

            'amount_gross' => ['required', 'numeric', 'min:0'],
            'fee_total' => ['required', 'numeric', 'min:0'],
            'tax_total' => ['required', 'numeric', 'min:0'],
            'amount_net' => ['required', 'numeric', 'min:0'],

            'status' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                Rule::in(['pending', 'processing', 'paid', 'failed', 'cancelled']),
            ],

            'method' => [
                'nullable',
                'string',
                'max:32',
                Rule::in(['manual', 'bank_wire', 'stripe_transfer', 'paypal_payout']),
            ],

            'paid_at' => ['nullable', 'date'],

            'notes' => ['nullable', 'string'],
            'meta' => ['nullable', 'array'],

            'created_by' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'updated_by' => ['nullable', 'integer', Rule::exists('users', 'id')],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->filled(['amount_gross', 'fee_total', 'tax_total', 'amount_net'])) {
                $expectedNet = (float) $this->input('amount_gross')
                    - (float) $this->input('fee_total')
                    - (float) $this->input('tax_total');

                $actualNet = (float) $this->input('amount_net');

                if (abs($expectedNet - $actualNet) > 0.01) {
                    $validator->errors()->add(
                        'amount_net',
                        'Сумма нетто должна равняться amount_gross - fee_total - tax_total.'
                    );
                }
            }

            if ($this->input('status') === 'paid' && !$this->filled('paid_at')) {
                $validator->errors()->add(
                    'paid_at',
                    'Для статуса paid необходимо указать дату выплаты.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'school_instructor_profile_id.required' => 'Укажите получателя выплаты.',
            'school_instructor_profile_id.exists' => 'Выбранный инструктор не найден.',

            'school_provider_account_id.exists' => 'Аккаунт платёжного провайдера не найден.',

            'number.required' => 'Укажите номер выплаты.',
            'number.unique' => 'Такой номер выплаты уже существует.',
            'number.max' => 'Номер выплаты не должен превышать 32 символа.',

            'period_start.date' => 'Дата начала периода указана некорректно.',
            'period_end.date' => 'Дата окончания периода указана некорректно.',
            'period_end.after_or_equal' => 'Дата окончания периода не может быть раньше даты начала.',

            'currency.required' => 'Укажите валюту.',
            'currency.size' => 'Код валюты должен состоять из 3 букв.',
            'currency.alpha' => 'Код валюты должен содержать только буквы.',

            'amount_gross.required' => 'Укажите сумму брутто.',
            'amount_gross.numeric' => 'Сумма брутто должна быть числом.',
            'amount_gross.min' => 'Сумма брутто не может быть отрицательной.',

            'fee_total.required' => 'Укажите сумму комиссий.',
            'fee_total.numeric' => 'Сумма комиссий должна быть числом.',
            'fee_total.min' => 'Сумма комиссий не может быть отрицательной.',

            'tax_total.required' => 'Укажите сумму налогов/удержаний.',
            'tax_total.numeric' => 'Сумма налогов должна быть числом.',
            'tax_total.min' => 'Сумма налогов не может быть отрицательной.',

            'amount_net.required' => 'Укажите сумму к выплате.',
            'amount_net.numeric' => 'Сумма к выплате должна быть числом.',
            'amount_net.min' => 'Сумма к выплате не может быть отрицательной.',

            'status.required' => 'Укажите статус выплаты.',
            'status.in' => 'Недопустимый статус выплаты.',

            'method.in' => 'Недопустимый способ выплаты.',
            'method.max' => 'Способ выплаты не должен превышать 32 символа.',

            'paid_at.date' => 'Дата выплаты указана некорректно.',

            'notes.string' => 'Заметки должны быть текстом.',
            'meta.array' => 'Поле meta должно быть объектом/массивом.',

            'created_by.exists' => 'Пользователь created_by не найден.',
            'updated_by.exists' => 'Пользователь updated_by не найден.',
        ];
    }

    public function attributes(): array
    {
        return [
            'school_instructor_profile_id' => 'Инструктор',
            'school_provider_account_id' => 'Аккаунт провайдера',
            'number' => 'Номер выплаты',
            'period_start' => 'Начало периода',
            'period_end' => 'Конец периода',
            'currency' => 'Валюта',
            'amount_gross' => 'Сумма брутто',
            'fee_total' => 'Комиссии',
            'tax_total' => 'Налоги/удержания',
            'amount_net' => 'Сумма к выплате',
            'status' => 'Статус',
            'method' => 'Способ выплаты',
            'paid_at' => 'Дата выплаты',
            'notes' => 'Заметки',
            'meta' => 'Метаданные',
            'created_by' => 'Кто создал',
            'updated_by' => 'Кто обновил',
        ];
    }
}
