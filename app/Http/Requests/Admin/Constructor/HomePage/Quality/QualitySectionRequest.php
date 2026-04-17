<?php

namespace App\Http\Requests\Admin\Constructor\HomePage\Quality;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QualitySectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // locale нормально приводим
        if ($this->filled('locale')) {
            $this->merge([
                'locale' => strtolower(trim($this->input('locale'))),
            ]);
        }

        // Остальные поля приводим ТОЛЬКО если они пришли в запросе
        if ($this->has('is_dark')) {
            $this->merge([
                'is_dark' => filter_var($this->input('is_dark'), FILTER_VALIDATE_BOOLEAN),
            ]);
        }
        if ($this->has('activity')) {
            $this->merge([
                'activity' => filter_var($this->input('activity'), FILTER_VALIDATE_BOOLEAN),
            ]);
        }
        if ($this->has('sort')) {
            $this->merge([
                'sort' => is_numeric($this->input('sort')) ? (int)$this->input('sort') : $this->input('sort'),
            ]);
        }
    }

    public function rules(): array
    {
        // пытаемся корректно взять ID секции из роут-модели
        $sectionId =
            ($this->route('section')->id ?? null) ??
            ($this->route('quality_section')->id ?? null) ??
            ($this->route('id') ?? null);

        return [
            // 2 латинские буквы, уникально среди секций
            'locale'   => [
                'required', 'string', 'regex:/^[a-z]{2}$/i',
                Rule::unique('quality_sections', 'locale')->ignore($sectionId),
            ],

            'subtitle' => ['nullable', 'string', 'max:255'],
            'title'    => ['nullable', 'string', 'max:255'],
            'screenshot_alt' => ['nullable', 'string', 'max:255'],

            'sort'     => ['sometimes', 'integer', 'min:0'],
            'is_dark'  => ['sometimes', 'boolean'],
            'activity' => ['sometimes', 'boolean'],

            'light'    => ['sometimes', 'file', 'image', 'max:8192'], // 8MB как пример
            'dark'     => ['sometimes', 'file', 'image', 'max:8192'],
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

            // title / subtitle
            'title.string'    => ':attribute должна быть строкой.',
            'title.max'       => ':attribute не должна превышать :max символов.',
            'subtitle.string' => ':attribute должна быть строкой.',
            'subtitle.max'    => ':attribute не должна превышать :max символов.',

            'screenshot_alt.string' => 'Alt должен быть строкой.',
            'screenshot_alt.max'    => 'Alt не должен превышать :max символов.',

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
            'locale'   => 'язык',
            'subtitle' => 'подзаголовок',
            'title'    => 'заголовок',
            'sort'     => 'порядок',
            'is_dark'  => 'тёмная тема',
            'activity' => 'активность',
        ];
    }
}
