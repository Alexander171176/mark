<?php

namespace App\Http\Resources\Admin\Constructor\HomePage\Demo;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DemoSectionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'locale'               => (string) ($this->locale ?? 'en'),
            'title'                => $this->title,
            'subtitle'             => $this->subtitle,
            'search_placeholder'   => $this->search_placeholder,
            'sort'                 => (int) ($this->sort ?? 0),
            'is_dark'              => (bool) ($this->is_dark ?? false),
            'activity'             => (bool) ($this->activity ?? true),

            'groups'               => DemoGroupResource::collection(
                $this->whenLoaded('groups')
            ),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
