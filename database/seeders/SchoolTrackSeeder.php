<?php

namespace Database\Seeders;

use App\Models\Admin\School\SchoolTrack\SchoolTrack;
use Illuminate\Database\Seeder;

class SchoolTrackSeeder extends Seeder
{
    public function run(): void
    {
        $tracks = [
            ['id' => 1, 'parent_id' => null, 'slug' => 'web-development', 'views' => 3200],
            ['id' => 2, 'parent_id' => null, 'slug' => 'software-engineering', 'views' => 2800],

            ['id' => 3, 'parent_id' => 1, 'slug' => 'frontend-development', 'views' => 2100],
            ['id' => 4, 'parent_id' => 1, 'slug' => 'backend-development', 'views' => 2300],

            ['id' => 5, 'parent_id' => 2, 'slug' => 'devops-engineering', 'views' => 1700],
            ['id' => 6, 'parent_id' => 2, 'slug' => 'database-engineering', 'views' => 1600],

            ['id' => 7, 'parent_id' => 3, 'slug' => 'html-css', 'views' => 1400],
            ['id' => 8, 'parent_id' => 3, 'slug' => 'javascript-vue', 'views' => 1900],

            ['id' => 9, 'parent_id' => 4, 'slug' => 'php-laravel', 'views' => 2200],
            ['id' => 10, 'parent_id' => 4, 'slug' => 'api-development', 'views' => 1800],

            ['id' => 11, 'parent_id' => 5, 'slug' => 'docker-containers', 'views' => 1500],
            ['id' => 12, 'parent_id' => 5, 'slug' => 'ci-cd', 'views' => 1300],

            ['id' => 13, 'parent_id' => 6, 'slug' => 'mysql-design', 'views' => 1450],
            ['id' => 14, 'parent_id' => 6, 'slug' => 'database-optimization', 'views' => 1250],
        ];

        foreach ($tracks as $track) {
            SchoolTrack::updateOrCreate(
                ['id' => $track['id']],
                [
                    'parent_id' => $track['parent_id'],
                    'sort' => $track['id'],
                    'activity' => true,
                    'slug' => $track['slug'],
                    'views' => $track['views'],
                ]
            );
        }
    }
}
