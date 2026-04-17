<?php

namespace App\Http\Resources\Admin\Constructor\HomePage\Hero;

use App\Models\Admin\Constructor\HomePage\Hero\HeroScreenshot;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HeroScreenshotResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $light = $this->getFirstMedia(HeroScreenshot::MEDIA_COLLECTION_LIGHT);
        $dark  = $this->getFirstMedia(HeroScreenshot::MEDIA_COLLECTION_DARK);

        return [
            'id'              => $this->id,
            'hero_section_id' => $this->hero_section_id,
            'alt'             => $this->alt,
            'sort'            => (int) $this->sort,
            'activity'        => (bool) $this->activity,

            // === Плоские поля под Vue ===
            'light_url'       => $light?->getUrl(),
            'light_webp_url'  => $light && $light->hasGeneratedConversion('webp')  ? $light->getUrl('webp')  : null,
            'light_thumb_url' => $light && $light->hasGeneratedConversion('thumb') ? $light->getUrl('thumb') : null,

            'dark_url'        => $dark?->getUrl(),
            'dark_webp_url'   => $dark && $dark->hasGeneratedConversion('webp')   ? $dark->getUrl('webp')   : null,
            'dark_thumb_url'  => $dark && $dark->hasGeneratedConversion('thumb')  ? $dark->getUrl('thumb')  : null,

            // (опционально) мета — только если подгружена связь media
            'light_mime_type'   => $this->whenLoaded('media', fn () => $light?->mime_type),
            'light_size'        => $this->whenLoaded('media', fn () => $light?->size),
            'light_size_human'  => $this->whenLoaded('media', fn () => $light?->humanReadableSize),

            'dark_mime_type'    => $this->whenLoaded('media', fn () => $dark?->mime_type),
            'dark_size'         => $this->whenLoaded('media', fn () => $dark?->size),
            'dark_size_human'   => $this->whenLoaded('media', fn () => $dark?->humanReadableSize),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
