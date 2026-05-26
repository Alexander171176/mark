<?php

namespace App\Http\Requests\Admin\School\Price;

use Illuminate\Foundation\Http\FormRequest;

class SchoolBundlePriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['school_bundle_id', 'currency_id', 'sort'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        if ($this->has('activity')) {
            $data['activity'] = filter_var(
                $this->input('activity'),
                FILTER_VALIDATE_BOOL,
                FILTER_NULL_ON_FAILURE
            );
        }

        if ($this->filled('meta') && is_string($this->input('meta'))) {
            $decoded = json_decode($this->input('meta'), true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $data['meta'] = $decoded;
            }
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $requiredOnCreate = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'school_bundle_id' => [$requiredOnCreate, 'integer', 'exists:school_bundles,id'],
            'currency_id' => [$requiredOnCreate, 'integer', 'exists:currencies,id'],

            'price' => [$requiredOnCreate, 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],

            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],

            'activity' => [$requiredOnCreate, 'boolean'],
            'sort' => ['nullable', 'integer', 'min:0'],

            'meta' => ['nullable', 'array'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $price = $this->input('price');
            $sale = $this->input('sale_price');
            $compare = $this->input('compare_at_price');

            if ($compare === null || $compare === '') {
                return;
            }

            $priceStr = $price !== null ? str_replace(',', '.', (string) $price) : null;
            $saleStr = $sale !== null ? str_replace(',', '.', (string) $sale) : null;
            $compareStr = str_replace(',', '.', (string) $compare);

            $effective = ($saleStr !== null && $saleStr !== '' && is_numeric($saleStr) && bccomp($saleStr, '0', 2) === 1)
                ? $saleStr
                : $priceStr;

            if ($effective === null || $effective === '' || !is_numeric($effective) || !is_numeric($compareStr)) {
                return;
            }

            if (bccomp($compareStr, $effective, 2) !== 1) {
                $validator->errors()->add(
                    'compare_at_price',
                    'Старая цена должна быть больше текущей цены.'
                );
            }

            if ($saleStr !== null && $saleStr !== '' && is_numeric($saleStr)) {
                if (bccomp($saleStr, $compareStr, 2) !== -1) {
                    $validator->errors()->add(
                        'sale_price',
                        'Акционная цена должна быть меньше старой цены.'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'school_bundle_id.required' => 'Не указан набор курсов.',
            'school_bundle_id.integer' => 'Идентификатор набора должен быть числом.',
            'school_bundle_id.exists' => 'Указанный набор курсов не найден.',

            'currency_id.required' => 'Укажите валюту.',
            'currency_id.integer' => 'Идентификатор валюты должен быть числом.',
            'currency_id.exists' => 'Указанная валюта не найдена.',

            'price.required' => 'Укажите базовую цену.',
            'price.numeric' => 'Цена должна быть числом.',
            'price.min' => 'Цена не может быть отрицательной.',

            'sale_price.numeric' => 'Акционная цена должна быть числом.',
            'sale_price.min' => 'Акционная цена не может быть отрицательной.',
            'sale_price.lt' => 'Акционная цена должна быть меньше базовой цены.',

            'compare_at_price.numeric' => 'Старая цена должна быть числом.',
            'compare_at_price.min' => 'Старая цена не может быть отрицательной.',

            'starts_at.date' => 'Дата начала действия должна быть корректной датой.',
            'ends_at.date' => 'Дата окончания должна быть корректной датой.',
            'ends_at.after_or_equal' => 'Дата окончания не может быть раньше даты начала.',

            'activity.required' => 'Укажите, активна ли цена.',
            'activity.boolean' => 'Поле активности должно быть булевым значением.',

            'sort.integer' => 'Позиция должна быть целым числом.',
            'sort.min' => 'Позиция не может быть отрицательной.',

            'meta.array' => 'Meta должен быть объектом JSON.',
        ];
    }

    public function attributes(): array
    {
        return [
            'school_bundle_id' => 'Набор курсов',
            'currency_id' => 'Валюта',
            'price' => 'Цена',
            'sale_price' => 'Акционная цена',
            'compare_at_price' => 'Старая цена',
            'starts_at' => 'Дата начала',
            'ends_at' => 'Дата окончания',
            'activity' => 'Активность',
            'sort' => 'Позиция',
            'meta' => 'Meta-данные',
        ];
    }
}
