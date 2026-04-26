<?php

namespace App\Http\Resources\Admin\Blog\BlogTag;

use App\Http\Resources\Admin\Blog\BlogArticle\BlogArticleSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogTagResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();

        $currentTranslation = $this->whenLoaded('translations', function () use ($currentLocale) {
            return $this->translations->firstWhere('locale', $currentLocale)
                ?: $this->translations->firstWhere('locale', config('app.fallback_locale', 'ru'))
                    ?: $this->translations->first();
        });

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            /**
             * Flags
             */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

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
            'icon' => $this->icon,
            'slug' => $this->slug,
            'views' => (int) $this->views,

            /**
             * Текущий перевод
             */
            'translation' => $currentTranslation
                ? new BlogTagTranslationResource($currentTranslation)
                : null,

            /**
             * Все переводы
             */
            'translations' => BlogTagTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            /**
             * Timestamps
             */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            /**
             * Counts
             */
            'articles_count' => $this->whenCounted('articles'),

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

            'articles' => BlogArticleSharedResource::collection($this->whenLoaded('articles')),
        ];
    }
}
