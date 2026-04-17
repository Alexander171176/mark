<?php

namespace App\Http\Requests\Admin\School\Quiz;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class QuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        // При необходимости подключишь Policy
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

        // Целочисленные поля
        foreach ([
                     'course_id',
                     'module_id',
                     'lesson_id',
                     'attempts_limit',
                     'time_limit_minutes',
                     'pass_score',
                     'sort',
                 ] as $intField) {
            if ($this->has($intField)
                && $this->input($intField) !== null
                && $this->input($intField) !== ''
            ) {
                $data[$intField] = (int) $this->input($intField);
            }
        }

        // Булевые флаги
        foreach (['activity', 'left', 'main', 'right'] as $boolField) {
            if ($this->has($boolField)) {
                $data[$boolField] = filter_var(
                    $this->input($boolField),
                    FILTER_VALIDATE_BOOL,
                    FILTER_NULL_ON_FAILURE
                );
            }
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $routeParam = $this->route('quiz');

        // quiz может прийти как модель или ID
        $id = $routeParam instanceof \Illuminate\Database\Eloquent\Model
            ? $routeParam->getKey()
            : ($routeParam ?? $this->input('id'));

        $localeOptions = ['ru', 'en', 'kk'];
        $typeOptions   = ['graded', 'practice'];

        return [
            // Контекст привязки
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'module_id' => ['nullable', 'integer', 'exists:modules,id'],
            'lesson_id' => ['nullable', 'integer', 'exists:lessons,id'],

            // Локаль
            'locale' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'size:2',
                Rule::in($localeOptions),
            ],

            // Основное
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
                Rule::unique('quizzes', 'slug')->ignore($id),
            ],
            'short'       => ['nullable', 'string'],
            'description' => ['nullable', 'string'],

            // Тип квиза
            'type' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                Rule::in($typeOptions),
            ],

            // Ограничения/настройки
            'attempts_limit' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                'min:0',          // 0 = без лимита
            ],
            'time_limit_minutes' => [
                'nullable',
                'integer',
                'min:1',          // null = без лимита
            ],
            'pass_score' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                'min:0',
                'max:100',
            ],

            // Флаги отображения и активность
            'activity' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'boolean',
            ],
            'left' => [
                'sometimes',
                'boolean',
            ],
            'main' => [
                'sometimes',
                'boolean',
            ],
            'right' => [
                'sometimes',
                'boolean',
            ],

            // Сортировка + публикация
            'sort'         => ['nullable', 'integer', 'min:0'],
            'published_at' => ['nullable', 'date'],

            // ✅ Изображения задания (по аналогии, но своя таблица)
            'images'           => ['nullable', 'array'],
            'images.*.id'      => [
                'nullable',
                'integer',
                Rule::exists('quiz_images', 'id'),
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
            'deletedImages.*' => ['integer', 'exists:quiz_images,id'],
        ];
    }

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

            // locale
            'locale.required' => 'Укажите локаль квиза.',
            'locale.string'   => 'Локаль должна быть строкой.',
            'locale.size'     => 'Локаль должна состоять из двух символов.',
            'locale.in'       => 'Недопустимая локаль. Допустимые значения: ru, en, kk.',

            // Основное
            'title.required' => 'Укажите заголовок квиза.',
            'title.string'   => 'Заголовок должен быть строкой.',
            'title.max'      => 'Заголовок не должен превышать :max символов.',

            'slug.required' => 'Укажите слаг.',
            'slug.string'   => 'Слаг должен быть строкой.',
            'slug.max'      => 'Слаг не должен превышать :max символов.',
            'slug.regex'    => 'Слаг может содержать только латинские буквы, цифры и дефисы.',
            'slug.unique'   => 'Такой слаг уже используется.',

            'short.string'       => 'Краткое описание должно быть строкой.',
            'description.string' => 'Описание должно быть текстом.',

            // Тип
            'type.required' => 'Укажите тип квиза.',
            'type.string'   => 'Тип квиза должен быть строкой.',
            'type.in'       => 'Недопустимый тип квиза. Разрешено: graded или practice.',

            // Ограничения/настройки
            'attempts_limit.required' => 'Укажите лимит попыток (0 — без ограничений).',
            'attempts_limit.integer'  => 'Лимит попыток должен быть целым числом.',
            'attempts_limit.min'      => 'Лимит попыток не может быть отрицательным.',

            'time_limit_minutes.integer' => 'Лимит времени должен быть целым числом минут.',
            'time_limit_minutes.min'     => 'Минимальный лимит времени — :min минута.',

            'pass_score.required' => 'Укажите проходной порог в процентах.',
            'pass_score.integer'  => 'Проходной порог должен быть целым числом.',
            'pass_score.min'      => 'Проходной порог не может быть меньше :min.',
            'pass_score.max'      => 'Проходной порог не может быть больше :max.',

            // Флаги
            'activity.required' => 'Укажите, активен ли квиз.',
            'activity.boolean'  => 'Поле активности должно быть булевым значением.',

            'left.boolean'   => 'Поле «левая колонка» должно быть булевым значением.',
            'main.boolean'   => 'Поле «главная колонка» должно быть булевым значением.',
            'right.boolean'  => 'Поле «правая колонка» должно быть булевым значением.',

            // Сортировка и дата
            'sort.integer'   => 'Позиция сортировки должна быть целым числом.',
            'sort.min'       => 'Позиция сортировки не может быть отрицательной.',

            'published_at.date' => 'Дата публикации указана некорректно.',

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
            'course_id'          => 'Курс',
            'module_id'          => 'Модуль',
            'lesson_id'          => 'Урок',

            'locale'             => 'Локаль квиза',
            'title'              => 'Заголовок',
            'slug'               => 'Слаг',
            'short'              => 'Краткое описание',
            'description'        => 'Описание',
            'type'               => 'Тип квиза',

            'attempts_limit'     => 'Лимит попыток',
            'time_limit_minutes' => 'Лимит времени (мин)',
            'pass_score'         => 'Проходной порог (%)',

            'activity'           => 'Активность',
            'left'               => 'Показать в левой колонке',
            'main'               => 'Показать в центре',
            'right'              => 'Показать в правой колонке',
            'sort'               => 'Позиция сортировки',
            'published_at'       => 'Дата публикации',
            'images'             => 'Изображения викторины',
        ];
    }
}
