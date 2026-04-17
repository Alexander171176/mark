<?php

namespace Database\Seeders;

use App\Models\Admin\Finance\Order\Order;
use App\Models\Admin\Finance\OrderItem\OrderItem;
use App\Models\Admin\School\Bundle\Bundle;
use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\Enrollment\Enrollment;
use App\Models\Admin\School\Lesson\Lesson;
use App\Models\Admin\School\Module\Module;
use App\Models\Admin\School\QaThread\QaThread;
use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class QaThreadSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('qa_threads') || !Schema::hasTable('users')) {
            $this->command?->warn('Нет таблиц qa_threads/users — пропускаю QaThreadSeeder.');
            return;
        }

        $users = User::query()->get(['id','name']);
        if ($users->isEmpty()) {
            $this->command?->warn('Пользователи не найдены — пропускаю QaThreadSeeder.');
            return;
        }

        $faker = fake();
        $created = 0; $updated = 0;

        // Собираем целевые сущности (каждая — опционально)
        $courses = Schema::hasTable('courses')
            ? Course::query()->get(['id','title'])->all()
            : [];

        $modules = Schema::hasTable('modules')
            ? Module::query()->get(['id','course_id','title'])->all()
            : [];

        $lessons = Schema::hasTable('lessons')
            ? Lesson::query()->get(['id','module_id','title'])->all()
            : [];

        $bundles = Schema::hasTable('bundles')
            ? Bundle::query()->get(['id','title'])->all()
            : [];

        // ===== Курсы
        foreach ($courses as $course) {
            $candidateUsers = $this->candidateUsersForCourse($course->id, $users);
            [$c,$u] = $this->seedForThreadable($faker, $candidateUsers, $users, $course, \App\Models\Admin\School\Course\Course::class);
            $created += $c; $updated += $u;
        }

        // ===== Модули
        foreach ($modules as $module) {
            $courseId = (int)($module->course_id ?? 0);
            $candidateUsers = $courseId
                ? $this->candidateUsersForCourse($courseId, $users)
                : $users->pluck('id');
            [$c,$u] = $this->seedForThreadable($faker, $candidateUsers, $users, $module, \App\Models\Admin\School\Module\Module::class);
            $created += $c; $updated += $u;
        }

        // ===== Уроки
        foreach ($lessons as $lesson) {
            $courseId = null;
            if ($lesson->module_id && Schema::hasTable('modules')) {
                $courseId = Module::query()->where('id', $lesson->module_id)->value('course_id');
            }
            $candidateUsers = $courseId
                ? $this->candidateUsersForCourse((int)$courseId, $users)
                : $users->pluck('id');
            [$c,$u] = $this->seedForThreadable($faker, $candidateUsers, $users, $lesson, \App\Models\Admin\School\Lesson\Lesson::class);
            $created += $c; $updated += $u;
        }

        // ===== Бандлы
        foreach ($bundles as $bundle) {
            $candidateUsers = $this->candidateUsersForBundle($bundle->id, $users);
            [$c,$u] = $this->seedForThreadable($faker, $candidateUsers, $users, $bundle, \App\Models\Admin\School\Bundle\Bundle::class);
            $created += $c; $updated += $u;
        }

        $this->command?->info("QA threads upserted: created {$created}, updated {$updated}.");
    }

    /**
     * Пользователи, связанные с курсом через зачисления; иначе — все.
     */
    private function candidateUsersForCourse(int $courseId, $allUsers)
    {
        if ($courseId && Schema::hasTable('enrollments')) {
            $ids = Enrollment::query()
                ->where('course_id', $courseId)
                ->pluck('user_id')
                ->unique()
                ->values();
            if ($ids->isNotEmpty()) return $ids;
        }
        return $allUsers->pluck('id');
    }

    /**
     * Пользователи, покупавшие бандл через order_items → orders; иначе — все.
     */
    private function candidateUsersForBundle(int $bundleId, $allUsers)
    {
        if ($bundleId && Schema::hasTable('order_items') && Schema::hasTable('orders')) {
            $orderIds = OrderItem::query()
                ->where('purchasable_type', 'bundle')   // alias из morphMap
                ->where('purchasable_id', $bundleId)    // ID бандла
                ->pluck('order_id')
                ->unique()
                ->values();

            if ($orderIds->isNotEmpty()) {
                $userIds = Order::query()
                    ->whereIn('id', $orderIds)
                    ->pluck('user_id')
                    ->unique()
                    ->values();

                if ($userIds->isNotEmpty()) {
                    return $userIds;
                }
            }
        }

        return $allUsers->pluck('id');
    }

    /**
     * Создаёт/обновляет 2–3 темы для одной threadable‑сущности.
     *
     * @param Generator $faker
     * @param Collection $candidateUserIds
     * @param \Illuminate\Database\Eloquent\Collection $allUsers
     * @param Model $threadable
     * @param string $type Полный класс типа (Course::class, Module::class, и т.д.)
     * @return array [created, updated]
     */
    private function seedForThreadable($faker, $candidateUserIds, $allUsers, $threadable, string $type): array
    {
        $created = 0; $updated = 0;

        if ($candidateUserIds->isEmpty()) {
            $candidateUserIds = $allUsers->pluck('id');
        }
        if ($candidateUserIds->isEmpty()) return [0,0];

        $count = min(max(2, rand(2, 3)), $candidateUserIds->count());
        $userIds = $candidateUserIds->random($count)->values();

        $titlesPool = [
            'Вопрос по домашнему заданию',
            'Как подготовиться к следующему уроку?',
            'Непонятно объяснение в разделе',
            'Подскажите по проекту',
            'Есть ли дополнительные материалы?',
            'Техническая проблема с доступом',
            'Рекомендации по литературе',
        ];

        foreach ($userIds as $uid) {
            $title = $faker->randomElement($titlesPool);
            // Небольшой шанс разнообразить заголовок
            if ($faker->boolean(30)) {
                $title .= ': ' . $faker->words(rand(2,4), true);
            }

            $body = $faker->boolean(70) ? $faker->paragraphs(rand(1, 2), true) : null;

            // Статусы и флаги
            $status = $faker->randomElement(['open','open','open','closed','archived']);
            $isLocked = $status !== 'open' ? $faker->boolean(70) : false;
            $isPinned = $faker->boolean(10);

            // Активность
            $createdAt = now()->subDays(rand(0, 120))->subHours(rand(0, 72));
            $repliesCount = $faker->numberBetween(0, 8);
            $lastReplyAt = $repliesCount > 0 ? $createdAt->copy()->addDays(rand(0, 30))->addMinutes(rand(0, 1440)) : null;
            $lastActivityAt = $lastReplyAt ?? $createdAt;

            $where = [
                'user_id'         => $uid,
                'threadable_type' => $type,
                'threadable_id'   => $threadable->id,
                'title'           => $title, // считаем связкой «уникат» для идемпотентности
            ];

            $payload = [
                'body'              => $body,
                'status'            => $status,
                'is_locked'         => $isLocked,
                'is_pinned'         => $isPinned,
                'replies_count'     => $repliesCount,
                'last_reply_at'     => $lastReplyAt,
                'last_activity_at'  => $lastActivityAt,
                'meta'              => [
                    'seeded'   => true,
                    'seed_run' => now()->toDateTimeString(),
                ],
                // фиксируем даты так, чтобы seed выглядел правдоподобно
                'created_at'        => $createdAt,
                'updated_at'        => $lastActivityAt,
            ];

            $existing = QaThread::query()
                ->where('user_id', $uid)
                ->where('threadable_type', $type)
                ->where('threadable_id', $threadable->id)
                ->where('title', $title)
                ->first();

            if ($existing) {
                // Обновляем без перетирания created_at
                $existing->fill(collect($payload)->except('created_at')->all())->save();
                $updated++;
            } else {
                QaThread::query()->create(array_merge($where, $payload));
                $created++;
            }
        }

        return [$created, $updated];
    }
}
