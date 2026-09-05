<?php

namespace App\Http\Resources\Admin\System\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSharedResource extends JsonResource
{
    /**
     * Компактное представление пользователя.
     *
     * Основное назначение:
     * - Admin Index;
     * - select/справочные списки;
     * - связанные сущности, где не нужен полный UserResource.
     *
     * Контракт:
     * - roles и permissions возвращаются компактными объектами {id, name};
     * - используются только eager-loaded relations;
     * - никаких вызовов getRoleNames()/getPermissionNames();
     * - никаких дополнительных SQL из Resource.
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
            'profile_photo_url' => $this->profile_photo_url,

            /** Счётчики */
            'roles_count' => $this->whenCounted('roles'),
            'permissions_count' => $this->whenCounted('permissions'),

            /** Компактные роли */
            'roles' => $this->whenLoaded(
                'roles',
                fn () => $this->roles
                    ->map(fn ($role) => [
                        'id' => (int) $role->id,
                        'name' => $role->name,
                    ])
                    ->values()
                    ->all()
            ),

            /** Компактные прямые разрешения */
            'permissions' => $this->whenLoaded(
                'permissions',
                fn () => $this->permissions
                    ->map(fn ($permission) => [
                        'id' => (int) $permission->id,
                        'name' => $permission->name,
                    ])
                    ->values()
                    ->all()
            ),

            /** Даты нужны Index для frontend-сортировки */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
