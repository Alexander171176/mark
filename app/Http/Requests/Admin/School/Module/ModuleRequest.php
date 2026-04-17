<?php

namespace App\Http\Requests\Admin\School\Module;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Подключишь Policy при необходимости
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

        // sort → int
        if ($this->has('sort') && is_numeric($this->input('sort'))) {
            $data['sort'] = (int) $this->input('sort');
        }

        // difficulty → int
        if ($this->has('difficulty') && $this->input('difficulty') !== null) {
            $data['difficulty'] = (int) $this->input('difficulty');
        }

        // duration → int
        if ($this->has('duration') && $this->input('duration') !== null) {
            $data['duration'] = (int) $this->input('duration');
        }

        // lessons_count → int (если вдруг прилетит из админки)
        if ($this->has('lessons_count') && $this->input('lessons_count') !== null) {
            $data['lessons_count'] = (int) $this->input('lessons_count');
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
        // Модуль из роута (Model или ID)
        $routeParam = $this->route('module');

        /** @var int|string|null $moduleId */
        $moduleId = $routeParam instanceof Model
            ? $routeParam->getKey()
            : $routeParam;

        // Для уникальности slug в пределах course_id нужно знать course_id
        $courseIdForUnique = $this->input(
            'course_id',
            $routeParam instanceof Model ? $routeParam->course_id ?? null : null
        );

        $availabilityOptions = ['public', 'unlisted', 'private'];
        $statusOptions       = ['draft', 'published', 'archived'];

        return [
            'course_id' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                'exists:courses,id',
            ],

            // Локаль (ru/en/kk)
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
                // уникальность slug в рамках одного course_id
                Rule::unique('modules', 'slug')
                    ->where(fn ($q) => $q->where('course_id', $courseIdForUnique))
                    ->ignore($moduleId),
            ],

            'subtitle'    => ['nullable', 'string', 'max:255'],
            'short'       => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            // SEO
            'meta_title'    => ['nullable', 'string', 'max:160'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_desc'     => ['nullable', 'string', 'max:255'],

            // Метаданные содержания
            'difficulty'       => ['nullable', 'integer', 'min:0', 'max:5'],
            'duration'         => ['nullable', 'integer', 'min:0'],

            // Публикация/видимость
            'availability' => ['sometimes', 'string', Rule::in($availabilityOptions)],
            'status'       => ['sometimes', 'string', Rule::in($statusOptions)],
            'published_at' => ['nullable', 'date'],

            // Управление списком
            'activity' => ['required', 'boolean'],
            'sort'     => ['nullable', 'integer', 'min:0'],

            // Метрики (обычно считаются системой)
            'lessons_count' => ['nullable', 'integer', 'min:0'],
            'popularity'    => ['nullable', 'integer', 'min:0'],
            'rating_count'  => ['nullable', 'integer', 'min:0'],
            'rating_avg'    => ['nullable', 'numeric', 'min:0', 'max:5'],
            'views'         => ['nullable', 'integer', 'min:0'],
            'likes'         => ['nullable', 'integer', 'min:0'],

            // Изображения модуля (по аналогии с CourseRequest)
            'images'           => ['nullable', 'array'],
            'images.*.id'      => [
                'nullable',
                'integer',
                Rule::exists('module_images', 'id'),
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
            'deletedImages.*' => ['integer', 'exists:module_images,id'],
        ];
    }

    /**
     * RU-сообщения об ошибках.
     */
    public function messages(): array
    {
        return [
            // course_id
            'course_id.required' => 'Укажите курс, к которому относится модуль.',
            'course_id.integer'  => 'Идентификатор курса должен быть числом.',
            'course_id.exists'   => 'Указанный курс не найден.',

            // locale
            'locale.required' => 'Укажите локаль модуля.',
            'locale.string'   => 'Локаль должна быть строкой.',
            'locale.size'     => 'Локаль должна состоять из двух символов.',
            'locale.in'       => 'Недопустимая локаль. Допустимые значения: ru, en, kk.',

            // title
            'title.required' => 'Введите заголовок модуля.',
            'title.string'   => 'Заголовок должен быть строкой.',
            'title.max'      => 'Заголовок не должен превышать :max символов.',

            // slug
            'slug.required' => 'Укажите слаг (ЧПУ) модуля.',
            'slug.string'   => 'Слаг должен быть строкой.',
            'slug.max'      => 'Слаг не должен превышать :max символов.',
            'slug.regex'    => 'Слаг может содержать только латинские буквы, цифры и дефисы.',
            'slug.unique'   => 'Такой слаг уже используется в этом курсе.',

            // subtitle
            'subtitle.string' => 'Подзаголовок должен быть строкой.',
            'subtitle.max'    => 'Подзаголовок не должен превышать :max символов.',

            // short
            'short.string' => 'Краткое описание должно быть строкой.',
            'short.max'    => 'Краткое описание не должно превышать :max символов.',

            // description
            'description.string' => 'Описание должно быть текстом.',

            // SEO
            'meta_title.string'    => 'Поле «Meta Title» должно быть строкой.',
            'meta_title.max'       => 'Поле «Meta Title» не должно превышать :max символов.',
            'meta_keywords.string' => 'Поле «Meta Keywords» должно быть строкой.',
            'meta_keywords.max'    => 'Поле «Meta Keywords» не должно превышать :max символов.',
            'meta_desc.string'     => 'Поле «Meta Description» должно быть строкой.',
            'meta_desc.max'        => 'Поле «Meta Description» не должно превышать :max символов.',

            // difficulty
            'difficulty.integer' => 'Сложность должна быть целым числом.',
            'difficulty.min'     => 'Сложность не может быть меньше :min.',
            'difficulty.max'     => 'Сложность не может быть больше :max.',

            // duration
            'duration.integer' => 'Длительность должна быть целым числом (в минутах).',
            'duration.min'     => 'Длительность не может быть отрицательной.',

            // availability / status
            'availability.in' => 'Недопустимое значение поля доступности модуля.',
            'status.in'       => 'Недопустимое значение статуса модуля.',

            // published_at
            'published_at.date' => 'Дата публикации имеет неверный формат.',

            // activity / sort
            'activity.required' => 'Укажите, активен ли модуль.',
            'activity.boolean'  => 'Поле «Активность» должно быть булевым значением.',
            'sort.integer'      => 'Поле «Сортировка» должно быть целым числом.',
            'sort.min'          => 'Поле «Сортировка» не может быть отрицательным.',

            // Метрики
            'lessons_count.integer' => 'Количество уроков должно быть целым числом.',
            'lessons_count.min'     => 'Количество уроков не может быть отрицательным.',

            'popularity.integer' => 'Популярность должна быть целым числом.',
            'popularity.min'     => 'Популярность не может быть отрицательной.',

            'rating_count.integer' => 'Количество оценок должно быть целым числом.',
            'rating_count.min'     => 'Количество оценок не может быть отрицательным.',

            'rating_avg.numeric' => 'Средняя оценка должна быть числом.',
            'rating_avg.min'     => 'Средняя оценка не может быть меньше :min.',
            'rating_avg.max'     => 'Средняя оценка не может быть больше :max.',

            'views.integer' => 'Количество просмотров должно быть целым числом.',
            'views.min'     => 'Количество просмотров не может быть отрицательным.',

            'likes.integer' => 'Количество лайков должно быть целым числом.',
            'likes.min'     => 'Количество лайков не может быть отрицательным.',

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
            'course_id'        => 'Курс',
            'locale'           => 'Локаль',
            'title'            => 'Заголовок модуля',
            'slug'             => 'Слаг',
            'subtitle'         => 'Подзаголовок',
            'short'            => 'Краткое описание',
            'description'      => 'Описание',
            'meta_title'       => 'Meta Title',
            'meta_keywords'    => 'Meta Keywords',
            'meta_desc'        => 'Meta Description',
            'difficulty'       => 'Сложность',
            'duration'         => 'Длительность',
            'availability'     => 'Доступность',
            'status'           => 'Статус',
            'published_at'     => 'Дата публикации',

            'activity'   => 'Активность',
            'sort'       => 'Сортировка',
            'lessons_count' => 'Количество уроков',
            'popularity'    => 'Популярность',
            'rating_count'  => 'Количество оценок',
            'rating_avg'    => 'Средняя оценка',
            'views'         => 'Просмотры',
            'likes'         => 'Лайки',

            'images' => 'Изображения модуля',
        ];
    }
}
