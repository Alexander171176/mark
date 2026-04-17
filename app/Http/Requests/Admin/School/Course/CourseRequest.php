<?php

namespace App\Http\Requests\Admin\School\Course;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Подключи Policy при необходимости
        return true;
    }

    /**
     * Нормализуем входные данные до валидации.
     */
    protected function prepareForValidation(): void
    {
        $data = [];

        // locale → 2 буквы, нижний регистр (ru/en/kk)
        if ($this->has('locale') && is_string($this->input('locale'))) {
            $data['locale'] = mb_strtolower(trim($this->input('locale')));
        }

        // Приводим slug к формату slug, если пришёл
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

        // Булевые флаги: is_new, is_hit, is_sale, left, main, right
        foreach (['is_new', 'is_hit', 'is_sale', 'left', 'main', 'right'] as $flag) {
            if ($this->has($flag)) {
                $data[$flag] = filter_var(
                    $this->input($flag),
                    FILTER_VALIDATE_BOOL,
                    FILTER_NULL_ON_FAILURE
                );
            }
        }

        // sort → int
        if ($this->has('sort') && is_numeric($this->input('sort'))) {
            $data['sort'] = (int) $this->input('sort');
        }

        // difficulty → int
        if ($this->has('difficulty') && $this->input('difficulty') !== null) {
            $data['difficulty'] = is_numeric($this->input('difficulty'))
                ? (float) $this->input('difficulty')
                : null;
        }

        // duration → int
        if ($this->has('duration') && $this->input('duration') !== null) {
            $data['duration'] = (int) $this->input('duration');
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
        // Получаем модель или ID из параметра маршрута `course`
        $routeParam = $this->route('course');

        $courseId = $routeParam instanceof Model
            ? $routeParam->getKey()
            : $routeParam;

        $availabilityOptions = ['public', 'unlisted', 'private'];
        $statusOptions       = ['draft', 'published', 'archived'];

        return [
            'instructor_profile_id' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                'exists:instructor_profiles,id',
            ],

            // Локаль (3 языка: ru, en, kk) — 2 символа
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
                Rule::unique('courses', 'slug')->ignore($courseId),
            ],

            'subtitle'    => ['nullable', 'string', 'max:255'],
            'short'       => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'level' => ['nullable', 'string', 'max:32'],

            'difficulty' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'duration'   => ['nullable', 'integer', 'min:0'],

            // Публикация/видимость
            'availability' => ['sometimes', 'string', Rule::in($availabilityOptions)],
            'status'       => ['sometimes', 'string', Rule::in($statusOptions)],
            'published_at' => ['nullable', 'date'],

            // Булевые флаги витрины
            'is_new'  => ['sometimes', 'boolean'],
            'is_hit'  => ['sometimes', 'boolean'],
            'is_sale' => ['sometimes', 'boolean'],
            'left'    => ['sometimes', 'boolean'],
            'main'    => ['sometimes', 'boolean'],
            'right'   => ['sometimes', 'boolean'],

            // Управление списком
            'activity' => ['required', 'boolean'],
            'sort'     => ['nullable', 'integer', 'min:0'],

            // SEO
            'meta_title'    => ['nullable', 'string', 'max:160'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_desc'     => ['nullable', 'string', 'max:255'],

            // Метрики (обычно считаются системой, но оставляем для админки)
            'rating_avg'     => ['nullable', 'numeric', 'min:0', 'max:5'],
            'rating_count'   => ['nullable', 'integer', 'min:0'],
            'students_count' => ['nullable', 'integer', 'min:0'],
            'popularity'     => ['nullable', 'integer', 'min:0'],
            'views'          => ['nullable', 'integer', 'min:0'],
            'likes'          => ['nullable', 'integer', 'min:0'],

            // Категории обучения (оставляем)
            'learning_category_ids'   => ['nullable', 'array'],
            'learning_category_ids.*' => ['integer', 'exists:learning_categories,id'],

            // ✅ НОВОЕ: полиморфные хештеги
            'hashtag_ids'   => ['nullable', 'array'],
            'hashtag_ids.*' => ['integer', 'exists:hashtags,id'],

            // Рекомендованные курсы (M:M через course_related)
            'related_course_ids'   => ['nullable', 'array'],
            'related_course_ids.*' => [
                'integer',
                Rule::notIn([$courseId]),
                'exists:courses,id',
            ],

            // Изображения курса (по аналогии с InstructorProfile)
            'images'           => ['nullable', 'array'],
            'images.*.id'      => [
                'nullable',
                'integer',
                Rule::exists('course_images', 'id'),
                // При создании ID запрещён — только файл
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
                'max:10240', // 10 МБ
            ],

            'deletedImages'   => ['sometimes', 'array'],
            'deletedImages.*' => ['integer', 'exists:course_images,id'],
        ];
    }

    /**
     * RU-сообщения об ошибках.
     */
    public function messages(): array
    {
        return [
            // instructor_profile_id
            'instructor_profile_id.required' => 'Укажите владельца/преподавателя курса.',
            'instructor_profile_id.integer'  => 'Идентификатор преподавателя должен быть числом.',
            'instructor_profile_id.exists'   => 'Указанный преподаватель не найден.',

            // locale
            'locale.required' => 'Укажите локаль курса.',
            'locale.string'   => 'Локаль должна быть строкой.',
            'locale.size'     => 'Локаль должна состоять из двух символов.',
            'locale.in'       => 'Недопустимая локаль. Допустимые значения: ru, en, kk.',

            // title
            'title.required' => 'Введите заголовок курса.',
            'title.string'   => 'Заголовок должен быть строкой.',
            'title.max'      => 'Заголовок не должен превышать :max символов.',

            // slug
            'slug.required' => 'Укажите слаг (ЧПУ) курса.',
            'slug.string'   => 'Слаг должен быть строкой.',
            'slug.max'      => 'Слаг не должен превышать :max символов.',
            'slug.regex'    => 'Слаг может содержать только латинские буквы, цифры и дефисы.',
            'slug.unique'   => 'Такой слаг уже используется другим курсом.',

            // subtitle
            'subtitle.string' => 'Подзаголовок должен быть строкой.',
            'subtitle.max'    => 'Подзаголовок не должен превышать :max символов.',

            // short
            'short.string' => 'Краткое описание должно быть строкой.',
            'short.max'    => 'Краткое описание не должно превышать :max символов.',

            // description
            'description.string' => 'Описание должно быть текстом.',

            // level
            'level.string' => 'Уровень должен быть строкой.',
            'level.max'    => 'Уровень не должен превышать :max символов.',

            // difficulty
            'difficulty.integer' => 'Сложность должна быть целым числом.',
            'difficulty.min'     => 'Сложность не может быть меньше :min.',
            'difficulty.max'     => 'Сложность не может быть больше :max.',

            // duration
            'duration.integer' => 'Длительность должна быть целым числом (в минутах).',
            'duration.min'     => 'Длительность не может быть отрицательной.',

            // availability / status
            'availability.in' => 'Недопустимое значение поля доступности.',
            'status.in'       => 'Недопустимое значение статуса.',

            // published_at
            'published_at.date' => 'Дата публикации имеет неверный формат.',

            // Булевые флаги
            'is_new.boolean'  => 'Поле «Новинка» должно быть булевым значением.',
            'is_hit.boolean'  => 'Поле «Хит» должно быть булевым значением.',
            'is_sale.boolean' => 'Поле «Распродажа» должно быть булевым значением.',
            'left.boolean'    => 'Поле «В левой колонке» должно быть булевым значением.',
            'main.boolean'    => 'Поле «В главном блоке» должно быть булевым значением.',
            'right.boolean'   => 'Поле «В правой колонке» должно быть булевым значением.',

            // activity / sort
            'activity.required' => 'Укажите, активен ли курс.',
            'activity.boolean'  => 'Поле «Активность» должно быть булевым значением.',
            'sort.integer'      => 'Поле «Сортировка» должно быть целым числом.',
            'sort.min'          => 'Поле «Сортировка» не может быть отрицательным.',

            // SEO
            'meta_title.string'    => 'Поле «Meta Title» должно быть строкой.',
            'meta_title.max'       => 'Поле «Meta Title» не должно превышать :max символов.',
            'meta_keywords.string' => 'Поле «Meta Keywords» должно быть строкой.',
            'meta_keywords.max'    => 'Поле «Meta Keywords» не должно превышать :max символов.',
            'meta_desc.string'     => 'Поле «Meta Description» должно быть строкой.',
            'meta_desc.max'        => 'Поле «Meta Description» не должно превышать :max символов.',

            // Категории обучения
            'learning_category_ids.array'        => 'Список категорий обучения должен быть массивом.',
            'learning_category_ids.*.integer'    => 'ID категории обучения должен быть целым числом.',
            'learning_category_ids.*.exists'     => 'Некоторых категорий обучения не существует.',

            // ✅ Хештеги
            'hashtag_ids.array'        => 'Список хештегов должен быть массивом.',
            'hashtag_ids.*.integer'    => 'ID хештега должен быть целым числом.',
            'hashtag_ids.*.exists'     => 'Некоторых хештегов не существует.',

            // Рекомендованные курсы
            'related_course_ids.array'        => 'Список рекомендованных курсов должен быть массивом.',
            'related_course_ids.*.integer'    => 'ID рекомендованного курса должен быть целым числом.',
            'related_course_ids.*.exists'     => 'Некоторых рекомендованных курсов не существует.',

            // images (массив)
            'images.array' => 'Неверный формат поля «Изображения».',

            // images.*.id
            'images.*.id.integer'    => 'ID изображения должен быть целым числом.',
            'images.*.id.exists'     => 'Указанное изображение не найдено.',
            'images.*.id.prohibited' => 'ID изображения нельзя передавать при создании — только при редактировании.',

            // images.*.order
            'images.*.order.integer' => 'Порядок изображения должен быть целым числом.',
            'images.*.order.min'     => 'Порядок изображения не может быть отрицательным.',

            // images.*.alt / caption
            'images.*.alt.string'     => 'Alt-текст изображения должен быть строкой.',
            'images.*.alt.max'        => 'Alt-текст изображения не должен превышать :max символов.',
            'images.*.caption.string' => 'Подпись изображения должна быть строкой.',
            'images.*.caption.max'    => 'Подпись изображения не должна превышать :max символов.',

            // images.*.file
            'images.*.file.required_without' => 'Загрузите файл изображения или укажите существующий ID.',
            'images.*.file.file'    => 'Неверный файл изображения.',
            'images.*.file.image'   => 'Файл должен быть изображением.',
            'images.*.file.mimes'   => 'Разрешённые форматы: jpeg, jpg, png, gif, svg, webp.',
            'images.*.file.max'     => 'Максимальный размер изображения — 10 МБ.',

            // deletedImages
            'deletedImages.array'     => 'Неверный формат списка удаляемых изображений.',
            'deletedImages.*.integer' => 'ID удаляемого изображения должен быть целым числом.',
            'deletedImages.*.exists'  => 'Некоторых изображений для удаления не существует.',
        ];
    }

    /**
     * Человекочитаемые имена полей.
     */
    public function attributes(): array
    {
        return [
            'instructor_profile_id' => 'Преподаватель',
            'locale'                => 'Локаль',
            'title'                 => 'Заголовок курса',
            'slug'                  => 'Слаг',
            'subtitle'              => 'Подзаголовок',
            'short'                 => 'Краткое описание',
            'description'           => 'Описание',
            'level'                 => 'Уровень',
            'difficulty'            => 'Сложность',
            'duration'              => 'Длительность',
            'availability'          => 'Доступность',
            'status'                => 'Статус',
            'published_at'          => 'Дата публикации',

            'is_new'  => 'Новинка',
            'is_hit'  => 'Хит',
            'is_sale' => 'Распродажа',
            'left'    => 'Отображать в левой колонке',
            'main'    => 'Отображать в главном блоке',
            'right'   => 'Отображать в правой колонке',

            'activity'   => 'Активность',
            'sort'       => 'Сортировка',

            'meta_title'    => 'Meta Title',
            'meta_keywords' => 'Meta Keywords',
            'meta_desc'     => 'Meta Description',

            'rating_avg'     => 'Средняя оценка',
            'rating_count'   => 'Количество оценок',
            'students_count' => 'Количество студентов',
            'popularity'     => 'Популярность',
            'views'          => 'Просмотры',
            'likes'          => 'Лайки',

            'learning_category_ids' => 'Категории обучения',
            'hashtag_ids'           => 'Хештеги',
            'related_course_ids'    => 'Рекомендованные курсы',

            'images' => 'Изображения курса',
        ];
    }
}
