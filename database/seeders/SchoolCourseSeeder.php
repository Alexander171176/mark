<?php

namespace Database\Seeders;

use App\Models\Admin\School\Course\SchoolCourse;
use Illuminate\Database\Seeder;

class SchoolCourseSeeder extends Seeder
{
    public function run(): void
    {
        $trackSlugs = [
            1 => 'web-development',
            2 => 'software-engineering',
            3 => 'frontend-development',
            4 => 'backend-development',
            5 => 'devops-engineering',
            6 => 'database-engineering',
            7 => 'html-css',
            8 => 'javascript-vue',
            9 => 'php-laravel',
            10 => 'api-development',
            11 => 'docker-containers',
            12 => 'ci-cd',
            13 => 'mysql-design',
            14 => 'database-optimization',
        ];

        $courseTypes = [
            1 => 'fundamentals',
            2 => 'practice',
            3 => 'advanced',
            4 => 'project',
            5 => 'architecture',
            6 => 'production',
        ];

        $id = 1;

        foreach ($trackSlugs as $trackId => $trackSlug) {
            foreach ($courseTypes as $typeId => $typeSlug) {
                $instructorId = (($id - 1) % 6) + 2;

                $course = SchoolCourse::updateOrCreate(
                    ['id' => $id],
                    [
                        'school_instructor_profile_id' => $instructorId,
                        'sort' => $id,
                        'activity' => true,

                        'is_new' => $typeId === 1,
                        'is_hit' => in_array($typeId, [3, 4], true),
                        'is_sale' => in_array($typeId, [2, 6], true),

                        'left' => $id % 3 === 0,
                        'main' => $id % 4 === 0,
                        'right' => $id % 5 === 0,

                        'slug' => $trackSlug . '-' . $typeSlug,

                        'published_at' => now()->subDays($id),

                        'level' => match ($typeId) {
                            1, 2 => 'beginner',
                            3, 4 => 'intermediate',
                            default => 'advanced',
                        },

                        'status' => 'published',
                        'availability' => 'public',

                        'difficulty' => match ($typeId) {
                            1 => 1,
                            2 => 2,
                            3, 4 => 3,
                            5 => 4,
                            default => 5,
                        },

                        'duration' => 360 + ($typeId * 90),

                        'students_count' => 40 + ($id * 3),
                        'popularity' => 100 + ($id * 5),
                        'rating_count' => 10 + $id,
                        'rating_avg' => 4.20 + (($id % 7) / 10),
                        'views' => 800 + ($id * 35),
                        'likes' => 50 + ($id * 4),
                    ]
                );

                $course->tracks()->syncWithoutDetaching([$trackId]);

                $id++;
            }
        }
    }
}
