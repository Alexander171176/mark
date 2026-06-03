<?php

namespace App\Http\Requests\Admin\School\SchoolFaqCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolFaqCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['slug', 'locale', 'title', 'description'] as $field) {
            if ($this->filled($field) && is_string($this->input($field))) {
                $data[$field] = trim($this->input($field));
            }
        }

        if ($this->filled('sort')) {
            $data['sort'] = (int) $this->input('sort');
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
        $category = $this->route('school_faq_category')
            ?? $this->route('faqCategory')
            ?? $this->route('schoolFaqCategory');

        $id = is_object($category)
            ? $category->id
            : ($category ? (int) $category : null);

        return [
            'sort' => ['sometimes', 'integer', 'min:0'],
            'activity' => ['sometimes', 'boolean'],

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('school_faq_categories', 'slug')->ignore($id),
            ],

            'locale' => ['required', 'string', 'max:10'],

            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('school_faq_category_translations', 'title')
                    ->where(fn ($q) => $q->where('locale', $this->input('locale')))
                    ->ignore(
                        $this->input('translation_id'),
                        'id'
                    ),
            ],

            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.required' => 'Укажите slug категории FAQ.',
            'slug.unique' => 'Категория FAQ с таким slug уже существует.',

            'locale.required' => 'Укажите локаль.',
            'locale.max' => 'Локаль не должна превышать 10 символов.',

            'title.required' => 'Укажите название категории FAQ.',
            'title.unique' => 'Категория FAQ с таким названием уже существует в этой локали.',

            'sort.integer' => 'Порядок сортировки должен быть числом.',
            'sort.min' => 'Порядок сортировки не может быть отрицательным.',

            'activity.boolean' => 'Поле активности должно быть булевым.',
        ];
    }

    public function attributes(): array
    {
        return [
            'sort' => 'Порядок сортировки',
            'activity' => 'Активность',
            'slug' => 'Slug',
            'locale' => 'Локаль',
            'title' => 'Название категории',
            'description' => 'Описание категории',
        ];
    }
}
