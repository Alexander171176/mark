<?php

namespace App\Http\Resources\Admin\Constructor\NavigationItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NavigationItemResource extends JsonResource
{
    /**
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'menu_id'    => $this->menu_id,
            'parent_id'  => $this->parent_id,
            'title'      => $this->title,
            'type'       => $this->type,           // custom|internal|route
            'url'        => $this->url,
            'route_name' => $this->route_name,
            'route_params' => $this->route_params,
            'target'     => $this->target,         // _self|_blank
            'icon'       => $this->icon,
            'activity'   => $this->activity,
            'sort'       => $this->sort,
            'meta'       => $this->meta,

            // Удобные вычисляемые поля из модели
            'href'         => $this->href,
            'is_clickable' => $this->is_clickable,

            // Вложенные пункты (если подгружены через ->with('children'))
            'children' => $this->whenLoaded('children', fn () =>
            NavigationItemResource::collection($this->children)
            ),

            'created_at' => optional($this->created_at)?->toIso8601String(),
            'updated_at' => optional($this->updated_at)?->toIso8601String(),
            'deleted_at' => optional($this->deleted_at)?->toIso8601String(),
        ];
    }
}
