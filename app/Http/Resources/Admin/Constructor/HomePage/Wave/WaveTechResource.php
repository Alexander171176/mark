<?php

namespace App\Http\Resources\Admin\Constructor\HomePage\Wave;

use App\Models\Admin\Constructor\HomePage\Wave\WaveTech;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WaveTechResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $light = $this->getFirstMedia(WaveTech::MEDIA_COLLECTION_LIGHT);
        $dark  = $this->getFirstMedia(WaveTech::MEDIA_COLLECTION_DARK);

        // fallback на «старые» колонки (если медиа ещё нет)
        $lightUrlFallback = $this->image_light ? (str_starts_with($this->image_light, 'http') ? $this->image_light : asset($this->image_light)) : null;
        $darkUrlFallback  = $this->image_dark  ? (str_starts_with($this->image_dark, 'http')  ? $this->image_dark  : asset($this->image_dark))  : null;

        return [
            'id'              => $this->id,
            'wave_section_id' => $this->wave_section_id,
            'title'           => $this->title,
            'subtitle'        => $this->subtitle,
            'description'     => $this->description,
            'alt'             => $this->alt,
            'sort'            => (int) $this->sort,
            'activity'        => (bool) $this->activity,

            // === Ключи под фронт (как в Edit.vue) ===
            'image_light_url'       => $light?->getUrl() ?? $lightUrlFallback,
            'image_light_webp_url'  => $light && $light->hasGeneratedConversion('webp')  ? $light->getUrl('webp')  : null,
            'image_light_thumb_url' => $light && $light->hasGeneratedConversion('thumb') ? $light->getUrl('thumb') : null,

            'image_dark_url'        => $dark?->getUrl() ?? $darkUrlFallback,
            'image_dark_webp_url'   => $dark && $dark->hasGeneratedConversion('webp')   ? $dark->getUrl('webp')   : null,
            'image_dark_thumb_url'  => $dark && $dark->hasGeneratedConversion('thumb')  ? $dark->getUrl('thumb')  : null,

            // Метаданные — только если связь media подгружена
            'image_light_mime_type'   => $this->whenLoaded('media', fn () => $light?->mime_type),
            'image_light_size'        => $this->whenLoaded('media', fn () => $light?->size),
            'image_light_size_human'  => $this->whenLoaded('media', fn () => $light?->humanReadableSize),

            'image_dark_mime_type'    => $this->whenLoaded('media', fn () => $dark?->mime_type),
            'image_dark_size'         => $this->whenLoaded('media', fn () => $dark?->size),
            'image_dark_size_human'   => $this->whenLoaded('media', fn () => $dark?->humanReadableSize),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
