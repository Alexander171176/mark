<?php

namespace App\Http\Requests\Admin\School\SchoolCourse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SchoolCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->has('slug') && is_string($this->input('slug'))) {
            $data['slug'] = Str::slug($this->input('slug'));
        }

        foreach (['activity', 'is_new', 'is_hit', 'is_sale', 'left', 'main', 'right'] as $field) {
            if ($this->has($field)) {
                $data[$field] = filter_var(
                    $this->input($field),
                    FILTER_VALIDATE_BOOL,
                    FILTER_NULL_ON_FAILURE
                );
            }
        }

        foreach (['sort', 'duration', 'students_count', 'popularity', 'rating_count', 'views', 'likes'] as $field) {
            if ($this->has($field) && is_numeric($this->input($field))) {
                $data[$field] = (int) $this->input($field);
            }
        }

        if ($this->has('difficulty') && is_numeric($this->input('difficulty'))) {
            $data['difficulty'] = (int) $this->input('difficulty');
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $course = $this->route('schoolCourse')
            ?? $this->route('course')
            ?? $this->route('id');

        $courseId = is_object($course)
            ? $course->id
            : ($course ? (int) $course : null);

        return [
            'school_instructor_profile_id' => [
                'required',
                'integer',
                Rule::exists('school_instructor_profiles', 'id'),
            ],

            'sort' => ['nullable', 'integer', 'min:0'],
            'activity' => ['nullable', 'boolean'],

            'is_new' => ['nullable', 'boolean'],
            'is_hit' => ['nullable', 'boolean'],
            'is_sale' => ['nullable', 'boolean'],
            'left' => ['nullable', 'boolean'],
            'main' => ['nullable', 'boolean'],
            'right' => ['nullable', 'boolean'],

            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('school_courses', 'slug')->ignore($courseId),
            ],

            'published_at' => ['nullable', 'date'],

            'level' => ['nullable', 'string', 'max:32'],
            'status' => ['nullable', 'string', Rule::in(['draft', 'published', 'archived'])],
            'availability' => ['nullable', 'string', Rule::in(['unlisted', 'public', 'private'])],

            'difficulty' => ['nullable', 'integer', 'min:0', 'max:5'],
            'duration' => ['nullable', 'integer', 'min:0'],

            'students_count' => ['nullable', 'integer', 'min:0'],
            'popularity' => ['nullable', 'integer', 'min:0'],
            'rating_count' => ['nullable', 'integer', 'min:0'],
            'rating_avg' => ['nullable', 'numeric', 'between:0,5'],
            'views' => ['nullable', 'integer', 'min:0'],
            'likes' => ['nullable', 'integer', 'min:0'],

            'translations' => ['required', 'array', 'min:1'],

            'translations.*.title' => ['required', 'string', 'max:255'],
            'translations.*.subtitle' => ['nullable', 'string', 'max:255'],
            'translations.*.short' => ['nullable', 'string', 'max:255'],
            'translations.*.description' => ['nullable', 'string'],
            'translations.*.meta_title' => ['nullable', 'string', 'max:160'],
            'translations.*.meta_keywords' => ['nullable', 'string', 'max:255'],
            'translations.*.meta_desc' => ['nullable', 'string', 'max:255'],

            'track_ids' => ['nullable', 'array'],
            'track_ids.*' => ['integer', Rule::exists('school_tracks', 'id')],

            'hashtag_ids' => ['nullable', 'array'],
            'hashtag_ids.*' => ['integer', Rule::exists('school_hashtags', 'id')],

            'related_course_ids' => ['nullable', 'array'],
            'related_course_ids.*' => [
                'integer',
                Rule::notIn([$courseId]),
                Rule::exists('school_courses', 'id'),
            ],

            'images' => ['nullable', 'array'],
            'images.*.id' => [
                'nullable',
                'integer',
                Rule::exists('school_course_images', 'id'),
                Rule::prohibitedIf(fn () => $this->isMethod('post')),
            ],
            'images.*.order' => ['nullable', 'integer', 'min:0'],
            'images.*.alt' => ['nullable', 'string', 'max:255'],
            'images.*.caption' => ['nullable', 'string', 'max:255'],
            'images.*.file' => [
                'nullable',
                'required_without:images.*.id',
                'file',
                'image',
                'mimes:jpeg,jpg,png,gif,svg,webp',
                'max:10240',
            ],

            'deletedImages' => ['sometimes', 'array'],
            'deletedImages.*' => ['integer', Rule::exists('school_course_images', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'school_instructor_profile_id.required' => 'Укажите преподавателя курса.',
            'school_instructor_profile_id.exists' => 'Указанный преподаватель не найден.',

            'activity.boolean' => 'Поле активности должно быть булевым значением.',

            'slug.required' => 'Укажите slug курса.',
            'slug.regex' => 'Slug может содержать только латинские буквы, цифры и дефисы.',
            'slug.unique' => 'Курс с таким slug уже существует.',

            'published_at.date' => 'Дата публикации имеет неверный формат.',
            'status.in' => 'Недопустимый статус курса.',
            'availability.in' => 'Недопустимое значение доступности.',

            'difficulty.integer' => 'Сложность должна быть целым числом.',
            'difficulty.min' => 'Сложность не может быть меньше 0.',
            'difficulty.max' => 'Сложность не может быть больше 5.',

            'duration.integer' => 'Длительность должна быть целым числом.',
            'duration.min' => 'Длительность не может быть отрицательной.',

            'translations.required' => 'Необходимо передать переводы курса.',
            'translations.array' => 'Переводы курса должны быть массивом.',
            'translations.min' => 'Необходимо добавить хотя бы один перевод.',

            'translations.*.title.required' => 'Укажите название курса.',
            'translations.*.title.string' => 'Название курса должно быть строкой.',
            'translations.*.title.max' => 'Название курса не должно превышать 255 символов.',

            'translations.*.subtitle.string' => 'Подзаголовок курса должен быть строкой.',
            'translations.*.subtitle.max' => 'Подзаголовок курса не должен превышать 255 символов.',

            'translations.*.short.string' => 'Краткое описание должно быть строкой.',
            'translations.*.short.max' => 'Краткое описание не должно превышать 255 символов.',

            'translations.*.description.string' => 'Описание курса должно быть строкой.',

            'translations.*.meta_title.string' => 'SEO заголовок должен быть строкой.',
            'translations.*.meta_title.max' => 'SEO заголовок не должен превышать 160 символов.',

            'translations.*.meta_keywords.string' => 'SEO ключевые слова должны быть строкой.',
            'translations.*.meta_keywords.max' => 'SEO ключевые слова не должны превышать 255 символов.',

            'translations.*.meta_desc.string' => 'SEO описание должно быть строкой.',
            'translations.*.meta_desc.max' => 'SEO описание не должно превышать 255 символов.',

            'track_ids.array' => 'Список треков должен быть массивом.',
            'track_ids.*.exists' => 'Некоторых треков не существует.',

            'hashtag_ids.array' => 'Список хештегов должен быть массивом.',
            'hashtag_ids.*.exists' => 'Некоторых хештегов не существует.',

            'related_course_ids.array' => 'Список рекомендованных курсов должен быть массивом.',
            'related_course_ids.*.not_in' => 'Курс не может быть связан сам с собой.',
            'related_course_ids.*.exists' => 'Некоторых рекомендованных курсов не существует.',

            'images.array' => 'Неверный формат поля изображений.',
            'images.*.id.exists' => 'Указанное изображение не найдено.',
            'images.*.id.prohibited' => 'ID изображения нельзя передавать при создании.',
            'images.*.file.required_without' => 'Загрузите файл изображения или укажите существующий ID.',
            'images.*.file.image' => 'Файл должен быть изображением.',
            'images.*.file.mimes' => 'Разрешённые форматы: jpeg, jpg, png, gif, svg, webp.',
            'images.*.file.max' => 'Максимальный размер изображения — 10 МБ.',

            'deletedImages.array' => 'Неверный формат списка удаляемых изображений.',
            'deletedImages.*.exists' => 'Некоторых изображений для удаления не существует.',
        ];
    }
}
