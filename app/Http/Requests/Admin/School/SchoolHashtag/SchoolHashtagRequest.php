<?php

namespace App\Http\Requests\Admin\School\SchoolHashtag;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolHashtagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $hashtag = $this->route('schoolHashtag')
            ?? $this->route('id');

        $hashtagId = is_object($hashtag)
            ? $hashtag->id
            : ($hashtag ? (int) $hashtag : null);

        return [
            'sort' => ['nullable', 'integer', 'min:0'],
            'activity' => ['nullable', 'boolean'],

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('school_hashtags', 'slug')->ignore($hashtagId),
            ],

            'color' => ['nullable', 'string', 'max:16'],

            'views' => ['nullable', 'integer', 'min:0'],
            'likes' => ['nullable', 'integer', 'min:0'],

            'translations' => ['required', 'array', 'min:1'],

            'translations.*.name' => [
                'required',
                'string',
                'max:255',
            ],

            'translations.*.short' => [
                'nullable',
                'string',
                'max:255',
            ],

            'translations.*.description' => [
                'nullable',
                'string',
            ],

            'translations.*.meta_title' => [
                'nullable',
                'string',
                'max:160',
            ],

            'translations.*.meta_keywords' => [
                'nullable',
                'string',
                'max:255',
            ],

            'translations.*.meta_desc' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'sort.integer' => 'Поле сортировки должно быть числом.',
            'sort.min' => 'Поле сортировки не может быть отрицательным.',

            'activity.boolean' => 'Поле активности должно быть булевым значением.',

            'slug.required' => 'Пожалуйста, укажите slug хештега.',
            'slug.string' => 'Slug должен быть строкой.',
            'slug.max' => 'Slug не должен превышать 255 символов.',
            'slug.unique' => 'Хештег с таким slug уже существует.',

            'color.string' => 'Поле цвета должно быть строкой.',
            'color.max' => 'Поле цвета должно быть максимум 16 символов.',

            'views.integer' => 'Количество просмотров должно быть числом.',
            'views.min' => 'Количество просмотров не может быть отрицательным.',

            'likes.integer' => 'Количество лайков должно быть числом.',
            'likes.min' => 'Количество лайков не может быть отрицательным.',

            'translations.required' => 'Необходимо передать переводы хештега.',
            'translations.array' => 'Переводы должны быть массивом.',
            'translations.min' => 'Необходимо добавить хотя бы один перевод.',

            'translations.*.name.required' => 'Укажите название хештега.',
            'translations.*.name.string' => 'Название хештега должно быть строкой.',
            'translations.*.name.max' => 'Название хештега не должно превышать 255 символов.',

            'translations.*.short.string' => 'Краткое описание должно быть строкой.',
            'translations.*.short.max' => 'Краткое описание не должно превышать 255 символов.',

            'translations.*.description.string' => 'Описание должно быть строкой.',

            'translations.*.meta_title.string' => 'SEO заголовок должен быть строкой.',
            'translations.*.meta_title.max' => 'SEO заголовок не должен превышать 160 символов.',

            'translations.*.meta_keywords.string' => 'SEO ключевые слова должны быть строкой.',
            'translations.*.meta_keywords.max' => 'SEO ключевые слова не должны превышать 255 символов.',

            'translations.*.meta_desc.string' => 'SEO описание должно быть строкой.',
            'translations.*.meta_desc.max' => 'SEO описание не должно превышать 255 символов.',
        ];
    }
}
