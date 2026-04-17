<?php

namespace Database\Seeders;

use App\Models\Admin\School\Bookmark\Bookmark;
use App\Models\Admin\School\Bundle\Bundle;
use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\Lesson\Lesson;
use App\Models\Admin\School\Module\Module;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class BookmarkSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('bookmarks') || !Schema::hasTable('users')) {
            $this->command?->warn('Нет таблиц bookmarks/users — пропускаю BookmarkSeeder.');
            return;
        }

        $users = User::query()->get(['id','name','email']);
        if ($users->isEmpty()) {
            $this->command?->warn('Пользователи не найдены — пропускаю BookmarkSeeder.');
            return;
        }

        // Подтягиваем потенциальные цели (только если таблицы есть и там есть данные)
        $targets = [];

        // Course
        if (Schema::hasTable('courses')) {
            $courseModel = Course::class;
            $courseIds   = $courseModel::query()->pluck('id')->all();
            if (!empty($courseIds)) $targets[] = ['class' => $courseModel, 'ids' => $courseIds];
        }

        // Module
        if (Schema::hasTable('modules')) {
            $moduleModel = Module::class;
            $moduleIds   = $moduleModel::query()->pluck('id')->all();
            if (!empty($moduleIds)) $targets[] = ['class' => $moduleModel, 'ids' => $moduleIds];
        }

        // Lesson
        if (Schema::hasTable('lessons')) {
            $lessonModel = Lesson::class;
            $lessonIds   = $lessonModel::query()->pluck('id')->all();
            if (!empty($lessonIds)) $targets[] = ['class' => $lessonModel, 'ids' => $lessonIds];
        }

        // Bundle (опционально)
        if (Schema::hasTable('bundles')) {
            $bundleModel = Bundle::class;
            $bundleIds   = $bundleModel::query()->pluck('id')->all();
            if (!empty($bundleIds)) $targets[] = ['class' => $bundleModel, 'ids' => $bundleIds];
        }

        if (empty($targets)) {
            $this->command?->warn('Нет целей для закладок (courses/modules/lessons/bundles пусты) — пропускаю BookmarkSeeder.');
            return;
        }

        $faker   = fake();
        $total   = 0;

        foreach ($users as $user) {
            // Удалим только наши прошлые посевы
            Bookmark::query()
                ->where('user_id', $user->id)
                ->where('meta->seeded', true)
                ->delete();

            $count   = $faker->numberBetween(5, 15);
            $folders = [null, null, 'Избранное', 'Смотреть позже', 'Повторить', 'Важное']; // null встречается чаще
            $pos     = 1;

            $usedPairs = []; // защита от дубля по уникальному ключу (user_id + type + id)

            for ($i = 0; $i < $count; $i++) {
                // Выбираем случайный тип и конкретный ID
                $pick   = $targets[array_rand($targets)];
                $class  = $pick['class'];
                $ids    = $pick['ids'];
                $bid    = $ids[array_rand($ids)];

                $pairKey = $class . '#' . $bid;
                if (isset($usedPairs[$pairKey])) {
                    // попытка ещё раз
                    $i--;
                    continue;
                }
                $usedPairs[$pairKey] = true;

                $folder      = $folders[array_rand($folders)];
                $isFavorite  = (bool) $faker->boolean(30);
                $note        = $faker->boolean(40) ? $faker->sentence() : null;

                // Времена: распределим в пределах последних 60 дней
                $createdAt = now()->subDays($faker->numberBetween(0, 60))->setTime(
                    $faker->numberBetween(8, 22), $faker->numberBetween(0, 59), $faker->numberBetween(0, 59)
                );

                // Идемпотентная вставка по уникальному ключу: user + morph
                Bookmark::query()->updateOrCreate(
                    [
                        'user_id'          => $user->id,
                        'bookmarkable_type'=> $class,
                        'bookmarkable_id'  => $bid,
                    ],
                    [
                        'is_favorite'      => $isFavorite,
                        'folder'           => $folder,
                        'position'         => $pos++,
                        'note'             => $note,
                        'meta'             => [
                            'seeded'   => true,
                            'seed_run' => now()->toDateTimeString(),
                            'source'   => 'BookmarkSeeder',
                        ],
                        'created_at'       => $createdAt,
                        'updated_at'       => $createdAt,
                    ]
                );

                $total++;
            }
        }

        $this->command?->info("Bookmarks seeded/updated: {$total} записей.");
    }
}
