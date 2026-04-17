<?php

namespace App\Http\Requests\Admin\Constructor\HomePage\Feature;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FeatureSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_dark'  => filter_var($this->input('is_dark', false), FILTER_VALIDATE_BOOLEAN),
            'activity' => filter_var($this->input('activity', false), FILTER_VALIDATE_BOOLEAN),
            'sort'     => is_numeric($this->input('sort')) ? (int) $this->input('sort') : 0,
        ]);
    }

    public function rules(): array
    {
        // Пытаемся достать ID из разных возможных имён параметра
        $routeParam = $this->route('section') ?? $this->route('feature_section') ?? $this->route('id');
        $sectionId  = is_object($routeParam) ? $routeParam->getKey() : $routeParam;

        return [
            'locale'   => [
                'required', 'string', 'size:2',
                // одна запись на локаль (уникальная), допускаем обновление текущей
                Rule::unique('feature_sections', 'locale')->ignore($sectionId),
            ],
            'title'    => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],

            'sort'     => ['sometimes', 'integer', 'min:0'],
            'is_dark'  => ['sometimes', 'boolean'],
            'activity' => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'locale'   => 'локаль',
            'title'    => 'заголовок',
            'subtitle' => 'подзаголовок',
            'sort'     => 'сортировка',
            'is_dark'  => 'тёмная тема',
            'activity' => 'активность',
        ];
    }

    public function messages(): array
    {
        return [
            'locale.required' => 'Укажите «:attribute».',
            'locale.string'   => 'Поле «:attribute» должно быть строкой.',
            'locale.size'     => 'Поле «:attribute» должно состоять из :size символов (например, ru, en).',
            'locale.unique'   => 'Запись с такой «:attribute» уже существует.',

            'title.string'    => 'Поле «:attribute» должно быть строкой.',
            'title.max'       => 'В «:attribute» не более :max символов.',

            'subtitle.string' => 'Поле «:attribute» должно быть строкой.',
            'subtitle.max'    => 'В «:attribute» не более :max символов.',

            'sort.integer'    => 'Поле «:attribute» должно быть целым числом.',
            'sort.min'        => 'Поле «:attribute» не может быть меньше :min.',

            'is_dark.boolean' => 'Поле «:attribute» должно быть булевым значением.',
            'activity.boolean'=> 'Поле «:attribute» должно быть булевым значением.',
        ];
    }
}
