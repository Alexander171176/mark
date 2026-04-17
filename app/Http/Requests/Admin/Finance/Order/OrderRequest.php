<?php

namespace App\Http\Requests\Admin\Finance\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        // user_id может быть null (гость)
        if ($this->filled('user_id')) {
            $merge['user_id'] = (int) $this->input('user_id');
        }

        // Денежные поля
        foreach ([
                     'subtotal',
                     'discount_total',
                     'tax_total',
                     'total',
                     'delivery_cost',
                     'total_shop_currency',
                     'delivery_shop_currency',
                 ] as $field) {
            if ($this->filled($field) && is_numeric($this->input($field))) {
                $merge[$field] = $this->input($field);
            }
        }

        // Валюта
        if ($this->filled('currency') && is_string($this->input('currency'))) {
            $merge['currency'] = strtoupper($this->input('currency'));
        }

        // JSON-поля
        foreach (['shipping_address_parts', 'delivery_options', 'items', 'meta'] as $jsonField) {
            if ($this->filled($jsonField) && is_string($this->input($jsonField))) {
                $decoded = json_decode($this->input($jsonField), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $merge[$jsonField] = $decoded;
                }
            }
        }

        if ($merge) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        $id = $this->route('order')?->id ?? $this->input('id');

        /**
         * =========================
         *  UPDATE (PUT/PATCH)
         *  Редактирование заказа в админке
         *  =========================
         *  Из Edit.vue реально приходят только:
         *    - status
         *    - is_paid
         *    - manager_comment
         *    - external_id
         *  Всё остальное — read-only.
         */
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                // Статус заказа (переключаем в форме)
                'status' => [
                    'required',
                    'string',
                    Rule::in(['new', 'processing', 'delivered', 'cancelled', 'refunded', 'completed']),
                ],

                // Флаг оплаты (чекбокс "Оплачен")
                'is_paid' => ['required', 'boolean'],

                // Комментарий менеджера (textarea)
                'manager_comment' => ['nullable', 'string'],

                // Внешний ID (интеграции CRM/1C и т.п.)
                'external_id' => ['nullable', 'string', 'max:128'],
            ];
        }

        /**
         * =========================
         *  БАЗОВЫЕ ПРАВИЛА (на будущее под store/import)
         *  =========================
         *  Здесь оставляем твои исходные правила
         *  на случай, если позже появится create/store.
         */
        return [
            // Модуль 1 — кто оформил
            'user_id' => ['nullable', 'integer', 'exists:users,id'],

            'number'  => [
                'required',
                'string',
                'max:32',
                Rule::unique('orders', 'number')->ignore($id),
            ],

            // Модуль 2 — покупатель (физ/юр)
            'buyer_name'      => ['nullable', 'string', 'max:255'],
            'buyer_email'     => ['nullable', 'string', 'email', 'max:255'],
            'buyer_phone'     => ['nullable', 'string', 'max:64'],
            'billing_company' => ['nullable', 'string', 'max:255'],
            'billing_tax_id'  => ['nullable', 'string', 'max:64'],
            'billing_address' => ['nullable', 'string'],

            // Модуль 3 — адрес и доставка
            'shipping_address'       => ['nullable', 'string'],
            'shipping_address_parts' => ['nullable', 'array'],
            'delivery_method_id'     => ['nullable', 'integer'],
            'delivery_cost'          => ['nullable', 'numeric', 'min:0'],
            'delivery_options'       => ['nullable', 'array'],
            'delivery_interval'      => ['nullable', 'string', 'max:64'],
            'warehouse'              => ['nullable', 'string', 'max:128'],
            'delivery_date'          => ['nullable', 'date'],

            // Модуль 4 — оплата
            'is_paid'            => ['sometimes', 'boolean'],
            'paid_at'            => ['nullable', 'date'],
            'payment_method_id'  => ['nullable', 'integer'],
            'payment_method'     => ['nullable', 'string', 'max:32'],
            'payment_provider'   => ['nullable', 'string', 'max:32'],
            'payment_reference'  => ['nullable', 'string', 'max:128'],
            'confirmation_code'  => ['nullable', 'string', 'max:128'],
            'confirmation_status'=> ['nullable', 'string', Rule::in(['pending','confirmed','failed'])],
            'failure_reason'     => ['nullable', 'string'],

            // Модуль 5 — мультивалютность
            'currency'            => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
            'subtotal'            => ['required', 'numeric', 'min:0'],
            'discount_total'      => ['required', 'numeric', 'min:0'],
            'tax_total'           => ['required', 'numeric', 'min:0'],
            'total'               => ['required', 'numeric', 'min:0'],
            'total_shop_currency' => ['nullable', 'numeric', 'min:0'],
            'delivery_shop_currency' => ['nullable', 'numeric', 'min:0'],

            // Модуль 6 — статусы
            'status' => [
                'required',
                'string',
                Rule::in(['new', 'processing', 'delivered', 'cancelled', 'refunded', 'completed']),
            ],
            'payment_status' => [
                'required',
                'string',
                Rule::in(['pending', 'paid', 'failed', 'refunded', 'partial']),
            ],

            // Модуль 7 — контент заказа
            'items' => ['nullable', 'array'],
            'meta'  => ['nullable', 'array'],

            // Модуль 8 — комментарии
            'user_comment'    => ['nullable', 'string'],
            'manager_comment' => ['nullable', 'string'],

            // Модуль 9 — интеграции
            'external_id' => ['nullable', 'string', 'max:128'],
            'exported_at' => ['nullable', 'date'],

            // Модуль 10 — тех.данные
            'client_ip'   => ['nullable', 'string', 'max:45'],
            'user_agent'  => ['nullable', 'string'],
            'public_hash' => ['nullable', 'string', 'max:64'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.integer' => 'Идентификатор пользователя должен быть числом.',
            'user_id.exists'  => 'Указанный пользователь не найден.',

            'number.required' => 'Номер заказа обязателен.',
            'number.max'      => 'Номер заказа не должен превышать :max символов.',
            'number.unique'   => 'Такой номер заказа уже существует.',

            'currency.required' => 'Валюта обязательна.',
            'currency.size'     => 'Валюта должна содержать ровно 3 символа (ISO 4217).',
            'currency.regex'    => 'Код валюты должен соответствовать ISO 4217 (например, USD, EUR).',

            'subtotal.required'       => 'Сумма позиций обязательна.',
            'subtotal.numeric'        => 'Сумма позиций должна быть числом.',
            'discount_total.required' => 'Сумма скидок обязательна.',
            'tax_total.required'      => 'Сумма налогов обязательна.',
            'total.required'          => 'Итоговая сумма обязательна.',

            'status.in'         => 'Недопустимый статус заказа.',
            'payment_status.in' => 'Недопустимый статус оплаты.',

            'shipping_address_parts.array' => 'Структурированный адрес должен быть массивом.',
            'delivery_options.array'       => 'Параметры доставки должны быть массивом.',

            'items.array' => 'Поле items должно быть массивом.',
            'meta.array'  => 'Поле meta должно быть массивом (JSON-объектом).',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id'        => 'Пользователь',
            'number'         => 'Номер заказа',
            'buyer_name'     => 'Имя покупателя',
            'buyer_email'    => 'Email покупателя',
            'buyer_phone'    => 'Телефон покупателя',
            'billing_company'=> 'Компания',
            'billing_tax_id' => 'ИНН/БИН',
            'billing_address'=> 'Юридический адрес',

            'shipping_address'       => 'Адрес доставки',
            'shipping_address_parts' => 'Структурированный адрес доставки',
            'delivery_method_id'     => 'Способ доставки',
            'delivery_cost'          => 'Стоимость доставки',
            'delivery_interval'      => 'Интервал доставки',
            'warehouse'              => 'Склад отгрузки',
            'delivery_date'          => 'Дата доставки',

            'is_paid'         => 'Флаг оплаты',
            'paid_at'         => 'Дата оплаты',
            'payment_method'  => 'Способ оплаты',
            'payment_status'  => 'Статус оплаты',
            'status'          => 'Статус заказа',
            'currency'        => 'Валюта',
            'subtotal'        => 'Сумма позиций',
            'discount_total'  => 'Сумма скидок',
            'tax_total'       => 'Сумма налогов',
            'total'           => 'Итог к оплате',

            'user_comment'    => 'Комментарий покупателя',
            'manager_comment' => 'Комментарий менеджера',
            'items'           => 'Содержимое заказа',
            'meta'            => 'Дополнительные данные',
        ];
    }
}
