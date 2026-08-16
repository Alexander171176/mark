<?php

namespace App\Http\Resources\Admin\School\SchoolLesson;

use App\Http\Resources\Admin\School\SchoolHashtag\SchoolHashtagSharedResource;
use App\Http\Resources\Admin\School\SchoolModule\SchoolModuleSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolLessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->relationLoaded('translations')
            ? $this->translations->first()
            : null;

        $courseTranslation = null;

        if (
            $this->relationLoaded('module')
            && $this->module
            && $this->module->relationLoaded('course')
            && $this->module->course
            && $this->module->course->relationLoaded('translations')
        ) {
            $courseTranslation = $this->module
                ->course
                ->translations
                ->first();
        }

        return [
            'id' => $this->id,
            'school_module_id' => $this->school_module_id,

            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'slug' => $this->slug,

            'title' => $translation?->title,
            'subtitle' => $translation?->subtitle,
            'short' => $translation?->short,
            'description' => $translation?->description,

            'meta_title' => $translation?->meta_title,
            'meta_keywords' => $translation?->meta_keywords,
            'meta_desc' => $translation?->meta_desc,

            'content_type' => $this->content_type,
            'content_id' => $this->content_id,

            'published_at' => optional($this->published_at)->format('Y-m-d'),

            'status' => $this->status,
            'availability' => $this->availability,
            'access_type' => $this->access_type,

            'difficulty' => $this->difficulty !== null ? (int) $this->difficulty : null,
            'duration' => $this->duration !== null ? (int) $this->duration : null,

            'preview_mode' => $this->preview_mode,
            'preview_value' => $this->preview_value !== null ? (int) $this->preview_value : null,

            'popularity' => (int) $this->popularity,
            'rating_count' => (int) $this->rating_count,
            'rating_avg' => $this->rating_avg !== null ? (float) $this->rating_avg : null,
            'views' => (int) $this->views,
            'likes' => (int) $this->likes,

            'already_liked' => (bool) ($this->already_liked ?? false),

            'primary_image' => $this->whenLoaded(
                'images',
                fn () => $this->primary_image
                    ? new SchoolLessonImageResource($this->primary_image)
                    : null
            ),

            'images' => SchoolLessonImageResource::collection(
                $this->whenLoaded('images')
            ),

            'translations' => SchoolLessonTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            'module' => new SchoolModuleSharedResource(
                $this->whenLoaded('module')
            ),

            'course' => $this->when(
                $this->relationLoaded('module')
                && $this->module
                && $this->module->relationLoaded('course')
                && $this->module->course,
                fn () => [
                    'id' => $this->module->course->id,
                    'slug' => $this->module->course->slug,
                    'title' => $courseTranslation?->title,
                ]
            ),

            'hashtags' => SchoolHashtagSharedResource::collection(
                $this->whenLoaded('hashtags')
            ),

            'content' => $this->whenLoaded('content', function () {
                $content = $this->content;

                if (!$content) {
                    return null;
                }

                $title = null;

                if (method_exists($content, 'translationOrFallback')) {
                    $title = $content->translationOrFallback()?->title;
                } elseif (method_exists($content, 'translation')) {
                    $title = $content->translation()?->title;
                } else {
                    $title = $content->title ?? null;
                }

                return array_filter([
                    'id' => $content->id ?? null,
                    'title' => $title,
                    'slug' => $content->slug ?? $content->url ?? null,
                    'type' => class_basename($content),
                ]);
            }),

            'likes_count' => $this->when(isset($this->likes_count), fn () => (int) $this->likes_count),
            'hashtags_count' => $this->when(isset($this->hashtags_count), fn () => (int) $this->hashtags_count),
            'images_count' => $this->when(isset($this->images_count), fn () => (int) $this->images_count),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
