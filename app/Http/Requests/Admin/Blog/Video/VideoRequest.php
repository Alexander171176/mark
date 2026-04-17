<?php

namespace App\Http\Requests\Admin\Blog\Video;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;

class VideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $isCreating = $this->isMethod('post');

        // route('video') может быть моделью или id — делаем универсально
        $routeVideo = $this->route('video');
        $videoId = is_object($routeVideo) ? ($routeVideo->id ?? null) : $routeVideo;

        // owner/user_id: уникальности в миграции завязаны на user_id + locale
        $userId = (int) ($this->input('user_id') ?: ($this->user()?->id ?? 0));

        return [
            'sort'       => ['nullable', 'integer', 'min:0'],
            'activity'   => ['required', 'boolean'],
            'is_private' => ['required', 'boolean'],
            'left'     => ['required', 'boolean'],
            'main'     => ['required', 'boolean'],
            'right'    => ['required', 'boolean'],

            'locale' => ['required', 'string', 'max:10'],

            // moderation (как и в других сущностях)
            'moderation_status' => ['nullable', 'integer', Rule::in([0, 1, 2])],
            'moderation_note'   => ['nullable', 'string', 'max:500'],
            // moderated_by / moderated_at — не принимаем с фронта

            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('videos', 'title')
                    ->where(fn ($q) => $q
                        ->where('user_id', $userId)
                        ->where('locale', $this->input('locale'))
                    )
                    ->ignore($videoId),
            ],

            'url' => [
                'required',
                'string',
                'max:500',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('videos', 'url')
                    ->where(fn ($q) => $q
                        ->where('user_id', $userId)
                        ->where('locale', $this->input('locale'))
                    )
                    ->ignore($videoId),
            ],

            'short'       => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            // В модели поле pseudonym (не author)
            'pseudonym' => ['nullable', 'string', 'max:255'],

            'published_at' => ['nullable', 'date_format:Y-m-d'],

            // окно показа (в миграции есть)
            'show_from_at' => ['nullable', 'date'],
            'show_to_at'   => ['nullable', 'date'],

            'duration'    => ['nullable', 'integer', 'min:0'],
            'source_type' => ['required', Rule::in(['local', 'youtube', 'vimeo', 'code'])],

            'embed_code' => [
                Rule::requiredIf(fn () => $this->input('source_type') === 'code'),
                'nullable',
                'string',
            ],

            'external_video_id' => [
                Rule::requiredIf(fn () => in_array($this->input('source_type'), ['youtube', 'vimeo'], true)),
                'nullable',
                'string',
                'max:500', // по миграции
            ],

            // локальный файл (Spatie Media Library коллекция videos)
            'video_file' => [
                Rule::requiredIf(fn () => $this->input('source_type') === 'local' && $isCreating),
                'nullable',
                'file',
                'mimes:mp4,mov,ogg,qt,webm,avi,mpeg,wmv',
                'max:204800',
            ],

            // views есть в миграции, но обычно системное; оставляю как у тебя
            'views' => ['nullable', 'integer', 'min:0'],

            'meta_title'    => ['nullable', 'string', 'max:255'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_desc'     => ['nullable', 'string'],

            /**
             * ВАЖНО:
             * - sections УДАЛЕНЫ (их нет)
             * - articles УДАЛЕНЫ (привязка видео управляется со стороны статьи)
             */

            // related videos (vue-multiselect)
            'related_videos' => ['nullable', 'array'],
            'related_videos.*.id' => [
                'required_with:related_videos',
                'integer',
                'exists:videos,id',
                // запрет "сам на себя"
                Rule::when($videoId, ['not_in:' . $videoId]),
            ],

            // ---------- images (НЕ ТРОГАЮ ЛОГИКУ) ----------
            'images' => ['nullable', 'array'],
            'images.*.id' => [
                'nullable',
                'integer',
                Rule::exists('video_images', 'id'),
                Rule::prohibitedIf(fn () => $isCreating),
            ],
            'images.*.order'   => ['nullable', 'integer', 'min:0'],
            'images.*.alt'     => ['nullable', 'string', 'max:255'],
            'images.*.caption' => ['nullable', 'string', 'max:255'],
            'images.*.file' => [
                'nullable',
                'file',
                'image',
                'mimes:jpeg,jpg,png,gif,svg,webp',
                'max:10240',
                'required_without:images.*.id',
            ],

            'deletedImages'   => ['sometimes', 'array'],
            'deletedImages.*' => ['integer', 'exists:video_images,id'],
        ];
    }

    public function messages(): array
    {
        return Lang::get('admin/requests');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'activity' => filter_var($this->input('activity'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'is_private' => filter_var($this->input('is_private'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'left'     => filter_var($this->input('left'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'main'     => filter_var($this->input('main'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'right'    => filter_var($this->input('right'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,

            'locale' => is_string($this->input('locale')) ? trim($this->input('locale')) : $this->input('locale'),
            'title'  => is_string($this->input('title')) ? trim($this->input('title')) : $this->input('title'),
            'url'    => is_string($this->input('url')) ? trim($this->input('url')) : $this->input('url'),
        ]);

        // Нормализация: чистим поля по source_type (как у тебя, но без video_url)
        if ($this->input('source_type') !== 'code') {
            $this->merge(['embed_code' => null]);
        }

        if (! in_array($this->input('source_type'), ['youtube', 'vimeo'], true)) {
            $this->merge(['external_video_id' => null]);
        }

        if ($this->input('source_type') !== 'local') {
            // video_file — это upload input, но чистить можно безопасно (не обяз.)
            $this->merge(['video_file' => null]);
        }
    }
}
