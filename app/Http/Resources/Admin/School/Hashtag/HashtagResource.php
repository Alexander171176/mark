<?php

namespace App\Http\Resources\Admin\School\Hashtag;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HashtagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,

            'sort'      => $this->sort,
            'activity'  => (bool) $this->activity,

            'name'      => $this->name,
            'slug'      => $this->slug,
            'locale'    => $this->locale,

            'color'      => $this->color,
            'short'      => $this->short,
            'description'=> $this->description,

            'views'     => (int) $this->views,
            'likes'     => (int) $this->likes,

            'meta_title'    => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_desc'     => $this->meta_desc,

            // Счётчики связей (если в запросе был withCount)
            'courses_count' => $this->when(
                isset($this->courses_count),
                fn () => (int) $this->courses_count
            ),
            'modules_count' => $this->when(
                isset($this->modules_count),
                fn () => (int) $this->modules_count
            ),
            'lessons_count' => $this->when(
                isset($this->lessons_count),
                fn () => (int) $this->lessons_count
            ),

            // Таймстемпы
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
            'deleted_at' => optional($this->deleted_at)->toDateTimeString(),
        ];
    }
}
