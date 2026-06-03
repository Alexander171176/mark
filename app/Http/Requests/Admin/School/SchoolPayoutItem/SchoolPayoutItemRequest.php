<?php

namespace App\Http\Requests\Admin\School\SchoolPayoutItem;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SchoolPayoutItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['currency', 'title'] as $field) {
            if ($this->filled($field) && is_string($this->input($field))) {
                $data[$field] = trim($this->input($field));
            }
        }

        if (!empty($data['currency'])) {
            $data['currency'] = strtoupper($data['currency']);
        }

        foreach ([
                     'school_payout_id',
                     'school_order_id',
                     'school_order_item_id',
                     'school_course_id',
                     'school_bundle_id',
                     'school_subscription_id',
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
        return [
            'school_payout_id' => ['required', 'integer', Rule::exists('school_payouts', 'id')],

            'school_order_id' => ['nullable', 'integer', Rule::exists('school_orders', 'id')],
            'school_order_item_id' => ['nullable', 'integer', Rule::exists('school_order_items', 'id')],
            'school_course_id' => ['nullable', 'integer', Rule::exists('school_courses', 'id')],
            'school_bundle_id' => ['nullable', 'integer', Rule::exists('school_bundles', 'id')],
            'school_subscription_id' => ['nullable', 'integer', Rule::exists('school_subscriptions', 'id')],

            'currency' => ['required', 'string', 'size:3', 'alpha'],

            'amount_gross' => ['required', 'numeric', 'min:0'],
            'fee_total' => ['required', 'numeric', 'min:0'],
            'tax_total' => ['required', 'numeric', 'min:0'],
            'amount_net' => ['required', 'numeric', 'min:0'],

            'earned_at' => ['nullable', 'date'],
            'title' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'meta' => ['nullable', 'array'],
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

            if (
                $this->filled('school_order_item_id')
                && $this->filled('school_order_id')
            ) {
                $orderItem = \DB::table('school_order_items')
                    ->where('id', $this->input('school_order_item_id'))
                    ->first();

                if ($orderItem && (int) $orderItem->school_order_id !== (int) $this->input('school_order_id')) {
                    $validator->errors()->add(
                        'school_order_item_id',
                        'Позиция заказа не относится к выбранному заказу.'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'school_payout_id.required' => 'Не указана выплата.',
            'school_payout_id.exists' => 'Указанная выплата не найдена.',

            'school_order_id.exists' => 'Заказ не найден.',
            'school_order_item_id.exists' => 'Позиция заказа не найдена.',
            'school_course_id.exists' => 'Курс не найден.',
            'school_bundle_id.exists' => 'Набор курсов не найден.',
            'school_subscription_id.exists' => 'Подписка не найдена.',

            'currency.required' => 'Укажите валюту.',
            'currency.size' => 'Код валюты должен состоять из 3 букв.',
            'currency.alpha' => 'Код валюты должен содержать только буквы.',

            'amount_gross.required' => 'Укажите сумму брутто.',
            'amount_gross.numeric' => 'Сумма брутто должна быть числом.',
            'amount_gross.min' => 'Сумма брутто не может быть отрицательной.',

            'fee_total.required' => 'Укажите сумму комиссий.',
            'fee_total.numeric' => 'Сумма комиссий должна быть числом.',
            'fee_total.min' => 'Комиссии не могут быть отрицательными.',

            'tax_total.required' => 'Укажите сумму налогов/удержаний.',
            'tax_total.numeric' => 'Сумма налогов должна быть числом.',
            'tax_total.min' => 'Налоги/удержания не могут быть отрицательными.',

            'amount_net.required' => 'Укажите сумму нетто.',
            'amount_net.numeric' => 'Сумма нетто должна быть числом.',
            'amount_net.min' => 'Сумма нетто не может быть отрицательной.',

            'earned_at.date' => 'Дата начисления дохода указана некорректно.',
            'title.max' => 'Подпись позиции не должна превышать 255 символов.',
            'notes.string' => 'Заметки должны быть текстом.',
            'meta.array' => 'Поле meta должно быть объектом/массивом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'school_payout_id' => 'Выплата',
            'school_order_id' => 'Заказ',
            'school_order_item_id' => 'Позиция заказа',
            'school_course_id' => 'Курс',
            'school_bundle_id' => 'Набор курсов',
            'school_subscription_id' => 'Подписка',
            'currency' => 'Валюта',
            'amount_gross' => 'Сумма брутто',
            'fee_total' => 'Комиссии',
            'tax_total' => 'Налоги/удержания',
            'amount_net' => 'Сумма нетто',
            'earned_at' => 'Дата начисления',
            'title' => 'Подпись позиции',
            'notes' => 'Заметки',
            'meta' => 'Метаданные',
        ];
    }
}
