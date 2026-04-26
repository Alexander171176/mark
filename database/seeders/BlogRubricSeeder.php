<?php

namespace Database\Seeders;

use App\Models\Admin\Blog\BlogRubric\BlogRubric;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogRubricSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            /**
             * Для тестов привязываем к super user.
             * При необходимости замени на нужный ID.
             */
            $userId = 1;

            /*
             |--------------------------------------------------------------------------
             | LEVEL 1
             |--------------------------------------------------------------------------
             */
            $frontend = BlogRubric::create([
                'user_id' => $userId,
                'parent_id' => null,
                'level' => 1,
                'in_menu' => true,
                'sort' => 0,
                'activity' => true,
                'moderation_status' => 1,
                'moderated_by' => $userId,
                'moderated_at' => now(),
                'moderation_note' => null,
                'icon' => 'code',
                'url' => 'frontend-development',
                'views' => 120,
            ]);

            $backend = BlogRubric::create([
                'user_id' => $userId,
                'parent_id' => null,
                'level' => 1,
                'in_menu' => true,
                'sort' => 1,
                'activity' => true,
                'moderation_status' => 1,
                'moderated_by' => $userId,
                'moderated_at' => now(),
                'moderation_note' => null,
                'icon' => 'server',
                'url' => 'backend-development',
                'views' => 140,
            ]);

            $devops = BlogRubric::create([
                'user_id' => $userId,
                'parent_id' => null,
                'level' => 1,
                'in_menu' => true,
                'sort' => 2,
                'activity' => true,
                'moderation_status' => 1,
                'moderated_by' => $userId,
                'moderated_at' => now(),
                'moderation_note' => null,
                'icon' => 'cloud',
                'url' => 'devops-and-infrastructure',
                'views' => 90,
            ]);

            /*
             |--------------------------------------------------------------------------
             | LEVEL 2
             |--------------------------------------------------------------------------
             */
            $htmlCss = BlogRubric::create([
                'user_id' => $userId,
                'parent_id' => $frontend->id,
                'level' => 2,
                'in_menu' => true,
                'sort' => 0,
                'activity' => true,
                'moderation_status' => 1,
                'moderated_by' => $userId,
                'moderated_at' => now(),
                'moderation_note' => null,
                'icon' => 'layout',
                'url' => 'html-and-css',
                'views' => 80,
            ]);

            $javascript = BlogRubric::create([
                'user_id' => $userId,
                'parent_id' => $frontend->id,
                'level' => 2,
                'in_menu' => true,
                'sort' => 1,
                'activity' => true,
                'moderation_status' => 1,
                'moderated_by' => $userId,
                'moderated_at' => now(),
                'moderation_note' => null,
                'icon' => 'file-code',
                'url' => 'javascript',
                'views' => 200,
            ]);

            $php = BlogRubric::create([
                'user_id' => $userId,
                'parent_id' => $backend->id,
                'level' => 2,
                'in_menu' => true,
                'sort' => 0,
                'activity' => true,
                'moderation_status' => 1,
                'moderated_by' => $userId,
                'moderated_at' => now(),
                'moderation_note' => null,
                'icon' => 'terminal',
                'url' => 'php',
                'views' => 160,
            ]);

            $databases = BlogRubric::create([
                'user_id' => $userId,
                'parent_id' => $backend->id,
                'level' => 2,
                'in_menu' => true,
                'sort' => 1,
                'activity' => true,
                'moderation_status' => 1,
                'moderated_by' => $userId,
                'moderated_at' => now(),
                'moderation_note' => null,
                'icon' => 'database',
                'url' => 'databases',
                'views' => 110,
            ]);

            $docker = BlogRubric::create([
                'user_id' => $userId,
                'parent_id' => $devops->id,
                'level' => 2,
                'in_menu' => true,
                'sort' => 0,
                'activity' => true,
                'moderation_status' => 1,
                'moderated_by' => $userId,
                'moderated_at' => now(),
                'moderation_note' => null,
                'icon' => 'box',
                'url' => 'docker',
                'views' => 95,
            ]);

            $ciCd = BlogRubric::create([
                'user_id' => $userId,
                'parent_id' => $devops->id,
                'level' => 2,
                'in_menu' => true,
                'sort' => 1,
                'activity' => true,
                'moderation_status' => 1,
                'moderated_by' => $userId,
                'moderated_at' => now(),
                'moderation_note' => null,
                'icon' => 'git-branch',
                'url' => 'ci-cd',
                'views' => 70,
            ]);

            /*
             |--------------------------------------------------------------------------
             | LEVEL 3
             |--------------------------------------------------------------------------
             */
            $vue = BlogRubric::create([
                'user_id' => $userId,
                'parent_id' => $javascript->id,
                'level' => 3,
                'in_menu' => true,
                'sort' => 0,
                'activity' => true,
                'moderation_status' => 1,
                'moderated_by' => $userId,
                'moderated_at' => now(),
                'moderation_note' => null,
                'icon' => 'component',
                'url' => 'vue-js',
                'views' => 180,
            ]);

            $tailwind = BlogRubric::create([
                'user_id' => $userId,
                'parent_id' => $htmlCss->id,
                'level' => 3,
                'in_menu' => true,
                'sort' => 1,
                'activity' => true,
                'moderation_status' => 1,
                'moderated_by' => $userId,
                'moderated_at' => now(),
                'moderation_note' => null,
                'icon' => 'palette',
                'url' => 'tailwind-css',
                'views' => 150,
            ]);

            $laravel = BlogRubric::create([
                'user_id' => $userId,
                'parent_id' => $php->id,
                'level' => 3,
                'in_menu' => true,
                'sort' => 0,
                'activity' => true,
                'moderation_status' => 1,
                'moderated_by' => $userId,
                'moderated_at' => now(),
                'moderation_note' => null,
                'icon' => 'flame',
                'url' => 'laravel',
                'views' => 210,
            ]);

            $mysql = BlogRubric::create([
                'user_id' => $userId,
                'parent_id' => $databases->id,
                'level' => 3,
                'in_menu' => true,
                'sort' => 0,
                'activity' => true,
                'moderation_status' => 1,
                'moderated_by' => $userId,
                'moderated_at' => now(),
                'moderation_note' => null,
                'icon' => 'cylinder',
                'url' => 'mysql',
                'views' => 85,
            ]);

            $postgresql = BlogRubric::create([
                'user_id' => $userId,
                'parent_id' => $databases->id,
                'level' => 3,
                'in_menu' => true,
                'sort' => 1,
                'activity' => true,
                'moderation_status' => 1,
                'moderated_by' => $userId,
                'moderated_at' => now(),
                'moderation_note' => null,
                'icon' => 'circle-stack',
                'url' => 'postgresql',
                'views' => 88,
            ]);

            $dockerCompose = BlogRubric::create([
                'user_id' => $userId,
                'parent_id' => $docker->id,
                'level' => 3,
                'in_menu' => true,
                'sort' => 0,
                'activity' => true,
                'moderation_status' => 1,
                'moderated_by' => $userId,
                'moderated_at' => now(),
                'moderation_note' => null,
                'icon' => 'layers',
                'url' => 'docker-compose',
                'views' => 65,
            ]);

            $githubActions = BlogRubric::create([
                'user_id' => $userId,
                'parent_id' => $ciCd->id,
                'level' => 3,
                'in_menu' => true,
                'sort' => 0,
                'activity' => true,
                'moderation_status' => 1,
                'moderated_by' => $userId,
                'moderated_at' => now(),
                'moderation_note' => null,
                'icon' => 'workflow',
                'url' => 'github-actions',
                'views' => 60,
            ]);

            app(BlogRubricTranslationSeeder::class)->run();
        });
    }
}
