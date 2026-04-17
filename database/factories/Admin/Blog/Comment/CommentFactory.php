<?php

namespace Database\Factories\Admin\Blog\Comment;

use App\Models\Admin\Blog\Comment\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            // Автор комментария (случайный пользователь)
            'user_id' => User::query()->inRandomOrder()->value('id'),

            // Эти поля задаются из сидера (оставляем null по умолчанию)
            'commentable_id'   => null,
            'commentable_type' => null,
            'parent_id'        => null,

            'content' => $this->faker->realText(120),

            // Новая система вместо approved
            'activity' => true,

            // 0=pending, 1=approved, 2=rejected
            'moderation_status' => 1,

            // Так как у нас блог сообщества — модерирует владелец (user_id=1)
            'moderated_by' => 1,
            'moderated_at' => now(),
            'moderation_note' => null,
        ];
    }
}
