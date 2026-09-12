<?php

namespace App\Http\Resources\Admin\Analytics\AnalyticsVisitorLog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnalyticsVisitorLogSharedResource extends JsonResource
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

            'user_id' => $this->user_id,
            'visitor_uuid' => $this->visitor_uuid,

            'module' => $this->module,
            'entity_type' => $this->entity_type,
            'entity_id' => $this->entity_id,
            'event_type' => $this->event_type,

            'page_title' => $this->page_title,
            'url' => $this->url,
            'referer' => $this->referer,

            'time_on_page' => $this->time_on_page,
            'scroll_depth' => $this->scroll_depth,
            'clicks_count' => $this->clicks_count,

            'locale' => $this->locale,
            'visited_at' => $this->visited_at,
        ];
    }
}
