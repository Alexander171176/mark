<?php

namespace App\Http\Resources\Admin\System\Role;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleSharedResource extends JsonResource
{
    /**
     * Компактное представление роли.
     *
     * Основное назначение:
     * - Admin Index;
     * - select/справочные списки;
     * - связанные сущности, где не нужен полный RoleResource.
     *
     * Контракт:
     * - permissions возвращаются компактными объектами {id, name};
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
            'permissions_count' => $this->whenCounted('permissions'),

            /** Компактные разрешения */
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
