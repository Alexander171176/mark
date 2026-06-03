<?php

namespace Database\Seeders;

use App\Models\Admin\School\SchoolQuiz\SchoolQuiz;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use Illuminate\Database\Seeder;

class SchoolQuizSeeder extends Seeder
{
    public function run(): void
    {
        $quizSlugs = [
            1 => 'basic-knowledge-quiz',
            2 => 'theory-practice-quiz',
            3 => 'code-understanding-quiz',
            4 => 'debugging-skills-quiz',
            5 => 'architecture-thinking-quiz',
            6 => 'final-lesson-quiz',
        ];

        foreach ($quizSlugs as $id => $slug) {
            $lesson = SchoolLesson::with('module.course')->find($id);

            if (!$lesson || !$lesson->module || !$lesson->module->course) {
                continue;
            }

            SchoolQuiz::updateOrCreate(
                ['id' => $id],
                [
                    'school_course_id' => $lesson->module->course->id,
                    'school_module_id' => $lesson->module->id,
                    'school_lesson_id' => $lesson->id,

                    'slug' => $slug,
                    'type' => $id % 2 === 0 ? 'practice' : 'graded',

                    'attempts_limit' => match ($id) {
                        1, 2 => 0,
                        3, 4 => 3,
                        default => 2,
                    },

                    'time_limit_minutes' => match ($id) {
                        1 => 10,
                        2 => 15,
                        3 => 20,
                        4 => 25,
                        5 => 30,
                        default => 40,
                    },

                    'pass_score' => match ($id) {
                        1, 2 => 60,
                        3, 4 => 70,
                        default => 80,
                    },

                    'sort' => $id,
                    'activity' => true,
                    'published_at' => now()->subDays($id),

                    'left' => $id === 2,
                    'main' => in_array($id, [1, 3, 5], true),
                    'right' => $id === 4,
                ]
            );
        }
    }
}
