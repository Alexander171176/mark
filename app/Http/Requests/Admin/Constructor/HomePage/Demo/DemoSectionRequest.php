<?php

namespace App\Http\Requests\Admin\Constructor\HomePage\Demo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DemoSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'activity' => filter_var($this->input('activity', false), FILTER_VALIDATE_BOOLEAN),
            'is_dark'  => filter_var($this->input('is_dark', false), FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function rules(): array
    {
        // Попытка аккуратно достать id из роут-модели
        $sectionId =
            ($this->route('section')->id ?? null) ??
            ($this->route('demo_section')->id ?? null) ??
            ($this->route('id') ?? null);

        return [
            // Ровно 2 латинских символа, уникально среди секций
            'locale' => [
                'required', 'string', 'regex:/^[a-z]{2}$/i',
                Rule::unique('demo_sections', 'locale')->ignore($sectionId),
            ],

            'title'              => ['nullable', 'string', 'max:255'],
            'subtitle'           => ['nullable', 'string', 'max:255'],
            'search_placeholder' => ['nullable', 'string', 'max:255'],

            'sort'     => ['sometimes', 'integer', 'min:0'],
            'is_dark'  => ['sometimes', 'boolean'],
            'activity' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            // locale
            'locale.required' => 'Поле ":attribute" обязательно.',
            'locale.string'   => 'Поле ":attribute" должно быть строкой.',
            'locale.regex'    => 'Поле ":attribute" должно состоять ровно из двух латинских букв (например: ru, en).',
            'locale.unique'   => 'Запись для выбранного языка уже существует.',

            // title / subtitle / placeholder
            'title.string'              => ':attribute должна быть строкой.',
            'title.max'                 => ':attribute не должна превышать :max символов.',
            'subtitle.string'           => ':attribute должна быть строкой.',
            'subtitle.max'              => ':attribute не должна превышать :max символов.',
            'search_placeholder.string' => ':attribute должна быть строкой.',
            'search_placeholder.max'    => ':attribute не должна превышать :max символов.',

            // sort / flags
            'sort.integer'  => ':attribute должен быть целым числом.',
            'sort.min'      => ':attribute не может быть меньше :min.',
            'is_dark.boolean'  => ':attribute должно быть булевым значением.',
            'activity.boolean' => ':attribute должно быть булевым значением.',
        ];
    }

    public function attributes(): array
    {
        return [
            'locale'             => 'язык',
            'title'              => 'заголовок',
            'subtitle'           => 'подзаголовок',
            'search_placeholder' => 'placeholder поиска',
            'sort'               => 'порядок',
            'is_dark'            => 'тёмная тема',
            'activity'           => 'активность',
        ];
    }
}
