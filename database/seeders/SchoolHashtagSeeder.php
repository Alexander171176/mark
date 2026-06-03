<?php

namespace Database\Seeders;

use App\Models\Admin\School\SchoolHashtag\SchoolHashtag;
use Illuminate\Database\Seeder;

class SchoolHashtagSeeder extends Seeder
{
    public function run(): void
    {
        $hashtags = [
            ['id' => 1, 'sort' => 1, 'slug' => 'php', 'color' => '#777BB4', 'views' => 1250, 'likes' => 180],
            ['id' => 2, 'sort' => 2, 'slug' => 'laravel', 'color' => '#FF2D20', 'views' => 2100, 'likes' => 320],
            ['id' => 3, 'sort' => 3, 'slug' => 'vue-js', 'color' => '#42B883', 'views' => 1850, 'likes' => 270],
            ['id' => 4, 'sort' => 4, 'slug' => 'javascript', 'color' => '#F7DF1E', 'views' => 2300, 'likes' => 350],
            ['id' => 5, 'sort' => 5, 'slug' => 'typescript', 'color' => '#3178C6', 'views' => 1600, 'likes' => 240],
            ['id' => 6, 'sort' => 6, 'slug' => 'html', 'color' => '#E34F26', 'views' => 1400, 'likes' => 190],
            ['id' => 7, 'sort' => 7, 'slug' => 'css', 'color' => '#1572B6', 'views' => 1500, 'likes' => 210],
            ['id' => 8, 'sort' => 8, 'slug' => 'tailwind-css', 'color' => '#06B6D4', 'views' => 1700, 'likes' => 260],
            ['id' => 9, 'sort' => 9, 'slug' => 'mysql', 'color' => '#4479A1', 'views' => 1100, 'likes' => 150],
            ['id' => 10, 'sort' => 10, 'slug' => 'docker', 'color' => '#2496ED', 'views' => 1350, 'likes' => 205],
            ['id' => 11, 'sort' => 11, 'slug' => 'api', 'color' => '#6B7280', 'views' => 1200, 'likes' => 175],
            ['id' => 12, 'sort' => 12, 'slug' => 'git', 'color' => '#F05032', 'views' => 1000, 'likes' => 145],
        ];

        foreach ($hashtags as $hashtag) {
            SchoolHashtag::updateOrCreate(
                ['id' => $hashtag['id']],
                [
                    'sort' => $hashtag['sort'],
                    'activity' => true,
                    'slug' => $hashtag['slug'],
                    'color' => $hashtag['color'],
                    'views' => $hashtag['views'],
                    'likes' => $hashtag['likes'],
                ]
            );
        }
    }
}
