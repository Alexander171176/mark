<?php

namespace App\Http\Requests\Admin\Finance\Invoice;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Подключите Policy при необходимости
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach ([
                     'number','status','currency','bill_to_name','bill_to_tax_id','bill_to_email',
                     'bill_to_address1','bill_to_address2','bill_to_city','bill_to_region',
                     'bill_to_postcode','bill_to_country','notes'
                 ] as $key) {
            if ($this->filled($key) && is_string($this->input($key))) {
                $merge[$key] = trim($this->input($key));
            }
        }

        if (!empty($merge['currency'])) {
            $merge['currency'] = strtoupper($merge['currency']);
        }
        if (!empty($merge['bill_to_country'])) {
            $merge['bill_to_country'] = strtoupper($merge['bill_to_country']);
        }

        // meta: принять строковый JSON
        if ($this->filled('meta') && is_string($this->input('meta'))) {
            $decoded = json_decode($this->input('meta'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $merge['meta'] = $decoded;
            }
        }

        if ($merge) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        $routeModel = $this->route('invoice');
        $id = is_object($routeModel) ? $routeModel->id : ($routeModel ? (int)$routeModel : null);

        $statuses = ['draft','issued','paid','void','refunded'];

        return [
            'order_id'        => [$this->isMethod('post') ? 'required' : 'sometimes', 'integer','exists:orders,id'],

            'number'          => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string','max:50',
                Rule::unique('invoices','number')->ignore($id),
            ],

            'status'          => ['sometimes','string', Rule::in($statuses)],

            'currency'        => ['sometimes','string','size:3','alpha'],
            'subtotal'        => ['sometimes','numeric','gte:0'],
            'discount_total'  => ['sometimes','numeric','gte:0'],
            'tax_total'       => ['sometimes','numeric','gte:0'],
            'total'           => ['sometimes','numeric','gte:0'],

            'issued_at'       => ['nullable','date'],
            'due_at'          => ['nullable','date'],
            'paid_at'         => ['nullable','date'],

            // Реквизиты плательщика
            'bill_to_name'     => ['nullable','string','max:255'],
            'bill_to_tax_id'   => ['nullable','string','max:64'],
            'bill_to_email'    => ['nullable','email','max:255'],
            'bill_to_address1' => ['nullable','string','max:255'],
            'bill_to_address2' => ['nullable','string','max:255'],
            'bill_to_city'     => ['nullable','string','max:191'],
            'bill_to_region'   => ['nullable','string','max:191'],
            'bill_to_postcode' => ['nullable','string','max:32'],
            'bill_to_country'  => ['nullable','string','size:2'],

            'notes'           => ['nullable','string'],
            'meta'            => ['nullable','array'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            // due_at не раньше issued_at
            if ($this->filled('issued_at') && $this->filled('due_at')) {
                try {
                    $issued = Carbon::parse($this->input('issued_at'));
                    $due    = Carbon::parse($this->input('due_at'));
                    if ($due->lt($issued)) {
                        $v->errors()->add('due_at', 'Срок оплаты не может быть раньше даты выставления.');
                    }
                } catch (\Throwable $e) { /* базовая валидация date отработает */ }
            }

            // paid: если есть paid_at — статус не должен быть draft/void
            if ($this->filled('paid_at')) {
                $status = $this->input('status');
                if (in_array($status, ['draft','void'], true)) {
                    $v->errors()->add('status', 'Оплаченный инвойс не может иметь статус draft или void.');
                }
            }

            // консистентность сумм: total = subtotal - discount_total + tax_total (мягкая проверка)
            if ($this->filled(['subtotal','discount_total','tax_total','total'])) {
                $expected = (float)$this->input('subtotal') - (float)$this->input('discount_total') + (float)$this->input('tax_total');
                $actual   = (float)$this->input('total');
                // допускаем копеечные расхождения округления
                if (abs($expected - $actual) > 0.01) {
                    $v->errors()->add('total', 'Итоговая сумма (total) должна равняться subtotal - discount_total + tax_total.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'Не указан связанный заказ.',
            'order_id.integer'  => 'Идентификатор заказа должен быть целым числом.',
            'order_id.exists'   => 'Указанный заказ не найден.',

            'number.required' => 'Укажите номер инвойса.',
            'number.string'   => 'Номер инвойса должен быть строкой.',
            'number.max'      => 'Номер инвойса слишком длинный.',
            'number.unique'   => 'Инвойс с таким номером уже существует.',

            'status.in'       => 'Недопустимый статус инвойса.',

            'currency.size'   => 'Код валюты должен быть из 3 букв (ISO 4217).',
            'currency.alpha'  => 'Код валюты должен состоять только из букв.',

            'subtotal.numeric'       => 'Сумма позиций должна быть числом.',
            'subtotal.gte'           => 'Сумма позиций не может быть отрицательной.',
            'discount_total.numeric' => 'Сумма скидки должна быть числом.',
            'discount_total.gte'     => 'Сумма скидки не может быть отрицательной.',
            'tax_total.numeric'      => 'Сумма налога должна быть числом.',
            'tax_total.gte'          => 'Сумма налога не может быть отрицательной.',
            'total.numeric'          => 'Итоговая сумма должна быть числом.',
            'total.gte'              => 'Итоговая сумма не может быть отрицательной.',

            'issued_at.date'  => 'Дата выставления указана неверно.',
            'due_at.date'     => 'Срок оплаты указан неверно.',
            'paid_at.date'    => 'Дата оплаты указана неверно.',

            'bill_to_email.email' => 'Email плательщика указан неверно.',
            'bill_to_country.size'=> 'Код страны должен состоять из 2 букв (ISO-3166-1 alpha-2).',

            'meta.array'      => 'Поле meta должно быть объектом/массивом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'order_id'        => 'заказ',
            'number'          => 'номер инвойса',
            'status'          => 'статус',
            'currency'        => 'валюта',
            'subtotal'        => 'сумма позиций',
            'discount_total'  => 'сумма скидки',
            'tax_total'       => 'сумма налога',
            'total'           => 'итог к оплате',
            'issued_at'       => 'дата выставления',
            'due_at'          => 'срок оплаты',
            'paid_at'         => 'дата оплаты',
            'bill_to_name'    => 'плательщик',
            'bill_to_tax_id'  => 'налоговый номер',
            'bill_to_email'   => 'email плательщика',
            'bill_to_address1'=> 'адрес 1',
            'bill_to_address2'=> 'адрес 2',
            'bill_to_city'    => 'город',
            'bill_to_region'  => 'регион',
            'bill_to_postcode'=> 'почтовый индекс',
            'bill_to_country' => 'страна',
            'notes'           => 'примечания',
            'meta'            => 'метаданные',
        ];
    }
}
