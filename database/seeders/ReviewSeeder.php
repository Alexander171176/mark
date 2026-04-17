<?php

namespace Database\Seeders;

use App\Models\Admin\Finance\OrderItem\OrderItem;
use App\Models\Admin\School\Bundle\Bundle;
use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\Enrollment\Enrollment;
use App\Models\Admin\School\Lesson\Lesson;
use App\Models\Admin\School\Module\Module;
use App\Models\Admin\School\Review\Review;
use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('reviews') || !Schema::hasTable('users')) {
            $this->command?->warn('Нет таблиц reviews/users — пропускаю ReviewSeeder.');
            return;
        }

        $users = User::query()->get(['id','name','email']);
        if ($users->isEmpty()) {
            $this->command?->warn('Пользователи не найдены — пропускаю ReviewSeeder.');
            return;
        }

        $faker = fake();
        $created = 0; $updated = 0;

        /* ===== Сбор сущностей ===== */
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

        /* ===== Генерация отзывов для разных типов ===== */
        // Курсы
        foreach ($courses as $course) {
            $candidateUserIds = $this->candidateUsersForCourse($course->id, $users);
            [$c, $u] = $this->seedForReviewable($faker, $candidateUserIds, $users, $course, Course::class);
            $created += $c; $updated += $u;
        }

        // Модули
        foreach ($modules as $module) {
            $courseId = (int) ($module->course_id ?? 0);
            $candidateUserIds = $courseId ? $this->candidateUsersForCourse($courseId, $users) : $users->pluck('id');
            [$c, $u] = $this->seedForReviewable($faker, $candidateUserIds, $users, $module, Module::class);
            $created += $c; $updated += $u;
        }

        // Уроки
        foreach ($lessons as $lesson) {
            // найдём course_id через модуль (если таблицы есть)
            $courseId = null;
            if ($lesson->module_id && Schema::hasTable('modules')) {
                $courseId = Module::query()->where('id', $lesson->module_id)->value('course_id');
            }
            $candidateUserIds = $courseId ? $this->candidateUsersForCourse((int)$courseId, $users) : $users->pluck('id');
            [$c, $u] = $this->seedForReviewable($faker, $candidateUserIds, $users, $lesson, Lesson::class);
            $created += $c; $updated += $u;
        }

        // Бандлы
        foreach ($bundles as $bundle) {
            $candidateUserIds = $this->candidateUsersForBundle($bundle->id, $users);
            [$c, $u] = $this->seedForReviewable($faker, $candidateUserIds, $users, $bundle, Bundle::class);
            $created += $c; $updated += $u;
        }

        $this->command?->info("Reviews upserted: created {$created}, updated {$updated}.");
    }

    /**
     * Вернуть список user_id, связанных с курсом через зачисления (если есть), иначе все пользователи.
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
     * Вернуть список user_id, покупавших бандл через order_items (если есть), иначе все пользователи.
     */
    private function candidateUsersForBundle(int $bundleId, $allUsers)
    {
        if ($bundleId && Schema::hasTable('order_items') && Schema::hasTable('orders')) {
            // ищем позиции заказа, где purchasable = Bundle с нужным ID
            $orderIds = OrderItem::query()
                ->where('purchasable_type', 'bundle')   // alias из morphMap
                ->where('purchasable_id', $bundleId)    // ID бандла
                ->pluck('order_id')
                ->unique()
                ->values();

            if ($orderIds->isNotEmpty()) {
                $userIds = \App\Models\Admin\Finance\Order\Order::query()
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
     * Создаёт/обновляет 3–10 отзывов для одной reviewable-сущности.
     *
     * @param Generator $faker
     * @param Collection $candidateUserIds
     * @param \Illuminate\Database\Eloquent\Collection $allUsers
     * @param Model $reviewable
     * @param string $type Полный класс типа (Course::class, Module::class и т.д.)
     * @return array [created, updated]
     */
    private function seedForReviewable($faker, $candidateUserIds, $allUsers, $reviewable, string $type): array
    {
        $created = 0; $updated = 0;

        if ($candidateUserIds->isEmpty()) {
            $candidateUserIds = $allUsers->pluck('id');
        }
        if ($candidateUserIds->isEmpty()) {
            return [0, 0];
        }

        $count = min(max(3, rand(3, 10)), $candidateUserIds->count());
        $userIds = $candidateUserIds->random($count)->values();

        foreach ($userIds as $uid) {
            $rating = $this->biasedRating(); // 3..5, чаще 4–5

            // статус/публикация
            $status = $faker->randomElement(['approved','approved','approved','pending','rejected']); // bias к approved
            $publishedAt = $status === 'approved' ? now()->subDays(rand(0, 120))->subHours(rand(0, 72)) : null;

            $title = $faker->boolean(70)
                ? $faker->randomElement([
                    'Отличный материал',
                    'Очень полезно',
                    'Хороший курс',
                    'Есть куда расти',
                    'Рекомендую!',
                ])
                : null;

            $body = $faker->boolean(85)
                ? $faker->paragraphs(rand(1, 3), true)
                : null;

            $helpful = $faker->numberBetween(0, max(1, (int)ceil($rating * 5))); // немного завязано на рейтинг
            $reported = $faker->boolean(10) ? $faker->numberBetween(0, 2) : 0;

            $where = [
                'user_id'         => $uid,
                'reviewable_type' => $type,
                'reviewable_id'   => $reviewable->id,
            ];

            $payload = [
                'rating'         => $rating,
                'title'          => $title,
                'body'           => $body,
                'status'         => $status,
                'is_public'      => $status === 'approved',
                'published_at'   => $publishedAt,
                'helpful_count'  => $helpful,
                'reported_count' => $reported,
                'meta'           => [
                    'seeded'   => true,
                    'seed_run' => now()->toDateTimeString(),
                ],
            ];

            /** @var Review|null $existing */
            $existing = Review::query()->where($where)->first();
            if ($existing) {
                $existing->fill($payload)->save();
                $updated++;
            } else {
                Review::query()->create(array_merge($where, $payload));
                $created++;
            }
        }

        return [$created, $updated];
    }

    private function biasedRating(): int
    {
        // больше 4–5, реже 3
        $pool = [5,5,5,4,4,4,4,3,3];
        return $pool[array_rand($pool)];
    }
}
