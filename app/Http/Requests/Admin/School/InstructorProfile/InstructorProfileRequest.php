<?php

namespace App\Http\Requests\Admin\School\InstructorProfile;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InstructorProfileRequest extends FormRequest
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

        // Приводим slug к формату slug, если пришёл
        if ($this->has('slug') && is_string($this->input('slug'))) {
            $data['slug'] = Str::slug($this->input('slug'));
        }

        // Булево
        if ($this->has('activity')) {
            $data['activity'] = filter_var($this->input('activity'), FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
        }

        // sort → int
        if ($this->has('sort') && is_numeric($this->input('sort'))) {
            $data['sort'] = (int)$this->input('sort');
        }

        // social_links: если строка — попробуем распарсить JSON
        if ($this->has('social_links') && is_string($this->input('social_links'))) {
            $decoded = json_decode($this->input('social_links'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['social_links'] = $decoded;
            }
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
        // Получаем модель или ID из параметра маршрута `instructorProfile`
        $routeParam = $this->route('instructorProfile');

        $instructorProfileId = $routeParam instanceof Model
            ? $routeParam->getKey()
            : $routeParam;

        return [
            'sort'     => 'nullable|integer|min:0',
            'activity' => 'required|boolean',
            'locale'   => ['required','string','size:2'],

            'title'    => [
                'required','string','max:255',
                Rule::unique('instructor_profiles')
                    ->where(fn($q) => $q->where('locale', $this->input('locale')))
                    ->ignore($instructorProfileId),
            ],

            'slug'     => [
                'required','string','max:500',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('instructor_profiles')
                    ->where(fn($q) => $q->where('locale', $this->input('locale')))
                    ->ignore($instructorProfileId),
            ],
            'short'              => 'nullable|string|max:255',
            'bio' => ['nullable', 'string'],
            'views'              => 'nullable|integer|min:0',

            'meta_title'         => 'nullable|string|max:255',
            'meta_keywords'      => 'nullable|string|max:255',
            'meta_desc'          => 'nullable|string',

            'user_id' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                'exists:users,id',
            ],

            // Параметры/метрики
            'rating_avg' => ['nullable', 'numeric', 'between:0,5'],
            'rating_count' => ['nullable', 'integer', 'min:0'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:80'],

            // Соц. ссылки
            'social_links' => ['sometimes', 'nullable', 'array', 'max:50'],
            'social_links.*' => ['nullable', 'string', 'max:255'],

            // популярные ключи — если присутствуют, проверим как URL
            'social_links.github' => ['sometimes', 'nullable', 'url', 'max:255'],
            'social_links.linkedin' => ['sometimes', 'nullable', 'url', 'max:255'],
            'social_links.telegram' => ['sometimes', 'nullable', 'url', 'max:255'],
            'social_links.site' => ['sometimes', 'nullable', 'url', 'max:255'],

            // Изображения
            'images'             => ['nullable','array'],
            'images.*.id'        => [
                'nullable','integer',
                Rule::exists('instructor_profile_images','id'),
                Rule::prohibitedIf(fn() => $this->isMethod('POST')),
            ],
            'images.*.order'     => ['nullable','integer','min:0'],
            'images.*.alt'       => ['nullable','string','max:255'],
            'images.*.caption'   => ['nullable','string','max:255'],
            'images.*.file'      => [
                'nullable',
                'required_without:images.*.id',
                'file',
                'image',
                'mimes:jpeg,jpg,png,gif,svg,webp',
                'max:10240',
            ],

            'deletedImages'      => ['sometimes','array'],
            'deletedImages.*'    => ['integer','exists:instructor_profile_images,id'],
        ];
    }

    /**
     * RU-сообщения об ошибках.
     */
    public function messages(): array
    {
        return [
            // user_id
            'user_id.required' => 'Не указан пользователь, владелец профиля.',
            'user_id.integer' => 'Идентификатор пользователя должен быть целым числом.',
            'user_id.exists' => 'Указанный пользователь не найден.',

            // locale
            'locale.required' => 'Укажите локаль.',
            'locale.string'   => 'Локаль должна быть строкой.',
            'locale.size'     => 'Локаль должна состоять из двух символов.',
            'locale.in'       => 'Недопустимая локаль.',

            // title
            'title.required' => 'Укажите название инструктора.',
            'title.string'   => 'Название инструктора должно быть строкой.',
            'title.max'      => 'Название инструктора не должно превышать :max символов.',
            'title.unique'   => 'Такое название инструктора уже используется в этой локали.',

            // slug
            'slug.required' => 'Поле «Слаг» обязательно для заполнения.',
            'slug.string' => 'Поле «Слаг» должно быть строкой.',
            'slug.max' => 'Поле «Слаг» не может превышать :max символов.',
            'slug.regex' => 'Слаг может содержать только латинские буквы, цифры и дефисы.',
            'slug.unique' => 'Такой слаг уже используется в этой локали.',

            // short
            'short.string' => 'Короткое описание должно быть строкой.',
            'short.max'    => 'Короткое описание не должно превышать :max символов.',

            // description
            'bio.string' => 'Поле «Биография» должно быть текстом.',

            // activity
            'activity.required' => 'Укажите, опубликована ли категория.',
            'activity.boolean'  => 'Поле «Опубликовано» должно быть булевым значением.',

            // sort
            'sort.integer' => 'Поле «Сортировка» должно быть целым числом.',
            'sort.min'     => 'Поле «Сортировка» не может быть отрицательным.',

            // метрики
            'rating_avg.numeric' => '«Средняя оценка» должна быть числом.',
            'rating_avg.between' => '«Средняя оценка» должна быть от :min до :max.',
            'rating_count.integer' => '«Количество оценок» должно быть целым числом.',
            'rating_count.min' => '«Количество оценок» не может быть отрицательным.',
            'hourly_rate.numeric' => '«Почасовая ставка» должна быть числом.',
            'hourly_rate.min' => '«Почасовая ставка» не может быть отрицательной.',
            'experience_years.integer' => '«Опыт (лет)» должен быть целым числом.',
            'experience_years.min' => '«Опыт (лет)» не может быть отрицательным.',
            'experience_years.max' => '«Опыт (лет)» не может превышать :max.',

            // соцссылки
            'social_links.array' => 'Поле «Соц. ссылки» должно быть массивом.',
            'social_links.*.string' => 'Каждая соц. ссылка должна быть строкой.',
            'social_links.*.max' => 'Каждая соц. ссылка не должна превышать :max символов.',
            'social_links.github.url' => 'Ссылка GitHub должна быть корректным URL.',
            'social_links.linkedin.url' => 'Ссылка LinkedIn должна быть корректным URL.',
            'social_links.telegram.url' => 'Ссылка Telegram должна быть корректным URL.',
            'social_links.site.url' => 'Ссылка на сайт должна быть корректным URL.',

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
            'user_id' => 'Пользователь',
            'locale' => 'Локаль',
            'title' => 'Должность',
            'short' => 'Короткое описание',
            'bio' => 'Биография',
            'slug' => 'Слаг',
            'activity' => 'Опубликован',
            'sort' => 'Сортировка',
            'rating_avg' => 'Средняя оценка',
            'rating_count' => 'Количество оценок',
            'hourly_rate' => 'Почасовая ставка',
            'experience_years' => 'Опыт (лет)',
            'social_links' => 'Соц. ссылки',
            'meta_title' => 'Meta Title',
            'meta_keywords' => 'Meta Keywords',
            'meta_desc' => 'Meta Description',
            'images' => 'Изображения',
        ];
    }
}
