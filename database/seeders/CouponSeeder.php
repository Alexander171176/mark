<?php

namespace Database\Seeders;

use App\Models\Admin\School\Bundle\Bundle;
use App\Models\Admin\School\Coupon\Coupon;
use App\Models\Admin\School\Course\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('coupons')) {
            $this->command?->warn('Нет таблицы coupons — пропускаю CouponSeeder.');
            return;
        }

        // Доступность связей для привязки
        $hasCoursesTable   = Schema::hasTable('courses') && Schema::hasTable('coupon_has_course');
        $hasBundlesTable   = Schema::hasTable('bundles') && Schema::hasTable('coupon_has_bundle');

        $courses = collect();
        $bundles = collect();

        if ($hasCoursesTable) {
            $courses = Course::query()->inRandomOrder()->get(['id', 'title']);
        }
        if ($hasBundlesTable) {
            $bundles = Bundle::query()->inRandomOrder()->get(['id', 'title']);
        }

        $now = now();

        // Набор демонстрационных купонов
        $seed = [
            // ===== Процентные (any) =====
            [
                'code'        => 'WELCOME10',
                'name'        => 'Приветственная скидка 10%',
                'description' => 'Даёт 10% на любой заказ. Действует всегда.',
                'type'        => 'percent',
                'value'       => 10,
                'currency'    => null,
                'applies_to'  => 'any',
                'min_order_total'     => null,
                'max_uses'            => 1000,
                'max_uses_per_user'   => 1,
                'starts_at'           => null,
                'ends_at'             => null,
                'activity'            => true,
                'stackable'           => false,
            ],
            [
                'code'        => 'SUMMER20',
                'name'        => 'Летняя скидка 20%',
                'description' => 'Сезонная акция.',
                'type'        => 'percent',
                'value'       => 20,
                'currency'    => null,
                'applies_to'  => 'any',
                'min_order_total'     => 50,
                'max_uses'            => 500,
                'max_uses_per_user'   => 2,
                'starts_at'           => $now->copy()->subWeeks(1),
                'ends_at'             => $now->copy()->addWeeks(2),
                'activity'            => true,
                'stackable'           => true,
            ],

            // ===== Фиксированная сумма (any/courses/bundles) =====
            [
                'code'        => 'SAVE15',
                'name'        => 'Скидка $15',
                'description' => 'Фиксированная скидка на заказ от $80',
                'type'        => 'fixed',
                'value'       => 15,
                'currency'    => 'USD',
                'applies_to'  => 'any',
                'min_order_total'     => 80,
                'max_uses'            => 300,
                'max_uses_per_user'   => 3,
                'starts_at'           => $now->copy()->subDays(3),
                'ends_at'             => $now->copy()->addDays(10),
                'activity'            => true,
                'stackable'           => false,
            ],
            [
                'code'        => 'COURSE50',
                'name'        => 'Курс − $50',
                'description' => 'Скидка $50 только на курсы',
                'type'        => 'fixed',
                'value'       => 50,
                'currency'    => 'USD',
                'applies_to'  => 'courses',
                'min_order_total'     => 100,
                'max_uses'            => 200,
                'max_uses_per_user'   => 2,
                'starts_at'           => $now->copy()->subDays(7),
                'ends_at'             => $now->copy()->addDays(30),
                'activity'            => true,
                'stackable'           => false,
            ],
            [
                'code'        => 'BUNDLE15',
                'name'        => 'Бандлы − $15',
                'description' => 'Небольшая скидка для бандлов',
                'type'        => 'fixed',
                'value'       => 15,
                'currency'    => 'USD',
                'applies_to'  => 'bundles',
                'min_order_total'     => null,
                'max_uses'            => 9999,
                'max_uses_per_user'   => 10,
                'starts_at'           => null,
                'ends_at'             => null,
                'activity'            => true,
                'stackable'           => true,
            ],

            // ===== Полная скидка (free) =====
            [
                'code'        => 'FREE100',
                'name'        => 'Полностью бесплатно',
                'description' => '100% скидка, ограниченное число применений',
                'type'        => 'free',
                'value'       => 100,
                'currency'    => null,
                'applies_to'  => 'any',
                'min_order_total'     => null,
                'max_uses'            => 20,
                'max_uses_per_user'   => 1,
                'starts_at'           => $now->copy()->subDays(1),
                'ends_at'             => $now->copy()->addDays(2),
                'activity'            => true,
                'stackable'           => false,
            ],

            // ===== Будущий (ещё не начался) =====
            [
                'code'        => 'BLACKFRIDAY30',
                'name'        => 'Black Friday −30%',
                'description' => 'Активируется ближе к распродаже',
                'type'        => 'percent',
                'value'       => 30,
                'currency'    => null,
                'applies_to'  => 'any',
                'min_order_total'     => 30,
                'max_uses'            => null,
                'max_uses_per_user'   => 3,
                'starts_at'           => $now->copy()->addWeeks(4),
                'ends_at'             => $now->copy()->addWeeks(5),
                'activity'            => true,
                'stackable'           => true,
            ],

            // ===== Истёкший (для истории) =====
            [
                'code'        => 'SPRING15',
                'name'        => 'Весенняя −15%',
                'description' => 'Истекшая акция',
                'type'        => 'percent',
                'value'       => 15,
                'currency'    => null,
                'applies_to'  => 'any',
                'min_order_total'     => null,
                'max_uses'            => 1000,
                'max_uses_per_user'   => 2,
                'starts_at'           => $now->copy()->subMonths(2),
                'ends_at'             => $now->copy()->subMonths(1),
                'activity'            => false,
                'stackable'           => false,
            ],
        ];

        foreach ($seed as $row) {
            $code = Str::upper($row['code']);

            /** @var Coupon $coupon */
            $coupon = Coupon::query()->updateOrCreate(
                ['code' => $code],
                [
                    'name'               => $row['name'],
                    'description'        => $row['description'],
                    'type'               => $row['type'],         // percent|fixed|free
                    'value'              => $row['value'],
                    'currency'           => $row['currency'],
                    'min_order_total'    => $row['min_order_total'],
                    'max_uses'           => $row['max_uses'],
                    'max_uses_per_user'  => $row['max_uses_per_user'],
                    // used_count НЕ трогаем — это счётчик реального применения
                    'applies_to'         => $row['applies_to'],   // any|courses|bundles (общая подсказка)
                    'starts_at'          => $row['starts_at'],
                    'ends_at'            => $row['ends_at'],
                    'activity'           => $row['activity' ],
                    'stackable'          => $row['stackable'],
                    'meta'               => [
                        'seeded'   => true,
                        'seed_run' => now()->toDateTimeString(),
                    ],
                ]
            );

            // Привязки к конкретным сущностям (если нужно)
            if ($row['applies_to'] === 'courses' && $hasCoursesTable && $courses->isNotEmpty()) {
                // случайный набор 3–8 курсов
                $ids = $courses->pluck('id')->shuffle()->take(rand(3, min(8, max(3, $courses->count()))))->all();
                $coupon->courses()->syncWithoutDetaching($ids);
            }

            if ($row['applies_to'] === 'bundles' && $hasBundlesTable && $bundles->isNotEmpty()) {
                // случайный набор 2–5 бандлов
                $ids = $bundles->pluck('id')->shuffle()->take(rand(2, min(5, max(2, $bundles->count()))))->all();
                $coupon->bundles()->syncWithoutDetaching($ids);
            }

            // Для "any" — иногда сузим область для демонстрации привязок
            if ($row['applies_to'] === 'any') {
                if ($hasCoursesTable && $courses->isNotEmpty() && rand(0,100) < 25) {
                    $ids = $courses->pluck('id')->shuffle()->take(rand(2, min(6, $courses->count())))->all();
                    $coupon->courses()->syncWithoutDetaching($ids);
                }
                if ($hasBundlesTable && $bundles->isNotEmpty() && rand(0,100) < 25) {
                    $ids = $bundles->pluck('id')->shuffle()->take(rand(1, min(4, $bundles->count())))->all();
                    $coupon->bundles()->syncWithoutDetaching($ids);
                }
            }
        }

        $this->command?->info('Coupons seeded/updated (идемпотентно).');
    }
}
