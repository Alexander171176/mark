<?php

namespace App\Http\Requests\Admin\Finance\CoursePrice;

use Illuminate\Foundation\Http\FormRequest;

class CoursePriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->has('course_id')) {
            $data['course_id'] = (int) $this->input('course_id');
        }

        if ($this->has('currency_id')) {
            $data['currency_id'] = (int) $this->input('currency_id');
        }

        if ($this->has('sort')) {
            $data['sort'] = (int) $this->input('sort');
        }

        if ($this->has('activity')) {
            $data['activity'] = filter_var(
                $this->input('activity'),
                FILTER_VALIDATE_BOOL,
                FILTER_NULL_ON_FAILURE
            );
        }

        // meta: строка JSON -> array
        if ($this->has('meta') && is_string($this->input('meta'))) {
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
            'course_id'   => [$requiredOnCreate, 'integer', 'exists:courses,id'],
            'currency_id' => [$requiredOnCreate, 'integer', 'exists:currencies,id'],

            'price'       => [$requiredOnCreate, 'numeric', 'min:0'],

            // ✅ sale_price: если задана — должна быть меньше базовой price (логично)
            'sale_price'  => ['nullable', 'numeric', 'min:0', 'lt:price'],

            // ✅ compare_at_price: НЕ сравниваем с price (иначе ломаем кейсы с sale_price)
            // Требование "больше текущей цены" проверим в after()
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],

            'starts_at' => ['nullable', 'date'],
            'ends_at'   => ['nullable', 'date', 'after_or_equal:starts_at'],

            'activity' => [$requiredOnCreate, 'boolean'],
            'sort'     => ['sometimes', 'integer', 'min:0'],

            'meta' => ['nullable', 'array'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $price   = $this->input('price');
            $sale    = $this->input('sale_price');
            $compare = $this->input('compare_at_price');

            // Если compare не задана — ок
            if ($compare === null || $compare === '') {
                return;
            }

            // Текущая эффективная цена:
            // если sale_price есть и > 0 -> берём её, иначе price
            $effective = null;

            // Приводим к строкам с точкой на случай запятых
            $priceStr   = $price !== null   ? str_replace(',', '.', (string) $price)   : null;
            $saleStr    = $sale !== null    ? str_replace(',', '.', (string) $sale)    : null;
            $compareStr = str_replace(',', '.', (string) $compare);

            if ($saleStr !== null && $saleStr !== '' && is_numeric($saleStr) && bccomp($saleStr, '0', 2) === 1) {
                $effective = $saleStr;
            } else {
                $effective = $priceStr;
            }

            if ($effective === null || $effective === '' || !is_numeric($effective) || !is_numeric($compareStr)) {
                return;
            }

            // ✅ compare_at_price должна быть больше эффективной цены (sale если есть, иначе price)
            if (bccomp($compareStr, $effective, 2) !== 1) {
                $v->errors()->add(
                    'compare_at_price',
                    'Старая цена должна быть больше текущей цены (акционной, если она указана).'
                );
            }

            // (необязательно, но логично оставить) если оба заданы — sale_price < compare_at_price
            if ($saleStr !== null && $saleStr !== '' && is_numeric($saleStr)) {
                if (bccomp($saleStr, $compareStr, 2) !== -1) {
                    $v->errors()->add(
                        'sale_price',
                        'Акционная цена должна быть меньше старой цены (compare_at_price).'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'course_id.required' => 'Не указан курс.',
            'course_id.integer'  => 'Идентификатор курса должен быть числом.',
            'course_id.exists'   => 'Указанный курс не найден.',

            'currency_id.required' => 'Укажите валюту.',
            'currency_id.integer'  => 'Идентификатор валюты должен быть числом.',
            'currency_id.exists'   => 'Указанная валюта не найдена.',

            'price.required' => 'Укажите базовую цену.',
            'price.numeric'  => 'Цена должна быть числом.',
            'price.min'      => 'Цена не может быть отрицательной.',

            'sale_price.numeric' => 'Акционная цена должна быть числом.',
            'sale_price.min'     => 'Акционная цена не может быть отрицательной.',
            'sale_price.lt'      => 'Акционная цена должна быть меньше базовой цены.',

            'compare_at_price.numeric' => 'Старая цена должна быть числом.',
            'compare_at_price.min'     => 'Старая цена не может быть отрицательной.',

            'starts_at.date' => 'Дата начала действия должна быть корректной датой.',
            'ends_at.date'   => 'Дата окончания должна быть корректной датой.',
            'ends_at.after_or_equal' => 'Дата окончания не может быть раньше даты начала.',

            'activity.required' => 'Укажите, активна ли цена.',
            'activity.boolean'  => 'Поле публикации должно быть булевым.',

            'sort.integer' => 'Позиция должна быть целым числом.',
            'sort.min'     => 'Позиция не может быть отрицательной.',

            'meta.array' => 'Meta должен быть объектом (JSON).',
        ];
    }

    public function attributes(): array
    {
        return [
            'course_id'   => 'Курс',
            'currency_id' => 'Валюта',
            'price'       => 'Цена',
            'sale_price'  => 'Акционная цена',
            'compare_at_price' => 'Старая цена',
            'starts_at'   => 'Дата начала',
            'ends_at'     => 'Дата окончания',
            'activity'    => 'Публикация',
            'sort'        => 'Позиция',
            'meta'        => 'Meta-данные',
        ];
    }
}
