<?php

namespace App\Http\Requests\Admin\Constructor\HomePage\Quality;

use Illuminate\Foundation\Http\FormRequest;

class QualityItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'reveal_from' => $this->input('reveal_from') ? strtolower(trim($this->input('reveal_from'))) : null,
            'activity'    => filter_var($this->input('activity', true), FILTER_VALIDATE_BOOLEAN),

            'sort'        => is_numeric($this->input('sort')) ? (int) $this->input('sort') : $this->input('sort'),
            'delay'       => is_numeric($this->input('delay')) ? (int) $this->input('delay') : $this->input('delay'),
            'distance'    => is_numeric($this->input('distance')) ? (int) $this->input('distance') : $this->input('distance'),
            'threshold'   => is_numeric($this->input('threshold')) ? (float) $this->input('threshold') : $this->input('threshold'),
        ]);
    }

    public function rules(): array
    {
        return [
            'section_id'  => ['required', 'integer', 'exists:quality_sections,id'],

            'top_title'   => ['required', 'string', 'max:255'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],

            // анимация/поведение
            'reveal_from' => ['nullable', 'string', 'in:bottom,top,left,right'],
            'delay'       => ['nullable', 'integer', 'min:0', 'max:60000'], // мс
            'threshold'   => ['nullable', 'numeric', 'between:0,1'],        // 0..1
            'distance'    => ['nullable', 'integer', 'min:0', 'max:1000'],  // px

            // SVG как текст
            'icon_svg_light' => ['nullable', 'string'],
            'icon_svg_dark'  => ['nullable', 'string'],
            'icon_alt'       => ['nullable', 'string', 'max:255'],

            // служебные
            'sort'     => ['sometimes', 'integer', 'min:0'],
            'activity' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            // fk
            'section_id.required' => 'Выберите :attribute.',
            'section_id.integer'  => ':attribute должен быть целым числом.',
            'section_id.exists'   => 'Указанная :attribute не найдена.',

            // заголовки / описание
            'top_title.required' => 'Поле ":attribute" обязательно.',
            'top_title.string'   => ':attribute должна быть строкой.',
            'top_title.max'      => ':attribute не должна превышать :max символов.',

            'title.required' => 'Поле ":attribute" обязательно.',
            'title.string'   => ':attribute должна быть строкой.',
            'title.max'      => ':attribute не должна превышать :max символов.',

            'description.string' => ':attribute должна быть строкой.',
            'description.max'    => ':attribute не должна превышать :max символов.',

            // анимация/поведение
            'reveal_from.string' => ':attribute должна быть строкой.',
            'reveal_from.in'     => ':attribute может быть только: bottom, top, left или right.',
            'delay.integer'      => ':attribute должен быть целым числом (миллисекунды).',
            'delay.min'          => ':attribute не может быть меньше :min.',
            'delay.max'          => ':attribute не может превышать :max.',
            'threshold.numeric'  => ':attribute должно быть числом от 0 до 1.',
            'threshold.between'  => ':attribute должно быть в диапазоне от :min до :max.',
            'distance.integer'   => ':attribute должен быть целым числом.',
            'distance.min'       => ':attribute не может быть меньше :min.',
            'distance.max'       => ':attribute не может превышать :max.',

            // svg / alt
            'icon_svg_light.string' => ':attribute должна быть строкой.',
            'icon_svg_dark.string'  => ':attribute должна быть строкой.',
            'icon_alt.string'       => ':attribute должна быть строкой.',
            'icon_alt.max'          => ':attribute не должна превышать :max символов.',

            // служебные
            'sort.integer'   => ':attribute должен быть целым числом.',
            'sort.min'       => ':attribute не может быть меньше :min.',
            'activity.boolean' => ':attribute должно быть булевым значением.',
        ];
    }

    public function attributes(): array
    {
        return [
            'section_id'   => 'секция качества',
            'top_title'    => 'верхний заголовок',
            'title'        => 'заголовок',
            'description'  => 'описание',
            'reveal_from'  => 'направление анимации',
            'delay'        => 'задержка анимации, мс',
            'threshold'    => 'порог видимости',
            'distance'     => 'смещение при появлении, px',
            'icon_svg_light' => 'SVG (светлая тема)',
            'icon_svg_dark'  => 'SVG (тёмная тема)',
            'icon_alt'     => 'alt иконки',
            'sort'         => 'порядок',
            'activity'     => 'активность',
        ];
    }
}
