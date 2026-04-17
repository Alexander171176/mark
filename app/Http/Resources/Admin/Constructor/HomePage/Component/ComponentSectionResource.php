<?php

namespace App\Http\Resources\Admin\Constructor\HomePage\Component;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComponentSectionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'locale'    => (string) ($this->locale ?? 'ru'),
            'subtitle'  => $this->subtitle,
            'title'     => $this->title,

            // CTA под заголовком
            'cta_text'  => $this->cta_text,
            'cta_url'   => $this->cta_url ?? '/blocks',

            // Управление отображением
            'sort'      => (int) ($this->sort ?? 0),
            'activity'  => (bool) ($this->activity ?? true),

            // Вложенные ресурсы (подгружайте через ->with() в контроллере)
            'features'  => ComponentFeatureResource::collection(
                $this->whenLoaded('features')
            ),
            'tabs'      => ComponentTabResource::collection(
                $this->whenLoaded('tabs')
            ),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
