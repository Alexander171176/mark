<?php

namespace Database\Seeders;

use App\Models\Admin\School\Enrollment\SchoolEnrollment;
use App\Models\Admin\School\Quiz\SchoolQuiz;
use App\Models\Admin\School\QuizAttempt\SchoolQuizAttempt;
use Illuminate\Database\Seeder;

class SchoolQuizAttemptSeeder extends Seeder
{
    public function run(): void
    {
        $id = 1;

        foreach (range(1, 6) as $quizId) {
            $quiz = SchoolQuiz::find($quizId);

            if (!$quiz) {
                continue;
            }

            foreach (range(2, 7) as $userId) {
                $enrollment = SchoolEnrollment::where('user_id', $userId)
                    ->where('school_course_id', $quiz->school_course_id)
                    ->first();

                $maxScore = 14;
                $score = 8 + (($id * 2) % 7);
                $percent = (int) floor(($score / $maxScore) * 100);

                SchoolQuizAttempt::updateOrCreate(
                    ['id' => $id],
                    [
                        'user_id' => $userId,
                        'school_quiz_id' => $quiz->id,
                        'school_enrollment_id' => $enrollment?->id,
                        'school_course_id' => $quiz->school_course_id,
                        'school_module_id' => $quiz->school_module_id,
                        'school_lesson_id' => $quiz->school_lesson_id,

                        'attempt_number' => 1,
                        'score' => $score,
                        'max_score' => $maxScore,
                        'percent' => $percent,
                        'status' => $percent >= 70 ? 'graded' : 'completed',

                        'started_at' => now()->subDays($id)->subMinutes(35),
                        'finished_at' => now()->subDays($id),
                        'duration_seconds' => 2100,

                        'ip_address' => '127.0.0.1',
                        'user_agent' => 'Seeder Demo User Agent',
                    ]
                );

                $id++;
            }
        }
    }
}
