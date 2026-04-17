<?php

namespace App\Http\Requests\Admin\Finance\PayoutItem;

use Illuminate\Foundation\Http\FormRequest;

class PayoutItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // защищайте доступ Policy/гейтами
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        // Трим некоторых строк
        foreach (['currency','title'] as $k) {
            if ($this->filled($k) && is_string($this->input($k))) {
                $merge[$k] = trim($this->input($k));
            }
        }

        // Нормализация валюты
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
        return [
            'payout_id' => ['required','integer','exists:payouts,id'],

            // Источник/нормализованные связи (все опциональны)
            'order_id'        => ['nullable','integer','exists:orders,id'],
            'order_item_id'   => ['nullable','integer','exists:order_items,id'],
            'course_id'       => ['nullable','integer','exists:courses,id'],
            'bundle_id'       => ['nullable','integer','exists:bundles,id'],
            'subscription_id' => ['nullable','integer','exists:subscriptions,id'],

            // Деньги/валюта
            'currency'     => ['required','string','size:3'],
            'amount_gross' => ['required','numeric','min:0'],
            'fee_total'    => ['required','numeric','min:0'],
            'tax_total'    => ['required','numeric','min:0'],
            'amount_net'   => ['required','numeric','min:0'],

            // Даты/прочее
            'earned_at' => ['nullable','date'],
            'title'     => ['nullable','string','max:255'],
            'notes'     => ['nullable','string'],
            'meta'      => ['nullable','array'],
        ];
    }

    public function messages(): array
    {
        return [
            'payout_id.required' => 'Не указана выплата, к которой относится позиция.',
            'payout_id.exists'   => 'Указанная выплата не найдена.',

            'order_id.exists'        => 'Указанный заказ не найден.',
            'order_item_id.exists'   => 'Указанная позиция заказа не найдена.',
            'course_id.exists'       => 'Указанный курс не найден.',
            'bundle_id.exists'       => 'Указанный бандл не найден.',
            'subscription_id.exists' => 'Указанная подписка не найдена.',

            'currency.required' => 'Укажите валюту позиции.',
            'currency.size'     => 'Код валюты должен состоять из 3 букв (ISO 4217).',

            'amount_gross.required' => 'Укажите сумму начислений по позиции (брутто).',
            'amount_gross.min'      => 'Сумма брутто не может быть отрицательной.',
            'fee_total.required'    => 'Укажите сумму комиссий по позиции.',
            'fee_total.min'         => 'Комиссии не могут быть отрицательными.',
            'tax_total.required'    => 'Укажите сумму налогов/удержаний по позиции.',
            'tax_total.min'         => 'Налоги/удержания не могут быть отрицательными.',
            'amount_net.required'   => 'Укажите сумму к выплате по позиции (нетто).',
            'amount_net.min'        => 'Нетто не может быть отрицательной.',

            'earned_at.date'        => 'Некорректный формат даты заработка.',
            'title.max'             => 'Слишком длинная подпись позиции.',
            'meta.array'            => 'Поле meta должно быть объектом/массивом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'payout_id'      => 'выплата',
            'order_id'       => 'заказ',
            'order_item_id'  => 'позиция заказа',
            'course_id'      => 'курс',
            'bundle_id'      => 'бандл',
            'subscription_id'=> 'подписка',
            'currency'       => 'валюта',
            'amount_gross'   => 'сумма брутто',
            'fee_total'      => 'комиссии',
            'tax_total'      => 'налоги/удержания',
            'amount_net'     => 'сумма нетто',
            'earned_at'      => 'дата заработка',
            'title'          => 'подпись позиции',
            'notes'          => 'примечания',
            'meta'           => 'метаданные',
        ];
    }
}
