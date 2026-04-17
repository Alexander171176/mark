<?php

namespace App\Http\Requests\Admin\Constructor\HomePage\Demo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DemoGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $slug = $this->input('slug');
        if ($slug !== null) {
            $slug = Str::of($slug)->lower()->replace(' ', '-')->__toString();
        }

        $this->merge([
            'slug'           => $slug,
            'section_id'     => $this->input('section_id') ?? optional($this->route('group'))->section_id,
            'icon_svg_light' => $this->input('icon_svg_light', $this->input('inline_svg_light')),
            'icon_svg_dark'  => $this->input('icon_svg_dark',  $this->input('inline_svg_dark')),
            'activity'       => filter_var($this->input('activity', false), FILTER_VALIDATE_BOOLEAN),
            'sort'           => is_numeric($this->input('sort')) ? (int) $this->input('sort') : $this->input('sort'),
        ]);
    }

    public function rules(): array
    {
        // id группы для ignore()
        $groupId =
            ($this->route('group')->id ?? null) ??
            ($this->route('demo_group')->id ?? null) ??
            ($this->route('id') ?? null);

        $sectionId = $this->input('section_id');

        return [
            'section_id' => ['required', 'integer', 'exists:demo_sections,id'],

            'slug' => [
                'required', 'string', 'alpha_dash', 'max:100',
                // уникальность slug в рамках section_id
                Rule::unique('demo_groups', 'slug')
                    ->ignore($groupId)
                    ->where(fn ($q) => $q->where('section_id', $sectionId)),
            ],

            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],

            // Inline SVG (как текст)
            'icon_svg_light' => ['nullable', 'string'],
            'icon_svg_dark'  => ['nullable', 'string'],
            'icon_alt'       => ['nullable', 'string', 'max:255'],

            'sort'     => ['sometimes', 'integer', 'min:0'],
            'activity' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'section_id.required' => 'Выберите :attribute.',
            'section_id.integer'  => ':attribute должен быть целым числом.',
            'section_id.exists'   => 'Указанная :attribute не найдена.',

            'slug.required'   => 'Поле ":attribute" обязательно.',
            'slug.string'     => ':attribute должна быть строкой.',
            'slug.alpha_dash' => ':attribute может содержать только буквы, цифры, дефис и нижнее подчёркивание.',
            'slug.max'        => ':attribute не должна превышать :max символов.',
            'slug.unique'     => ':attribute уже используется в этой секции.',

            'title.required' => 'Поле ":attribute" обязательно.',
            'title.string'   => ':attribute должна быть строкой.',
            'title.max'      => ':attribute не должна превышать :max символов.',

            'description.string' => ':attribute должна быть строкой.',
            'description.max'    => ':attribute не должна превышать :max символов.',

            'icon_svg_light.string' => ':attribute должна быть строкой (SVG как текст).',
            'icon_svg_dark.string'  => ':attribute должна быть строкой (SVG как текст).',
            'icon_alt.string'       => ':attribute должна быть строкой.',
            'icon_alt.max'          => ':attribute не должна превышать :max символов.',

            'sort.integer'  => ':attribute должен быть целым числом.',
            'sort.min'      => ':attribute не может быть меньше :min.',
            'activity.boolean' => ':attribute должно быть булевым значением.',
        ];
    }

    public function attributes(): array
    {
        return [
            'section_id'     => 'секция демо',
            'slug'           => 'slug группы',
            'title'          => 'заголовок группы',
            'description'    => 'описание группы',
            'icon_svg_light' => 'SVG (светлая тема)',
            'icon_svg_dark'  => 'SVG (тёмная тема)',
            'icon_alt'       => 'alt иконки',
            'sort'           => 'порядок',
            'activity'       => 'активность',
        ];
    }
}
