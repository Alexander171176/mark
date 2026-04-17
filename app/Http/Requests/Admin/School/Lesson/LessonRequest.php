<?php

namespace App\Http\Requests\Admin\School\Lesson;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LessonRequest extends FormRequest
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

        // preview_value → int
        if ($this->has('preview_value') && $this->input('preview_value') !== null) {
            $data['preview_value'] = (int) $this->input('preview_value');
        }

        // Денорм-метрики → int/float (если вдруг редактируются из админки)
        foreach (['popularity', 'rating_count', 'views', 'likes'] as $intField) {
            if ($this->has($intField) && $this->input($intField) !== null) {
                $data[$intField] = (int) $this->input($intField);
            }
        }

        if ($this->has('rating_avg') && $this->input('rating_avg') !== null) {
            $data['rating_avg'] = (float) $this->input('rating_avg');
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
        // Параметр маршрута `lesson` (модель или ID)
        $routeParam = $this->route('lesson');

        $lessonId = $routeParam instanceof Model
            ? $routeParam->getKey()
            : $routeParam;

        // Для уникальности slug нужен module_id (уникален в рамках модуля)
        $moduleIdForUnique = $this->input(
            'module_id',
            $routeParam instanceof Model ? $routeParam->module_id : null
        );

        $availabilityOptions = ['public', 'unlisted', 'private'];
        $statusOptions       = ['draft', 'published', 'archived'];

        // Режимы превью (можешь потом расширить)
        $previewModes = ['none', 'full', 'percent', 'duration', 'chars'];

        // Тип доступа (можешь дополнять при необходимости)
        $accessTypes = ['free', 'paid', 'bonus'];

        return [
            'module_id' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                'exists:modules,id',
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
                Rule::unique('lessons', 'slug')
                    ->where(fn ($q) => $q->where('module_id', $moduleIdForUnique))
                    ->ignore($lessonId),
            ],

            'subtitle'    => ['nullable', 'string', 'max:255'],
            'short'       => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            // Публикация / видимость
            'availability' => ['sometimes', 'string', Rule::in($availabilityOptions)],
            'status'       => ['sometimes', 'string', Rule::in($statusOptions)],
            'published_at' => ['nullable', 'date'],

            // Тип доступа и сложность/длительность
            'access_type' => ['sometimes', 'string', Rule::in($accessTypes)],
            'difficulty'  => ['nullable', 'integer', 'min:0', 'max:5'],
            'duration'    => ['nullable', 'integer', 'min:0'],

            // Превью
            'preview_mode'  => ['nullable', 'string', 'max:32', Rule::in($previewModes)],
            'preview_value' => ['nullable', 'integer', 'min:0'],

            // Управление списком
            'activity' => ['required', 'boolean'],
            'sort'     => ['nullable', 'integer', 'min:0'],

            // SEO
            'meta_title'    => ['nullable', 'string', 'max:160'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_desc'     => ['nullable', 'string', 'max:255'],

            // Денорм-метрики (если вдруг меняются через админку)
            'rating_avg'   => ['nullable', 'numeric', 'min:0', 'max:5'],
            'rating_count' => ['nullable', 'integer', 'min:0'],
            'popularity'   => ['nullable', 'integer', 'min:0'],
            'views'        => ['nullable', 'integer', 'min:0'],
            'likes'        => ['nullable', 'integer', 'min:0'],

            // ✅ Полиморфные хештеги
            'hashtag_ids'   => ['nullable', 'array'],
            'hashtag_ids.*' => ['integer', 'exists:hashtags,id'],

            // ✅ Изображения урока (по аналогии с курсами/модулями)
            'images'           => ['nullable', 'array'],
            'images.*.id'      => [
                'nullable',
                'integer',
                Rule::exists('lesson_images', 'id'),
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
            'deletedImages.*' => ['integer', 'exists:lesson_images,id'],

            // Полиморфный контент урока (опционально, без жёстких ограничений)
            'content_type' => ['nullable', 'string', 'max:255'],
            'content_id'   => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * RU-сообщения об ошибках.
     */
    public function messages(): array
    {
        return [
            // module_id
            'module_id.required' => 'Укажите модуль, к которому относится урок.',
            'module_id.integer'  => 'Идентификатор модуля должен быть числом.',
            'module_id.exists'   => 'Указанный модуль не найден.',

            // locale
            'locale.required' => 'Укажите локаль урока.',
            'locale.string'   => 'Локаль должна быть строкой.',
            'locale.size'     => 'Локаль должна состоять из двух символов.',
            'locale.in'       => 'Недопустимая локаль. Допустимые значения: ru, en, kk.',

            // title
            'title.required' => 'Введите заголовок урока.',
            'title.string'   => 'Заголовок должен быть строкой.',
            'title.max'      => 'Заголовок не должен превышать :max символов.',

            // slug
            'slug.required' => 'Укажите слаг (ЧПУ) урока.',
            'slug.string'   => 'Слаг должен быть строкой.',
            'slug.max'      => 'Слаг не должен превышать :max символов.',
            'slug.regex'    => 'Слаг может содержать только латинские буквы, цифры и дефисы.',
            'slug.unique'   => 'Такой слаг уже используется в этом модуле.',

            // subtitle
            'subtitle.string' => 'Подзаголовок должен быть строкой.',
            'subtitle.max'    => 'Подзаголовок не должен превышать :max символов.',

            // short
            'short.string' => 'Краткое описание должно быть строкой.',
            'short.max'    => 'Краткое описание не должно превышать :max символов.',

            // description
            'description.string' => 'Описание должно быть текстом.',

            // availability / status
            'availability.in' => 'Недопустимое значение доступности урока.',
            'status.in'       => 'Недопустимое значение статуса урока.',

            // published_at
            'published_at.date' => 'Дата публикации имеет неверный формат.',

            // access_type / difficulty / duration
            'access_type.in'       => 'Недопустимый тип доступа к уроку.',
            'difficulty.integer'   => 'Сложность должна быть целым числом.',
            'difficulty.min'       => 'Сложность не может быть меньше :min.',
            'difficulty.max'       => 'Сложность не может быть больше :max.',
            'duration.integer'     => 'Длительность должна быть целым числом.',
            'duration.min'         => 'Длительность не может быть отрицательной.',

            // preview
            'preview_mode.in'     => 'Недопустимый режим превью.',
            'preview_value.integer' => 'Значение превью должно быть целым числом.',
            'preview_value.min'     => 'Значение превью не может быть отрицательным.',

            // activity / sort
            'activity.required' => 'Укажите, активен ли урок.',
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

            // Метрики
            'rating_avg.numeric'   => 'Средняя оценка должна быть числом.',
            'rating_avg.min'       => 'Средняя оценка не может быть меньше :min.',
            'rating_avg.max'       => 'Средняя оценка не может быть больше :max.',
            'rating_count.integer' => 'Количество оценок должно быть целым числом.',
            'rating_count.min'     => 'Количество оценок не может быть отрицательным.',
            'popularity.integer'   => 'Популярность должна быть целым числом.',
            'popularity.min'       => 'Популярность не может быть отрицательной.',
            'views.integer'        => 'Количество просмотров должно быть целым числом.',
            'views.min'            => 'Количество просмотров не может быть отрицательным.',
            'likes.integer'        => 'Количество лайков должно быть целым числом.',
            'likes.min'            => 'Количество лайков не может быть отрицательным.',

            // ✅ Хештеги
            'hashtag_ids.array'     => 'Список хештегов должен быть массивом.',
            'hashtag_ids.*.integer' => 'ID хештега должен быть целым числом.',
            'hashtag_ids.*.exists'  => 'Некоторых хештегов не существует.',

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

            // content_type / content_id
            'content_type.string' => 'Тип контента должен быть строкой.',
            'content_type.max'    => 'Тип контента не должен превышать :max символов.',
            'content_id.integer'  => 'ID связанного контента должен быть целым числом.',
            'content_id.min'      => 'ID связанного контента должен быть положительным.',
        ];
    }

    /**
     * Человекочитаемые имена полей.
     */
    public function attributes(): array
    {
        return [
            'module_id'    => 'Модуль',
            'locale'       => 'Локаль',
            'title'        => 'Заголовок урока',
            'slug'         => 'Слаг',
            'subtitle'     => 'Подзаголовок',
            'short'        => 'Краткое описание',
            'description'  => 'Описание',

            'availability' => 'Доступность',
            'status'       => 'Статус',
            'published_at' => 'Дата публикации',

            'access_type'  => 'Тип доступа',
            'difficulty'   => 'Сложность',
            'duration'     => 'Длительность',
            'preview_mode' => 'Режим превью',
            'preview_value'=> 'Значение превью',

            'activity'   => 'Активность',
            'sort'       => 'Сортировка',

            'meta_title'    => 'Meta Title',
            'meta_keywords' => 'Meta Keywords',
            'meta_desc'     => 'Meta Description',

            'rating_avg'   => 'Средняя оценка',
            'rating_count' => 'Количество оценок',
            'popularity'   => 'Популярность',
            'views'        => 'Просмотры',
            'likes'        => 'Лайки',

            'hashtag_ids'  => 'Хештеги',

            'images' => 'Изображения урока',

            'content_type' => 'Тип связанного контента',
            'content_id'   => 'ID связанного контента',
        ];
    }
}
