<?php

namespace App\Http\Requests\Admin\School\SchoolFaq;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolFaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['locale', 'question', 'answer', 'meta_title', 'meta_description'] as $field) {
            if ($this->filled($field) && is_string($this->input($field))) {
                $data[$field] = trim($this->input($field));
            }
        }

        foreach (['school_faq_category_id', 'sort'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        if ($this->has('activity')) {
            $data['activity'] = filter_var(
                $this->input('activity'),
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            );
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        return [
            'school_faq_category_id' => [
                'nullable',
                'integer',
                Rule::exists('school_faq_categories', 'id'),
            ],

            'sort' => ['sometimes', 'integer', 'min:0'],
            'activity' => ['sometimes', 'boolean'],

            'locale' => ['required', 'string', 'max:10'],

            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],

            'meta_title' => ['nullable', 'string', 'max:160'],
            'meta_description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'school_faq_category_id.exists' => 'Категория FAQ не найдена.',

            'sort.integer' => 'Порядок сортировки должен быть числом.',
            'sort.min' => 'Порядок сортировки не может быть отрицательным.',

            'activity.boolean' => 'Поле активности должно быть булевым.',

            'locale.required' => 'Укажите локаль.',
            'locale.max' => 'Локаль не должна превышать 10 символов.',

            'question.required' => 'Укажите вопрос.',
            'question.max' => 'Вопрос не должен превышать 255 символов.',

            'answer.required' => 'Укажите ответ.',

            'meta_title.max' => 'Meta Title не должен превышать 160 символов.',
            'meta_description.max' => 'Meta Description не должен превышать 255 символов.',
        ];
    }

    public function attributes(): array
    {
        return [
            'school_faq_category_id' => 'Категория FAQ',
            'sort' => 'Порядок сортировки',
            'activity' => 'Активность',
            'locale' => 'Локаль',
            'question' => 'Вопрос',
            'answer' => 'Ответ',
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
        ];
    }
}
