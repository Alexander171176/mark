<?php

namespace Database\Seeders;

use App\Models\Admin\School\Certificate\Certificate;
use App\Models\Admin\School\Enrollment\Enrollment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CertificateSeeder extends Seeder
{
    public function run(): void
    {
        // Проверка таблиц
        $needed = ['certificates','users','courses','enrollments'];
        foreach ($needed as $tbl) {
            if (!Schema::hasTable($tbl)) {
                $this->command?->warn("Нет таблицы {$tbl} — пропускаю CertificateSeeder.");
                return;
            }
        }

        // Кандидаты на сертификаты:
        // - completed
        // - либо active c прогрессом >= 80
        $enrollments = Enrollment::query()
            ->with(['user:id,name','course:id,title'])
            ->where(function ($q) {
                $q->where('status', 'completed')
                    ->orWhere(function ($w) {
                        $w->where('status', 'active')->where('progress_percent', '>=', 80);
                    });
            })
            ->get([
                'id','user_id','course_id','status','progress_percent',
                'started_at','completed_at','created_at'
            ]);

        if ($enrollments->isEmpty()) {
            $this->command?->warn('Подходящих зачислений нет (completed / active>=80) — пропускаю CertificateSeeder.');
            return;
        }

        $faker   = fake();
        $created = 0; $updated = 0;

        foreach ($enrollments as $enroll) {
            $user   = $enroll->user;
            $course = $enroll->course;
            if (!$user || !$course) { continue; }

            // Идемпотентный ключ — один сертификат на (user, course)
            $where = [
                'user_id'   => $enroll->user_id,
                'course_id' => $enroll->course_id,
            ];

            // Выбор даты выдачи
            $issuedAt = $enroll->completed_at
                ? $enroll->completed_at->copy()
                : ($enroll->started_at
                    ? $enroll->started_at->copy()->addDays(rand(7, 30))
                    : now()->subDays(rand(5, 30)));

            // Иногда добавим срок действия (например, 30% кейсов)
            $expiresAt = null;
            if ($faker->boolean(30)) {
                $expiresAt = $issuedAt->copy()->addYears(rand(2, 5));
            }

            // Итоговые “оценка/часы”
            $score = $enroll->status === 'completed'
                ? $faker->numberBetween(75, 100)
                : max(80, (int)$enroll->progress_percent);
            $hours = $faker->randomElement([8, 12, 16, 20, 24, 30, 36, 40]);

            // Стабильные номер и код, чтобы не ломать уникальные индексы при повторном посеве
            $year   = ($enroll->completed_at?->format('Y')) ?: now()->format('Y');
            $number = $this->makeNumber((int)$year, $enroll->user_id, $enroll->course_id);
            $vcode  = $this->makeVerificationCode($enroll->user_id, $enroll->course_id);

            $payload = [
                'enrollment_id'        => $enroll->id,
                'number'               => $number,
                'verification_code'    => $vcode,
                'issued_at'            => $issuedAt,
                'expires_at'           => $expiresAt,
                'score'                => $score,
                'hours'                => $hours,
                'status'               => 'issued',
                'revoked_at'           => null,
                'name_on_certificate'  => $user->name,
                'notes'                => null,
                'meta'                 => [
                    'seeded'     => true,
                    'seed_run'   => now()->toDateTimeString(),
                    'source'     => 'CertificateSeeder',
                    'enrollment' => [
                        'status'   => $enroll->status,
                        'progress' => $enroll->progress_percent,
                    ],
                ],
                // фиксируем таймстемпы детерминированно
                'created_at'           => $issuedAt,
                'updated_at'           => $issuedAt,
            ];

            /** @var Certificate $model */
            $model = Certificate::query()->where($where)->first();

            if ($model) {
                // Если сертификат уже есть, — обновим только “мягкие” поля, но не сломаем уникальные number/code
                $model->fill(array_merge(
                    $payload,
                    [
                        // сохраняем старые number/code, если отличались (на случай предыдущих сидов)
                        'number'            => $model->number ?: $number,
                        'verification_code' => $model->verification_code ?: $vcode,
                        // updated_at освежим
                        'updated_at'        => now(),
                    ]
                ));
                $model->save();
                $updated++;
            } else {
                // create
                $model = new Certificate(array_merge($where, $payload));
                $model->save();
                $created++;
            }
        }

        $this->command?->info("Certificates upserted: created {$created}, updated {$updated}.");
    }

    private function makeNumber(int $year, int $userId, int $courseId): string
    {
        // Человеко-читаемый и детерминированный
        // Пример: CERT-2025-U00012-C00345
        return sprintf('CERT-%d-U%05d-C%05d', $year, $userId, $courseId);
    }

    private function makeVerificationCode(int $userId, int $courseId): string
    {
        // Короткий, но достаточно уникальный/стабильный код
        // Можно заменить на UUID, если хотите полностью случайный.
        $raw = "cert:{$userId}:{$courseId}:seed-v1";
        return strtoupper(substr(sha1($raw), 0, 20)); // 20 hex-символов
    }
}
