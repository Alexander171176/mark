<?php

namespace App\Http\Resources\Admin\Blog\Rubric;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RubricSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'parent_id'=> $this->parent_id,
            'level'    => $this->level,
            'in_menu'  => $this->in_menu,

            'sort'     => $this->sort,
            'activity' => $this->activity,

            'locale'   => $this->locale,
            'title'    => $this->title,
            'url'      => $this->url,
            'icon'     => $this->icon,

            'is_approved' => (int) $this->moderation_status === 1,
        ];
    }
}
