<?php

namespace App\Http\Requests\Admin\Constructor\HomePage\Feature;

use Illuminate\Foundation\Http\FormRequest;

class FeatureItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'activity' => filter_var($this->input('activity', false), FILTER_VALIDATE_BOOLEAN),
            'sort'     => is_numeric($this->input('sort')) ? (int) $this->input('sort') : 0,
        ]);
    }

    public function rules(): array
    {
        // Создаём/обновляем карточку фичи. SVG храним инлайном в TEXT.
        // Проверяем, что если поле заполнено — это похоже на SVG (<svg ...>).
        $svgTextRule = ['nullable', 'string', 'regex:/<svg[\s>]/i'];

        return [
            'feature_section_id' => ['required', 'integer', 'exists:feature_sections,id'],

            'title'       => ['required', 'string', 'max:255'],
            'subtitle'    => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            // Инлайновые SVG / HTML
            'image_light' => $svgTextRule,
            'image_dark'  => $svgTextRule,
            'alt'         => ['nullable', 'string', 'max:255'],

            'sort'        => ['sometimes', 'integer', 'min:0'],
            'activity'    => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'feature_section_id' => 'секция',
            'title'              => 'заголовок',
            'subtitle'           => 'подзаголовок',
            'description'        => 'описание',
            'image_light'        => 'SVG (светлая тема)',
            'image_dark'         => 'SVG (тёмная тема)',
            'alt'                => 'alt-текст',
            'sort'               => 'сортировка',
            'activity'           => 'активность',
        ];
    }

    public function messages(): array
    {
        return [
            'feature_section_id.required' => 'Не выбрана «:attribute».',
            'feature_section_id.integer'  => 'Поле «:attribute» должно быть числом.',
            'feature_section_id.exists'   => 'Выбранная «:attribute» не найдена.',

            'title.required' => 'Укажите «:attribute».',
            'title.string'   => 'Поле «:attribute» должно быть строкой.',
            'title.max'      => 'В «:attribute» не более :max символов.',

            'subtitle.string' => 'Поле «:attribute» должно быть строкой.',
            'subtitle.max'    => 'В «:attribute» не более :max символов.',

            'description.string' => 'Поле «:attribute» должно быть строкой.',

            'image_light.string' => 'Поле «:attribute» должно быть строкой с inline-SVG.',
            'image_light.regex'  => 'Поле «:attribute» должно содержать корректный SVG (тег <svg>).',

            'image_dark.string'  => 'Поле «:attribute» должно быть строкой с inline-SVG.',
            'image_dark.regex'   => 'Поле «:attribute» должно содержать корректный SVG (тег <svg>).',

            'alt.string' => 'Поле «:attribute» должно быть строкой.',
            'alt.max'    => 'В «:attribute» не более :max символов.',

            'sort.integer' => 'Поле «:attribute» должно быть целым числом.',
            'sort.min'     => 'Поле «:attribute» не может быть меньше :min.',

            'activity.boolean' => 'Поле «:attribute» должно быть булевым значением.',
        ];
    }
}
