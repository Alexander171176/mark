<?php

namespace App\Http\Resources\Admin\System\Permission;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionSharedResource extends JsonResource
{
    /**
     * Компактное представление разрешения.
     *
     * Основное назначение:
     * - Admin Index;
     * - select/справочные списки;
     * - связанные сущности, где не нужен полный PermissionResource.
     *
     * Контракт:
     * - roles возвращаются компактными объектами {id, name};
     * - используются только eager-loaded relations;
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
            'guard_name' => $this->guard_name,

            /** Счётчики */
            'roles_count' => $this->whenCounted('roles'),

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

            /** Даты нужны Index для frontend-сортировки */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
