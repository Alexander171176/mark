<?php

namespace App\Http\Resources\Admin\Analytics\AnalyticsVisitorLog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnalyticsVisitorLogResource extends JsonResource
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
            'visitor_uuid' => $this->visitor_uuid,

            'user_gender' => $this->user_gender,
            'user_age' => $this->user_age,
            'user_age_group' => $this->user_age_group,

            /*
            |--------------------------------------------------------------------------
            | Backend данные запроса
            |--------------------------------------------------------------------------
            */

            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,

            'method' => $this->method,
            'url' => $this->url,
            'page_title' => $this->page_title,
            'route_name' => $this->route_name,

            'module' => $this->module,
            'entity_type' => $this->entity_type,
            'entity_id' => $this->entity_id,
            'event_type' => $this->event_type,

            'request_type' => $this->request_type,
            'status_code' => $this->status_code,
            'response_time' => $this->response_time,

            /*
            |--------------------------------------------------------------------------
            | География
            |--------------------------------------------------------------------------
            */

            'country' => $this->country,
            'region' => $this->region,
            'city' => $this->city,

            /*
            |--------------------------------------------------------------------------
            | Устройство
            |--------------------------------------------------------------------------
            */

            'device_type' => $this->device_type,
            'device_name' => $this->device_name,

            'browser' => $this->browser,
            'browser_version' => $this->browser_version,

            'os' => $this->os,
            'os_version' => $this->os_version,

            /*
            |--------------------------------------------------------------------------
            | Frontend данные
            |--------------------------------------------------------------------------
            */

            'screen_width' => $this->screen_width,
            'screen_height' => $this->screen_height,

            'browser_language' => $this->browser_language,
            'timezone' => $this->timezone,

            /*
            |--------------------------------------------------------------------------
            | Источник посещения
            |--------------------------------------------------------------------------
            */

            'referer' => $this->referer,
            'source_type' => $this->source_type,
            'search_engine' => $this->search_engine,

            /*
            |--------------------------------------------------------------------------
            | Поведение пользователя
            |--------------------------------------------------------------------------
            */

            'time_on_page' => $this->time_on_page,
            'scroll_depth' => $this->scroll_depth,
            'clicks_count' => $this->clicks_count,

            /*
            |--------------------------------------------------------------------------
            | Системные данные
            |--------------------------------------------------------------------------
            */

            'locale' => $this->locale,
            'visited_at' => $this->visited_at,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
