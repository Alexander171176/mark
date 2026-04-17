<?php

namespace App\Http\Resources\Admin\School\Testimonial;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
{
    /**
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,

            'quote'        => $this->quote,
            'author_name'  => $this->author_name,
            'author_title' => $this->author_title,
            'company'      => $this->company,

            'avatar_url'   => $this->avatar_url,
            'source_url'   => $this->source_url,

            'rating'       => $this->rating,
            'activity'     => $this->activity,
            'sort'         => $this->sort,
            'locale'       => $this->locale,
            'meta'         => $this->meta,

            'created_at'   => optional($this->created_at)?->toIso8601String(),
            'updated_at'   => optional($this->updated_at)?->toIso8601String(),
            'deleted_at'   => optional($this->deleted_at)?->toIso8601String(),
        ];
    }
}
