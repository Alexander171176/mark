<?php

namespace App\Http\Resources\Admin\School\Review;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,

            // автор
            'user_id'         => $this->user_id,
            'user'            => $this->whenLoaded('user', fn () => [
                'id'    => $this->user->id,
                'name'  => $this->user->name ?? null,
                'email' => $this->user->email ?? null,
            ]),

            // полиморфная цель
            'reviewable_type' => $this->reviewable_type,
            'reviewable_id'   => $this->reviewable_id,
            'reviewable'      => $this->whenLoaded('reviewable', function () {
                $data = [
                    'type' => class_basename($this->reviewable_type),
                    'id'   => $this->reviewable?->getKey(),
                ];
                // по возможности отдадим витрину
                foreach (['title', 'name', 'slug'] as $field) {
                    if (isset($this->reviewable->{$field})) {
                        $data[$field] = $this->reviewable->{$field};
                    }
                }
                return $data;
            }),

            // оценка и текст
            'rating'          => (int) $this->rating,
            'title'           => $this->title,
            'body'            => $this->body,

            // публикация/модерация
            'status'          => $this->status,
            'is_public'       => (bool) $this->is_public,
            'published_at'    => optional($this->published_at)?->toIso8601String(),

            // счётчики
            'helpful_count'   => (int) $this->helpful_count,
            'reported_count'  => (int) $this->reported_count,

            // метаданные
            'meta'            => $this->meta,

            // системное
            'created_at'      => optional($this->created_at)?->toIso8601String(),
            'updated_at'      => optional($this->updated_at)?->toIso8601String(),
        ];
    }
}
