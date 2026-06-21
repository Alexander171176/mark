<?php

namespace App\Http\Requests\Admin\System\ImageProcessor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImageProcessorProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'key' => $this->normalizeKey($this->input('key')),
            'name' => $this->normalizeNullableString($this->input('name')),
            'description' => $this->normalizeNullableString($this->input('description')),

            'activity' => filter_var(
                $this->input('activity', false),
                FILTER_VALIDATE_BOOLEAN
            ),

            'sort' => $this->filled('sort')
                ? (int) $this->input('sort')
                : 0,
        ]);
    }

    public function rules(): array
    {
        $profileId = $this->route('imageProcessorProfile')?->id
            ?? $this->route('imageProcessorProfile')
            ?? $this->route('id');

        return [
            'key' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('image_processor_profiles', 'key')
                    ->ignore($profileId),
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
            'key.required' => 'Укажите ключ профиля.',
            'key.max' => 'Ключ профиля не должен превышать 100 символов.',
            'key.regex' => 'Ключ профиля может содержать только латинские буквы, цифры и нижнее подчёркивание.',
            'key.unique' => 'Профиль с таким ключом уже существует.',

            'name.required' => 'Укажите название профиля.',
            'name.max' => 'Название профиля не должно превышать 255 символов.',

            'description.max' => 'Описание профиля не должно превышать 500 символов.',

            'activity.boolean' => 'Поле активности должно быть логическим значением.',

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
