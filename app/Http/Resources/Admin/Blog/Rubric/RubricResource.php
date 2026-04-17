<?php

namespace App\Http\Resources\Admin\Blog\Rubric;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RubricResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'user_id'  => $this->user_id,

            // tree
            'parent_id' => $this->parent_id,
            'level'     => $this->level,
            'in_menu'   => $this->in_menu,

            // flags
            'sort'     => $this->sort,
            'activity' => $this->activity,

            // moderation
            'moderation_status' => $this->moderation_status, // 0/1/2
            'is_approved'       => (int) $this->moderation_status === 1,
            'moderated_by'      => $this->moderated_by,
            'moderated_at'      => $this->moderated_at?->toISOString(),
            'moderation_note'   => $this->moderation_note,

            // content
            'icon'        => $this->icon,
            'locale'      => $this->locale,
            'title'       => $this->title,
            'url'         => $this->url,
            'subtitle'    => $this->subtitle,
            'short'       => $this->short,
            'description' => $this->description,
            'views'       => $this->views,

            // seo
            'meta_title'    => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_desc'     => $this->meta_desc,

            // relations (только если подгружены)
            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'id'    => $this->owner?->id,
                    'name'  => $this->owner?->name,
                    'email' => $this->owner?->email,
                    'profile_photo_url' => $this->owner?->profile_photo_url, // ✅ Jetstream
                ];
            }),

            'moderator' => $this->whenLoaded('moderator', function () {
                return [
                    'id'   => $this->moderator?->id,
                    'name' => $this->moderator?->name,
                ];
            }),

            // Изображения (если загружены)
            'images' => RubricImageResource::collection($this->whenLoaded('images')),
            'images_count' => $this->whenCounted('images'),

            // children (дерево) — если загрузил children
            'children' => RubricResource::collection($this->whenLoaded('children')),

            // количество статей принадлежащие к этой рубрики
            'articles_count' => (int) ($this->articles_count ?? 0),

            // timestamps (лучше ISO для фронта)
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
