<?php

namespace App\Http\Resources\Admin\Constructor\HomePage\Quickstart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuickstartSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Если связь 'media' подгружена — получим объекты для расширенного ответа
        $poster = $this->relationLoaded('media') ? $this->getFirstMedia('poster') : null;
        $video  = $this->relationLoaded('media') ? $this->getFirstMedia('video')  : null;

        return [
            'id'                        => $this->id,
            'locale'                    => (string) $this->locale,
            'title'                     => $this->title,
            'subtitle'                  => $this->subtitle,

            // Primary CTA
            'primary_label'             => $this->primary_label,
            'primary_url'               => $this->primary_url,
            'primary_icon'              => $this->primary_icon,

            // Secondary CTA (+popover)
            'secondary_label'           => $this->secondary_label,
            'secondary_url'             => $this->secondary_url,
            'secondary_icon'            => $this->secondary_icon,
            'secondary_popover_enabled' => (bool) $this->secondary_popover_enabled,
            'secondary_popover_title'   => $this->secondary_popover_title,
            'secondary_popover_text'    => $this->secondary_popover_text,

            // Video / Poster content
            'video_alt'                 => $this->video_alt,
            'video_caption'             => $this->video_caption,
            'video_options'             => $this->video_options ?? [],

            // Флаги / сортировка
            'sort'                      => (int) $this->sort,
            'is_dark'                   => (bool) $this->is_dark,
            'activity'                  => (bool) $this->activity,

            // Удобные ссылки через аксессоры модели (не требуют eager load)
            'video_url'                 => $this->video_url,   // null если нет
            'poster_url'                => $this->poster_url,  // null если нет
            // Если в модели добавлены аксессоры/конверсии — тоже отдадим
            'poster_thumb_url'          => $this->poster_thumb_url ?? null,
            'poster_webp_url'           => $this->poster_webp_url ?? null,

            // Полные медиа-объекты (отдадим только если связь media загружена)
            'video_media'  => $video ? [
                'id'         => $video->id,
                'file_name'  => $video->file_name,
                'mime_type'  => $video->mime_type,
                'size'       => $video->size,
                'url'        => $video->getUrl(),
                'full_url'   => $video->getFullUrl(),
                'disk'       => $video->disk,
                'custom'     => $video->custom_properties ?? [],
            ] : null,

            'poster_media' => $poster ? [
                'id'         => $poster->id,
                'file_name'  => $poster->file_name,
                'mime_type'  => $poster->mime_type,
                'size'       => $poster->size,
                'url'        => $poster->getUrl(),
                'full_url'   => $poster->getFullUrl(),
                'disk'       => $poster->disk,
                'custom'     => $poster->custom_properties ?? [],
                'conversions'=> [
                    // вернём только если реально сгенерировано
                    'thumb' => method_exists($poster, 'hasGeneratedConversion') && $poster->hasGeneratedConversion('thumb')
                        ? $poster->getUrl('thumb') : null,
                    'webp'  => method_exists($poster, 'hasGeneratedConversion') && $poster->hasGeneratedConversion('webp')
                        ? $poster->getUrl('webp') : null,
                ],
            ] : null,

            // Даты
            'created_at'                => optional($this->created_at)->toISOString(),
            'updated_at'                => optional($this->updated_at)->toISOString(),
        ];
    }
}
