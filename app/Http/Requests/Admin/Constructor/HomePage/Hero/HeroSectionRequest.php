<?php

namespace App\Http\Requests\Admin\Constructor\HomePage\Hero;

use Illuminate\Foundation\Http\FormRequest;

class HeroSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Приводим булевы
        $this->merge([
            'activity' => filter_var($this->input('activity', false), FILTER_VALIDATE_BOOLEAN),
            'is_dark'  => filter_var($this->input('is_dark', false), FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function rules(): array
    {
        return [
            // Ровно 2 латинских символа без списка языков
            'locale'               => ['required', 'string', 'regex:/^[a-z]{2}$/i'],

            'title'                => ['nullable','string','max:255'],
            'subtitle'             => ['nullable','string','max:255'],
            'badge_text'           => ['nullable','string','max:255'],
            'description'          => ['nullable','string'],

            'primary_btn_text'     => ['nullable','string','max:255'],
            'primary_btn_url'      => ['nullable','string','max:2048'],
            'primary_btn_target'   => ['required','in:_self,_blank'],

            'secondary_btn_text'   => ['nullable','string','max:255'],
            'secondary_btn_url'    => ['nullable','string','max:2048'],
            'secondary_btn_target' => ['required','in:_self,_blank'],

            'is_dark'              => ['sometimes','boolean'],
            'activity'             => ['sometimes','boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            // locale
            'locale.required' => 'Поле ":attribute" обязательно.',
            'locale.string'   => 'Поле ":attribute" должно быть строкой.',
            'locale.regex'    => 'Поле ":attribute" должно состоять ровно из двух латинских букв (например: ru, en).',

            // title
            'title.string'    => ':attribute должна быть строкой.',
            'title.max'       => ':attribute не должна превышать :max символов.',

            // subtitle
            'subtitle.string' => ':attribute должна быть строкой.',
            'subtitle.max'    => ':attribute не должна превышать :max символов.',

            // badge_text
            'badge_text.string' => ':attribute должна быть строкой.',
            'badge_text.max'    => ':attribute не должна превышать :max символов.',

            // description
            'description.string' => ':attribute должна быть строкой.',

            // primary button
            'primary_btn_text.string'  => ':attribute должна быть строкой.',
            'primary_btn_text.max'     => ':attribute не должна превышать :max символов.',
            'primary_btn_url.string'   => ':attribute должна быть строкой.',
            'primary_btn_url.max'      => ':attribute не должна превышать :max символов.',
            'primary_btn_target.required' => 'Выберите :attribute.',
            'primary_btn_target.in'       => ':attribute может быть только _self или _blank.',

            // secondary button
            'secondary_btn_text.string' => ':attribute должна быть строкой.',
            'secondary_btn_text.max'    => ':attribute не должна превышать :max символов.',
            'secondary_btn_url.string'  => ':attribute должна быть строкой.',
            'secondary_btn_url.max'     => ':attribute не должна превышать :max символов.',
            'secondary_btn_target.required' => 'Выберите :attribute.',
            'secondary_btn_target.in'       => ':attribute может быть только _self или _blank.',

            // flags
            'is_dark.boolean'  => ':attribute должно быть булевым значением.',
            'activity.boolean' => ':attribute должно быть булевым значением.',
        ];
    }

    public function attributes(): array
    {
        return [
            'locale'               => 'язык',
            'title'                => 'заголовок',
            'subtitle'             => 'подзаголовок',
            'badge_text'           => 'бейдж',
            'description'          => 'описание',
            'primary_btn_text'     => 'текст кнопки 1',
            'primary_btn_url'      => 'ссылка кнопки 1',
            'primary_btn_target'   => 'target кнопки 1',
            'secondary_btn_text'   => 'текст кнопки 2',
            'secondary_btn_url'    => 'ссылка кнопки 2',
            'secondary_btn_target' => 'target кнопки 2',
            'is_dark'              => 'тёмная тема',
            'activity'             => 'активность',
        ];
    }
}
