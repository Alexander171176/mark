<?php

namespace App\Http\Resources\Admin\Blog\Comment;

use App\Http\Resources\Admin\Blog\BlogArticle\BlogArticleSharedResource;
use App\Http\Resources\Admin\Blog\BlogVideo\BlogVideoSharedResource;
use App\Http\Resources\Admin\System\User\UserSharedResource;
use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        /**
         * Перевод объекта комментария.
         *
         * Поддерживаются только:
         * - BlogArticle;
         * - BlogVideo.
         *
         * Resource не выполняет
         * дополнительных SQL-запросов.
         */
        $commentableTranslation = null;

        if (
            $this->relationLoaded('commentable')
            && $this->commentable
            && $this->commentable->relationLoaded('translations')
        ) {
            $commentableTranslation =
                $this->commentable
                    ->translations
                    ->firstWhere(
                        'locale',
                        $locale
                    )
                    ?: $this->commentable
                    ->translations
                    ->firstWhere(
                        'locale',
                        $fallbackLocale
                    )
                    ?: $this->commentable
                        ->translations
                        ->first();
        }

        /**
         * Название статьи или видео.
         */
        $commentableTitle = match (true) {
            $this->commentable instanceof BlogArticle,
                $this->commentable instanceof BlogVideo =>
            $commentableTranslation?->title,

            default =>
            null,
        };

        return [
            'id' => $this->id,

            /**
             * Keys.
             */
            'user_id' =>
                $this->user_id,

            'parent_id' =>
                $this->parent_id,

            /**
             * Content.
             */
            'content' =>
                $this->content,

            'activity' =>
                (bool) $this->activity,

            /**
             * Moderation.
             */
            'moderation_status' =>
                (int) $this->moderation_status,

            'is_approved' =>
                (int) $this->moderation_status === 1,

            'moderated_by' =>
                $this->moderated_by,

            'moderated_at' =>
                $this->moderated_at?->toISOString(),

            'moderation_note' =>
                $this->moderation_note,

            /**
             * Timestamps.
             */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),

            /**
             * Автор комментария.
             */
            'user' => $this->whenLoaded(
                'user',
                fn () => $this->user
                    ? new UserSharedResource(
                        $this->user
                    )
                    : null
            ),

            /**
             * Модератор.
             */
            'moderator' => $this->whenLoaded(
                'moderator',
                fn () => $this->moderator
                    ? new UserSharedResource(
                        $this->moderator
                    )
                    : null
            ),

            /**
             * Polymorphic object.
             */
            'commentable_type' =>
                $this->commentable_type,

            'commentable_id' =>
                $this->commentable_id,

            'commentable_title' =>
                $commentableTitle,

            /**
             * Краткий объект статьи или видео.
             */
            'commentable' => $this->whenLoaded(
                'commentable',
                function () {
                    if (!$this->commentable) {
                        return null;
                    }

                    return match (true) {
                        $this->commentable instanceof BlogArticle =>
                        new BlogArticleSharedResource(
                            $this->commentable
                        ),

                        $this->commentable instanceof BlogVideo =>
                        new BlogVideoSharedResource(
                            $this->commentable
                        ),

                        default =>
                        null,
                    };
                }
            ),

            /**
             * Replies.
             */
            'replies' =>
                CommentSharedResource::collection(
                    $this->whenLoaded('replies')
                ),

            'replies_count' => $this->when(
                isset($this->replies_count),
                fn () => (int) $this->replies_count
            ),
        ];
    }
}
