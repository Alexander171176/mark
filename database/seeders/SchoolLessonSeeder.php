<?php

namespace Database\Seeders;

use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use Illuminate\Database\Seeder;

class SchoolLessonSeeder extends Seeder
{
    public function run(): void
    {
        $lessonSlugs = [
            1 => 'overview',
            2 => 'theory',
            3 => 'examples',
            4 => 'practice',
            5 => 'common-mistakes',
            6 => 'homework',
        ];

        $id = 1;

        for ($moduleId = 1; $moduleId <= 504; $moduleId++) {
            foreach ($lessonSlugs as $lessonNumber => $slug) {
                SchoolLesson::updateOrCreate(
                    ['id' => $id],
                    [
                        'school_module_id' => $moduleId,
                        'sort' => $id,
                        'activity' => true,
                        'slug' => $slug,

                        'content_type' => null,
                        'content_id' => null,

                        'published_at' => now()->subDays($id),

                        'status' => 'published',
                        'availability' => 'public',
                        'access_type' => $lessonNumber === 1 ? 'free' : 'paid',

                        'difficulty' => match ($lessonNumber) {
                            1 => 1,
                            2, 3 => 2,
                            4 => 3,
                            5 => 4,
                            default => 5,
                        },

                        'duration' => 10 + ($lessonNumber * 5),

                        'preview_mode' => $lessonNumber === 1 ? 'full' : 'minutes',
                        'preview_value' => $lessonNumber === 1 ? null : 5,

                        'popularity' => 100 + ($id * 2),
                        'rating_count' => 3 + $lessonNumber,
                        'rating_avg' => 4.10 + (($lessonNumber % 5) / 10),
                        'views' => 150 + ($id * 5),
                        'likes' => 10 + ($id * 2),
                    ]
                );

                $id++;
            }
        }
    }
}
