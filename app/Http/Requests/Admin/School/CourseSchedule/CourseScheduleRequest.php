<?php

namespace App\Http\Requests\Admin\School\CourseSchedule;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CourseScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Включи Policy при необходимости
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

        // is_online → bool
        if ($this->has('is_online')) {
            $data['is_online'] = filter_var(
                $this->input('is_online'),
                FILTER_VALIDATE_BOOL,
                FILTER_NULL_ON_FAILURE
            );
        }

        // sort → int
        if ($this->has('sort') && is_numeric($this->input('sort'))) {
            $data['sort'] = (int) $this->input('sort');
        }

        // capacity → int
        if ($this->has('capacity') && $this->input('capacity') !== null) {
            $data['capacity'] = (int) $this->input('capacity');
        }

        // views → int (если вдруг редактируется через админку)
        if ($this->has('views') && $this->input('views') !== null) {
            $data['views'] = (int) $this->input('views');
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
        // Параметр маршрута `course_schedule` (модель или ID)
        // Если у тебя другой параметр (schedule/flow и т.п.) — просто поменяй имя.
        $routeParam = $this->route('courseSchedule');

        $scheduleId = $routeParam instanceof Model
            ? $routeParam->getKey()
            : $routeParam;

        $statusOptions = ['draft', 'published', 'archived', 'cancelled'];

        return [
            'course_id' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                'exists:courses,id',
            ],

            'instructor_profile_id' => [
                'nullable',
                'integer',
                'exists:instructor_profiles,id',
            ],

            // Локаль (2 символа, ru/en/kk)
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
                Rule::unique('course_schedules', 'slug')->ignore($scheduleId),
            ],

            'subtitle'    => ['nullable', 'string', 'max:255'],
            'short'       => ['nullable', 'string'],
            'description' => ['nullable', 'string'],

            // SEO
            'meta_title'    => ['nullable', 'string', 'max:160'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_desc'     => ['nullable', 'string', 'max:255'],

            // Даты
            'starts_at'        => ['nullable', 'date'],
            'ends_at'          => ['nullable', 'date', 'after_or_equal:starts_at'],
            'enroll_starts_at' => ['nullable', 'date'],
            'enroll_ends_at'   => ['nullable', 'date', 'after_or_equal:enroll_starts_at'],

            // Лимит мест
            'capacity' => ['nullable', 'integer', 'min:0'],

            // Формат
            'is_online'   => ['required', 'boolean'],
            'location'    => ['nullable', 'string', 'max:255'],
            'meeting_url' => ['nullable', 'url', 'max:255'],
            'timezone'    => ['sometimes', 'timezone'],

            // Управление списком
            'activity' => ['required', 'boolean'],
            'sort'     => ['nullable', 'integer', 'min:0'],

            // Статус + метрика
            'status' => ['sometimes', 'string', Rule::in($statusOptions)],
            'views'  => ['nullable', 'integer', 'min:0'],

            // Заметки
            'notes' => ['nullable', 'string'],

            // ============ Изображения потока (по паттерну уроков) ============

            'images'           => ['nullable', 'array'],
            'images.*.id'      => [
                'nullable',
                'integer',
                Rule::exists('course_schedule_images', 'id'),
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
            'deletedImages.*' => ['integer', 'exists:course_schedule_images,id'],
        ];
    }

    public function messages(): array
    {
        return [
            // course_id
            'course_id.required' => 'Не указан курс.',
            'course_id.integer'  => 'Идентификатор курса должен быть числом.',
            'course_id.exists'   => 'Указанный курс не найден.',

            // instructor_profile_id
            'instructor_profile_id.integer' => 'Идентификатор преподавателя должен быть числом.',
            'instructor_profile_id.exists'  => 'Указанный преподаватель не найден.',

            // locale
            'locale.required' => 'Укажите локаль потока.',
            'locale.string'   => 'Локаль должна быть строкой.',
            'locale.size'     => 'Локаль должна состоять из двух символов.',
            'locale.in'       => 'Недопустимая локаль. Допустимые: ru, en, kk.',

            // title
            'title.required' => 'Введите название потока.',
            'title.string'   => 'Название должно быть строкой.',
            'title.max'      => 'Название не должно превышать :max символов.',

            // slug
            'slug.required' => 'Укажите слаг (ЧПУ) потока.',
            'slug.string'   => 'Слаг должен быть строкой.',
            'slug.max'      => 'Слаг не должен превышать :max символов.',
            'slug.regex'    => 'Слаг может содержать только латинские буквы, цифры и дефисы.',
            'slug.unique'   => 'Такой слаг уже используется.',

            // subtitle / short / description
            'subtitle.string'    => 'Подзаголовок должен быть строкой.',
            'subtitle.max'       => 'Подзаголовок не должен превышать :max символов.',
            'short.string'       => 'Краткое описание должно быть строкой.',
            'description.string' => 'Описание должно быть текстом.',

            // SEO
            'meta_title.string'    => 'Поле «Meta Title» должно быть строкой.',
            'meta_title.max'       => 'Поле «Meta Title» не должно превышать :max символов.',
            'meta_keywords.string' => 'Поле «Meta Keywords» должно быть строкой.',
            'meta_keywords.max'    => 'Поле «Meta Keywords» не должно превышать :max символов.',
            'meta_desc.string'     => 'Поле «Meta Description» должно быть строкой.',
            'meta_desc.max'        => 'Поле «Meta Description» не должно превышать :max символов.',

            // Даты
            'starts_at.date'         => 'Дата начала указана неверно.',
            'ends_at.date'           => 'Дата окончания указана неверно.',
            'ends_at.after_or_equal' => 'Дата окончания не может быть раньше даты начала.',
            'enroll_starts_at.date'  => 'Дата начала записи указана неверно.',
            'enroll_ends_at.date'    => 'Дата окончания записи указана неверно.',
            'enroll_ends_at.after_or_equal' => 'Дата окончания записи не может быть раньше даты начала записи.',

            // capacity
            'capacity.integer' => 'Лимит мест должен быть целым числом.',
            'capacity.min'     => 'Лимит мест не может быть отрицательным.',

            // формат
            'is_online.required' => 'Укажите формат потока (онлайн/оффлайн).',
            'is_online.boolean'  => 'Поле «Онлайн» должно быть логическим значением.',
            'location.string'    => 'Адрес должен быть строкой.',
            'location.max'       => 'Адрес не должен превышать :max символов.',
            'meeting_url.url'    => 'Ссылка на трансляцию указана некорректно.',
            'meeting_url.max'    => 'Ссылка на трансляцию слишком длинная.',
            'timezone.timezone'  => 'Укажите корректный часовой пояс (IANA).',

            // activity / sort
            'activity.required' => 'Укажите, активен ли поток.',
            'activity.boolean'  => 'Поле «Активно» должно быть логическим значением.',
            'sort.integer'      => 'Поле «Сортировка» должно быть целым числом.',
            'sort.min'          => 'Поле «Сортировка» не может быть отрицательным.',

            // status / views
            'status.in'    => 'Недопустимый статус. Возможные: draft, published, archived, cancelled.',
            'views.integer'=> 'Количество просмотров должно быть целым числом.',
            'views.min'    => 'Количество просмотров не может быть отрицательным.',

            // notes
            'notes.string' => 'Заметки должны быть текстом.',

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

    public function attributes(): array
    {
        return [
            'course_id'             => 'Курс',
            'instructor_profile_id' => 'Преподаватель',

            'locale'   => 'Локаль',
            'activity' => 'Активность',
            'sort'     => 'Сортировка',

            'title'       => 'Название потока',
            'slug'        => 'Слаг',
            'subtitle'    => 'Подзаголовок',
            'short'       => 'Краткое описание',
            'description' => 'Описание',

            'meta_title'    => 'Meta Title',
            'meta_keywords' => 'Meta Keywords',
            'meta_desc'     => 'Meta Description',

            'starts_at'        => 'Дата начала потока',
            'ends_at'          => 'Дата окончания потока',
            'enroll_starts_at' => 'Начало записи',
            'enroll_ends_at'   => 'Окончание записи',

            'capacity'    => 'Лимит мест',
            'is_online'   => 'Онлайн',
            'location'    => 'Локация',
            'meeting_url' => 'Ссылка на трансляцию',
            'timezone'    => 'Часовой пояс',

            'status' => 'Статус потока',
            'views'  => 'Просмотры',

            'notes'  => 'Заметки администратора',

            'images' => 'Изображения потока',
        ];
    }
}
