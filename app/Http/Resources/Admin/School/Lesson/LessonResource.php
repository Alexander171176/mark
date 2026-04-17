<?php

namespace App\Http\Resources\Admin\School\Lesson;

use App\Http\Resources\Admin\School\Hashtag\HashtagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    /**
     * Представление урока.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'module_id' => $this->module_id,

            // Локаль
            'locale'    => $this->locale,

            // Управление списком
            'activity'  => (bool) $this->activity,
            'sort'      => (int) $this->sort,

            // Основные поля
            'title'       => $this->title,
            'slug'        => $this->slug,
            'subtitle'    => $this->subtitle,
            'short'       => $this->short,
            'description' => $this->description,

            // Полиморфный контент
            'content_type' => $this->content_type,
            'content_id'   => $this->content_id,

            // Настройки доступа / сложность / длительность
            'access_type' => $this->access_type,
            'difficulty'  => $this->difficulty,
            'duration'    => $this->duration,

            // Превью
            'preview_mode'  => $this->preview_mode,
            'preview_value' => $this->preview_value,

            // Метрики
            'rating_avg'   => (float) $this->rating_avg,
            'rating_count' => (int) $this->rating_count,
            'popularity'   => (int) $this->popularity,
            'views'        => (int) $this->views,
            'likes'        => (int) $this->likes,

            // Публикация/видимость
            'status'       => $this->status,                 // draft|published|archived
            'availability' => $this->availability,           // public|unlisted|private
            'published_at' => $this->published_at?->format('Y-m-d'), // YYYY-MM-DD

            // SEO
            'meta_title'    => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_desc'     => $this->meta_desc,

            // Таймстампы
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),

            // Мини-данные по курсу (если подгружен)
            'course' => $this->when(
                $this->relationLoaded('module') && $this->module && $this->module->relationLoaded('course') && $this->module->course,
                function () {
                    return [
                        'id'     => $this->module->course->id,
                        'title'  => $this->module->course->title,
                        'slug'   => $this->module->course->slug,
                        'locale' => $this->module->course->locale,

                        'instructorProfile' => $this->when(
                            $this->module->course->relationLoaded('instructorProfile') && $this->module->course->instructorProfile,
                            function () {
                                return [
                                    'id'         => $this->module->course->instructorProfile->id,
                                    'title'      => $this->module->course->instructorProfile->title,
                                    'public_name'=> $this->module->course->instructorProfile->public_name,
                                    'images'     => $this->module->course->instructorProfile->relationLoaded('images')
                                        ? $this->module->course->instructorProfile->images->map(fn ($img) => [
                                            'id'        => $img->id,
                                            'order'     => $img->pivot->order ?? 0,
                                            'alt'       => $img->alt ?? null,
                                            'caption'   => $img->caption ?? null,
                                            'url'       => $img->url ?? null,
                                            'webp_url'  => $img->webp_url ?? null,
                                            'image_url' => $img->image_url ?? null,
                                            'thumb_url' => $img->thumb_url ?? null,
                                        ])->values()
                                        : [],
                                ];
                            }
                        ),
                    ];
                }
            ),

            // Мини-данные по модулю (если подгружен)
            'module' => $this->whenLoaded('module', function () {
                return [
                    'id'     => $this->module->id,
                    'title'  => $this->module->title,
                    'slug'   => $this->module->slug,
                    'locale' => $this->module->locale,

                    'course' => $this->when(
                        $this->module->relationLoaded('course') && $this->module->course,
                        function () {
                            return [
                                'id'     => $this->module->course->id,
                                'title'  => $this->module->course->title,
                                'slug'   => $this->module->course->slug,
                                'locale' => $this->module->course->locale,
                            ];
                        }
                    ),
                ];
            }),

            // 🔹 Полиморфные хештеги (через HashtagResource)
            'hashtags' => $this->whenLoaded('hashtags', fn () =>
            HashtagResource::collection($this->hashtags)
            ),

            // 🔹 Счётчик хештегов, если был withCount('hashtags')
            'hashtags_count' => $this->when(
                isset($this->hashtags_count),
                (int) $this->hashtags_count
            ),

            // Изображения модуля (через LessonImageResource)
            'images' => $this->whenLoaded('images', fn () =>
            LessonImageResource::collection($this->images)
            ),

            // 🔹 Сам контент (Article / Video / Quiz / Assignment и т.п.), если подгрузишь ->with('content')
            'content' => $this->whenLoaded('content', function () {
                $content = $this->content;
                if (!$content) {
                    return null;
                }

                return array_filter([
                    'id'    => $content->id ?? null,
                    'title' => $content->title ?? null,
                    'slug'  => $content->slug ?? null,
                    'type'  => class_basename($content),
                ]);
            }),
        ];
    }
}
