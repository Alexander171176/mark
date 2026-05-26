<?php

namespace Database\Seeders;

use App\Models\Admin\School\InstructorProfile\SchoolInstructorProfile;
use Illuminate\Database\Seeder;

class SchoolInstructorProfileSeeder extends Seeder
{
    public function run(): void
    {
        $instructors = [
            [
                'id' => 2,
                'user_id' => 2,
                'slug' => 'alexey-smirnov',
                'sort' => 2,
                'experience_years' => 8,
                'hourly_rate' => 25.00,
                'rating_count' => 120,
                'rating_avg' => 4.8,
                'views' => 3200,
            ],
            [
                'id' => 3,
                'user_id' => 3,
                'slug' => 'john-doe',
                'sort' => 3,
                'experience_years' => 10,
                'hourly_rate' => 35.00,
                'rating_count' => 210,
                'rating_avg' => 4.9,
                'views' => 4100,
            ],
            [
                'id' => 4,
                'user_id' => 4,
                'slug' => 'aigerim-nur',
                'sort' => 4,
                'experience_years' => 6,
                'hourly_rate' => 20.00,
                'rating_count' => 95,
                'rating_avg' => 4.7,
                'views' => 2500,
            ],
            [
                'id' => 5,
                'user_id' => 5,
                'slug' => 'michael-lee',
                'sort' => 5,
                'experience_years' => 12,
                'hourly_rate' => 40.00,
                'rating_count' => 180,
                'rating_avg' => 4.85,
                'views' => 3900,
            ],
            [
                'id' => 6,
                'user_id' => 6,
                'slug' => 'dana-askar',
                'sort' => 6,
                'experience_years' => 5,
                'hourly_rate' => 18.00,
                'rating_count' => 70,
                'rating_avg' => 4.6,
                'views' => 2100,
            ],
            [
                'id' => 7,
                'user_id' => 7,
                'slug' => 'sergey-ivanov',
                'sort' => 7,
                'experience_years' => 9,
                'hourly_rate' => 28.00,
                'rating_count' => 150,
                'rating_avg' => 4.75,
                'views' => 3300,
            ],
        ];

        foreach ($instructors as $item) {
            SchoolInstructorProfile::updateOrCreate(
                ['id' => $item['id']],
                [
                    'sort' => $item['sort'],
                    'activity' => true,
                    'user_id' => $item['user_id'],
                    'slug' => $item['slug'],
                    'experience_years' => $item['experience_years'],
                    'hourly_rate' => $item['hourly_rate'],
                    'rating_count' => $item['rating_count'],
                    'rating_avg' => $item['rating_avg'],
                    'views' => $item['views'],
                    'social_links' => [
                        'linkedin' => 'https://linkedin.com',
                        'github' => 'https://github.com',
                    ],
                ]
            );
        }
    }
}
