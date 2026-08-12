<?php

namespace App\Http\Resources\Admin\School\SchoolHashtag;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolHashtagSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /**
         * В публичных запросах relation translations
         * уже загружается через scopeWithLocale()
         * только для текущей локали.
         */
        $translation = $this->relationLoaded('translations')
            ? $this->translations->first()
            : null;

        return [
            'id' => $this->id,

            'slug' => $this->slug,
            'color' => $this->color,

            'name' => $translation?->name,

            'views' => (int) $this->views,
            'likes' => (int) $this->likes,
        ];
    }
}
