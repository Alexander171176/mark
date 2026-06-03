<?php

namespace App\Http\Requests\Admin\School\SchoolInstructorProfile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SchoolInstructorProfileRequest extends FormRequest
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

        if ($this->has('activity')) {
            $data['activity'] = filter_var(
                $this->input('activity'),
                FILTER_VALIDATE_BOOL,
                FILTER_NULL_ON_FAILURE
            );
        }

        if ($this->has('sort') && is_numeric($this->input('sort'))) {
            $data['sort'] = (int) $this->input('sort');
        }

        if ($this->has('social_links') && is_string($this->input('social_links'))) {
            $decoded = json_decode($this->input('social_links'), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $data['social_links'] = $decoded;
            }
        }

        if ($this->has('translations') && is_array($this->input('translations'))) {
            $data['translations'] = collect($this->input('translations'))
                ->map(function ($translation) {
                    if (isset($translation['locale']) && is_string($translation['locale'])) {
                        $translation['locale'] = mb_strtolower(trim($translation['locale']));
                    }

                    return $translation;
                })
                ->toArray();
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $instructor = $this->route('schoolInstructorProfile')
            ?? $this->route('id');

        $instructorId = is_object($instructor)
            ? $instructor->id
            : ($instructor ? (int) $instructor : null);

        return [
            'sort' => ['nullable', 'integer', 'min:0'],
            'activity' => ['required', 'boolean'],

            'user_id' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                'exists:users,id',
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('school_instructor_profiles', 'slug')->ignore($instructorId),
            ],

            'experience_years' => ['nullable', 'integer', 'min:0', 'max:80'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],

            'rating_count' => ['nullable', 'integer', 'min:0'],
            'rating_avg' => ['nullable', 'numeric', 'between:0,5'],
            'views' => ['nullable', 'integer', 'min:0'],

            'social_links' => ['sometimes', 'nullable', 'array', 'max:50'],
            'social_links.*' => ['nullable', 'string', 'max:255'],
            'social_links.github' => ['sometimes', 'nullable', 'url', 'max:255'],
            'social_links.linkedin' => ['sometimes', 'nullable', 'url', 'max:255'],
            'social_links.telegram' => ['sometimes', 'nullable', 'url', 'max:255'],
            'social_links.site' => ['sometimes', 'nullable', 'url', 'max:255'],

            'translations' => ['required', 'array', 'min:1'],

            'translations.*.title' => ['nullable', 'string', 'max:255'],
            'translations.*.short' => ['nullable', 'string', 'max:255'],
            'translations.*.bio' => ['nullable', 'string'],

            'translations.*.meta_title' => ['nullable', 'string', 'max:255'],
            'translations.*.meta_keywords' => ['nullable', 'string', 'max:255'],
            'translations.*.meta_desc' => ['nullable', 'string'],

            'images' => ['nullable', 'array'],
            'images.*.id' => [
                'nullable',
                'integer',
                Rule::exists('school_instructor_profile_images', 'id'),
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
            'deletedImages.*' => [
                'integer',
                Rule::exists('school_instructor_profile_images', 'id'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Не указан пользователь, владелец профиля.',
            'user_id.integer' => 'Идентификатор пользователя должен быть целым числом.',
            'user_id.exists' => 'Указанный пользователь не найден.',

            'slug.required' => 'Поле «Слаг» обязательно для заполнения.',
            'slug.string' => 'Поле «Слаг» должно быть строкой.',
            'slug.max' => 'Поле «Слаг» не должно превышать 255 символов.',
            'slug.regex' => 'Слаг может содержать только латинские буквы, цифры и дефисы.',
            'slug.unique' => 'Такой слаг уже используется.',

            'activity.required' => 'Укажите активность профиля.',
            'activity.boolean' => 'Поле активности должно быть булевым значением.',

            'sort.integer' => 'Поле сортировки должно быть целым числом.',
            'sort.min' => 'Поле сортировки не может быть отрицательным.',

            'experience_years.integer' => 'Опыт должен быть целым числом.',
            'experience_years.min' => 'Опыт не может быть отрицательным.',
            'experience_years.max' => 'Опыт не может превышать :max лет.',

            'hourly_rate.numeric' => 'Почасовая ставка должна быть числом.',
            'hourly_rate.min' => 'Почасовая ставка не может быть отрицательной.',

            'rating_count.integer' => 'Количество оценок должно быть целым числом.',
            'rating_count.min' => 'Количество оценок не может быть отрицательным.',

            'rating_avg.numeric' => 'Средняя оценка должна быть числом.',
            'rating_avg.between' => 'Средняя оценка должна быть от :min до :max.',

            'views.integer' => 'Количество просмотров должно быть числом.',
            'views.min' => 'Количество просмотров не может быть отрицательным.',

            'social_links.array' => 'Поле соцсетей должно быть массивом.',
            'social_links.*.string' => 'Каждая соцссылка должна быть строкой.',
            'social_links.*.max' => 'Каждая соцссылка не должна превышать :max символов.',
            'social_links.github.url' => 'Ссылка GitHub должна быть корректным URL.',
            'social_links.linkedin.url' => 'Ссылка LinkedIn должна быть корректным URL.',
            'social_links.telegram.url' => 'Ссылка Telegram должна быть корректным URL.',
            'social_links.site.url' => 'Ссылка на сайт должна быть корректным URL.',

            'translations.required' => 'Необходимо передать переводы профиля инструктора.',
            'translations.array' => 'Переводы должны быть массивом.',
            'translations.min' => 'Добавьте хотя бы один перевод.',

            'translations.*.locale.required' => 'Укажите локаль перевода.',
            'translations.*.locale.string' => 'Локаль должна быть строкой.',
            'translations.*.locale.max' => 'Локаль не должна превышать 10 символов.',
            'translations.*.locale.distinct' => 'Локали переводов не должны повторяться.',

            'translations.*.title.string' => 'Должность должна быть строкой.',
            'translations.*.title.max' => 'Должность не должна превышать 255 символов.',

            'translations.*.short.string' => 'Краткое описание должно быть строкой.',
            'translations.*.short.max' => 'Краткое описание не должно превышать 255 символов.',

            'translations.*.bio.string' => 'Биография должна быть текстом.',

            'translations.*.meta_title.string' => 'Meta Title должен быть строкой.',
            'translations.*.meta_title.max' => 'Meta Title не должен превышать 255 символов.',

            'translations.*.meta_keywords.string' => 'Meta Keywords должны быть строкой.',
            'translations.*.meta_keywords.max' => 'Meta Keywords не должны превышать 255 символов.',

            'translations.*.meta_desc.string' => 'Meta Description должен быть текстом.',

            'images.array' => 'Неверный формат поля изображений.',
            'images.*.id.integer' => 'ID изображения должен быть целым числом.',
            'images.*.id.exists' => 'Указанное изображение не найдено.',
            'images.*.id.prohibited' => 'ID изображения нельзя передавать при создании.',

            'images.*.order.integer' => 'Порядок изображения должен быть целым числом.',
            'images.*.order.min' => 'Порядок изображения не может быть отрицательным.',

            'images.*.alt.string' => 'Alt-текст изображения должен быть строкой.',
            'images.*.alt.max' => 'Alt-текст изображения не должен превышать 255 символов.',

            'images.*.caption.string' => 'Подпись изображения должна быть строкой.',
            'images.*.caption.max' => 'Подпись изображения не должна превышать 255 символов.',

            'images.*.file.required_without' => 'Загрузите файл изображения или укажите существующий ID.',
            'images.*.file.file' => 'Неверный файл изображения.',
            'images.*.file.image' => 'Файл должен быть изображением.',
            'images.*.file.mimes' => 'Разрешённые форматы: jpeg, jpg, png, gif, svg, webp.',
            'images.*.file.max' => 'Максимальный размер изображения — 10 МБ.',

            'deletedImages.array' => 'Неверный формат списка удаляемых изображений.',
            'deletedImages.*.integer' => 'ID удаляемого изображения должен быть целым числом.',
            'deletedImages.*.exists' => 'Некоторых изображений для удаления не существует.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'Пользователь',
            'slug' => 'Слаг',
            'activity' => 'Активность',
            'sort' => 'Сортировка',
            'experience_years' => 'Опыт',
            'hourly_rate' => 'Почасовая ставка',
            'rating_count' => 'Количество оценок',
            'rating_avg' => 'Средняя оценка',
            'views' => 'Просмотры',
            'social_links' => 'Соцсети',
            'translations' => 'Переводы',
            'images' => 'Изображения',
            'deletedImages' => 'Удаляемые изображения',
        ];
    }
}
