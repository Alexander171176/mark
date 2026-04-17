<?php

namespace App\Http\Resources\Admin\Constructor\NavigationMenu;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NavigationMenuResource extends JsonResource
{
    /**
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'slug'      => $this->slug,
            'location'  => $this->location,          // header|footer|sidebar|custom
            'activity' => $this->activity,
            'sort'  => $this->sort,
            'meta'      => $this->meta,

            // Вложенные пункты меню (если подгружены с ->with('items'))
            'items' => $this->whenLoaded('items', function () {
                // Если у тебя будет отдельный NavigationItemResource — замени на него.
                return $this->items->map(fn ($item) => [
                    'id'        => $item->id,
                    'parent_id' => $item->parent_id ?? null,
                    'title'     => $item->title ?? null,
                    'url'       => $item->url ?? null,
                    'target'    => $item->target ?? null,
                    'activity'  => (bool)($item->activity ?? true),
                    'sort'      => (int)($item->sort ?? 0),
                    'meta'      => $item->meta ?? null,
                ]);
            }),

            'created_at' => optional($this->created_at)?->toIso8601String(),
            'updated_at' => optional($this->updated_at)?->toIso8601String(),
            'deleted_at' => optional($this->deleted_at)?->toIso8601String(),
        ];
    }
}
