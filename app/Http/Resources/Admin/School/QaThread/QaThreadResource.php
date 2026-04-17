<?php

namespace App\Http\Resources\Admin\School\QaThread;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QaThreadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,

            // автор
            'user_id'          => $this->user_id,
            'user'             => $this->whenLoaded('user', fn () => [
                'id'    => $this->user->id,
                'name'  => $this->user->name ?? null,
                'email' => $this->user->email ?? null,
            ]),

            // полиморфная цель
            'threadable_type'  => $this->threadable_type,
            'threadable_id'    => $this->threadable_id,
            'threadable'       => $this->whenLoaded('threadable', function () {
                $data = [
                    'type' => class_basename($this->threadable_type),
                    'id'   => $this->threadable?->getKey(),
                ];
                foreach (['title','name','slug'] as $k) {
                    if (isset($this->threadable->{$k})) {
                        $data[$k] = $this->threadable->{$k};
                    }
                }
                return $data;
            }),

            // контент
            'title'            => $this->title,
            'body'             => $this->body,

            // статус/флаги
            'status'           => $this->status,               // open|closed|archived
            'is_locked'        => (bool) $this->is_locked,
            'is_pinned'        => (bool) $this->is_pinned,

            // метрики
            'replies_count'    => (int) $this->replies_count,
            'last_reply_at'    => optional($this->last_reply_at)?->toIso8601String(),
            'last_activity_at' => optional($this->last_activity_at)?->toIso8601String(),

            // метаданные
            'meta'             => $this->meta,

            // системное
            'created_at'       => optional($this->created_at)?->toIso8601String(),
            'updated_at'       => optional($this->updated_at)?->toIso8601String(),

            // при необходимости можно отдать сообщения (если загружены)
            'messages'         => $this->whenLoaded('messages', fn () =>
            $this->messages->map(function ($m) {
                return [
                    'id'         => $m->id,
                    'user_id'    => $m->user_id,
                    'body'       => $m->body,
                    'created_at' => optional($m->created_at)?->toIso8601String(),
                    'updated_at' => optional($m->updated_at)?->toIso8601String(),
                ];
            })
            ),
        ];
    }
}
