<?php

namespace App\Http\Requests\Admin\School\Assignment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Подключишь Policy при необходимости
        return true;
    }

    /**
     * Нормализация входных данных до валидации.
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

        // left → bool
        if ($this->has('left')) {
            $data['left'] = filter_var(
                $this->input('left'),
                FILTER_VALIDATE_BOOL,
                FILTER_NULL_ON_FAILURE
            );
        }

        // main → bool
        if ($this->has('main')) {
            $data['main'] = filter_var(
                $this->input('main'),
                FILTER_VALIDATE_BOOL,
                FILTER_NULL_ON_FAILURE
            );
        }

        // right → bool
        if ($this->has('right')) {
            $data['right'] = filter_var(
                $this->input('right'),
                FILTER_VALIDATE_BOOL,
                FILTER_NULL_ON_FAILURE
            );
        }

        // sort, max_score, attempts_limit → int
        foreach (['sort', 'max_score', 'attempts_limit'] as $intField) {
            if ($this->has($intField) && $this->input($intField) !== null && $this->input($intField) !== '') {
                $data[$intField] = (int) $this->input($intField);
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
        // Параметр маршрута `assignment` (модель или ID)
        $routeParam = $this->route('assignment');

        $assignmentId = $routeParam instanceof Model
            ? $routeParam->getKey()
            : $routeParam;

        // Допустимые значения
        $statusOptions     = ['draft', 'published', 'archived'];
        $visibilityOptions = ['public', 'enrolled', 'private'];
        $gradingTypes      = ['manual', 'auto'];

        return [
            // Привязки (все опциональны)
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'module_id' => ['nullable', 'integer', 'exists:modules,id'],
            'lesson_id' => ['nullable', 'integer', 'exists:lessons,id'],

            // Автор/инструктор (опционально)
            'instructor_profile_id' => ['nullable', 'integer', 'exists:instructor_profiles,id'],

            // Локаль (3 языка: ru, en, kk) — 2 символа
            'locale' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'size:2',
                Rule::in(['ru', 'en', 'kk']),
            ],

            // Основные поля
            'title' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:255',
            ],
            'slug' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:255',
                // общий unique, т.к. в миграции просто ->unique()
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('assignments', 'slug')->ignore($assignmentId),
            ],
            'subtitle'    => ['nullable', 'string', 'max:255'],
            'short'       => ['nullable', 'string', 'max:65535'], // text
            'description' => ['nullable', 'string'],
            'instructions'=> ['nullable', 'string'],

            // Публикация / статус / видимость
            'status' => ['sometimes', 'string', Rule::in($statusOptions)],
            'visibility' => ['sometimes', 'string', Rule::in($visibilityOptions)],
            'published_at' => ['nullable', 'date'],

            // Параметры оценки/прохождения
            'max_score' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'attempts_limit' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'grading_type' => ['sometimes', 'string', Rule::in($gradingTypes)],

            // Активность и сортировка
            'activity' => ['required', 'boolean'],
            'left'     => ['required', 'boolean'],
            'main'     => ['required', 'boolean'],
            'right'    => ['required', 'boolean'],
            'sort'     => ['nullable', 'integer', 'min:0'],

            // Дедлайн
            'due_at' => ['nullable', 'date'],

            // ✅ Изображения задания (по аналогии с уроками, но своя таблица)
            'images'           => ['nullable', 'array'],
            'images.*.id'      => [
                'nullable',
                'integer',
                Rule::exists('assignment_images', 'id'),
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
            'deletedImages.*' => ['integer', 'exists:assignment_images,id'],
        ];
    }

    /**
     * RU-сообщения об ошибках.
     */
    public function messages(): array
    {
        return [
            // Привязки
            'course_id.integer' => 'Идентификатор курса должен быть числом.',
            'course_id.exists'  => 'Указанный курс не найден.',
            'module_id.integer' => 'Идентификатор модуля должен быть числом.',
            'module_id.exists'  => 'Указанный модуль не найден.',
            'lesson_id.integer' => 'Идентификатор урока должен быть числом.',
            'lesson_id.exists'  => 'Указанный урок не найден.',

            'instructor_profile_id.integer' => 'Идентификатор преподавателя должен быть числом.',
            'instructor_profile_id.exists'  => 'Указанный преподаватель не найден.',

            // locale
            'locale.required' => 'Укажите локаль задания.',
            'locale.string'   => 'Локаль должна быть строкой.',
            'locale.size'     => 'Локаль должна состоять из двух символов.',
            'locale.in'       => 'Недопустимая локаль. Допустимые значения: ru, en, kk.',

            // Основные поля
            'title.required' => 'Введите заголовок задания.',
            'title.string'   => 'Заголовок должен быть строкой.',
            'title.max'      => 'Заголовок не должен превышать :max символов.',

            'slug.required' => 'Укажите слаг (ЧПУ) задания.',
            'slug.string'   => 'Слаг должен быть строкой.',
            'slug.max'      => 'Слаг не должен превышать :max символов.',
            'slug.regex'    => 'Слаг может содержать только латинские буквы, цифры и дефисы.',
            'slug.unique'   => 'Такой слаг уже используется.',

            'subtitle.string' => 'Подзаголовок должен быть строкой.',
            'subtitle.max'    => 'Подзаголовок не должен превышать :max символов.',

            'short.string' => 'Краткое описание должно быть строкой.',

            'description.string'  => 'Описание должно быть текстом.',
            'instructions.string' => 'Инструкции должны быть текстом.',

            // Статус / видимость / публикация
            'status.in'       => 'Недопустимый статус. Возможные: draft, published, archived.',
            'visibility.in'   => 'Недопустимая видимость. Возможные: public, enrolled, private.',
            'published_at.date' => 'Дата публикации указана в неверном формате.',

            // Оценка/прохождение
            'max_score.integer' => 'Максимальный балл должен быть целым числом.',
            'max_score.min'     => 'Максимальный балл должен быть не менее :min.',
            'max_score.max'     => 'Максимальный балл слишком велик.',

            'attempts_limit.integer' => 'Лимит попыток должен быть целым числом.',
            'attempts_limit.min'     => 'Лимит попыток не может быть отрицательным.',
            'attempts_limit.max'     => 'Лимит попыток слишком велик.',

            'grading_type.in' => 'Недопустимый тип проверки. Возможные: manual, auto.',

            // Активность/сортировка
            'activity.required' => 'Укажите, активно ли задание.',
            'activity.boolean'  => 'Поле «Активность» должно быть логическим значением.',
            'left.required'     => 'Укажите, активно ли в левой колонке.',
            'left.boolean'      => 'Поле «Показать в левой колонке» должно быть логическим значением.',
            'main.required'     => 'Укажите, активно ли в главном.',
            'main.boolean'      => 'Поле «Показать в главном» должно быть логическим значением.',
            'right.required'    => 'Укажите, активно ли в правой колонке.',
            'right.boolean'     => 'Поле «Показать в правой колонке» должно быть логическим значением.',
            'sort.integer'      => 'Позиция должна быть целым числом.',
            'sort.min'          => 'Позиция не может быть отрицательной.',

            // Дедлайн
            'due_at.date' => 'Дата дедлайна указана в неверном формате.',

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
            'course_id'             => 'Курс',
            'module_id'             => 'Модуль',
            'lesson_id'             => 'Урок',
            'instructor_profile_id' => 'Преподаватель',

            'locale'       => 'Локаль задания',
            'title'        => 'Заголовок задания',
            'slug'         => 'Слаг',
            'subtitle'     => 'Подзаголовок',
            'short'        => 'Краткое описание',
            'description'  => 'Описание',
            'instructions' => 'Инструкции',

            'status'       => 'Статус',
            'visibility'   => 'Видимость',
            'published_at' => 'Дата публикации',

            'max_score'      => 'Максимальный балл',
            'attempts_limit' => 'Лимит попыток',
            'grading_type'   => 'Тип проверки',

            'activity' => 'Активность',
            'left'     => 'Показать в левой колонке',
            'main'     => 'Показать в главном',
            'right'    => 'Показать в правой колонке',
            'sort'     => 'Позиция',

            'due_at' => 'Дедлайн',

            'images' => 'Изображения задания',
        ];
    }
}
