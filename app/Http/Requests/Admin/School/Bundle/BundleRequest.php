<?php

namespace App\Http\Requests\Admin\School\Bundle;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BundleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Нормализуем входные данные до валидации (как в CourseRequest).
     */
    protected function prepareForValidation(): void
    {
        $data = [];

        // locale → 2 буквы, lower
        if ($this->has('locale') && is_string($this->input('locale'))) {
            $data['locale'] = mb_strtolower(trim($this->input('locale')));
        }

        // slug → slugify
        if ($this->has('slug') && is_string($this->input('slug'))) {
            $data['slug'] = Str::slug($this->input('slug'));
        }

        // activity → bool
        if ($this->has('activity')) {
            $data['activity'] = filter_var(
                $this->input('activity'),
                FILTER_VALIDATE_BOOL,
                FILTER_NULL_ON_FAILURE
            );
        }

        // sort → int
        if ($this->has('sort') && is_numeric($this->input('sort'))) {
            $data['sort'] = (int) $this->input('sort');
        }

        // views/likes → int (обычно системные, но раз есть в fillable — валидируем)
        foreach (['views', 'likes'] as $metric) {
            if ($this->has($metric) && $this->input($metric) !== null && is_numeric($this->input($metric))) {
                $data[$metric] = (int) $this->input($metric);
            }
        }

        // meta (если пришёл строкой JSON) → array
        if ($this->filled('meta') && is_string($this->input('meta'))) {
            $decoded = json_decode($this->input('meta'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['meta'] = $decoded;
            }
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        // route('bundle') может быть Model или ID
        $routeParam = $this->route('bundle');

        $bundleId = $routeParam instanceof Model
            ? $routeParam->getKey()
            : $routeParam;

        return [
            // Локаль
            'locale' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'size:2',
                Rule::in(['ru', 'en', 'kk']),
            ],

            'title' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:255',
            ],

            'slug' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('bundles', 'slug')->ignore($bundleId),
            ],

            'subtitle'    => ['nullable', 'string', 'max:255'],
            'short'       => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            // Публикация/управление
            'activity'     => ['required', 'boolean'],
            'sort'         => ['nullable', 'integer', 'min:0'],
            'published_at' => ['nullable', 'date'],

            // SEO
            'meta_title'    => ['nullable', 'string', 'max:160'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_desc'     => ['nullable', 'string', 'max:255'],

            // Метрики (если реально даёшь править из админки)
            'views' => ['nullable', 'integer', 'min:0'],
            'likes' => ['nullable', 'integer', 'min:0'],

            // meta json
            'meta' => ['nullable', 'array'],

            // Курсы в наборе (sync)
            'course_ids'   => ['nullable', 'array'],
            'course_ids.*' => ['integer', 'exists:courses,id'],

            // Изображения бандла (аналогично CourseRequest)
            'images'           => ['nullable', 'array'],
            'images.*.id'      => [
                'nullable',
                'integer',
                Rule::exists('bundle_images', 'id'),
                Rule::prohibitedIf(fn () => $this->isMethod('post')),
            ],
            'images.*.order'   => ['nullable', 'integer', 'min:0'],
            'images.*.alt'     => ['nullable', 'string', 'max:255'],
            'images.*.caption' => ['nullable', 'string', 'max:255'],
            'images.*.file'    => [
                'nullable',
                'required_without:images.*.id',
                'file',
                'image',
                'mimes:jpeg,jpg,png,gif,svg,webp',
                'max:10240', // 10MB
            ],

            'deletedImages'   => ['sometimes', 'array'],
            'deletedImages.*' => ['integer', 'exists:bundle_images,id'],
        ];
    }

    public function messages(): array
    {
        return [
            // locale
            'locale.required' => 'Укажите локаль набора.',
            'locale.string'   => 'Локаль должна быть строкой.',
            'locale.size'     => 'Локаль должна состоять из двух символов.',
            'locale.in'       => 'Недопустимая локаль. Допустимые значения: ru, en, kk.',

            // title
            'title.required' => 'Укажите название набора.',
            'title.string'   => 'Название должно быть строкой.',
            'title.max'      => 'Название не должно превышать :max символов.',

            // slug
            'slug.required' => 'Укажите слаг (ЧПУ) набора.',
            'slug.string'   => 'Слаг должен быть строкой.',
            'slug.max'      => 'Слаг не должен превышать :max символов.',
            'slug.regex'    => 'Слаг может содержать только латинские буквы, цифры и дефисы.',
            'slug.unique'   => 'Такой слаг уже используется другим набором.',

            // subtitle/short/description
            'subtitle.string' => 'Подзаголовок должен быть строкой.',
            'subtitle.max'    => 'Подзаголовок не должен превышать :max символов.',
            'short.string'    => 'Краткое описание должно быть строкой.',
            'short.max'       => 'Краткое описание не должно превышать :max символов.',
            'description.string' => 'Описание должно быть текстом.',

            // activity/sort/published_at
            'activity.required'  => 'Укажите, активен ли набор.',
            'activity.boolean'   => 'Поле публикации должно быть булевым значением.',
            'sort.integer'       => 'Позиция должна быть целым числом.',
            'sort.min'           => 'Позиция не может быть отрицательной.',
            'published_at.date'  => 'Поле даты публикации должно быть корректной датой.',

            // SEO
            'meta_title.string'    => 'Поле «Meta Title» должно быть строкой.',
            'meta_title.max'       => 'Поле «Meta Title» не должно превышать :max символов.',
            'meta_keywords.string' => 'Поле «Meta Keywords» должно быть строкой.',
            'meta_keywords.max'    => 'Поле «Meta Keywords» не должно превышать :max символов.',
            'meta_desc.string'     => 'Поле «Meta Description» должно быть строкой.',
            'meta_desc.max'        => 'Поле «Meta Description» не должно превышать :max символов.',

            // metrics
            'views.integer' => 'Просмотры должны быть целым числом.',
            'views.min'     => 'Просмотры не могут быть отрицательными.',
            'likes.integer' => 'Лайки должны быть целым числом.',
            'likes.min'     => 'Лайки не могут быть отрицательными.',

            // meta
            'meta.array' => 'Meta должен быть объектом (JSON).',

            // course_ids
            'course_ids.array'     => 'Список курсов должен быть массивом.',
            'course_ids.*.integer' => 'Идентификатор курса должен быть числом.',
            'course_ids.*.exists'  => 'Один из указанных курсов не найден.',

            // images
            'images.array' => 'Неверный формат поля «Изображения».',

            'images.*.id.integer'    => 'ID изображения должен быть целым числом.',
            'images.*.id.exists'     => 'Указанное изображение не найдено.',
            'images.*.id.prohibited' => 'ID изображения нельзя передавать при создании — только при редактировании.',

            'images.*.order.integer' => 'Порядок изображения должен быть целым числом.',
            'images.*.order.min'     => 'Порядок изображения не может быть отрицательным.',

            'images.*.alt.string'     => 'Alt-текст изображения должен быть строкой.',
            'images.*.alt.max'        => 'Alt-текст изображения не должен превышать :max символов.',
            'images.*.caption.string' => 'Подпись изображения должна быть строкой.',
            'images.*.caption.max'    => 'Подпись изображения не должна превышать :max символов.',

            'images.*.file.required_without' => 'Загрузите файл изображения или укажите существующий ID.',
            'images.*.file.file'  => 'Неверный файл изображения.',
            'images.*.file.image' => 'Файл должен быть изображением.',
            'images.*.file.mimes' => 'Разрешённые форматы: jpeg, jpg, png, gif, svg, webp.',
            'images.*.file.max'   => 'Максимальный размер изображения — 10 МБ.',

            // deletedImages
            'deletedImages.array'     => 'Неверный формат списка удаляемых изображений.',
            'deletedImages.*.integer' => 'ID удаляемого изображения должен быть целым числом.',
            'deletedImages.*.exists'  => 'Некоторых изображений для удаления не существует.',
        ];
    }

    public function attributes(): array
    {
        return [
            'locale'       => 'Локаль',
            'title'        => 'Название',
            'slug'         => 'Слаг',
            'subtitle'     => 'Подзаголовок',
            'short'        => 'Краткое описание',
            'description'  => 'Описание',

            'activity'     => 'Публикация',
            'sort'         => 'Сортировка',
            'published_at' => 'Дата публикации',

            'meta_title'    => 'Meta Title',
            'meta_keywords' => 'Meta Keywords',
            'meta_desc'     => 'Meta Description',

            'views' => 'Просмотры',
            'likes' => 'Лайки',

            'meta' => 'Meta-данные',

            'course_ids'     => 'Курсы',
            'images'         => 'Изображения набора',
            'deletedImages'  => 'Удаляемые изображения',
        ];
    }
}
