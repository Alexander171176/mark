<?php

namespace App\Http\Resources\Admin\Blog\BlogArticle;

use App\Http\Resources\Admin\Blog\BlogRubric\BlogRubricSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogArticleSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();

        /**
         * Index:
         * translations содержит только текущую локаль.
         *
         * Create/Edit shared selects:
         * translations может содержать все локали.
         */
        $translation = $this->relationLoaded('translations')
            ? (
            $this->translations->firstWhere(
                'locale',
                $currentLocale
            )
                ?: $this->translations->firstWhere(
                'locale',
                config('app.fallback_locale', 'ru')
            )
                ?: $this->translations->first()
            )
            : null;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            /**
             * Основные поля.
             */
            'sort' => (int) $this->sort,

            'activity' => (bool) $this->activity,

            'left' => (bool) $this->left,
            'main' => (bool) $this->main,
            'right' => (bool) $this->right,

            'url' => $this->url,
            'img' => $this->img,

            'views' => (int) $this->views,

            /**
             * Публикация.
             */
            'published_at' =>
                $this->published_at?->format('Y-m-d'),

            'show_from_at' =>
                $this->show_from_at?->format('Y-m-d\TH:i'),

            'show_to_at' =>
                $this->show_to_at?->format('Y-m-d\TH:i'),

            /**
             * Текущий перевод.
             */
            'translation' => $translation
                ? [
                    'locale' => $translation->locale,
                    'title' => $translation->title,
                    'subtitle' => $translation->subtitle,
                    'short' => $translation->short,
                    'description' => $translation->description,
                    'pseudonym' => $translation->pseudonym,
                ]
                : null,

            /**
             * Все фактически загруженные переводы.
             *
             * На Index здесь будет одна locale.
             * В Create/Edit могут быть все.
             */
            'translations' =>
                BlogArticleTranslationResource::collection(
                    $this->whenLoaded('translations')
                ),

            /**
             * Модерация.
             */
            'moderation_status' =>
                (int) $this->moderation_status,

            'is_approved' =>
                (int) $this->moderation_status === 1,

            'moderated_by' => $this->moderated_by,

            'moderated_at' =>
                $this->moderated_at?->toISOString(),

            'moderation_note' =>
                $this->moderation_note,

            /**
             * Владелец.
             */
            'owner' => $this->whenLoaded(
                'owner',
                function () {
                    return [
                        'id' => $this->owner?->id,
                        'name' => $this->owner?->name,
                        'email' => $this->owner?->email,
                        'profile_photo_url' =>
                            $this->owner?->profile_photo_url,
                    ];
                }
            ),

            /**
             * Изображения.
             *
             * Index должен загрузить images.media.
             */
            'images' => BlogArticleImageResource::collection(
                $this->whenLoaded('images')
            ),

            /**
             * Рубрики.
             *
             * Они реально используются
             * таблицей и карточками Index.
             */
            'rubrics' => BlogRubricSharedResource::collection(
                $this->whenLoaded('rubrics')
            ),

            /**
             * Counts.
             */
            'comments_count' => $this->when(
                isset($this->comments_count),
                fn () => (int) $this->comments_count
            ),

            'rubrics_count' => $this->when(
                isset($this->rubrics_count),
                fn () => (int) $this->rubrics_count
            ),

            'tags_count' => $this->when(
                isset($this->tags_count),
                fn () => (int) $this->tags_count
            ),

            'images_count' => $this->when(
                isset($this->images_count),
                fn () => (int) $this->images_count
            ),

            'videos_count' => $this->when(
                isset($this->videos_count),
                fn () => (int) $this->videos_count
            ),

            'likes_count' => $this->when(
                isset($this->likes_count),
                fn () => (int) $this->likes_count
            ),

            'related_articles_count' => $this->when(
                isset($this->related_articles_count),
                fn () => (int) $this->related_articles_count
            ),

            /**
             * Timestamps.
             */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
