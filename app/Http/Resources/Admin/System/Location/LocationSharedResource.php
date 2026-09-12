<?php

namespace App\Http\Resources\Admin\System\Location;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationSharedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $translation =
            $this->translationOrFallback();

        $parentTranslation =
            $this->parent
                ? $this->parent
                ->translationOrFallback()
                : null;

        return [
            'id' => $this->id,

            'parent_id' =>
                $this->parent_id,

            'parent' =>
                $this->parent
                    ? [
                    'id' =>
                        $this->parent->id,

                    'type' =>
                        $this->parent->type,

                    'slug' =>
                        $this->parent->slug,

                    'translation' =>
                        $parentTranslation
                            ? [
                            'id' =>
                                $parentTranslation->id,

                            'locale' =>
                                $parentTranslation->locale,

                            'title' =>
                                $parentTranslation->title,

                            'title_in' =>
                                $parentTranslation->title_in,

                            'title_from' =>
                                $parentTranslation->title_from,
                        ]
                            : null,
                ]
                    : null,

            'type' => $this->type,
            'slug' => $this->slug,
            'code' => $this->code,

            'latitude' =>
                $this->latitude,

            'longitude' =>
                $this->longitude,

            'timezone' =>
                $this->timezone,

            'activity' =>
                (bool) $this->activity,

            'is_default' =>
                (bool) $this->is_default,

            'sort' =>
                $this->sort,

            'translation' =>
                $translation
                    ? [
                    'id' =>
                        $translation->id,

                    'locale' =>
                        $translation->locale,

                    'title' =>
                        $translation->title,

                    'title_in' =>
                        $translation->title_in,

                    'title_from' =>
                        $translation->title_from,

                    'short' =>
                        $translation->short,
                ]
                    : null,

            'created_at' =>
                $this->created_at,

            'updated_at' =>
                $this->updated_at,
        ];
    }
}
