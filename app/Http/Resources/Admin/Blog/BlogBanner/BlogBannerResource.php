<?php

namespace App\Http\Resources\Admin\Blog\BlogBanner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogBannerResource extends JsonResource
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
            'comment' => $this->comment,

            /**
             * Текущий перевод
             */
            'translation' => $currentTranslation
                ? new BlogBannerTranslationResource($currentTranslation)
                : null,

            /**
             * Все переводы
             */
            'translations' => BlogBannerTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

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
             * Images
             */
            'images' => BlogBannerImageResource::collection($this->whenLoaded('images')),
            'images_count' => $this->whenCounted('images'),

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
        ];
    }
}
