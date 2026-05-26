<?php

namespace Database\Seeders;

use App\Models\Admin\School\Module\SchoolModule;
use Illuminate\Database\Seeder;

class SchoolModuleSeeder extends Seeder
{
    public function run(): void
    {
        $moduleSlugs = [
            1 => 'introduction',
            2 => 'core-concepts',
            3 => 'practice-basics',
            4 => 'advanced-tools',
            5 => 'project-work',
            6 => 'final-review',
        ];

        $id = 1;

        for ($courseId = 1; $courseId <= 84; $courseId++) {
            foreach ($moduleSlugs as $moduleNumber => $slug) {
                SchoolModule::updateOrCreate(
                    ['id' => $id],
                    [
                        'school_course_id' => $courseId,
                        'sort' => $id,
                        'activity' => true,
                        'slug' => $slug,

                        'published_at' => now()->subDays($id),

                        'status' => 'published',
                        'availability' => 'public',

                        'difficulty' => match ($moduleNumber) {
                            1 => 1,
                            2, 3 => 2,
                            4 => 3,
                            5 => 4,
                            default => 5,
                        },

                        'duration' => 60 + ($moduleNumber * 30),

                        'lessons_count' => 0,
                        'popularity' => 100 + ($id * 2),
                        'rating_count' => 5 + $moduleNumber,
                        'rating_avg' => 4.10 + (($moduleNumber % 5) / 10),
                        'views' => 300 + ($id * 10),
                        'likes' => 20 + ($id * 2),
                    ]
                );

                $id++;
            }
        }
    }
}
