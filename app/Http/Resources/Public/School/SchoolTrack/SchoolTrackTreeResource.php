<?php

namespace App\Http\Resources\Public\School\SchoolTrack;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolTrackTreeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translationOrFallback();

        return [
            'id' => $this->id,
            'slug' => $this->slug,

            'translation' => $translation
                ? [
                    'locale' => $translation->locale,
                    'name' => $translation->name,
                ]
                : null,

            'children' => self::collection(
                $this->whenLoaded('children')
            ),
        ];
    }
}
