<?php

namespace App\Http\Resources\Admin\Constructor\Page;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    /**
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,

            'parent_id'    => $this->parent_id,
            'parent'       => $this->whenLoaded('parent', fn () => [
                'id'    => $this->parent->id,
                'title' => $this->parent->title,
                'slug'  => $this->parent->slug,
                'url'   => method_exists($this->parent, 'getUrlAttribute') ? $this->parent->url : null,
            ]),
            'children'     => $this->whenLoaded('children', fn () =>
            $this->children->map(fn ($c) => [
                'id'    => $c->id,
                'title' => $c->title,
                'slug'  => $c->slug,
                'sort' => $c->sort,
                'url'   => method_exists($c, 'getUrlAttribute') ? $c->url : null,
            ])
            ),

            'author_id'    => $this->author_id,
            'author'       => $this->whenLoaded('author', fn () => [
                'id'    => $this->author->id,
                'name'  => $this->author->name ?? null,
                'email' => $this->author->email ?? null,
            ]),

            'title'        => $this->title,
            'slug'         => $this->slug,
            'excerpt'      => $this->excerpt,
            'content'      => $this->content,

            'status'       => $this->status,
            'activity'     => (bool) $this->activity,
            'published_at' => optional($this->published_at)?->toIso8601String(),

            'template'     => $this->template,
            'layout'       => $this->layout,
            'locale'       => $this->locale,
            'sort'         => $this->sort,

            'meta'         => $this->meta,
            'url'          => $this->when(method_exists($this->resource, 'getUrlAttribute'), $this->url),

            'created_at'   => optional($this->created_at)?->toIso8601String(),
            'updated_at'   => optional($this->updated_at)?->toIso8601String(),
            'deleted_at'   => optional($this->deleted_at)?->toIso8601String(),
        ];
    }
}
