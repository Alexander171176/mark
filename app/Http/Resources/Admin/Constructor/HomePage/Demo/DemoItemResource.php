<?php

namespace App\Http\Resources\Admin\Constructor\HomePage\Demo;

use App\Models\Admin\Constructor\HomePage\Demo\DemoItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DemoItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $light = $this->getFirstMedia(DemoItem::MEDIA_COLLECTION_LIGHT);
        $dark  = $this->getFirstMedia(DemoItem::MEDIA_COLLECTION_DARK);

        return [
            'id'        => $this->id,
            'group_id'  => $this->group_id,
            'href'      => $this->href,
            'title'     => $this->title,
            'category'  => $this->category,
            'alt'       => $this->alt,
            'sort'      => (int) $this->sort,
            'activity'  => (bool) $this->activity,

            // Светлая картинка
            'light_url'       => $light?->getUrl(),
            'light_webp_url'  => $light && method_exists($light, 'hasGeneratedConversion') && $light->hasGeneratedConversion('webp')  ? $light->getUrl('webp')  : null,
            'light_thumb_url' => $light && method_exists($light, 'hasGeneratedConversion') && $light->hasGeneratedConversion('thumb') ? $light->getUrl('thumb') : null,

            // Тёмная картинка
            'dark_url'        => $dark?->getUrl(),
            'dark_webp_url'   => $dark && method_exists($dark, 'hasGeneratedConversion') && $dark->hasGeneratedConversion('webp')   ? $dark->getUrl('webp')   : null,
            'dark_thumb_url'  => $dark && method_exists($dark, 'hasGeneratedConversion') && $dark->hasGeneratedConversion('thumb')  ? $dark->getUrl('thumb')  : null,

            // (опционально) мета, если загружена связь media
            'light_mime_type'  => $this->whenLoaded('media', fn () => $light?->mime_type),
            'light_size'       => $this->whenLoaded('media', fn () => $light?->size),
            'dark_mime_type'   => $this->whenLoaded('media', fn () => $dark?->mime_type),
            'dark_size'        => $this->whenLoaded('media', fn () => $dark?->size),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
