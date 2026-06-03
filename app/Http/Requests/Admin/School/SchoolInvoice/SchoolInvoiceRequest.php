<?php

namespace App\Http\Requests\Admin\School\SchoolInvoice;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SchoolInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach ([
                     'number',
                     'status',
                     'currency',
                     'bill_to_name',
                     'bill_to_tax_id',
                     'bill_to_email',
                     'bill_to_address1',
                     'bill_to_address2',
                     'bill_to_city',
                     'bill_to_region',
                     'bill_to_postcode',
                     'bill_to_country',
                     'notes',
                 ] as $field) {
            if ($this->filled($field) && is_string($this->input($field))) {
                $data[$field] = trim($this->input($field));
            }
        }

        if (!empty($data['currency'])) {
            $data['currency'] = strtoupper($data['currency']);
        }

        if (!empty($data['bill_to_country'])) {
            $data['bill_to_country'] = strtoupper($data['bill_to_country']);
        }

        if ($this->filled('school_order_id')) {
            $data['school_order_id'] = (int) $this->input('school_order_id');
        }

        foreach (['subtotal', 'discount_total', 'tax_total', 'total'] as $field) {
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
        $invoice = $this->route('school_invoice')
            ?? $this->route('invoice')
            ?? $this->route('schoolInvoice');

        $id = is_object($invoice)
            ? $invoice->id
            : ($invoice ? (int) $invoice : null);

        return [
            'school_order_id' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                Rule::exists('school_orders', 'id'),
            ],

            'number' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:50',
                Rule::unique('school_invoices', 'number')->ignore($id),
            ],

            'status' => ['sometimes', 'string', Rule::in(['draft', 'issued', 'paid', 'void', 'refunded'])],

            'currency' => ['sometimes', 'string', 'size:3', 'alpha'],
            'subtotal' => ['sometimes', 'numeric', 'gte:0'],
            'discount_total' => ['sometimes', 'numeric', 'gte:0'],
            'tax_total' => ['sometimes', 'numeric', 'gte:0'],
            'total' => ['sometimes', 'numeric', 'gte:0'],

            'issued_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date'],
            'paid_at' => ['nullable', 'date'],

            'bill_to_name' => ['nullable', 'string', 'max:255'],
            'bill_to_tax_id' => ['nullable', 'string', 'max:64'],
            'bill_to_email' => ['nullable', 'email', 'max:255'],
            'bill_to_address1' => ['nullable', 'string', 'max:255'],
            'bill_to_address2' => ['nullable', 'string', 'max:255'],
            'bill_to_city' => ['nullable', 'string', 'max:191'],
            'bill_to_region' => ['nullable', 'string', 'max:191'],
            'bill_to_postcode' => ['nullable', 'string', 'max:32'],
            'bill_to_country' => ['nullable', 'string', 'size:2', 'alpha'],

            'notes' => ['nullable', 'string'],
            'meta' => ['nullable', 'array'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->filled('issued_at') && $this->filled('due_at')) {
                try {
                    $issued = Carbon::parse($this->input('issued_at'));
                    $due = Carbon::parse($this->input('due_at'));

                    if ($due->lt($issued)) {
                        $validator->errors()->add(
                            'due_at',
                            'Срок оплаты не может быть раньше даты выставления.'
                        );
                    }
                } catch (\Throwable) {
                    //
                }
            }

            if ($this->filled('paid_at')) {
                $status = $this->input('status');

                if (in_array($status, ['draft', 'void'], true)) {
                    $validator->errors()->add(
                        'status',
                        'Оплаченный инвойс не может иметь статус draft или void.'
                    );
                }
            }

            if ($this->filled(['subtotal', 'discount_total', 'tax_total', 'total'])) {
                $expected = (float) $this->input('subtotal')
                    - (float) $this->input('discount_total')
                    + (float) $this->input('tax_total');

                $actual = (float) $this->input('total');

                if (abs($expected - $actual) > 0.01) {
                    $validator->errors()->add(
                        'total',
                        'Итоговая сумма должна равняться subtotal - discount_total + tax_total.'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'school_order_id.required' => 'Не указан связанный заказ.',
            'school_order_id.integer' => 'Идентификатор заказа должен быть целым числом.',
            'school_order_id.exists' => 'Указанный заказ не найден.',

            'number.required' => 'Укажите номер инвойса.',
            'number.string' => 'Номер инвойса должен быть строкой.',
            'number.max' => 'Номер инвойса не должен превышать 50 символов.',
            'number.unique' => 'Инвойс с таким номером уже существует.',

            'status.in' => 'Недопустимый статус инвойса.',

            'currency.size' => 'Код валюты должен быть из 3 букв.',
            'currency.alpha' => 'Код валюты должен состоять только из букв.',

            'subtotal.numeric' => 'Сумма позиций должна быть числом.',
            'subtotal.gte' => 'Сумма позиций не может быть отрицательной.',

            'discount_total.numeric' => 'Сумма скидки должна быть числом.',
            'discount_total.gte' => 'Сумма скидки не может быть отрицательной.',

            'tax_total.numeric' => 'Сумма налога должна быть числом.',
            'tax_total.gte' => 'Сумма налога не может быть отрицательной.',

            'total.numeric' => 'Итоговая сумма должна быть числом.',
            'total.gte' => 'Итоговая сумма не может быть отрицательной.',

            'issued_at.date' => 'Дата выставления указана неверно.',
            'due_at.date' => 'Срок оплаты указан неверно.',
            'paid_at.date' => 'Дата оплаты указана неверно.',

            'bill_to_email.email' => 'Email плательщика указан неверно.',
            'bill_to_country.size' => 'Код страны должен состоять из 2 букв.',
            'bill_to_country.alpha' => 'Код страны должен состоять только из букв.',

            'meta.array' => 'Поле meta должно быть объектом/массивом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'school_order_id' => 'Заказ',
            'number' => 'Номер инвойса',
            'status' => 'Статус',
            'currency' => 'Валюта',
            'subtotal' => 'Сумма позиций',
            'discount_total' => 'Сумма скидки',
            'tax_total' => 'Сумма налога',
            'total' => 'Итог к оплате',
            'issued_at' => 'Дата выставления',
            'due_at' => 'Срок оплаты',
            'paid_at' => 'Дата оплаты',
            'bill_to_name' => 'Плательщик',
            'bill_to_tax_id' => 'Налоговый номер',
            'bill_to_email' => 'Email плательщика',
            'bill_to_address1' => 'Адрес 1',
            'bill_to_address2' => 'Адрес 2',
            'bill_to_city' => 'Город',
            'bill_to_region' => 'Регион',
            'bill_to_postcode' => 'Почтовый индекс',
            'bill_to_country' => 'Страна',
            'notes' => 'Примечания',
            'meta' => 'Метаданные',
        ];
    }
}
