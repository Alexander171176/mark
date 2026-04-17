<?php

namespace App\Http\Requests\Admin\Constructor\HomePage\Developer;

use App\Models\Admin\Constructor\HomePage\Developer\DeveloperSection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeveloperSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'locale'   => strtolower((string) $this->input('locale', '')),
            'is_dark'  => filter_var($this->input('is_dark', false), FILTER_VALIDATE_BOOLEAN),
            'activity' => filter_var($this->input('activity', true),  FILTER_VALIDATE_BOOLEAN),
            'sort'     => is_numeric($this->input('sort')) ? (int) $this->input('sort') : 0,
        ]);
    }

    public function rules(): array
    {
        /** @var DeveloperSection|null $section */
        $section   = $this->route('section');         // ожидаем route-model binding: {section}
        $sectionId = $section?->getKey();

        return [
            'locale'   => [
                'required','string','size:2',
                Rule::unique('developer_sections', 'locale')->ignore($sectionId),
            ],
            'title'    => ['nullable','string','max:255'],
            'subtitle' => ['nullable','string','max:255'],

            'sort'     => ['sometimes','integer','min:0'],
            'is_dark'  => ['sometimes','boolean'],
            'activity' => ['sometimes','boolean'],
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
            'locale.size'     => 'Поле «:attribute» должно состоять из :size символов.',
            'locale.unique'   => 'Запись с такой «:attribute» уже существует.',

            'title.string'    => 'Поле «:attribute» должно быть строкой.',
            'title.max'       => 'В «:attribute» не более :max символов.',

            'subtitle.string' => 'Поле «:attribute» должно быть строкой.',
            'subtitle.max'    => 'В «:attribute» не более :max символов.',

            'sort.integer'    => 'Поле «:attribute» должно быть целым числом.',
            'sort.min'        => 'Поле «:attribute» не может быть меньше :min.',

            'is_dark.boolean'  => 'Поле «:attribute» должно быть булевым значением.',
            'activity.boolean' => 'Поле «:attribute» должно быть булевым значением.',
        ];
    }
}
