<?php

namespace App\Http\Resources\Admin\Crm\EmailSubscription;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmailSubscriptionResource extends JsonResource
{
    /**
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,

            // Пользователь (может отсутствовать)
            'user_id'    => $this->user_id,
            'user'       => $this->whenLoaded('user', function () {
                return [
                    'id'    => $this->user->id,
                    'name'  => $this->user->name ?? null,
                    'email' => $this->user->email ?? null,
                ];
            }),

            // Подписка
            'email'      => $this->email,
            'list'       => $this->list,
            'status'     => $this->status, // pending|subscribed|unsubscribed|bounced|complained

            // Подтверждение/отписка
            'confirm_token'    => $this->confirm_token,
            'confirmed_at'     => optional($this->confirmed_at)?->toIso8601String(),
            'unsubscribed_at'  => optional($this->unsubscribed_at)?->toIso8601String(),
            'unsub_reason'     => $this->unsub_reason,

            // Источник/тех.инфо
            'source'     => $this->source,
            'locale'     => $this->locale,
            'ip'         => $this->ip,
            'user_agent' => $this->user_agent,

            // Интеграции
            'provider'                => $this->provider,
            'provider_subscriber_id'  => $this->provider_subscriber_id,
            'last_event'              => $this->last_event,

            // Метки/метаданные
            'tags'       => $this->tags,
            'meta'       => $this->meta,

            // Служебные даты
            'created_at' => optional($this->created_at)?->toIso8601String(),
            'updated_at' => optional($this->updated_at)?->toIso8601String(),
            'deleted_at' => optional($this->deleted_at)?->toIso8601String(),
        ];
    }
}
