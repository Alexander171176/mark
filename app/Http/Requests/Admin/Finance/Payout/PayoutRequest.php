<?php

namespace App\Http\Requests\Admin\Finance\Payout;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PayoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ограничьте Policy/гейтами при необходимости
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        // Трим строк
        foreach (['number','status','method','currency'] as $k) {
            if ($this->filled($k) && is_string($this->input($k))) {
                $merge[$k] = trim($this->input($k));
            }
        }

        // Нормализация валюты в верхний регистр
        if (!empty($merge['currency'])) {
            $merge['currency'] = strtoupper($merge['currency']);
        }

        // Попытка распарсить JSON-строки
        foreach (['meta'] as $jsonK) {
            if ($this->filled($jsonK) && is_string($this->input($jsonK))) {
                $decoded = json_decode($this->input($jsonK), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $merge[$jsonK] = $decoded;
                }
            }
        }

        if ($merge) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        $routeModel = $this->route('payout');
        $id = is_object($routeModel) ? $routeModel->id : ($routeModel ? (int)$routeModel : null);

        return [
            'instructor_profile_id' => ['required','integer','exists:instructor_profiles,id'],
            'provider_account_id'   => ['nullable','integer','exists:provider_accounts,id'],

            'number'   => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string','max:32',
                Rule::unique('payouts','number')->ignore($id),
            ],

            'period_start' => ['nullable','date'],
            'period_end'   => ['nullable','date','after_or_equal:period_start'],

            'currency'     => ['required','string','size:3'],
            'amount_gross' => ['required','numeric','min:0'],
            'fee_total'    => ['required','numeric','min:0'],
            'tax_total'    => ['required','numeric','min:0'],
            'amount_net'   => ['required','numeric','min:0'],

            'status' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                Rule::in(['pending','processing','paid','failed','cancelled']),
            ],
            'method' => ['nullable','string','max:32'],

            'paid_at' => ['nullable','date'],

            'notes' => ['nullable','string'],
            'meta'  => ['nullable','array'],

            'created_by' => ['nullable','integer','exists:users,id'],
            'updated_by' => ['nullable','integer','exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'instructor_profile_id.required' => 'Укажите получателя выплаты (инструктора).',
            'instructor_profile_id.exists'   => 'Выбранный инструктор не найден.',

            'provider_account_id.exists'     => 'Указанный аккаунт провайдера не найден.',

            'number.required' => 'Номер выплаты обязателен.',
            'number.unique'   => 'Такой номер выплаты уже существует.',
            'number.max'      => 'Номер выплаты слишком длинный.',

            'period_end.after_or_equal' => 'Дата окончания периода должна быть не раньше даты начала.',

            'currency.required' => 'Укажите валюту выплаты.',
            'currency.size'     => 'Код валюты должен быть в формате ISO 4217 (3 буквы).',

            'amount_gross.required' => 'Укажите сумму начислений (брутто).',
            'amount_gross.min'      => 'Сумма брутто не может быть отрицательной.',
            'fee_total.required'    => 'Укажите сумму комиссий.',
            'fee_total.min'         => 'Комиссии не могут быть отрицательными.',
            'tax_total.required'    => 'Укажите сумму налогов/удержаний.',
            'tax_total.min'         => 'Налоги/удержания не могут быть отрицательными.',
            'amount_net.required'   => 'Укажите сумму к выплате (нетто).',
            'amount_net.min'        => 'Нетто не может быть отрицательной.',

            'status.required' => 'Укажите статус выплаты.',
            'status.in'       => 'Недопустимый статус выплаты.',
            'method.max'      => 'Слишком длинное значение поля способа выплаты.',

            'meta.array'      => 'Поле meta должно быть объектом/массивом.',

            'created_by.exists' => 'Пользователь (created_by) не найден.',
            'updated_by.exists' => 'Пользователь (updated_by) не найден.',
        ];
    }

    public function attributes(): array
    {
        return [
            'instructor_profile_id' => 'инструктор',
            'provider_account_id'   => 'аккаунт провайдера',
            'number'                => 'номер выплаты',
            'period_start'          => 'начало периода',
            'period_end'            => 'конец периода',
            'currency'              => 'валюта',
            'amount_gross'          => 'сумма брутто',
            'fee_total'             => 'комиссии',
            'tax_total'             => 'налоги/удержания',
            'amount_net'            => 'сумма к выплате',
            'status'                => 'статус',
            'method'                => 'способ выплаты',
            'paid_at'               => 'дата выплаты',
            'notes'                 => 'примечания',
            'meta'                  => 'метаданные',
            'created_by'            => 'создал',
            'updated_by'            => 'обновил',
        ];
    }
}
