<?php

namespace App\Http\Resources\Admin\System\Role;

use App\Http\Resources\Admin\System\Permission\PermissionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Полное представление роли.
     *
     * Основное назначение:
     * - Edit;
     * - детальные страницы;
     * - места, где нужны полные данные разрешений.
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
            'permissions_count' => $this->whenCounted('permissions'),

            /** Полные связанные сущности */
            'permissions' => PermissionResource::collection(
                $this->whenLoaded('permissions')
            ),

            /** Даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
