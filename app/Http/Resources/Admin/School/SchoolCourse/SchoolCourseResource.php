<?php

namespace App\Http\Resources\Admin\School\SchoolCourse;

use App\Http\Resources\Admin\School\SchoolHashtag\SchoolHashtagSharedResource;
use App\Http\Resources\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileResource;
use App\Http\Resources\Admin\School\SchoolTrack\SchoolTrackSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolCourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /**
         * В публичных запросах scopeForPublic()
         * уже загружает translations текущей локали.
         */
        $translation = $this->relationLoaded('translations')
            ? $this->translations->first()
            : null;

        return [
            'id' => $this->id,
            'school_instructor_profile_id' => $this->school_instructor_profile_id,

            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'is_new' => (bool) $this->is_new,
            'is_hit' => (bool) $this->is_hit,
            'is_sale' => (bool) $this->is_sale,

            'left' => (bool) $this->left,
            'main' => (bool) $this->main,
            'right' => (bool) $this->right,

            'slug' => $this->slug,

            'title' => $translation?->title,
            'subtitle' => $translation?->subtitle,
            'short' => $translation?->short,
            'description' => $translation?->description,

            'meta_title' => $translation?->meta_title,
            'meta_keywords' => $translation?->meta_keywords,
            'meta_desc' => $translation?->meta_desc,

            'published_at' => optional($this->published_at)->format('Y-m-d'),

            'level' => $this->level,
            'status' => $this->status,
            'availability' => $this->availability,
            'difficulty' => $this->difficulty !== null ? (int) $this->difficulty : null,
            'duration' => $this->duration !== null ? (int) $this->duration : null,

            'students_count' => (int) $this->students_count,
            'popularity' => (int) $this->popularity,
            'rating_count' => (int) $this->rating_count,
            'rating_avg' => $this->rating_avg !== null ? (float) $this->rating_avg : null,
            'views' => (int) $this->views,
            'likes' => (int) $this->likes,

            'already_liked' => (bool) ($this->already_liked ?? false),

            'primary_image' => $this->whenLoaded(
                'images',
                fn () => $this->primary_image
                    ? new SchoolCourseImageResource($this->primary_image)
                    : null
            ),

            'images' => SchoolCourseImageResource::collection(
                $this->whenLoaded('images')
            ),

            'translations' => SchoolCourseTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            'instructorProfile' => new SchoolInstructorProfileResource(
                $this->whenLoaded('instructorProfile')
            ),

            'tracks' => SchoolTrackSharedResource::collection(
                $this->whenLoaded('tracks')
            ),

            'hashtags' => SchoolHashtagSharedResource::collection(
                $this->whenLoaded('hashtags')
            ),

            'prices' => $this->whenLoaded('prices', fn () => $this->prices->map(fn ($price) => [
                'id' => $price->id,
                'currency_id' => $price->currency_id,
                'price' => (string) $price->price,
                'sale_price' => $price->sale_price !== null ? (string) $price->sale_price : null,
                'compare_at_price' => $price->compare_at_price !== null ? (string) $price->compare_at_price : null,
                'effective_price' => $price->effective_price ?? null,
                'has_discount' => $price->has_discount ?? false,
                'discount_amount' => $price->discount_amount ?? null,
                'discount_percent' => $price->discount_percent ?? null,
                'starts_at' => optional($price->starts_at)->toIso8601String(),
                'ends_at' => optional($price->ends_at)->toIso8601String(),
                'activity' => (bool) $price->activity,
                'sort' => (int) $price->sort,
            ])),

            'related_courses' => SchoolCourseSharedResource::collection(
                $this->whenLoaded('relatedCourses')
            ),

            'related_by' => SchoolCourseSharedResource::collection(
                $this->whenLoaded('relatedBy')
            ),

            'modules_count' => $this->when(isset($this->modules_count), fn () => (int) $this->modules_count),
            'lessons_count' => $this->when(isset($this->lessons_count), fn () => (int) $this->lessons_count),
            'tracks_count' => $this->when(isset($this->tracks_count), fn () => (int) $this->tracks_count),
            'hashtags_count' => $this->when(isset($this->hashtags_count), fn () => (int) $this->hashtags_count),
            'images_count' => $this->when(isset($this->images_count), fn () => (int) $this->images_count),
            'prices_count' => $this->when(isset($this->prices_count), fn () => (int) $this->prices_count),
            'reviews_count' => $this->when(isset($this->reviews_count), fn () => (int) $this->reviews_count),
            'enrollments_count' => $this->when(isset($this->enrollments_count), fn () => (int) $this->enrollments_count),
            'schedules_count' => $this->when(isset($this->schedules_count), fn () => (int) $this->schedules_count),
            'quizzes_count' => $this->when(isset($this->quizzes_count), fn () => (int) $this->quizzes_count),
            'likes_count' => $this->when(isset($this->likes_count), fn () => (int) $this->likes_count),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
