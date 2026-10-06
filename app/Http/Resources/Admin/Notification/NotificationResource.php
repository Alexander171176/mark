<?php

namespace App\Http\Resources\Admin\Notification;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Преобразование уведомления
     * в универсальный формат PulsarCMS.
     */
    public function toArray(Request $request): array
    {
        $data = $this->data ?? [];

        /**
         * Получатель может быть полиморфной сущностью.
         *
         * На текущем этапе это User, но инфраструктура
         * уведомлений не должна быть жёстко привязана
         * только к модели пользователя.
         */
        $notifiable = $this->relationLoaded('notifiable')
            ? $this->notifiable
            : null;

        return [
            'id' => $this->id,

            'category' =>
                $data['category'] ?? 'system',

            'type' =>
                $data['type'] ?? null,

            'level' =>
                $data['level'] ?? 'info',

            'title' =>
                $data['title'] ?? '',

            'message' =>
                $data['message'] ?? '',

            'entity_type' =>
                $data['entity_type'] ?? null,

            'entity_id' =>
                $data['entity_id'] ?? null,

            'url' =>
                $data['url'] ?? null,

            'data' =>
                $data['data'] ?? [],

            /**
             * Получатель уведомления.
             *
             * Используется прежде всего суперпользователем
             * при просмотре общего центра уведомлений.
             */
            'notifiable' => [
                'type' => $this->notifiable_type,
                'id' => $this->notifiable_id,

                /**
                 * Дополнительные данные передаются только
                 * если relation notifiable был загружен.
                 *
                 * Это сохраняет универсальность ресурса
                 * для разных типов notifiable-моделей.
                 */
                'name' =>
                    $notifiable?->name,

                'email' =>
                    $notifiable?->email,
            ],

            /**
             * Принадлежит ли уведомление
             * текущему авторизованному пользователю.
             *
             * Admin может видеть чужие уведомления,
             * но изменять их состояние не должен.
             */
            'is_own' =>
                $request->user()?->getMorphClass() === $this->notifiable_type
                && (string) $request->user()?->getKey() === (string) $this->notifiable_id,

            'read_at' =>
                $this->read_at?->toISOString(),

            'is_read' =>
                $this->read_at !== null,

            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
