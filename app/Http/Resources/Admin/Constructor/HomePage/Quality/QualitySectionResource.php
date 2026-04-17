<?php

namespace App\Http\Resources\Admin\Constructor\HomePage\Quality;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QualitySectionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Подстраховка по названиям коллекций (screenshot_* или просто light/dark)
        $light = $this->getFirstMedia('screenshot_light') ?? $this->getFirstMedia('light');
        $dark  = $this->getFirstMedia('screenshot_dark')  ?? $this->getFirstMedia('dark');

        $hasConv = static function ($media, string $name): bool {
            return $media
                && method_exists($media, 'hasGeneratedConversion')
                && $media->hasGeneratedConversion($name);
        };

        return [
            'id'         => $this->id,
            'locale'     => (string) ($this->locale ?? 'en'),
            'subtitle'   => $this->subtitle,
            'title'      => $this->title,
            'screenshot_alt' => $this->screenshot_alt,

            // Управление отображением (если есть такие поля в миграции/модели)
            'sort'       => (int) ($this->sort ?? 0),
            'is_dark'    => (bool) ($this->is_dark ?? false),
            'activity'   => (bool) ($this->activity ?? true),

            // Скриншоты секции (Spatie)
            'light_url'       => $light ? url($light->getUrl()) : null,
            'light_webp_url'  => $hasConv($light, 'webp')  ? url($light->getUrl('webp'))  : null,
            'light_thumb_url' => $hasConv($light, 'thumb') ? url($light->getUrl('thumb')) : null,

            'dark_url'        => $dark ? url($dark->getUrl()) : null,
            'dark_webp_url'   => $hasConv($dark, 'webp')   ? url($dark->getUrl('webp'))   : null,
            'dark_thumb_url'  => $hasConv($dark, 'thumb')  ? url($dark->getUrl('thumb'))  : null,

            // Вложенные элементы (если загружены)
            'items' => QualityItemResource::collection(
                $this->whenLoaded('items')
            ),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
