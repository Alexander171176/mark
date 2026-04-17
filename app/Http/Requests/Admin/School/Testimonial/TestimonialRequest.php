<?php

namespace App\Http\Requests\Admin\School\Testimonial;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        // Нормализуем локаль
        if ($this->filled('locale')) {
            $merge['locale'] = strtolower((string)$this->input('locale'));
        }

        // Тримим текстовые поля
        foreach (['quote','author_name','author_title','company','avatar_url','source_url'] as $key) {
            if ($this->has($key) && is_string($this->input($key))) {
                $merge[$key] = trim((string)$this->input($key));
            }
        }

        if ($merge) {
            $this->merge($merge);
        }
    }

    /**
     * @return array<string,ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'quote'        => ['required','string'],
            'author_name'  => ['required','string','max:255'],
            'author_title' => ['nullable','string','max:255'],
            'company'      => ['nullable','string','max:255'],

            'avatar_url'   => ['nullable','url','max:2048'],
            'source_url'   => ['nullable','url','max:2048'],

            'rating'       => ['nullable','integer','between:1,5'],
            'activity'     => ['sometimes','boolean'],
            'sort'         => ['sometimes','integer','min:0'],
            'locale'       => ['nullable','string','max:16'],
            'meta'         => ['nullable','array'],
        ];
    }

    public function attributes(): array
    {
        return [
            'quote'        => 'текст отзыва',
            'author_name'  => 'имя автора',
            'author_title' => 'должность автора',
            'company'      => 'компания',
            'avatar_url'   => 'URL аватара',
            'source_url'   => 'ссылка на источник',
            'rating'       => 'оценка',
            'activity'     => 'активность',
            'sort'         => 'позиция',
            'locale'       => 'локаль',
            'meta'         => 'метаданные',
        ];
    }
}
