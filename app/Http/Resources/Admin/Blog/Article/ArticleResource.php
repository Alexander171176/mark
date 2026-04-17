<?php

namespace App\Http\Resources\Admin\Blog\Article;

use App\Http\Resources\Admin\Blog\Rubric\RubricSharedResource;
use App\Http\Resources\Admin\Blog\Tag\TagResource;
use App\Http\Resources\Admin\Blog\Video\VideoResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'user_id' => $this->user_id,

            // flags
            'sort'     => $this->sort,
            'activity' => $this->activity,
            'left'     => $this->left,
            'main'     => $this->main,
            'right'    => $this->right,

            // moderation
            'moderation_status' => $this->moderation_status,
            'is_approved'       => (int) $this->moderation_status === 1,
            'moderated_by'      => $this->moderated_by,
            'moderated_at'      => $this->moderated_at?->toISOString(),
            'moderation_note'   => $this->moderation_note,

            // content
            'locale'      => $this->locale,
            'title'       => $this->title,
            'url'         => $this->url,
            'img'         => $this->img,
            'subtitle'    => $this->subtitle,
            'short'       => $this->short,
            'description' => $this->description,

            // author display (в модели поле pseudonym)
            'pseudonym' => $this->pseudonym,

            // dates
            'published_at'  => $this->published_at?->format('Y-m-d'),
            'show_from_at'  => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at'    => $this->show_to_at?->format('Y-m-d\TH:i'),

            // counters
            'views' => $this->views,

            // seo
            'meta_title'    => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_desc'     => $this->meta_desc,

            // timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // --- counts (работает, если в контроллере withCount([...])) ---
            'comments_count' => $this->whenCounted('comments'),
            'rubrics_count'  => $this->whenCounted('rubrics'),
            'tags_count'     => $this->whenCounted('tags'),
            'images_count'   => $this->whenCounted('images'),
            'videos_count'   => $this->whenCounted('videos'),
            'likes_count'    => $this->whenCounted('likes', $this->likes_count), // fallback

            // relations (только если подгружены)
            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'id'    => $this->owner?->id,
                    'name'  => $this->owner?->name,
                    'email' => $this->owner?->email,
                    'profile_photo_url' => $this->owner?->profile_photo_url, // ✅ Jetstream
                ];
            }),

            'moderator' => $this->whenLoaded('moderator', function () {
                return [
                    'id'   => $this->moderator?->id,
                    'name' => $this->moderator?->name,
                ];
            }),

            'rubrics' => RubricSharedResource::collection($this->whenLoaded('rubrics')),
            'tags'    => TagResource::collection($this->whenLoaded('tags')),
            'images'  => ArticleImageResource::collection($this->whenLoaded('images')),
            'videos'  => VideoResource::collection($this->whenLoaded('videos')),

            // важно: relatedArticles relation называется relatedArticles
            'related_articles' => ArticleSharedResource::collection($this->whenLoaded('relatedArticles')),
        ];
    }
}
