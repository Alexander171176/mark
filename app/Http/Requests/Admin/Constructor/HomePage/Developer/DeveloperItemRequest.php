<?php

namespace App\Http\Requests\Admin\Constructor\HomePage\Developer;

use Illuminate\Foundation\Http\FormRequest;

class DeveloperItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'activity' => filter_var($this->input('activity', true), FILTER_VALIDATE_BOOLEAN),
            'sort'     => is_numeric($this->input('sort')) ? (int) $this->input('sort') : 0,
        ]);
    }

    public function rules(): array
    {
        $isUpdate = (bool) $this->route('item'); // ожидаем {item} при апдейте

        // мягкая проверка «похоже на SVG»
        $looksLikeSvg = function (string $attr, $value, $fail) {
            if (!filled($value)) return; // пустое — пропускаем, это nullable
            $head = strtolower(substr((string) $value, 0, 1024));
            if (!str_contains($head, '<svg')) {
                $fail('Значение поля «'.$this->attributes()[$attr] ?? $attr.'» должно содержать корректную SVG-разметку.');
            }
        };

        return [
            'developer_section_id' => $isUpdate
                ? ['sometimes','integer','exists:developer_sections,id']
                : ['required','integer','exists:developer_sections,id'],

            'title'       => ['required','string','max:255'],
            'subtitle'    => ['nullable','string','max:255'],
            'description' => ['nullable','string'],
            'alt'         => ['nullable','string','max:255'],

            'sort'        => ['sometimes','integer','min:0'],
            'activity'    => ['sometimes','boolean'],

            // inline SVG как текст
            'image_light' => ['nullable','string', $looksLikeSvg],
            'image_dark'  => ['nullable','string', $looksLikeSvg],
        ];
    }

    public function attributes(): array
    {
        return [
            'developer_section_id' => 'секция',
            'title'       => 'заголовок',
            'subtitle'    => 'подзаголовок',
            'description' => 'описание',
            'alt'         => 'alt-текст',
            'sort'        => 'сортировка',
            'activity'    => 'активность',
            'image_light' => 'SVG (светлая тема)',
            'image_dark'  => 'SVG (тёмная тема)',
        ];
    }

    public function messages(): array
    {
        return [
            'developer_section_id.required' => 'Не выбрана «:attribute».',
            'developer_section_id.integer'  => 'Поле «:attribute» должно быть числом.',
            'developer_section_id.exists'   => 'Выбранная «:attribute» не найдена.',

            'title.required' => 'Укажите «:attribute».',
            'title.string'   => 'Поле «:attribute» должно быть строкой.',
            'title.max'      => 'В «:attribute» не более :max символов.',

            'subtitle.string' => 'Поле «:attribute» должно быть строкой.',
            'subtitle.max'    => 'В «:attribute» не более :max символов.',

            'description.string' => 'Поле «:attribute» должно быть строкой.',

            'alt.string' => 'Поле «:attribute» должно быть строкой.',
            'alt.max'    => 'В «:attribute» не более :max символов.',

            'sort.integer' => 'Поле «:attribute» должно быть целым числом.',
            'sort.min'     => 'Поле «:attribute» не может быть меньше :min.',

            'activity.boolean' => 'Поле «:attribute» должно быть булевым значением.',

            // image_light / image_dark — текстовые, поэтому стандартных file/mime сообщений нет
        ];
    }
}
