<?php

namespace App\Http\Requests\Admin\School\Price;

use Illuminate\Foundation\Http\FormRequest;

class SchoolCoursePriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['school_course_id', 'currency_id', 'sort'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        foreach (['price', 'sale_price', 'compare_at_price'] as $field) {
            if ($this->has($field)) {
                $value = $this->input($field);

                $data[$field] = $value === null || $value === ''
                    ? null
                    : str_replace(',', '.', trim((string) $value));
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

        $this->merge($data);
    }

    public function rules(): array
    {
        $id = $this->route('schoolCoursePrice');
        $required = $id ? 'sometimes' : 'required';

        return [
            'school_course_id' => [$required, 'integer', 'exists:school_courses,id'],
            'currency_id' => [$required, 'integer', 'exists:currencies,id'],

            'price' => [$required, 'numeric', 'min:0.01'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],

            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],

            'activity' => [$required, 'boolean'],
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

            if ($price !== null && $sale !== null && $sale !== '') {
                if (bccomp((string) $sale, (string) $price, 2) !== -1) {
                    $validator->errors()->add(
                        'sale_price',
                        'Акционная цена должна быть меньше базовой цены.'
                    );
                }
            }

            if ($compare === null || $compare === '') {
                return;
            }

            $effective = ($sale !== null && $sale !== '' && bccomp((string) $sale, '0', 2) === 1)
                ? (string) $sale
                : (string) $price;

            if ($price !== null && bccomp((string) $compare, $effective, 2) !== 1) {
                $validator->errors()->add(
                    'compare_at_price',
                    'Старая цена должна быть больше текущей цены.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'school_course_id.required' => 'Не указан курс.',
            'school_course_id.exists' => 'Указанный курс не найден.',

            'currency_id.required' => 'Укажите валюту.',
            'currency_id.exists' => 'Указанная валюта не найдена.',

            'price.required' => 'Укажите базовую цену.',
            'price.numeric' => 'Цена должна быть числом.',
            'price.min' => 'Цена должна быть больше нуля.',

            'sale_price.numeric' => 'Акционная цена должна быть числом.',
            'sale_price.min' => 'Акционная цена не может быть отрицательной.',

            'compare_at_price.numeric' => 'Старая цена должна быть числом.',
            'compare_at_price.min' => 'Старая цена не может быть отрицательной.',

            'starts_at.date' => 'Дата начала действия должна быть корректной датой.',
            'ends_at.date' => 'Дата окончания должна быть корректной датой.',
            'ends_at.after_or_equal' => 'Дата окончания не может быть раньше даты начала.',

            'activity.required' => 'Укажите активность цены.',
            'activity.boolean' => 'Поле активности должно быть булевым значением.',

            'sort.integer' => 'Позиция должна быть целым числом.',
            'sort.min' => 'Позиция не может быть отрицательной.',

            'meta.array' => 'Meta должен быть объектом JSON.',
        ];
    }

    public function attributes(): array
    {
        return [
            'school_course_id' => 'Курс',
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
