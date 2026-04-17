<?php

namespace App\Http\Requests\Admin\Constructor\HomePage\Hero;

use Illuminate\Foundation\Http\FormRequest;

class HeroIconRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = in_array($this->method(), ['PUT', 'PATCH'], true);
        $req      = $isUpdate ? 'sometimes' : 'required';

        return [
            'hero_section_id' => [$req, 'integer', 'exists:hero_sections,id'],
            'label'           => ['nullable', 'string', 'max:255'],
            'svg'             => ['nullable', 'string'], // inline SVG как текст
            'sort'            => ['sometimes', 'integer', 'min:0'],
            'activity'        => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'hero_section_id' => 'секция Hero',
            'label'           => 'лейбл иконки',
            'svg'             => 'SVG код',
            'sort'            => 'порядок',
            'activity'        => 'активность',
        ];
    }

    public function messages(): array
    {
        return [
            'hero_section_id.required' => 'Выберите :attribute.',
            'hero_section_id.integer'  => ':attribute должен быть целым числом.',
            'hero_section_id.exists'   => 'Указанная :attribute не найдена.',

            'label.string' => ':attribute должна быть строкой.',
            'label.max'    => ':attribute не должна превышать :max символов.',

            'svg.string'   => ':attribute должна быть строкой (SVG как текст).',

            'sort.integer' => ':attribute должен быть целым числом.',
            'sort.min'     => ':attribute не может быть меньше :min.',

            'activity.boolean' => ':attribute должно быть булевым значением.',
        ];
    }

}
