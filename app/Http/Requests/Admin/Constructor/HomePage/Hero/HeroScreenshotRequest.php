<?php

namespace App\Http\Requests\Admin\Constructor\HomePage\Hero;

use Illuminate\Foundation\Http\FormRequest;

class HeroScreenshotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Метаполя (частичное обновление разрешено)
            'alt'      => ['sometimes', 'nullable', 'string', 'max:255'],
            'sort'     => ['sometimes', 'integer', 'min:0'],
            'activity' => ['sometimes', 'boolean'],

            // Файлы (любой из них может отсутствовать)
            'light'    => ['sometimes', 'nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:8192'], // ~8 МБ
            'dark'     => ['sometimes', 'nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:8192'],
        ];
    }

    public function attributes(): array
    {
        return [
            'alt'      => 'alt-текст',
            'sort'     => 'порядок',
            'activity' => 'активность',
            'light'    => 'светлое изображение',
            'dark'     => 'тёмное изображение',
        ];
    }

    public function messages(): array
    {
        return [
            'alt.string' => ':attribute должна быть строкой.',
            'alt.max'    => ':attribute не должна превышать :max символов.',

            'sort.integer' => ':attribute должен быть целым числом.',
            'sort.min'     => ':attribute не может быть меньше :min.',

            'activity.boolean' => ':attribute должно быть булевым значением.',

            'light.image' => ':attribute должна быть изображением.',
            'light.mimes' => ':attribute должна быть одного из типов: :values.',
            'light.max'   => ':attribute не должна превышать :max КБ.',
            'dark.image'  => ':attribute должна быть изображением.',
            'dark.mimes'  => ':attribute должна быть одного из типов: :values.',
            'dark.max'    => ':attribute не должна превышать :max КБ.',
        ];
    }
}
