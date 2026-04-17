<?php

namespace App\Http\Resources\Admin\Constructor\Cms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    /**
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,

            'author_id'      => $this->author_id,
            'author'         => $this->whenLoaded('author', fn () => [
                'id'    => $this->author->id,
                'name'  => $this->author->name ?? null,
                'email' => $this->author->email ?? null,
            ]),

            'title'          => $this->title,
            'slug'           => $this->slug,
            'excerpt'        => $this->excerpt,
            'content'        => $this->content,

            'status'         => $this->status,
            'activity'       => (bool) $this->activity,
            'published_at'   => optional($this->published_at)?->toIso8601String(),

            'cover_image_url'=> $this->cover_image_url,
            'reading_time'   => (int) $this->reading_time,
            'meta'           => $this->meta,
            'locale'         => $this->locale,

            'url'            => $this->when(method_exists($this->resource, 'getUrlAttribute'), $this->url),

            'created_at'     => optional($this->created_at)?->toIso8601String(),
            'updated_at'     => optional($this->updated_at)?->toIso8601String(),
            'deleted_at'     => optional($this->deleted_at)?->toIso8601String(),
        ];
    }
}
