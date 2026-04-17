<?php

namespace App\Http\Requests\Admin\Finance\OrderItem;

use Illuminate\Foundation\Http\FormRequest;

class OrderItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->filled('currency') && is_string($this->input('currency'))) {
            $merge['currency'] = strtoupper($this->input('currency'));
        }

        if ($this->filled('quantity'))    $merge['quantity']    = (int) $this->input('quantity');
        if ($this->filled('unit_price'))  $merge['unit_price']  = (float) $this->input('unit_price');
        if ($this->filled('discount'))    $merge['discount']    = (float) $this->input('discount');
        if ($this->filled('total'))       $merge['total']       = (float) $this->input('total');

        // attributes/meta могут прилететь строкой JSON
        foreach (['attributes', 'meta'] as $jsonField) {
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
        // допустимые типы сущностей, которые могут быть покупаемыми
        $allowedTypes = ['course', 'bundle', 'subscription_plan']; // потом дополнишь product, service и пр.

        return [
            'order_id' => ['required', 'integer', 'exists:orders,id'],

            'purchasable_type' => ['required', 'string', 'in:' . implode(',', $allowedTypes)],
            'purchasable_id'   => ['required', 'integer', 'min:1'],

            'title'     => ['required', 'string', 'max:255'],
            'sku'       => ['nullable', 'string', 'max:255'],
            'unit_name' => ['nullable', 'string', 'max:64'],

            'currency'   => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
            'quantity'   => ['required', 'integer', 'min:1', 'max:100000'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'discount'   => ['nullable', 'numeric', 'min:0'],
            'total'      => ['nullable', 'numeric', 'min:0'],

            'attributes' => ['nullable', 'array'],
            'meta'       => ['nullable', 'array'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $qty   = (int) $this->input('quantity', 1);
            $price = (float) $this->input('unit_price', 0);
            $disc  = (float) $this->input('discount', 0);

            if ($disc > $qty * $price) {
                $v->errors()->add('discount', 'Скидка не может превышать сумму позиции (quantity × unit_price).');
            }

            // Доп. проверка существования purchasable через morphMap
            $type = $this->input('purchasable_type');
            $id   = $this->input('purchasable_id');

            if ($type && $id) {
                $map = \Illuminate\Database\Eloquent\Relations\Relation::morphMap();

                if (! isset($map[$type])) {
                    $v->errors()->add('purchasable_type', 'Недопустимый тип покупаемой сущности.');
                    return;
                }

                $modelClass = $map[$type];

                /** @var \Illuminate\Database\Eloquent\Model $modelClass */
                if (! $modelClass::query()->whereKey($id)->exists()) {
                    $v->errors()->add('purchasable_id', 'Покупаемая сущность не найдена для указанного типа.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'Укажите идентификатор заказа.',
            'order_id.exists'   => 'Заказ не найден.',

            'purchasable_type.required' => 'Укажите тип позиции.',
            'purchasable_type.in'       => 'Недопустимый тип позиции.',
            'purchasable_id.required'   => 'Укажите идентификатор покупаемой сущности.',
            'purchasable_id.integer'    => 'Идентификатор покупаемой сущности должен быть числом.',

            'title.required' => 'Укажите название позиции.',
            'title.string'   => 'Название должно быть строкой.',
            'title.max'      => 'Название слишком длинное.',

            'currency.required' => 'Укажите валюту.',
            'currency.size'     => 'Код валюты должен состоять из 3 символов.',
            'currency.regex'    => 'Код валюты должен соответствовать ISO 4217 (например, USD, EUR).',

            'quantity.required' => 'Укажите количество.',
            'quantity.integer'  => 'Количество должно быть целым числом.',
            'quantity.min'      => 'Количество должно быть не меньше 1.',
            'quantity.max'      => 'Количество слишком большое.',

            'unit_price.required' => 'Укажите цену за единицу.',
            'unit_price.numeric'  => 'Цена за единицу должна быть числом.',
            'unit_price.min'      => 'Цена за единицу не может быть отрицательной.',

            'discount.numeric' => 'Скидка должна быть числом.',
            'discount.min'     => 'Скидка не может быть отрицательной.',

            'total.numeric' => 'Итог по позиции должен быть числом.',
            'total.min'     => 'Итог по позиции не может быть отрицательным.',

            'attributes.array' => 'Атрибуты должны быть массивом.',
            'meta.array'       => 'Поле meta должно быть массивом/JSON-объектом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'order_id'         => 'ID заказа',
            'purchasable_type' => 'Тип позиции',
            'purchasable_id'   => 'ID покупаемой сущности',
            'title'            => 'Название позиции',
            'sku'              => 'SKU',
            'unit_name'        => 'Единица измерения',
            'currency'         => 'Валюта',
            'quantity'         => 'Количество',
            'unit_price'       => 'Цена за единицу',
            'discount'         => 'Скидка',
            'total'            => 'Итог по позиции',
            'attributes'       => 'Атрибуты позиции',
            'meta'             => 'Мета-данные',
        ];
    }
}
