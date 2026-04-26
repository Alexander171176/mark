<?php

namespace Database\Seeders;

use App\Models\Admin\Blog\BlogTag\BlogTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogTagSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $userId = 1;

            BlogTag::updateOrCreate(
                ['slug' => 'php'],
                [
                    'user_id' => $userId,
                    'sort' => 0,
                    'activity' => true,
                    'moderation_status' => 1,
                    'moderated_by' => $userId,
                    'moderated_at' => now(),
                    'moderation_note' => null,
                    'icon' => 'code',
                    'views' => 120,
                ]
            );

            BlogTag::updateOrCreate(
                ['slug' => 'laravel'],
                [
                    'user_id' => $userId,
                    'sort' => 1,
                    'activity' => true,
                    'moderation_status' => 1,
                    'moderated_by' => $userId,
                    'moderated_at' => now(),
                    'moderation_note' => null,
                    'icon' => 'flame',
                    'views' => 180,
                ]
            );

            BlogTag::updateOrCreate(
                ['slug' => 'vue-js'],
                [
                    'user_id' => $userId,
                    'sort' => 2,
                    'activity' => true,
                    'moderation_status' => 1,
                    'moderated_by' => $userId,
                    'moderated_at' => now(),
                    'moderation_note' => null,
                    'icon' => 'component',
                    'views' => 160,
                ]
            );

            BlogTag::updateOrCreate(
                ['slug' => 'javascript'],
                [
                    'user_id' => $userId,
                    'sort' => 3,
                    'activity' => true,
                    'moderation_status' => 1,
                    'moderated_by' => $userId,
                    'moderated_at' => now(),
                    'moderation_note' => null,
                    'icon' => 'file-code',
                    'views' => 210,
                ]
            );

            BlogTag::updateOrCreate(
                ['slug' => 'tailwind-css'],
                [
                    'user_id' => $userId,
                    'sort' => 4,
                    'activity' => true,
                    'moderation_status' => 1,
                    'moderated_by' => $userId,
                    'moderated_at' => now(),
                    'moderation_note' => null,
                    'icon' => 'palette',
                    'views' => 145,
                ]
            );

            BlogTag::updateOrCreate(
                ['slug' => 'docker'],
                [
                    'user_id' => $userId,
                    'sort' => 5,
                    'activity' => true,
                    'moderation_status' => 1,
                    'moderated_by' => $userId,
                    'moderated_at' => now(),
                    'moderation_note' => null,
                    'icon' => 'box',
                    'views' => 130,
                ]
            );

            BlogTag::updateOrCreate(
                ['slug' => 'mysql'],
                [
                    'user_id' => $userId,
                    'sort' => 6,
                    'activity' => true,
                    'moderation_status' => 1,
                    'moderated_by' => $userId,
                    'moderated_at' => now(),
                    'moderation_note' => null,
                    'icon' => 'database',
                    'views' => 95,
                ]
            );

            BlogTag::updateOrCreate(
                ['slug' => 'postgresql'],
                [
                    'user_id' => $userId,
                    'sort' => 7,
                    'activity' => true,
                    'moderation_status' => 1,
                    'moderated_by' => $userId,
                    'moderated_at' => now(),
                    'moderation_note' => null,
                    'icon' => 'circle-stack',
                    'views' => 100,
                ]
            );

            BlogTag::updateOrCreate(
                ['slug' => 'api'],
                [
                    'user_id' => $userId,
                    'sort' => 8,
                    'activity' => true,
                    'moderation_status' => 1,
                    'moderated_by' => $userId,
                    'moderated_at' => now(),
                    'moderation_note' => null,
                    'icon' => 'plug',
                    'views' => 175,
                ]
            );

            BlogTag::updateOrCreate(
                ['slug' => 'devops'],
                [
                    'user_id' => $userId,
                    'sort' => 9,
                    'activity' => true,
                    'moderation_status' => 1,
                    'moderated_by' => $userId,
                    'moderated_at' => now(),
                    'moderation_note' => null,
                    'icon' => 'cloud',
                    'views' => 115,
                ]
            );

            app(BlogTagTranslationSeeder::class)->run();
        });
    }
}
