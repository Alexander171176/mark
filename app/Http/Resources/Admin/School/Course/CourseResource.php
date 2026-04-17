<?php

namespace App\Http\Resources\Admin\School\Course;

use App\Http\Resources\Admin\School\Hashtag\HashtagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Представление курса в API (админ/витрина).
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'instructor_profile_id' => $this->instructor_profile_id,

            // Локаль
            'locale'                => $this->locale,

            // Основные поля
            'title'                 => $this->title,
            'slug'                  => $this->slug,
            'subtitle'              => $this->subtitle,
            'short'                 => $this->short,
            'description'           => $this->description,

            // Метаданные содержания
            'level'                 => $this->level,
            'difficulty'            => $this->difficulty,
            'duration'              => $this->duration,

            // Публикация/видимость
            'status'       => $this->status,          // draft|published|archived
            'availability' => $this->availability,    // public|unlisted|private
            'published_at' => $this->published_at?->format('Y-m-d'), // YYYY-MM-DD

            // Флаги витрины
            'is_new'  => (bool) $this->is_new,
            'is_hit'  => (bool) $this->is_hit,
            'is_sale' => (bool) $this->is_sale,
            'left'    => (bool) $this->left,
            'main'    => (bool) $this->main,
            'right'   => (bool) $this->right,

            // SEO
            'meta_title'    => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_desc'     => $this->meta_desc,

            // Управление списком
            'activity' => (bool) $this->activity,
            'sort'     => (int) $this->sort,

            // Метрики
            'rating_avg'     => (float) $this->rating_avg,
            'rating_count'   => (int) $this->rating_count,
            'students_count' => (int) $this->students_count,
            'popularity'     => (int) $this->popularity,
            'views'          => (int) $this->views,
            'likes_count'    => $this->whenCounted('likes', $this->likes_count), // fallback
            'already_liked' => auth()->check()
                ? $this->likes()->where('user_id', auth()->id())->exists()
                : false,

            // Таймстампы
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),

            // Инструктор
            'instructorProfile' => $this->whenLoaded('instructorProfile', function () {
                return [
                    'id' => $this->instructorProfile->id,
                    'title' => $this->instructorProfile->title,
                    'slug' => $this->instructorProfile->slug,
                    'public_name' => $this->instructorProfile->public_name,
                    'short' => $this->instructorProfile->short,
                    'bio' => $this->instructorProfile->bio,
                    'experience_years' => $this->instructorProfile->experience_years,
                    'hourly_rate' => $this->instructorProfile->hourly_rate,
                    'rating_avg' => $this->instructorProfile->rating_avg,
                    'rating_count' => $this->instructorProfile->rating_count,

                    'images' => $this->instructorProfile->relationLoaded('images')
                        ? $this->instructorProfile->images
                            ->sortBy(fn ($image) => $image->pivot->order ?? PHP_INT_MAX)
                            ->map(function ($image) {
                                return [
                                    'id' => $image->id,
                                    'order' => $image->pivot->order ?? null,
                                    'alt' => $image->alt ?? null,
                                    'caption' => $image->caption ?? null,
                                    'url' => $image->url ?? null,
                                    'webp_url' => $image->webp_url ?? null,
                                    'image_url' => $image->image_url ?? null,
                                    'thumb_url' => $image->thumb_url ?? null,
                                ];
                            })
                            ->values()
                        : [],
                ];
            }),

            // Категории
            'learningCategories' => $this->whenLoaded('learningCategories', fn () =>
            $this->learningCategories->map(fn ($c) => [
                'id'   => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
            ])
            ),

            // 🔹 Полиморфные хештеги (через HashtagResource)
            'hashtags' => $this->whenLoaded('hashtags', fn () =>
            HashtagResource::collection($this->hashtags)
            ),

            // Изображения курса (через CourseImageResource)
            'images' => $this->whenLoaded('images', fn () =>
            CourseImageResource::collection($this->images)
            ),

            // Цены курса
            'prices' => $this->whenLoaded('prices', fn () =>
            $this->prices->map(fn ($p) => [
                'id'               => $p->id,
                'currency'         => $p->currency,
                'price'            => (string) $p->price,
                'sale_price'       => $p->sale_price !== null ? (string) $p->sale_price : null,
                'compare_at_price' => $p->compare_at_price !== null ? (string) $p->compare_at_price : null,
                'starts_at'        => $p->starts_at?->toISOString(),
                'ends_at'          => $p->ends_at?->toISOString(),
                'activity'         => (bool) $p->activity,
                'position'         => (int) $p->position,
            ])
            ),

            // Рекомендованные курсы
            'related_courses' => CourseSharedResource::collection($this->whenLoaded('relatedCourses')),

            // Счётчики при withCount()
            'modules_count'    => $this->when(isset($this->modules_count),    (int) $this->modules_count),
            'lessons_count'    => $this->when(isset($this->lessons_count),    (int) $this->lessons_count),
            'reviews_count'    => $this->when(isset($this->reviews_count),    (int) $this->reviews_count),
            'learning_categories_count' => $this->when(
                isset($this->learning_categories_count),
                (int) $this->learning_categories_count
            ),

            // 🔹 Счётчик хештегов (если в запросе был withCount('hashtags'))
            'hashtags_count' => $this->when(
                isset($this->hashtags_count),
                (int) $this->hashtags_count
            ),
        ];
    }
}
