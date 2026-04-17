<?php

namespace Database\Seeders;

use App\Models\Admin\School\Assignment\Assignment;
use App\Models\Admin\School\AssignmentSubmission\AssignmentSubmission;
use App\Models\Admin\School\Enrollment\Enrollment;
use App\Models\Admin\School\InstructorProfile\InstructorProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AssignmentSubmissionSeeder extends Seeder
{
    public function run(): void
    {
        if (
            !Schema::hasTable('assignment_submissions') ||
            !Schema::hasTable('assignments') ||
            !Schema::hasTable('users')
        ) {
            $this->command?->warn('Нет таблиц assignment_submissions/assignments/users — пропускаю AssignmentSubmissionSeeder.');
            return;
        }

        $assignments = Assignment::query()
            ->with(['course:id,instructor_profile_id', 'lesson:id'])
            ->get(['id','course_id','lesson_id','title','slug','due_at','max_score']);

        if ($assignments->isEmpty()) {
            $this->command?->warn('Задания не найдены — сперва посей AssignmentSeeder.');
            return;
        }

        $users = User::query()->get(['id','name','email']);
        if ($users->isEmpty()) {
            $this->command?->warn('Пользователи не найдены — пропускаю AssignmentSubmissionSeeder.');
            return;
        }

        $faker = fake();
        $created = 0; $updated = 0;

        foreach ($assignments as $a) {
            // Подбор кандидатов-студентов
            $candidateUserIds = collect();

            // Если есть зачисления на курс — возьмём их в приоритет
            if ($a->course_id && Schema::hasTable('enrollments')) {
                $enrolled = Enrollment::query()
                    ->where('course_id', $a->course_id)
                    ->pluck('user_id');
                if ($enrolled->isNotEmpty()) {
                    $candidateUserIds = $enrolled->unique()->values();
                }
            }

            if ($candidateUserIds->isEmpty()) {
                $candidateUserIds = $users->pluck('id');
            }

            if ($candidateUserIds->isEmpty()) {
                continue;
            }

            // Сколько сдач сделать по этому заданию
            $count = min(max(3, rand(3, 4)), $candidateUserIds->count());
            $pickedUserIds = $candidateUserIds->random($count)->values();

            // Определим возможного проверяющего
            $graderId = null;
            if ($a->course?->instructor_profile_id) {
                $graderId = InstructorProfile::query()
                    ->where('id', $a->course->instructor_profile_id)
                    ->value('user_id');
            }
            if (!$graderId) {
                $graderId = $users->random()->id; // fallback
            }

            foreach ($pickedUserIds as $uid) {
                // Статус и даты
                $statusPool = ['submitted','under_review','graded','needs_changes'];
                $status = $statusPool[array_rand($statusPool)];

                // Время отправки — до дедлайна, если он есть; иначе до "сейчас"
                $due      = $a->due_at ? $a->due_at->copy() : now();
                $submittedAt = (clone $due)->subDays(rand(0, 10))->subHours(rand(0, 72));

                $gradedAt = null;
                $score    = null;
                $review   = null;
                $finalGraderId = null;

                if ($status === 'graded') {
                    $gradedAt = (clone $submittedAt)->addDays(rand(0, 5))->addHours(rand(1, 48));
                    $maxScore = (int)($a->max_score ?? 100);
                    $score    = round(min($maxScore, max(0, $faker->numberBetween((int)($maxScore*0.5), $maxScore))), 2);
                    $review   = $faker->randomElement([
                        'Отличная работа! Пара мелких замечаний.',
                        'Хорошо, но стоит доработать оформление.',
                        'Превосходно — все критерии соблюдены.',
                        'Зачтено. См. комментарии по коду.',
                    ]);
                    $finalGraderId = $graderId;
                } elseif ($status === 'needs_changes') {
                    $review = $faker->randomElement([
                        'Нужно доработать: добавить тесты.',
                        'Не хватает описания шага установки.',
                        'Проверьте обработку ошибок и повторите сдачу.',
                    ]);
                    $finalGraderId = $graderId;
                }

                // Лёгкие вложения
                $attachments = $faker->boolean(40) ? [
                    [
                        'name' => 'solution.md',
                        'url'  => 'https://example.com/files/'.Str::uuid().'.md',
                        'size' => rand(2, 40) * 1024,
                        'mime' => 'text/markdown',
                    ],
                    $faker->boolean(50) ? [
                        'name' => 'screenshot.png',
                        'url'  => 'https://picsum.photos/seed/'.Str::uuid().'/600/400',
                        'size' => rand(40, 400) * 1024,
                        'mime' => 'image/png',
                    ] : null,
                ] : null;

                if (is_array($attachments)) {
                    $attachments = array_values(array_filter($attachments));
                }

                $content = $faker->boolean(80)
                    ? $faker->paragraphs(rand(2, 5), true)
                    : null;

                // Идемпотентно по (assignment_id, user_id)
                $where = [
                    'assignment_id' => $a->id,
                    'user_id'       => $uid,
                ];

                $payload = [
                    'lesson_id'       => $a->lesson_id,
                    'content'         => $content,
                    'attachments'     => $attachments,
                    'status'          => $status,
                    'score'           => $score,
                    'review_comment'  => $review,
                    'graded_by'       => $finalGraderId,
                    'submitted_at'    => $submittedAt,
                    'graded_at'       => $gradedAt,
                ];

                /** @var AssignmentSubmission $model */
                $model = AssignmentSubmission::query()->where($where)->first();
                if ($model) {
                    $model->fill($payload)->save();
                    $updated++;
                } else {
                    AssignmentSubmission::query()->create(array_merge($where, $payload));
                    $created++;
                }
            }
        }

        $this->command?->info("Assignment submissions upserted: created {$created}, updated {$updated}.");
    }
}
