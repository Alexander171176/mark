<?php

namespace App\Http\Resources\Admin\School\QaMessage;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QaMessageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,

            'thread_id'     => $this->thread_id,
            'thread'        => $this->whenLoaded('thread', fn () => [
                'id'               => $this->thread->id,
                'title'            => $this->thread->title,
                'status'           => $this->thread->status,
                'is_locked'        => (bool) $this->thread->is_locked,
                'last_activity_at' => optional($this->thread->last_activity_at)?->toIso8601String(),
            ]),

            'user_id'       => $this->user_id,
            'user'          => $this->whenLoaded('user', fn () => [
                'id'    => $this->user->id,
                'name'  => $this->user->name ?? null,
                'email' => $this->user->email ?? null,
            ]),

            'parent_id'     => $this->parent_id,
            'parent'        => $this->whenLoaded('parent', fn () => [
                'id'         => $this->parent->id,
                'user_id'    => $this->parent->user_id,
                'body'       => $this->parent->body,
                'created_at' => optional($this->parent->created_at)?->toIso8601String(),
            ]),

            'body'          => $this->body,

            'is_private'    => (bool) $this->is_private,
            'is_pinned'     => (bool) $this->is_pinned,

            'replies_count' => (int) $this->replies_count,
            'edited_at'     => optional($this->edited_at)?->toIso8601String(),
            'meta'          => $this->meta,

            'created_at'    => optional($this->created_at)?->toIso8601String(),
            'updated_at'    => optional($this->updated_at)?->toIso8601String(),

            // Если нужны дочерние ответы — просто предварительно подгрузи relation replies
            'replies'       => $this->whenLoaded('replies', fn () =>
            $this->replies->map(fn ($m) => [
                'id'         => $m->id,
                'user_id'    => $m->user_id,
                'body'       => $m->body,
                'created_at' => optional($m->created_at)?->toIso8601String(),
            ])
            ),
        ];
    }
}
