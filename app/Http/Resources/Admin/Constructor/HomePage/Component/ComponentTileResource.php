<?php

namespace App\Http\Resources\Admin\Constructor\HomePage\Component;

use App\Models\Admin\Constructor\HomePage\Component\ComponentTile as ComponentTileModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComponentTileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Медиа (используем константы ИЗ МОДЕЛИ)
        $light = $this->getFirstMedia(ComponentTileModel::MEDIA_COLLECTION_LIGHT);
        $dark  = $this->getFirstMedia(ComponentTileModel::MEDIA_COLLECTION_DARK);

        $hasConv = static function ($media, string $name): bool {
            return $media
                && method_exists($media, 'hasGeneratedConversion')
                && $media->hasGeneratedConversion($name);
        };

        // Оборачиваем в url() для абсолютных путей
        $abs = static fn($u) => $u ? url($u) : null;

        return [
            'id'        => $this->id,
            'tab_id'    => (int) $this->tab_id,
            'href'      => $this->href,
            'title'     => $this->title,
            'light_alt' => $this->light_alt,
            'dark_alt'  => $this->dark_alt,
            'sort'      => (int) ($this->sort ?? 0),
            'activity'  => (bool) ($this->activity ?? true),

            // Медиа URL
            'light_url'       => $abs($light?->getUrl()),
            'light_webp_url'  => $hasConv($light, 'webp')  ? $abs($light->getUrl('webp'))  : null,
            'light_thumb_url' => $hasConv($light, 'thumb') ? $abs($light->getUrl('thumb')) : null,

            'dark_url'        => $abs($dark?->getUrl()),
            'dark_webp_url'   => $hasConv($dark, 'webp')   ? $abs($dark->getUrl('webp'))   : null,
            'dark_thumb_url'  => $hasConv($dark, 'thumb')  ? $abs($dark->getUrl('thumb'))  : null,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
