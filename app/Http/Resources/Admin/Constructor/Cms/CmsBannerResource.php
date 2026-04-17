<?php

namespace App\Http\Resources\Admin\Constructor\Cms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmsBannerResource extends JsonResource
{
    /**
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'subtitle'     => $this->subtitle,
            'placement'    => $this->placement,

            'link_type'    => $this->link_type,     // url|route|none
            'link_url'     => $this->link_url,
            'link_route'   => $this->link_route,
            'link_params'  => $this->link_params,
            'link_target'  => $this->link_target,   // _self|_blank
            'button_label' => $this->button_label,

            'starts_at'    => optional($this->starts_at)?->toIso8601String(),
            'ends_at'      => optional($this->ends_at)?->toIso8601String(),

            'activity'     => $this->activity,
            'sort'         => $this->sort,
            'meta'         => $this->meta,

            // Удобные вычисляемые поля
            'href'         => $this->href,
            'is_clickable' => $this->is_clickable,

            // Медиа из spatie/medialibrary
            'image_url'        => method_exists($this, 'getFirstMediaUrl') ? ($this->getFirstMediaUrl('image') ?: null) : null,
            'image_mobile_url' => method_exists($this, 'getFirstMediaUrl') ? ($this->getFirstMediaUrl('image_mobile') ?: null) : null,

            'created_at'   => optional($this->created_at)?->toIso8601String(),
            'updated_at'   => optional($this->updated_at)?->toIso8601String(),
            'deleted_at'   => optional($this->deleted_at)?->toIso8601String(),
        ];
    }
}
