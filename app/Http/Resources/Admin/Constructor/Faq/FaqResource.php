<?php

namespace App\Http\Resources\Admin\Constructor\Faq;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
{
    /**
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'question'   => $this->question,
            'answer'     => $this->answer,
            'slug'       => $this->slug,
            'category'   => $this->category,
            'locale'     => $this->locale,
            'activity'   => $this->activity,
            'sort'       => $this->sort,
            'meta'       => $this->meta,

            'created_at' => optional($this->created_at)?->toIso8601String(),
            'updated_at' => optional($this->updated_at)?->toIso8601String(),
            'deleted_at' => optional($this->deleted_at)?->toIso8601String(),
        ];
    }
}
