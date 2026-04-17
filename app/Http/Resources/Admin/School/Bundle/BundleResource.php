<?php

namespace App\Http\Resources\Admin\School\Bundle;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BundleResource extends JsonResource
{
    /**
     * Представление набора (bundle) для API админки.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,

            // Локаль
            'locale'   => $this->locale,

            // Основные поля
            'title'       => $this->title,
            'slug'        => $this->slug,
            'subtitle'    => $this->subtitle,
            'short'       => $this->short,
            'description' => $this->description,

            // Публикация/видимость
            'activity'     => (bool) $this->activity,
            'sort'         => (int) $this->sort,
            'published_at' => $this->published_at?->format('Y-m-d'), // как в CourseResource

            // Метрики
            'views' => (int) $this->views,
            'likes' => (int) $this->likes,

            // SEO
            'meta_title'    => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_desc'     => $this->meta_desc,

            // JSON meta
            'meta' => $this->meta,

            // Таймстампы
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),

            /**
             * Relations (только если загружены)
             */

            // Курсы внутри бандла
            'courses' => $this->whenLoaded('courses', fn () =>
            $this->courses->map(fn ($c) => [
                'id'    => $c->id,
                'title' => $c->title,
                'slug'  => $c->slug,
                'locale'=> $c->locale ?? null, // если нужно в UI
            ])
            ),

            // Изображения бандла
            'images' => $this->whenLoaded('images', fn () =>
            BundleImageResource::collection($this->images)
            ),

            // Главное изображение (удобно для карточек)
            'primary_image' => $this->whenLoaded('images', function () {
                $img = $this->primary_image; // accessor из модели
                return $img ? (new BundleImageResource($img))->resolve() : null;
            }),

            // Цены
            'prices' => $this->whenLoaded('prices', fn () =>
            $this->prices->map(fn ($p) => [
                'id'         => $p->id,
                'currency'   => $p->currency,
                'price'      => (string) $p->price,
                'sale_price' => $p->sale_price !== null ? (string) $p->sale_price : null,
                'starts_at'  => $p->starts_at?->toISOString(),
                'ends_at'    => $p->ends_at?->toISOString(),
                'activity'   => (bool) $p->activity,
            ])
            ),
        ];
    }
}
