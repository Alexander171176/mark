<?php

namespace App\Http\Resources\Admin\System\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSharedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'profile_photo_url' => $this->profile_photo_url, // Добавим фото

            // ✅ Массив имён ролей
            'roles' => $this->whenLoaded('roles', fn () => $this->getRoleNames()->values()->all()),

            // ✅ Массив имён прямых permissions (не обязательный)
            'permissions' => $this->whenLoaded('permissions', fn () => $this->getPermissionNames()->values()->all()),
        ];
    }
}
