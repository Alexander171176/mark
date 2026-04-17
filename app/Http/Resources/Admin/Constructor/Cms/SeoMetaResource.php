<?php

namespace App\Http\Resources\Admin\Constructor\Cms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeoMetaResource extends JsonResource
{
    /**
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,

            // Полиморфная цель (короткая сводка, если подгружено)
            'seoable_type'     => $this->seoable_type,
            'seoable_id'       => $this->seoable_id,
            'seoable'          => $this->whenLoaded('seoable', function () {
                return [
                    'type' => class_basename($this->seoable),
                    'id'   => $this->seoable->getKey(),
                    'title'=> $this->seoable->title ?? null,
                    'slug' => $this->seoable->slug  ?? null,
                ];
            }),

            // Базовое SEO
            'title'            => $this->title,
            'description'      => $this->description,
            'keywords'         => $this->keywords,

            // Индексация/каноникал
            'robots_noindex'   => (bool) $this->robots_noindex,
            'robots_nofollow'  => (bool) $this->robots_nofollow,
            'robots'           => $this->robots ?? null,
            'canonical_url'    => $this->canonical_url,

            // OpenGraph
            'og_title'         => $this->og_title,
            'og_description'   => $this->og_description,
            'og_image_url'     => $this->og_image_url,
            'og_type'          => $this->og_type,

            // Twitter
            'twitter_card'     => $this->twitter_card,

            // Прочее
            'locale'           => $this->locale,
            'activity'         => (bool) $this->activity,
            'json_ld'          => $this->json_ld,
            'meta'             => $this->meta,

            // Служебное
            'created_at'       => optional($this->created_at)?->toIso8601String(),
            'updated_at'       => optional($this->updated_at)?->toIso8601String(),
            'deleted_at'       => optional($this->deleted_at)?->toIso8601String(),
        ];
    }
}
