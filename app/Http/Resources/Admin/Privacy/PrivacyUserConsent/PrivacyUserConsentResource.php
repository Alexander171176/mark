<?php

namespace App\Http\Resources\Admin\Privacy\PrivacyUserConsent;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrivacyUserConsentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Основные данные
            |--------------------------------------------------------------------------
            */

            'id' => $this->id,

            /*
            |--------------------------------------------------------------------------
            | Пользователь
            |--------------------------------------------------------------------------
            */

            'user_id' => $this->user_id,
            'session_id' => $this->session_id,

            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user?->id,
                    'name' => $this->user?->name,
                    'email' => $this->user?->email,
                ];
            }),

            /*
            |--------------------------------------------------------------------------
            | Технические данные
            |--------------------------------------------------------------------------
            */

            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'locale' => $this->locale,

            /*
            |--------------------------------------------------------------------------
            | Политика конфиденциальности
            |--------------------------------------------------------------------------
            */

            'policy_version' => $this->policy_version,
            'policy_url' => $this->policy_url,
            'policy_hash' => $this->policy_hash,

            /*
            |--------------------------------------------------------------------------
            | Согласие пользователя
            |--------------------------------------------------------------------------
            */

            'accepted' => $this->accepted,
            'accepted_at' => $this->accepted_at,
            'revoked_at' => $this->revoked_at,

            /*
            |--------------------------------------------------------------------------
            | Системные данные
            |--------------------------------------------------------------------------
            */

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
