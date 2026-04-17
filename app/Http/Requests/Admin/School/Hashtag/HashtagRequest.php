<?php

namespace App\Http\Requests\Admin\School\Hashtag;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HashtagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('hashtag')?->id;

        return [
            'name'  => ['required', 'string', 'max:255'],

            'slug'  => [
                'required',
                'string',
                'max:255',
                Rule::unique('hashtags')
                    ->ignore($id)
                    ->where(fn ($q) => $q->where('locale', $this->input('locale')))
            ],

            'locale' => ['nullable', 'string', 'max:2'],

            'activity' => ['boolean'],
            'sort'     => ['integer', 'min:0'],

            'color' => ['nullable', 'string', 'max:16'],
            'short' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'views' => ['integer', 'min:0'],
            'likes' => ['integer', 'min:0'],

            'meta_title'    => ['nullable', 'string', 'max:160'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_desc'     => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [

            /* ========== Основные поля ========== */

            'name.required' => 'Пожалуйста, укажите название хештега.',
            'name.string'   => 'Название должно быть строкой.',
            'name.max'      => 'Название не должно превышать 255 символов.',

            'slug.required' => 'Пожалуйста, укажите slug.',
            'slug.string'   => 'Slug должен быть строкой.',
            'slug.max'      => 'Slug не должен превышать 255 символов.',
            'slug.unique'   => 'Хештег с таким slug уже существует в этой локали.',

            'locale.string' => 'Локаль должна быть строкой.',
            'locale.max'    => 'Локаль должна быть длиной максимум 2 символа.',

            /* ========== Активность / порядок ========== */

            'activity.boolean' => 'Поле активности должно быть булевым значением.',
            'sort.integer'     => 'Поле сортировки должно быть числом.',
            'sort.min'         => 'Поле сортировки не может быть отрицательным.',

            /* ========== Внешний вид ========== */

            'color.string' => 'Поле цвета должно быть строкой.',
            'color.max'    => 'Поле цвета должно быть максимум 16 символов.',

            'short.string' => 'Краткое описание должно быть строкой.',
            'short.max'    => 'Краткое описание не должно превышать 255 символов.',

            'description.string' => 'Описание должно быть строкой.',

            /* ========== Статистика ========== */

            'views.integer' => 'Количество просмотров должно быть числом.',
            'views.min'     => 'Количество просмотров не может быть отрицательным.',

            'likes.integer' => 'Количество лайков должно быть числом.',
            'likes.min'     => 'Количество лайков не может быть отрицательным.',

            /* ========== SEO ========== */

            'meta_title.string' => 'SEO заголовок должен быть строкой.',
            'meta_title.max'    => 'SEO заголовок не должен превышать 160 символов.',

            'meta_keywords.string' => 'SEO ключевые слова должны быть строкой.',
            'meta_keywords.max'    => 'SEO ключевые слова не должны превышать 255 символов.',

            'meta_desc.string' => 'SEO описание должно быть строкой.',
            'meta_desc.max'    => 'SEO описание не должно превышать 255 символов.',
        ];
    }
}
