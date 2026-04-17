<?php

namespace App\Http\Resources\Admin\School\Bookmark;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookmarkResource extends JsonResource
{
    /**
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,

            'user_id'         => $this->user_id,
            'user'            => $this->whenLoaded('user', fn () => [
                'id'    => $this->user->id,
                'name'  => $this->user->name ?? null,
                'email' => $this->user->email ?? null,
            ]),

            'bookmarkable_type' => $this->bookmarkable_type,
            'bookmarkable_id'   => $this->bookmarkable_id,
            'bookmarkable'      => $this->whenLoaded('bookmarkable', function () {
                $model = $this->bookmarkable;
                return [
                    'type'       => class_basename($this->bookmarkable_type),
                    'id'         => $model?->getKey(),
                    // Популярные поля-ярлыки, если есть
                    'title'      => $model?->getAttribute('title'),
                    'name'       => $model?->getAttribute('name'),
                    'slug'       => $model?->getAttribute('slug'),
                ];
            }),

            'is_favorite'     => (bool) $this->is_favorite,
            'folder'          => $this->folder,
            'position'        => (int) $this->position,
            'note'            => $this->note,
            'meta'            => $this->meta,

            'created_at'      => optional($this->created_at)?->toIso8601String(),
            'updated_at'      => optional($this->updated_at)?->toIso8601String(),
            'deleted_at'      => optional($this->deleted_at)?->toIso8601String(),
        ];
    }
}
