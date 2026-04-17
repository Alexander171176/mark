<?php

namespace App\Http\Requests\Admin\Constructor\HomePage\Demo;

use Illuminate\Foundation\Http\FormRequest;

class DemoItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'activity' => filter_var($this->input('activity', false), FILTER_VALIDATE_BOOLEAN),
            'sort'     => is_numeric($this->input('sort')) ? (int)$this->input('sort') : $this->input('sort'),
        ]);
    }

    public function rules(): array
    {
        $isUpdate = in_array($this->method(), ['PUT', 'PATCH'], true);
        $reqMeta  = $isUpdate ? 'sometimes' : 'required'; // на create обычно требуем мету

        return [
            'group_id' => [$reqMeta, 'integer', 'exists:demo_groups,id'],

            'href'     => ['nullable', 'string', 'url', 'max:2048'],
            'title'    => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'alt'      => ['nullable', 'string', 'max:255'],

            'sort'     => ['sometimes', 'integer', 'min:0'],
            'activity' => ['sometimes', 'boolean'],

            // Файлы изображений (Spatie): как в Hero — light/dark, опциональны
            'light' => ['sometimes', 'nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:8192'],
            'dark'  => ['sometimes', 'nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:8192'],
        ];
    }

    public function messages(): array
    {
        return [
            'group_id.required' => 'Выберите :attribute.',
            'group_id.integer'  => ':attribute должен быть целым числом.',
            'group_id.exists'   => 'Указанная :attribute не найдена.',

            'href.string' => ':attribute должна быть строкой.',
            'href.url'    => ':attribute должна быть корректным URL.',
            'href.max'    => ':attribute не должна превышать :max символов.',

            'title.string'    => ':attribute должна быть строкой.',
            'title.max'       => ':attribute не должна превышать :max символов.',
            'category.string' => ':attribute должна быть строкой.',
            'category.max'    => ':attribute не должна превышать :max символов.',
            'alt.string'      => ':attribute должна быть строкой.',
            'alt.max'         => ':attribute не должна превышать :max символов.',

            'sort.integer' => ':attribute должен быть целым числом.',
            'sort.min'     => ':attribute не может быть меньше :min.',
            'activity.boolean' => ':attribute должно быть булевым значением.',

            'light.file'   => ':attribute должна быть файлом.',
            'light.image'  => ':attribute должна быть изображением.',
            'light.mimes'  => ':attribute должна быть одного из типов: :values.',
            'light.max'    => ':attribute не должна превышать :max КБ.',

            'dark.file'   => ':attribute должна быть файлом.',
            'dark.image'  => ':attribute должна быть изображением.',
            'dark.mimes'  => ':attribute должна быть одного из типов: :values.',
            'dark.max'    => ':attribute не должна превышать :max КБ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'group_id' => 'группа демо',
            'href'     => 'ссылка демо',
            'title'    => 'заголовок',
            'category' => 'категория',
            'alt'      => 'alt-текст',
            'sort'     => 'порядок',
            'activity' => 'активность',
            'light'    => 'изображение (светлое)',
            'dark'     => 'изображение (тёмное)',
        ];
    }
}
