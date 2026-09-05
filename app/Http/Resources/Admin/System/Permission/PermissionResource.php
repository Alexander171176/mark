<?php

namespace App\Http\Resources\Admin\System\Permission;

use App\Http\Resources\Admin\System\Role\RoleSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Полное представление разрешения.
     *
     * Основное назначение:
     * - Edit;
     * - детальные страницы;
     * - места, где нужны связанные роли.
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
            'guard_name' => $this->guard_name,

            /** Счётчики */
            'roles_count' => $this->whenCounted('roles'),

            /** Полные связанные данные */
            'roles' => RoleSharedResource::collection(
                $this->whenLoaded('roles')
            ),

            /** Даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
