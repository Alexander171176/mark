<?php

namespace Database\Seeders;

use App\Models\Admin\School\SchoolAssignment\SchoolAssignment;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use Illuminate\Database\Seeder;

class SchoolAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $assignmentSlugs = [
            1 => 'knowledge-check',
            2 => 'code-practice',
            3 => 'mini-project',
            4 => 'debugging-task',
            5 => 'architecture-task',
            6 => 'final-task',
        ];

        $id = 1;

        for ($lessonId = 1; $lessonId <= 3024; $lessonId++) {
            $lesson = SchoolLesson::with('module.course')->find($lessonId);

            if (!$lesson || !$lesson->module || !$lesson->module->course) {
                continue;
            }

            $module = $lesson->module;
            $course = $module->course;

            foreach ($assignmentSlugs as $assignmentNumber => $slug) {
                SchoolAssignment::updateOrCreate(
                    ['id' => $id],
                    [
                        'school_course_id' => $course->id,
                        'school_module_id' => $module->id,
                        'school_lesson_id' => $lesson->id,
                        'school_instructor_profile_id' => $course->school_instructor_profile_id,

                        'slug' => 'lesson-' . $lessonId . '-' . $slug,
                        'sort' => $id,
                        'activity' => true,

                        'left' => $id % 3 === 0,
                        'main' => $id % 4 === 0,
                        'right' => $id % 5 === 0,

                        'published_at' => now()->subDays($assignmentNumber),

                        'status' => 'published',
                        'visibility' => $assignmentNumber === 1 ? 'public' : 'enrolled',

                        'attempts_limit' => match ($assignmentNumber) {
                            1 => 0,
                            2, 3 => 3,
                            4, 5 => 2,
                            default => 1,
                        },

                        'grading_type' => $assignmentNumber === 1 ? 'auto' : 'manual',
                        'max_score' => match ($assignmentNumber) {
                            1 => 20,
                            2 => 40,
                            3 => 60,
                            4 => 70,
                            5 => 80,
                            default => 100,
                        },

                        'due_at' => now()->addDays(7 + $assignmentNumber),
                    ]
                );

                $id++;
            }
        }
    }
}
