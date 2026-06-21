<?php

namespace App\Http\Requests\Admin\System\ImageProcessor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImageProcessorVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'image_processor_profile_id' => $this->filled('image_processor_profile_id')
                ? (int) $this->input('image_processor_profile_id')
                : null,

            'key' => $this->normalizeKey($this->input('key')),
            'name' => $this->normalizeNullableString($this->input('name')),
            'description' => $this->normalizeNullableString($this->input('description')),

            'activity' => filter_var(
                $this->input('activity', false),
                FILTER_VALIDATE_BOOLEAN
            ),

            'width' => $this->filled('width')
                ? (int) $this->input('width')
                : null,

            'height' => $this->filled('height')
                ? (int) $this->input('height')
                : null,

            'allow_rotate' => filter_var(
                $this->input('allow_rotate', false),
                FILTER_VALIDATE_BOOLEAN
            ),

            'quality' => $this->filled('quality')
                ? (int) $this->input('quality')
                : 85,

            'format' => $this->normalizeKey($this->input('format')) ?: 'webp',
            'fit' => $this->normalizeKey($this->input('fit')) ?: 'crop',
            'shape' => $this->normalizeKey($this->input('shape')) ?: 'rectangle',

            'background_light' => $this->normalizeNullableString(
                $this->input('background_light')
            ),

            'background_dark' => $this->normalizeNullableString(
                $this->input('background_dark')
            ),

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
        $variantId = $this->route('imageProcessorVariant')?->id
            ?? $this->route('imageProcessorVariant')
            ?? $this->route('id');

        return [
            'image_processor_profile_id' => [
                'required',
                'integer',
                'exists:image_processor_profiles,id',
            ],

            'key' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('image_processor_variants', 'key')
                    ->where(
                        'image_processor_profile_id',
                        $this->input('image_processor_profile_id')
                    )
                    ->ignore($variantId),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:500',
            ],

            'activity' => [
                'nullable',
                'boolean',
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

            'allow_rotate' => [
                'nullable',
                'boolean',
            ],

            'quality' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],

            'format' => [
                'required',
                'string',
                'max:20',
            ],

            'fit' => [
                'required',
                'string',
                'max:50',
            ],

            'shape' => [
                'required',
                'string',
                'max:50',
            ],

            'background_light' => [
                'nullable',
                'string',
                'max:50',
            ],

            'background_dark' => [
                'nullable',
                'string',
                'max:50',
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
            'image_processor_profile_id.required' => 'Укажите профиль обработки.',
            'image_processor_profile_id.exists' => 'Выбранный профиль обработки не найден.',

            'key.required' => 'Укажите ключ варианта.',
            'key.max' => 'Ключ варианта не должен превышать 100 символов.',
            'key.regex' => 'Ключ варианта может содержать только латинские буквы, цифры и нижнее подчёркивание.',
            'key.unique' => 'В этом профиле уже есть вариант с таким ключом.',

            'name.required' => 'Укажите название варианта.',
            'name.max' => 'Название варианта не должно превышать 255 символов.',

            'description.max' => 'Описание варианта не должно превышать 500 символов.',

            'activity.boolean' => 'Поле активности должно быть логическим значением.',

            'width.required' => 'Укажите ширину изображения.',
            'width.integer' => 'Ширина должна быть числом.',
            'width.min' => 'Ширина должна быть больше 0.',
            'width.max' => 'Ширина не должна превышать 10000 пикселей.',

            'height.required' => 'Укажите высоту изображения.',
            'height.integer' => 'Высота должна быть числом.',
            'height.min' => 'Высота должна быть больше 0.',
            'height.max' => 'Высота не должна превышать 10000 пикселей.',

            'allow_rotate.boolean' => 'Поле разворота должно быть логическим значением.',

            'quality.required' => 'Укажите качество сжатия.',
            'quality.integer' => 'Качество должно быть числом.',
            'quality.min' => 'Качество не может быть меньше 1.',
            'quality.max' => 'Качество не может быть больше 100.',

            'format.required' => 'Укажите формат изображения.',
            'format.max' => 'Формат не должен превышать 20 символов.',

            'fit.required' => 'Укажите тип обработки изображения.',
            'fit.max' => 'Тип обработки не должен превышать 50 символов.',

            'shape.required' => 'Укажите форму изображения.',
            'shape.max' => 'Форма изображения не должна превышать 50 символов.',

            'background_light.max' => 'Цвет фона светлой темы не должен превышать 50 символов.',
            'background_dark.max' => 'Цвет фона тёмной темы не должен превышать 50 символов.',

            'keep_original.boolean' => 'Поле сохранения оригинала должно быть логическим значением.',

            'sort.integer' => 'Поле сортировки должно быть числом.',
            'sort.min' => 'Поле сортировки не может быть меньше 0.',
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
