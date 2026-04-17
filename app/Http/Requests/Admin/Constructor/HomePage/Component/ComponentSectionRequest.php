<?php

namespace App\Http\Requests\Admin\Constructor\HomePage\Component;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ComponentSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'locale'   => strtolower((string) $this->input('locale', '')),
            'sort'     => is_numeric($this->input('sort')) ? (int) $this->input('sort') : 0,
            'activity' => filter_var($this->input('activity', true), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
        ]);
    }

    public function rules(): array
    {
        // попытка вытащить ID из разных ключей маршрута
        $routeId = $this->route('section')
            ?? $this->route('component_section')
            ?? $this->route('id');

        return [
            'locale'   => [
                'required','string','size:2',
                Rule::unique('component_sections','locale')->ignore($routeId),
            ],
            'subtitle' => ['nullable','string','max:255'],
            'title'    => ['nullable','string','max:255'],

            'cta_text' => ['nullable','string','max:255'],
            'cta_url'  => ['nullable','string','max:1024'], // допускаем относительные пути
            // если хочешь строго URL: заменяй на ['nullable','url','max:1024']

            'sort'     => ['nullable','integer','min:0'],
            'activity' => ['nullable','boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'locale'   => 'локаль',
            'subtitle' => 'подзаголовок',
            'title'    => 'заголовок',
            'cta_text' => 'текст кнопки',
            'cta_url'  => 'ссылка кнопки',
            'sort'     => 'сортировка',
            'activity' => 'активность',
        ];
    }

    public function messages(): array
    {
        return [
            'locale.required' => 'Укажите :attribute.',
            'locale.size'     => ':attribute должна состоять из 2 символов (например, ru, en).',
            'locale.unique'   => 'Секция для такой локали уже существует.',

            'subtitle.max'    => ':attribute не может быть длиннее :max символов.',
            'title.max'       => ':attribute не может быть длиннее :max символов.',

            'cta_text.max'    => ':attribute не может быть длиннее :max символов.',
            'cta_url.max'     => ':attribute не может быть длиннее :max символов.',

            'sort.integer'    => ':attribute должна быть числом.',
            'sort.min'        => ':attribute не может быть меньше :min.',
            'activity.boolean'=> ':attribute имеет неверный формат.',
        ];
    }
}
