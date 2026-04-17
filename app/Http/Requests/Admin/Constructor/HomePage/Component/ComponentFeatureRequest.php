<?php

namespace App\Http\Requests\Admin\Constructor\HomePage\Component;

use App\Models\Admin\Constructor\HomePage\Component\ComponentFeature;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ComponentFeatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $feature = $this->route('feature'); // может быть моделью
        $this->merge([
            'section_id' => $this->input('section_id')
                ?? ($feature instanceof ComponentFeature ? $feature->section_id : null),
            'box_class' => $this->input('box_class') ?: 'is-primary',
            'sort'      => is_numeric($this->input('sort')) ? (int) $this->input('sort') : 0,
            'activity'  => filter_var($this->input('activity', true), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
        ]);
    }

    public function rules(): array
    {
        return [
            'section_id'     => ['required','integer', Rule::exists('component_sections','id')],
            'box_class'      => ['nullable','string','max:64'],
            'title'          => ['required','string','max:255'],
            'text'           => ['nullable','string'],

            'icon_svg_light' => ['nullable','string'],
            'icon_svg_dark'  => ['nullable','string'],
            'icon_alt'       => ['nullable','string','max:255'],

            'sort'           => ['nullable','integer','min:0'],
            'activity'       => ['nullable','boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'section_id'     => 'секция',
            'box_class'      => 'класс блока',
            'title'          => 'заголовок',
            'text'           => 'текст',
            'icon_svg_light' => 'SVG (светлая тема)',
            'icon_svg_dark'  => 'SVG (тёмная тема)',
            'icon_alt'       => 'ALT иконки',
            'sort'           => 'сортировка',
            'activity'       => 'активность',
        ];
    }

    public function messages(): array
    {
        return [
            'section_id.required' => 'Не указана :attribute.',
            'section_id.exists'   => 'Выбранная :attribute не найдена.',
            'title.required'      => 'Укажите :attribute.',
            'title.max'           => ':attribute не может быть длиннее :max символов.',
            'box_class.max'       => ':attribute не может быть длиннее :max символов.',
            'icon_alt.max'        => ':attribute не может быть длиннее :max символов.',
            'sort.integer'        => ':attribute должна быть числом.',
            'sort.min'            => ':attribute не может быть меньше :min.',
            'activity.boolean'    => ':attribute имеет неверный формат.',
        ];
    }
}
