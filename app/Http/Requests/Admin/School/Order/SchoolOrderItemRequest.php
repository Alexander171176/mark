<?php

namespace App\Http\Requests\Admin\School\Order;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolOrderItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['school_order_id', 'purchasable_id', 'quantity'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        foreach (['unit_price', 'discount', 'total'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = is_string($this->input($field))
                    ? str_replace(',', '.', trim($this->input($field)))
                    : $this->input($field);
            }
        }

        if ($this->filled('currency') && is_string($this->input('currency'))) {
            $data['currency'] = strtoupper(trim($this->input('currency')));
        }

        foreach (['attributes', 'meta'] as $jsonField) {
            if ($this->filled($jsonField) && is_string($this->input($jsonField))) {
                $decoded = json_decode($this->input($jsonField), true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    $data[$jsonField] = $decoded;
                }
            }
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        return [
            'school_order_id' => [
                'required',
                'integer',
                Rule::exists('school_orders', 'id'),
            ],

            'purchasable_type' => ['required', 'string', 'max:255'],
            'purchasable_id' => ['required', 'integer', 'min:1'],

            'title' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
            'unit_name' => ['nullable', 'string', 'max:255'],

            'currency' => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],

            'unit_price' => ['required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'total' => ['nullable', 'numeric', 'min:0'],

            'attributes' => ['nullable', 'array'],
            'meta' => ['nullable', 'array'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $quantity = (int) $this->input('quantity', 1);
            $unitPrice = (float) $this->input('unit_price', 0);
            $discount = (float) $this->input('discount', 0);

            if ($discount > ($quantity * $unitPrice)) {
                $validator->errors()->add(
                    'discount',
                    'Скидка не может превышать сумму позиции.'
                );
            }

            $type = $this->input('purchasable_type');
            $id = $this->input('purchasable_id');

            if (!$type || !$id) {
                return;
            }

            $morphMap = Relation::morphMap();
            $modelClass = $morphMap[$type] ?? $type;

            if (!class_exists($modelClass)) {
                $validator->errors()->add('purchasable_type', 'Недопустимый тип покупаемой сущности.');
                return;
            }

            if (!is_subclass_of($modelClass, \Illuminate\Database\Eloquent\Model::class)) {
                $validator->errors()->add('purchasable_type', 'Покупаемая сущность должна быть Eloquent-моделью.');
                return;
            }

            if (!$modelClass::query()->whereKey($id)->exists()) {
                $validator->errors()->add('purchasable_id', 'Покупаемая сущность не найдена.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'school_order_id.required' => 'Укажите заказ.',
            'school_order_id.integer' => 'ID заказа должен быть числом.',
            'school_order_id.exists' => 'Заказ не найден.',

            'purchasable_type.required' => 'Укажите тип покупаемой сущности.',
            'purchasable_type.string' => 'Тип покупаемой сущности должен быть строкой.',

            'purchasable_id.required' => 'Укажите ID покупаемой сущности.',
            'purchasable_id.integer' => 'ID покупаемой сущности должен быть числом.',
            'purchasable_id.min' => 'ID покупаемой сущности должен быть больше 0.',

            'title.required' => 'Укажите название позиции.',
            'title.max' => 'Название позиции не должно превышать 255 символов.',

            'currency.required' => 'Укажите валюту.',
            'currency.size' => 'Код валюты должен состоять из 3 символов.',
            'currency.regex' => 'Код валюты должен соответствовать ISO 4217, например USD.',

            'quantity.required' => 'Укажите количество.',
            'quantity.integer' => 'Количество должно быть целым числом.',
            'quantity.min' => 'Количество должно быть не меньше 1.',

            'unit_price.required' => 'Укажите цену за единицу.',
            'unit_price.numeric' => 'Цена за единицу должна быть числом.',
            'unit_price.min' => 'Цена за единицу не может быть отрицательной.',

            'discount.numeric' => 'Скидка должна быть числом.',
            'discount.min' => 'Скидка не может быть отрицательной.',

            'total.numeric' => 'Итоговая сумма должна быть числом.',
            'total.min' => 'Итоговая сумма не может быть отрицательной.',

            'attributes.array' => 'Атрибуты позиции должны быть массивом.',
            'meta.array' => 'Meta должен быть JSON-объектом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'school_order_id' => 'Заказ',
            'purchasable_type' => 'Тип позиции',
            'purchasable_id' => 'ID покупаемой сущности',
            'title' => 'Название позиции',
            'sku' => 'SKU',
            'unit_name' => 'Единица измерения',
            'currency' => 'Валюта',
            'quantity' => 'Количество',
            'unit_price' => 'Цена за единицу',
            'discount' => 'Скидка',
            'total' => 'Итого',
            'attributes' => 'Атрибуты',
            'meta' => 'Meta',
        ];
    }
}
