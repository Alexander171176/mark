<?php

namespace App\Http\Requests\Admin\Constructor\HomePage\Component;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ComponentTileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sort'     => is_numeric($this->input('sort')) ? (int) $this->input('sort') : 0,
            'activity' => filter_var($this->input('activity', true), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
        ]);
    }

    public function rules(): array
    {
        return [
            'tab_id'    => ['required','integer', Rule::exists('component_tabs','id')],
            'href'      => ['required','string','max:1024'], // допускаем относительные пути типа /blocks/*
            // если нужна строгая проверка URL, добавь 'url'
            'title'     => ['required','string','max:255'],

            'light_alt' => ['nullable','string','max:255'],
            'dark_alt'  => ['nullable','string','max:255'],

            'sort'      => ['nullable','integer','min:0'],
            'activity'  => ['nullable','boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tab_id'    => 'вкладка',
            'href'      => 'ссылка',
            'title'     => 'заголовок',
            'light_alt' => 'ALT (светлая тема)',
            'dark_alt'  => 'ALT (тёмная тема)',
            'sort'      => 'сортировка',
            'activity'  => 'активность',
        ];
    }

    public function messages(): array
    {
        return [
            'tab_id.required' => 'Не указана :attribute.',
            'tab_id.exists'   => 'Выбранная :attribute не найдена.',
            'href.required'   => 'Укажите :attribute.',
            'href.max'        => ':attribute не может быть длиннее :max символов.',
            'title.required'  => 'Укажите :attribute.',
            'title.max'       => ':attribute не может быть длиннее :max символов.',
            'light_alt.max'   => ':attribute не может быть длиннее :max символов.',
            'dark_alt.max'    => ':attribute не может быть длиннее :max символов.',
            'sort.integer'    => ':attribute должна быть числом.',
            'sort.min'        => ':attribute не может быть меньше :min.',
            'activity.boolean'=> ':attribute имеет неверный формат.',
        ];
    }
}
