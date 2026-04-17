<?php

namespace App\Http\Resources\Admin\Crm\Lead;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
{
    /**
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,

            // Контакты
            'name'            => $this->name,
            'email'           => $this->email,
            'phone'           => $this->phone,

            // Сообщение и источник
            'message'         => $this->message,
            'source'          => $this->source,
            'page_url'        => $this->page_url,
            'referrer'        => $this->referrer,

            // UTM
            'utm' => [
                'source'   => $this->utm_source,
                'medium'   => $this->utm_medium,
                'campaign' => $this->utm_campaign,
                'term'     => $this->utm_term,
                'content'  => $this->utm_content,
            ],

            // Тех.инфо
            'ip'              => $this->ip,
            'user_agent'      => $this->user_agent,
            'consent'         => (bool) $this->consent,

            // Статус/обработка
            'status'          => $this->status,
            'processed_at'    => optional($this->processed_at)?->toIso8601String(),

            // Ответственный
            'manager_id'      => $this->manager_id,
            'manager'         => $this->whenLoaded('manager', function () {
                return [
                    'id'    => $this->manager->id,
                    'name'  => $this->manager->name ?? null,
                    'email' => $this->manager->email ?? null,
                ];
            }),

            // Примечания/метаданные
            'notes'           => $this->notes,
            'meta'            => $this->meta,

            // Служебное
            'created_at'      => optional($this->created_at)?->toIso8601String(),
            'updated_at'      => optional($this->updated_at)?->toIso8601String(),
            'deleted_at'      => optional($this->deleted_at)?->toIso8601String(),
        ];
    }
}
