<?php

namespace App\Http\Resources\Admin\School\Bookmark;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolBookmarkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'user_id' => $this->user_id,

            'bookmarkable_type' => $this->bookmarkable_type,
            'bookmarkable_id' => $this->bookmarkable_id,

            'is_favorite' => (bool) $this->is_favorite,
            'folder' => $this->folder,
            'position' => (int) $this->position,
            'note' => $this->note,
            'meta' => $this->meta,

            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),

            'bookmarkable' => $this->whenLoaded('bookmarkable', function () {
                if (! $this->bookmarkable) {
                    return null;
                }

                return [
                    'type' => class_basename($this->bookmarkable_type),
                    'id' => $this->bookmarkable->getKey(),
                    'title' => $this->bookmarkable->translation?->title
                        ?? $this->bookmarkable->translation?->name
                            ?? $this->bookmarkable->title
                            ?? $this->bookmarkable->name
                            ?? null,
                    'slug' => $this->bookmarkable->slug ?? null,
                ];
            }),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
