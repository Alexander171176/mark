<?php

namespace Database\Seeders;

use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use App\Models\Admin\Blog\Comment\Comment;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->get();

        if ($users->isEmpty()) {
            return;
        }

        $articles = BlogArticle::query()->limit(8)->get();
        $videos = BlogVideo::query()->limit(3)->get();

        $commentables = [
            'article' => $articles,
            'video' => $videos,
        ];

        $moderatorId = User::query()->whereKey(1)->exists()
            ? 1
            : $users->first()->id;

        $moderation = [
            'activity' => true,
            'moderation_status' => 1,
            'moderated_by' => $moderatorId,
            'moderated_at' => now(),
            'moderation_note' => null,
        ];

        foreach ($commentables as $commentableType => $models) {
            foreach ($models as $model) {
                $parents = Comment::factory()
                    ->count(3)
                    ->create(array_merge($moderation, [
                        'user_id' => $users->random()->id,
                        'commentable_id' => $model->id,
                        'commentable_type' => $commentableType,
                        'parent_id' => null,
                    ]));

                foreach ($parents as $parentLevel1) {
                    $childrenLevel2 = Comment::factory()
                        ->count(rand(1, 2))
                        ->create(array_merge($moderation, [
                            'user_id' => $users->random()->id,
                            'commentable_id' => $parentLevel1->commentable_id,
                            'commentable_type' => $parentLevel1->commentable_type,
                            'parent_id' => $parentLevel1->id,
                        ]));

                    foreach ($childrenLevel2 as $parentLevel2) {
                        Comment::factory()
                            ->count(rand(1, 2))
                            ->create(array_merge($moderation, [
                                'user_id' => $users->random()->id,
                                'commentable_id' => $parentLevel2->commentable_id,
                                'commentable_type' => $parentLevel2->commentable_type,
                                'parent_id' => $parentLevel2->id,
                            ]));
                    }
                }
            }
        }
    }
}
