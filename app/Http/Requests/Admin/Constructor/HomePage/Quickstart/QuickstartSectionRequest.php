<?php

namespace App\Http\Requests\Admin\Constructor\HomePage\Quickstart;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class QuickstartSectionRequest extends FormRequest
{
    /**
     * Разрешаем доступ (контроль прав — на уровне роутов/политик).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // Базовые поля
            'locale'    => ['required_without_all:poster,video', 'string', 'size:2'],
            'title'     => ['nullable', 'string', 'max:255'],
            'subtitle'  => ['nullable', 'string', 'max:255'],

            // Primary CTA
            'primary_label' => ['nullable', 'string', 'max:255'],
            'primary_url'   => ['nullable', 'url', 'max:2048'],
            'primary_icon'  => ['nullable', 'string'], // может содержать SVG/HTML

            // Secondary CTA (+popover)
            'secondary_label'           => ['nullable', 'string', 'max:255'],
            'secondary_url'             => ['nullable', 'url', 'max:2048'],
            'secondary_icon'            => ['nullable', 'string'],
            'secondary_popover_enabled' => ['sometimes', 'boolean'],
            'secondary_popover_title'   => ['nullable', 'string', 'max:255', 'required_if:secondary_popover_enabled,1'],
            'secondary_popover_text'    => ['nullable', 'string', 'required_if:secondary_popover_enabled,1'],

            // Видео/постер (файлы для Spatie)
            // Постер — изображение (можно svg), 5 МБ
            'poster' => [
                'nullable',
                'file',
                'mimetypes:image/jpeg,image/png,image/webp,image/svg+xml',
                'max:5120', // КБ
            ],
            // Видео — mp4/webm/ogg, по умолчанию 500 МБ
            'video' => [
                'nullable',
                'file',
                'mimetypes:video/mp4,video/webm,video/ogg',
                'max:512000', // КБ
            ],

            // Мета к видео
            'video_alt'     => ['nullable', 'string', 'max:255'],
            'video_caption' => ['nullable', 'string', 'max:255'],

            // Опции плеера
            'video_options'              => ['nullable', 'array'],
            'video_options.autoplay'     => ['sometimes', 'boolean'],
            'video_options.muted'        => ['sometimes', 'boolean'],
            'video_options.loop'         => ['sometimes', 'boolean'],
            'video_options.controls'     => ['sometimes', 'boolean'],
            // при необходимости добавляй здесь другие опции (preload, playsinline и т.п.)

            // Флаги / сортировка
            'sort'     => ['nullable', 'integer', 'between:-2147483648,2147483647'],
            'is_dark'  => ['sometimes', 'boolean'],
            'activity' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Сообщения об ошибках.
     */
    public function messages(): array
    {
        return [
            'locale.required' => 'Локаль обязательна.',
            'locale.size'     => 'Локаль должна состоять из 2 символов.',
            'locale.in'       => 'Выберите доступную локаль: ru или en.',

            'title.max'       => 'Заголовок не должен превышать :max символов.',
            'subtitle.max'    => 'Подзаголовок не должен превышать :max символов.',

            'primary_label.max' => 'Текст основной кнопки не должен превышать :max символов.',
            'primary_url.url'   => 'Укажите корректный URL для основной кнопки.',
            'primary_url.max'   => 'URL основной кнопки слишком длинный.',
            'primary_icon.string' => 'Иконка основной кнопки должна быть строкой (например, SVG).',

            'secondary_label.max' => 'Текст дополнительной кнопки не должен превышать :max символов.',
            'secondary_url.url'   => 'Укажите корректный URL для дополнительной кнопки.',
            'secondary_url.max'   => 'URL дополнительной кнопки слишком длинный.',
            'secondary_icon.string' => 'Иконка дополнительной кнопки должна быть строкой.',
            'secondary_popover_enabled.boolean' => 'Флаг поповера должен быть логическим значением.',
            'secondary_popover_title.required_if' => 'Заголовок поповера обязателен, когда поповер включён.',
            'secondary_popover_title.max' => 'Заголовок поповера не должен превышать :max символов.',
            'secondary_popover_text.required_if' => 'Текст поповера обязателен, когда поповер включён.',

            'poster.file'      => 'Постер должен быть файлом.',
            'poster.mimetypes' => 'Постер должен быть изображением (jpeg, png, webp или svg).',
            'poster.max'       => 'Размер постера не должен превышать :max КБ.',

            'video.file'       => 'Видео должно быть файлом.',
            'video.mimetypes'  => 'Поддерживаются видео форматы: mp4, webm или ogg.',
            'video.max'        => 'Размер видео не должен превышать :max КБ.',

            'video_alt.max'     => 'Alt к видео не должен превышать :max символов.',
            'video_caption.max' => 'Подпись к видео не должна превышать :max символов.',

            'video_options.array'          => 'Опции видео должны быть массивом.',
            'video_options.*.boolean'      => 'Опция :attribute должна быть логическим значением.',

            'sort.integer' => 'Сортировка должна быть целым числом.',
            'is_dark.boolean'  => 'Поле «Тёмная тема» должно быть логическим значением.',
            'activity.boolean' => 'Поле «Активность» должно быть логическим значением.',
        ];
    }

    /**
     * Человекочитаемые имена атрибутов.
     */
    public function attributes(): array
    {
        return [
            'locale'  => 'локаль',
            'title'   => 'заголовок',
            'subtitle'=> 'подзаголовок',

            'primary_label' => 'текст основной кнопки',
            'primary_url'   => 'URL основной кнопки',
            'primary_icon'  => 'иконка основной кнопки',

            'secondary_label'           => 'текст дополнительной кнопки',
            'secondary_url'             => 'URL дополнительной кнопки',
            'secondary_icon'            => 'иконка дополнительной кнопки',
            'secondary_popover_enabled' => 'включить поповер',
            'secondary_popover_title'   => 'заголовок поповера',
            'secondary_popover_text'    => 'текст поповера',

            'poster'        => 'постер',
            'video'         => 'видео',
            'video_alt'     => 'alt к видео',
            'video_caption' => 'подпись к видео',

            'video_options'             => 'опции видео',
            'video_options.autoplay'    => 'автовоспроизведение',
            'video_options.muted'       => 'без звука',
            'video_options.loop'        => 'зациклить',
            'video_options.controls'    => 'контролы',

            'sort'     => 'сортировка',
            'is_dark'  => 'тёмная тема',
            'activity' => 'активность',
        ];
    }
}
