<?php

namespace App\Http\Requests\Admin\Constructor\HomePage\Wave;

use Illuminate\Foundation\Http\FormRequest;

class WaveSectionRequest extends FormRequest
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
            'sort'     => is_numeric($this->input('sort')) ? (int)$this->input('sort') : 0,
        ]);
    }

    public function rules(): array
    {
        return [
            // локаль — любые 2 символа (как договорились для Hero)
            'locale'     => ['required','string','size:2'],

            'title'      => ['nullable','string','max:255'],
            'subtitle'   => ['nullable','string','max:255'],
            'left_text'  => ['nullable','string'],
            'right_text' => ['nullable','string'],

            'sort'       => ['sometimes','integer','min:0'],
            'is_dark'    => ['sometimes','boolean'],
            'activity'   => ['sometimes','boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'locale'     => 'язык',
            'title'      => 'заголовок',
            'subtitle'   => 'подзаголовок',
            'left_text'  => 'левый текст',
            'right_text' => 'правый текст',
            'sort'       => 'сортировка',
            'is_dark'    => 'тёмная тема',
            'activity'   => 'активность',
        ];
    }

    public function messages(): array
    {
        return [
            'locale.required' => 'Укажите язык.',
            'locale.string'   => 'Поле «:attribute» должно быть строкой.',
            'locale.size'     => 'Поле «:attribute» должно содержать 2 символа.',

            'title.string'    => 'Поле «:attribute» должно быть строкой.',
            'title.max'       => 'В «:attribute» не более :max символов.',

            'subtitle.string' => 'Поле «:attribute» должно быть строкой.',
            'subtitle.max'    => 'В «:attribute» не более :max символов.',

            'left_text.string'  => 'Поле «:attribute» должно быть строкой.',
            'right_text.string' => 'Поле «:attribute» должно быть строкой.',

            'sort.integer'    => 'Поле «:attribute» должно быть целым числом.',
            'sort.min'        => 'Поле «:attribute» не может быть меньше :min.',
            'is_dark.boolean' => 'Поле «:attribute» должно быть булевым значением.',
            'activity.boolean'=> 'Поле «:attribute» должно быть булевым значением.',
        ];
    }
}
