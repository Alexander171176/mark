<?php

namespace App\Http\Resources\Admin\System\User;

use App\Http\Resources\Admin\System\Permission\PermissionResource;
use App\Http\Resources\Admin\System\Role\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Полное представление пользователя.
     *
     * Основное назначение:
     * - Edit;
     * - детальные страницы;
     * - места, где нужны полные данные ролей и прямых разрешений.
     *
     * Resource читает только заранее загруженные relations/counts
     * и не должен создавать дополнительные SQL-запросы.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /** Основные данные */
            'id' => (int) $this->id,
            'name' => $this->name,
            'email' => $this->email,

            /** Верификация */
            'email_verified_at' => $this->email_verified_at?->toISOString(),

            /** Фото профиля */
            'profile_photo_url' => $this->profile_photo_url,

            /** Счётчики */
            'roles_count' => $this->whenCounted('roles'),
            'permissions_count' => $this->whenCounted('permissions'),

            /** Полные связанные сущности */
            'roles' => RoleResource::collection(
                $this->whenLoaded('roles')
            ),

            'permissions' => PermissionResource::collection(
                $this->whenLoaded('permissions')
            ),

            /** Даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
