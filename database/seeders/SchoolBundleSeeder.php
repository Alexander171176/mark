<?php

namespace Database\Seeders;

use App\Models\Admin\School\Bundle\SchoolBundle;
use Illuminate\Database\Seeder;

class SchoolBundleSeeder extends Seeder
{
    public function run(): void
    {
        $bundles = [
            [1, 'frontend-developer-bundle', [7, 8, 13, 14, 19, 20]],
            [2, 'backend-developer-bundle', [9, 10, 15, 16, 21, 22]],
            [3, 'fullstack-laravel-vue-bundle', [7, 8, 9, 10, 31, 32]],
            [4, 'devops-docker-ci-cd-bundle', [25, 26, 27, 28, 29, 30]],
            [5, 'database-engineer-bundle', [31, 32, 33, 34, 35, 36]],
            [6, 'junior-programmer-start-bundle', [1, 2, 3, 7, 8, 9]],
        ];

        foreach ($bundles as [$id, $slug, $courseIds]) {
            $bundle = SchoolBundle::updateOrCreate(
                ['id' => $id],
                [
                    'sort' => $id,
                    'activity' => true,
                    'slug' => $slug,
                    'published_at' => now()->subDays($id),
                    'views' => 1000 + ($id * 250),
                    'likes' => 100 + ($id * 25),
                    'meta' => [
                        'source' => 'seeder',
                        'demo' => true,
                        'courses_count' => count($courseIds),
                    ],
                ]
            );

            $bundle->courses()->sync($courseIds);
        }
    }
}
