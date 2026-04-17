<?php

namespace App\Http\Requests\Admin\School\LearningCategory;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LearningCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Нормализуем входные данные до валидации.
     */
    protected function prepareForValidation(): void
    {
        $data = [];

        // locale → 2 буквы, нижний регистр
        if ($this->has('locale') && is_string($this->input('locale'))) {
            $data['locale'] = mb_strtolower(trim($this->input('locale')));
        }

        // Если slug передан — нормализуем. Если пустой и есть name — сгенерируем из name.
        $rawSlug = $this->input('slug');
        if (is_string($rawSlug) && $rawSlug !== '') {
            $data['slug'] = Str::slug($rawSlug);
        } elseif ($this->has('name') && is_string($this->input('name')) && $this->input('name') !== '') {
            $data['slug'] = Str::slug($this->input('name'));
        }

        // Булево activity из разных форматов ('on','1','true' и т.п.)
        if ($this->has('activity')) {
            $data['activity'] = filter_var($this->input('activity'), FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
        }

        // sort → int
        if ($this->has('sort') && is_numeric($this->input('sort'))) {
            $data['sort'] = (int) $this->input('sort');
        }

        // parent_id → int|null
        if ($this->has('parent_id')) {
            $pid = $this->input('parent_id');
            $data['parent_id'] = ($pid === '' || $pid === null) ? null : (int) $pid;
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    /**
     * Правила валидации.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Поддержка разных имён параметра в роутах: learningCategory / learning_category / learning-category
        $routeModel = $this->route('learningCategory')
            ?? $this->route('learning_category')
            ?? $this->route('learning-category');

        $learningCategoryId = is_object($routeModel) ? ($routeModel->id ?? null) : (is_numeric($routeModel) ? (int)$routeModel : null);
        $locale = $this->input('locale');

        // Если у вас есть конфиг со списком локалей — раскомментируйте
        // $allowedLocales = config('app.supported_locales', ['ru','en','kk']);

        return [
            'sort'               => ['nullable', 'integer', 'min:0'],
            'activity'           => ['required', 'boolean'],

            'locale'             => [
                'required', 'string', 'size:2',
                // Rule::in($allowedLocales),
            ],

            'name'               => [
                'required', 'string', 'max:255',
                Rule::unique('learning_categories', 'name')
                    ->where(fn($q) => $q->where('locale', $locale))
                    ->ignore($learningCategoryId),
            ],

            'slug'               => [
                'required', 'string', 'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('learning_categories', 'slug')
                    ->where(fn($q) => $q->where('locale', $locale))
                    ->ignore($learningCategoryId),
            ],

            'short'              => ['nullable', 'string', 'max:255'],
            'description'        => ['nullable', 'string'],

            'meta_title'         => ['nullable', 'string', 'max:255'],
            'meta_keywords'      => ['nullable', 'string', 'max:255'],
            'meta_desc'          => ['nullable', 'string'],

            'parent_id'          => [
                'nullable',
                'integer',
                Rule::notIn([$learningCategoryId]), // запрет на parent_id == id
                Rule::exists('learning_categories', 'id')
                    ->where(fn($q) => $q->where('locale', $locale)),
            ],

            // Изображения
            'images'             => ['nullable', 'array'],
            'images.*.id'        => [
                'nullable', 'integer',
                Rule::exists('learning_category_images', 'id'),
                Rule::prohibitedIf(fn() => $this->isMethod('POST')),
            ],
            'images.*.order'     => ['nullable', 'integer', 'min:0'],
            'images.*.alt'       => ['nullable', 'string', 'max:255'],
            'images.*.caption'   => ['nullable', 'string', 'max:255'],
            'images.*.file'      => [
                'nullable',
                'required_without:images.*.id',
                'file',
                'image',
                'mimes:jpeg,jpg,png,gif,svg,webp',
                'max:10240', // 10 MB
            ],

            'deletedImages'      => ['sometimes', 'array'],
            'deletedImages.*'    => ['integer', 'exists:learning_category_images,id'],
        ];
    }

    /**
     * RU-сообщения об ошибках.
     */
    public function messages(): array
    {
        return [
            // locale
            'locale.required' => 'Укажите локаль.',
            'locale.string'   => 'Локаль должна быть строкой.',
            'locale.size'     => 'Локаль должна состоять из двух символов.',
            'locale.in'       => 'Недопустимая локаль.',

            // name
            'name.required' => 'Укажите название категории.',
            'name.string'   => 'Название категории должно быть строкой.',
            'name.max'      => 'Название категории не должно превышать :max символов.',
            'name.unique'   => 'Такое название уже используется в этой локали.',

            // slug
            'slug.required' => 'Укажите слаг (ЧПУ) категории.',
            'slug.string'   => 'Слаг должен быть строкой.',
            'slug.max'      => 'Слаг не должен превышать :max символов.',
            'slug.regex'    => 'Слаг может содержать только латинские буквы, цифры и дефисы.',
            'slug.unique'   => 'Такой слаг уже используется в этой локали.',

            // short
            'short.string' => 'Короткое описание должно быть строкой.',
            'short.max'    => 'Короткое описание не должно превышать :max символов.',

            // description
            'description.string' => 'Описание должно быть текстом.',

            // activity
            'activity.required' => 'Укажите, опубликована ли категория.',
            'activity.boolean'  => 'Поле «Опубликовано» должно быть булевым значением.',

            // sort
            'sort.integer' => 'Поле «Сортировка» должно быть целым числом.',
            'sort.min'     => 'Поле «Сортировка» не может быть отрицательным.',

            // parent_id
            'parent_id.integer' => 'Идентификатор родителя должен быть целым числом.',
            'parent_id.exists'  => 'Указанная родительская категория не найдена в этой локали.',
            'parent_id.not_in'  => 'Категория не может быть родителем самой себя.',

            // SEO
            'meta_title.string'    => 'Поле «Meta Title» должно быть строкой.',
            'meta_title.max'       => 'Поле «Meta Title» не должно превышать :max символов.',
            'meta_keywords.string' => 'Поле «Meta Keywords» должно быть строкой.',
            'meta_keywords.max'    => 'Поле «Meta Keywords» не должно превышать :max символов.',
            'meta_desc.string'     => 'Поле «Meta Description» должно быть текстом.',

            // images (массив)
            'images.array' => 'Неверный формат поля «Изображения».',

            // images.*.id
            'images.*.id.integer'     => 'ID изображения должен быть целым числом.',
            'images.*.id.exists'      => 'Указанное изображение не найдено.',
            'images.*.id.prohibited'  => 'ID изображения нельзя передавать при создании — только при редактировании.',

            // images.*.order
            'images.*.order.integer' => 'Порядок изображения должен быть целым числом.',
            'images.*.order.min'     => 'Порядок изображения не может быть отрицательным.',

            // images.*.alt / images.*.caption
            'images.*.alt.string'    => 'Alt-текст изображения должен быть строкой.',
            'images.*.alt.max'       => 'Alt-текст изображения не должен превышать :max символов.',
            'images.*.caption.string'=> 'Подпись изображения должна быть строкой.',
            'images.*.caption.max'   => 'Подпись изображения не должна превышать :max символов.',

            // images.*.file
            'images.*.file.required_without' => 'Загрузите файл изображения или укажите существующий ID.',
            'images.*.file.file'    => 'Неверный файл изображения.',
            'images.*.file.image'   => 'Файл должен быть изображением.',
            'images.*.file.mimes'   => 'Разрешённые форматы: jpeg, jpg, png, gif, svg, webp.',
            'images.*.file.max'     => 'Максимальный размер изображения — 10 МБ.',

            // deletedImages
            'deletedImages.array'    => 'Неверный формат списка удаляемых изображений.',
            'deletedImages.*.integer'=> 'ID удаляемого изображения должен быть целым числом.',
            'deletedImages.*.exists' => 'Некоторых изображений для удаления не существует.',
        ];
    }

    /**
     * Человекочитаемые имена полей.
     */
    public function attributes(): array
    {
        return [
            'locale'      => 'Локаль',
            'name'        => 'Название',
            'slug'        => 'Слаг',
            'short'       => 'Короткое описание',
            'description' => 'Описание',
            'activity'    => 'Опубликовано',
            'sort'        => 'Сортировка',
            'parent_id'   => 'Родительская категория',
        ];
    }
}
