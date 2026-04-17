<?php

namespace App\Http\Requests\Admin\Constructor\Faq;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        // Нормализуем/генерируем slug из вопроса, если не передан
        $slug = $this->input('slug');
        if (is_string($slug) && $slug !== '') {
            $merge['slug'] = Str::slug($slug);
        } elseif ($this->filled('question')) {
            $merge['slug'] = Str::slug((string)$this->input('question'));
        }

        if ($this->filled('locale')) {
            $merge['locale'] = strtolower((string)$this->input('locale'));
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    /**
     * @return array<string,ValidationRule|array|string>
     */
    public function rules(): array
    {
        $ignoreId = $this->route('faq')?->id ?? null;

        return [
            'question'  => ['required','string','max:255'],
            'answer'    => ['required','string'],
            'slug'      => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', // безопасный slug
                Rule::unique('faqs', 'slug')->ignore($ignoreId),
            ],
            'category'  => ['nullable','string','max:128'],
            'locale'    => ['nullable','string','max:16'],
            'activity'  => ['sometimes','boolean'],
            'sort'  => ['sometimes','integer','min:0'],
            'meta'      => ['nullable','array'],
        ];
    }

    public function attributes(): array
    {
        return [
            'question'  => 'вопрос',
            'answer'    => 'ответ',
            'slug'      => 'слаг',
            'category'  => 'категория',
            'locale'    => 'локаль',
            'activity'  => 'активность',
            'sort'      => 'позиция',
            'meta'      => 'метаданные',
        ];
    }
}
