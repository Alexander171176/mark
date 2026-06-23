<?php

namespace App\Http\Requests\Admin\System\ImagePreset;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImagePresetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $shape = $this->input('shape', 'rectangle');

        $width = $this->filled('width')
            ? (int) $this->input('width')
            : null;

        $height = $this->filled('height')
            ? (int) $this->input('height')
            : null;

        if (in_array($shape, ['square', 'circle'], true)) {
            $height = $width;
        }

        $this->merge([
            'key' => $this->normalizeKey($this->input('key')),

            'description' => $this->normalizeNullableString(
                $this->input('description')
            ),

            'shape' => $shape,

            'width' => $width,
            'height' => $height,

            'image_rotation_enabled' => filter_var(
                $this->input('image_rotation_enabled', false),
                FILTER_VALIDATE_BOOLEAN
            ),

            'crop_rotation_enabled' => $shape === 'rectangle'
                ? filter_var(
                    $this->input('crop_rotation_enabled', false),
                    FILTER_VALIDATE_BOOLEAN
                )
                : false,

            'max_file_size_kb' => $this->filled('max_file_size_kb')
                ? (int) $this->input('max_file_size_kb')
                : 2048,

            'keep_original' => filter_var(
                $this->input('keep_original', true),
                FILTER_VALIDATE_BOOLEAN
            ),

            'sort' => $this->filled('sort')
                ? (int) $this->input('sort')
                : 0,
        ]);
    }

    public function rules(): array
    {
        $presetId = $this->route('imagePreset')?->id
            ?? $this->route('imagePreset')
            ?? $this->route('id');

        return [
            'key' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('image_presets', 'key')
                    ->ignore($presetId),
            ],

            'description' => [
                'nullable',
                'string',
                'max:500',
            ],

            'shape' => [
                'required',
                'string',
                Rule::in([
                    'rectangle',
                    'square',
                    'circle',
                ]),
            ],

            'width' => [
                'required',
                'integer',
                'min:1',
                'max:10000',
            ],

            'height' => [
                'required',
                'integer',
                'min:1',
                'max:10000',
            ],

            'image_rotation_enabled' => [
                'nullable',
                'boolean',
            ],

            'crop_rotation_enabled' => [
                'nullable',
                'boolean',
            ],

            'max_file_size_kb' => [
                'required',
                'integer',
                'min:128',
                'max:51200',
            ],

            'keep_original' => [
                'nullable',
                'boolean',
            ],

            'sort' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'key.required' => 'Укажите ключ пресета.',
            'key.max' => 'Ключ пресета не должен превышать 100 символов.',
            'key.regex' => 'Ключ может содержать только латинские буквы, цифры и нижнее подчёркивание.',
            'key.unique' => 'Пресет с таким ключом уже существует.',

            'description.max' => 'Описание не должно превышать 500 символов.',

            'shape.required' => 'Укажите форму изображения.',
            'shape.in' => 'Форма изображения должна быть rectangle, square или circle.',

            'width.required' => 'Укажите ширину изображения.',
            'width.integer' => 'Ширина должна быть числом.',
            'width.min' => 'Ширина должна быть больше 0.',
            'width.max' => 'Ширина не должна превышать 10000 пикселей.',

            'height.required' => 'Укажите высоту изображения.',
            'height.integer' => 'Высота должна быть числом.',
            'height.min' => 'Высота должна быть больше 0.',
            'height.max' => 'Высота не должна превышать 10000 пикселей.',

            'image_rotation_enabled.boolean' => 'Поле поворота изображения должно быть логическим значением.',
            'crop_rotation_enabled.boolean' => 'Поле поворота рамки должно быть логическим значением.',

            'max_file_size_kb.required' => 'Укажите максимальный размер файла.',
            'max_file_size_kb.integer' => 'Максимальный размер файла должен быть числом.',
            'max_file_size_kb.min' => 'Максимальный размер файла не должен быть меньше 128 КБ.',
            'max_file_size_kb.max' => 'Максимальный размер файла не должен превышать 51200 КБ.',

            'keep_original.boolean' => 'Поле сохранения оригинала должно быть логическим значением.',

            'sort.integer' => 'Сортировка должна быть числом.',
            'sort.min' => 'Сортировка не может быть меньше 0.',
        ];
    }

    protected function normalizeKey(mixed $value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        $value = trim((string) $value);
        $value = mb_strtolower($value);
        $value = str_replace(['-', ' '], '_', $value);

        return $value === '' ? null : $value;
    }

    protected function normalizeNullableString(mixed $value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
