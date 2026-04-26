<?php

namespace App\Http\Resources\Admin\Blog\BlogVideo;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogVideoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();

        $currentTranslation = $this->whenLoaded('translations', function () use ($currentLocale) {
            return $this->translations->firstWhere('locale', $currentLocale)
                ?: $this->translations->firstWhere('locale', config('app.fallback_locale', 'ru'))
                    ?: $this->translations->first();
        });

        $cover = null;

        if ($this->relationLoaded('images') && $this->images?->count()) {
            $cover = $this->images->first();
        }

        return [
            'id' => $this->id,

            'user_id' => $this->user_id,
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,
            'is_private' => (bool) $this->is_private,
            'is_public' => (bool) $this->is_public,

            'left' => (bool) $this->left,
            'main' => (bool) $this->main,
            'right' => (bool) $this->right,

            /**
             * Модерация
             */
            'moderation_status' => (int) $this->moderation_status,
            'is_approved' => (int) $this->moderation_status === 1,
            'moderated_by' => $this->moderated_by,
            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,

            /**
             * Общие поля
             */
            'url' => $this->url,

            'published_at' => $this->published_at?->format('Y-m-d'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            'duration' => $this->duration,
            'source_type' => $this->source_type,

            'video_url' => $this->video_url,
            'embed_url' => $this->embed_url,
            'embed_code' => $this->embed_code,
            'external_video_id' => $this->external_video_id,

            'views' => (int) $this->views,

            /**
             * Текущий перевод
             */
            'translation' => $currentTranslation
                ? new BlogVideoTranslationResource($currentTranslation)
                : null,

            /**
             * Все переводы
             */
            'translations' => BlogVideoTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            /**
             * Relations
             */
            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'id' => $this->owner?->id,
                    'name' => $this->owner?->name,
                    'email' => $this->owner?->email,
                    'profile_photo_url' => $this->owner?->profile_photo_url,
                ];
            }),

            'moderator' => $this->whenLoaded('moderator', function () {
                return [
                    'id' => $this->moderator?->id,
                    'name' => $this->moderator?->name,
                ];
            }),

            /**
             * Timestamps
             */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            /**
             * Cover
             */
            'cover_thumb_url' => $cover?->thumb_url,
            'cover_webp_url' => $cover?->webp_url,
            'cover_image_url' => $cover?->image_url,

            /**
             * Counts
             */
            'images_count' => $this->whenCounted('images'),
            'comments_count' => $this->whenCounted('comments'),
            'likes_count' => $this->whenCounted('likes'),

            /**
             * Relations
             */
            'images' => BlogVideoImageResource::collection($this->whenLoaded('images')),
            'related_videos' => BlogVideoSharedResource::collection($this->whenLoaded('relatedVideos')),
        ];
    }
}
