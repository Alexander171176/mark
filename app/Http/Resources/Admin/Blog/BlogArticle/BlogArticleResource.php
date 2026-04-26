<?php

namespace App\Http\Resources\Admin\Blog\BlogArticle;

use App\Http\Resources\Admin\Blog\BlogRubric\BlogRubricSharedResource;
use App\Http\Resources\Admin\Blog\BlogTag\BlogTagResource;
use App\Http\Resources\Admin\Blog\BlogVideo\BlogVideoResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogArticleResource extends JsonResource
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
             * Общие поля
             */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,
            'left' => (bool) $this->left,
            'main' => (bool) $this->main,
            'right' => (bool) $this->right,

            'img' => $this->img,
            'url' => $this->url,

            'published_at' => $this->published_at?->format('Y-m-d'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            'views' => (int) $this->views,

            /**
             * Модерация
             */
            'moderation_status' => (int) $this->moderation_status,
            'is_approved' => (int) $this->moderation_status === 1,
            'moderated_by' => $this->moderated_by,
            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,

            /**
             * Текущий перевод
             */
            'translation' => $currentTranslation
                ? new BlogArticleTranslationResource($currentTranslation)
                : null,

            /**
             * Все переводы
             */
            'translations' => BlogArticleTranslationResource::collection(
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
            'comments_count' => $this->whenCounted('comments'),
            'rubrics_count' => $this->whenCounted('rubrics'),
            'tags_count' => $this->whenCounted('tags'),
            'images_count' => $this->whenCounted('images'),
            'videos_count' => $this->whenCounted('videos'),
            'likes_count' => $this->whenCounted('likes'),
            'related_articles_count' => $this->whenCounted('relatedArticles'),

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

            'rubrics' => BlogRubricSharedResource::collection($this->whenLoaded('rubrics')),
            'tags' => BlogTagResource::collection($this->whenLoaded('tags')),
            'images' => BlogArticleImageResource::collection($this->whenLoaded('images')),
            'videos' => BlogVideoResource::collection($this->whenLoaded('videos')),

            /**
             * Важно: relation называется relatedArticles
             */
            'related_articles' => BlogArticleSharedResource::collection(
                $this->whenLoaded('relatedArticles')
            ),
        ];
    }
}
