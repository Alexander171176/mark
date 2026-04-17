<?php

namespace App\Http\Resources\Admin\Constructor\HomePage\Reason;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReasonSectionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'locale'   => (string) $this->locale,

            // Заголовки
            'subtitle' => $this->subtitle,    // "You'll love this product"
            'title'    => $this->title,       // "3 Reasons to choose Vulk"

            // CTA
            'cta_title'      => $this->cta_title,
            'cta_btn_text'   => $this->cta_btn_text,
            'cta_btn_url'    => $this->cta_btn_url,
            'cta_btn_target' => $this->cta_btn_target ?? '_self',

            // Технические
            'sort'     => (int) ($this->sort ?? 0),
            'activity' => (bool) ($this->activity ?? true),

            // Элементы (если заранее подгружены ->with('items') или ->load('items'))
            'items'    => ReasonItemResource::collection(
                $this->whenLoaded('items')
            ),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
