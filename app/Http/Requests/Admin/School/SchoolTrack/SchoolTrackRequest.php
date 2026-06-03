<?php

namespace App\Http\Requests\Admin\School\SchoolTrack;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SchoolTrackRequest extends FormRequest
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

        if ($this->has('parent_id')) {
            $parentId = $this->input('parent_id');
            $data['parent_id'] = ($parentId === '' || $parentId === null) ? null : (int) $parentId;
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
        $track = $this->route('schoolTrack')
            ?? $this->route('id');

        $trackId = is_object($track)
            ? $track->id
            : ($track ? (int) $track : null);

        return [
            'parent_id' => [
                'nullable',
                'integer',
                Rule::notIn([$trackId]),
                Rule::exists('school_tracks', 'id'),
            ],

            'sort' => ['nullable', 'integer', 'min:0'],
            'activity' => ['required', 'boolean'],

            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('school_tracks', 'slug')->ignore($trackId),
            ],

            'views' => ['nullable', 'integer', 'min:0'],

            'translations' => ['required', 'array', 'min:1'],

            'translations.*.name' => [
                'required',
                'string',
                'max:255',
            ],

            'translations.*.short' => [
                'nullable',
                'string',
                'max:255',
            ],

            'translations.*.description' => [
                'nullable',
                'string',
            ],

            'translations.*.meta_title' => [
                'nullable',
                'string',
                'max:160',
            ],

            'translations.*.meta_keywords' => [
                'nullable',
                'string',
                'max:255',
            ],

            'translations.*.meta_desc' => [
                'nullable',
                'string',
                'max:255',
            ],

            'images' => ['nullable', 'array'],
            'images.*.id' => [
                'nullable',
                'integer',
                Rule::exists('school_track_images', 'id'),
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
                Rule::exists('school_track_images', 'id'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'parent_id.integer' => 'Идентификатор родительского трека должен быть целым числом.',
            'parent_id.exists' => 'Указанный родительский трек не найден.',
            'parent_id.not_in' => 'Трек не может быть родителем самого себя.',

            'sort.integer' => 'Поле сортировки должно быть целым числом.',
            'sort.min' => 'Поле сортировки не может быть отрицательным.',

            'activity.required' => 'Укажите активность трека.',
            'activity.boolean' => 'Поле активности должно быть булевым значением.',

            'slug.required' => 'Укажите slug трека.',
            'slug.string' => 'Slug должен быть строкой.',
            'slug.max' => 'Slug не должен превышать 255 символов.',
            'slug.regex' => 'Slug может содержать только латинские буквы, цифры и дефисы.',
            'slug.unique' => 'Трек с таким slug уже существует.',

            'views.integer' => 'Количество просмотров должно быть числом.',
            'views.min' => 'Количество просмотров не может быть отрицательным.',

            'translations.required' => 'Необходимо передать переводы трека.',
            'translations.array' => 'Переводы должны быть массивом.',
            'translations.min' => 'Добавьте хотя бы один перевод.',

            'translations.*.name.required' => 'Укажите название трека.',
            'translations.*.name.string' => 'Название трека должно быть строкой.',
            'translations.*.name.max' => 'Название трека не должно превышать 255 символов.',

            'translations.*.short.string' => 'Краткое описание должно быть строкой.',
            'translations.*.short.max' => 'Краткое описание не должно превышать 255 символов.',

            'translations.*.description.string' => 'Описание должно быть текстом.',

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
            'parent_id' => 'Родительский трек',
            'sort' => 'Сортировка',
            'activity' => 'Активность',
            'slug' => 'Slug',
            'views' => 'Просмотры',
            'translations' => 'Переводы',
            'images' => 'Изображения',
            'deletedImages' => 'Удаляемые изображения',
        ];
    }
}
