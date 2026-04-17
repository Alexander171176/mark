<?php

namespace App\Http\Resources\Admin\Blog\Tag;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'locale'   => $this->locale,
            'name'     => $this->name,
            'slug'     => $this->slug,
            'activity' => $this->activity,
            'sort'     => $this->sort,
            'icon'     => $this->icon,
        ];
    }
}
