<?php

namespace Database\Seeders;

use App\Models\Admin\Finance\CoursePrice\CoursePrice;
use App\Models\Admin\School\Course\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CoursePriceSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['courses', 'course_prices', 'currencies'] as $table) {
            if (!Schema::hasTable($table)) {
                $this->command?->warn("Таблица {$table} отсутствует — пропускаю CoursePriceSeeder.");
                return;
            }
        }

        $courses = Course::query()
            ->select(['id', 'difficulty'])
            ->get();

        if ($courses->isEmpty()) {
            $this->command?->warn('Курсы не найдены — сперва посейте CourseSeeder.');
            return;
        }

        // code => id (только активные), нормализуем CODE в верхний регистр
        $rows = DB::table('currencies')
            ->select(['id', 'code', 'activity', 'sort'])
            ->orderBy('sort')
            ->get();

        $currencyMap = [];
        foreach ($rows as $row) {
            if ((int)$row->activity !== 1) continue;
            $currencyMap[strtoupper(trim((string)$row->code))] = (int)$row->id;
        }

        // гарантируем USD
        if (!isset($currencyMap['USD'])) {
            $usd = DB::table('currencies')->select(['id','activity'])->where('code', 'USD')->first();
            if (!$usd) {
                $this->command?->warn('Валюта USD не найдена в currencies — пропускаю CoursePriceSeeder.');
                return;
            }
            if ((int)$usd->activity !== 1) {
                $this->command?->warn('Валюта USD найдена, но не активна. Цены всё равно будут посеяны для USD.');
            }
            $currencyMap['USD'] = (int)$usd->id;
        }

        $availableCodes = array_keys($currencyMap);
        $now = now();

        DB::transaction(function () use ($courses, $availableCodes, $currencyMap, $now) {
            foreach ($courses as $course) {
                $usdBase = match ((int) ($course->difficulty ?? 2)) {
                    0, 1 => rand(29, 59),
                    2, 3 => rand(69, 129),
                    default => rand(139, 219),
                };

                $codes = ['USD'];

                $others = array_values(array_filter($availableCodes, fn ($c) => $c !== 'USD'));
                shuffle($others);
                $take = rand(0, min(2, count($others)));
                $codes = array_merge($codes, array_slice($others, 0, $take));

                $sort = 0;

                foreach (array_values(array_unique($codes)) as $code) {
                    $currencyId = $currencyMap[$code] ?? null;
                    if (!$currencyId) continue;

                    $mult = match ($code) {
                        'EUR' => 0.92,
                        'RUB' => 92.0,
                        'KZT' => 485.0,
                        default => 1.0,
                    };

                    $base = round($usdBase * $mult, 2);

                    $compare = rand(0, 100) < 55
                        ? round($base * rand(110, 135) / 100, 2)
                        : null;

                    $hasSale = rand(0, 100) < 50;
                    $sale    = $hasSale ? round($base * rand(70, 95) / 100, 2) : null;

                    $starts  = $hasSale ? $now->copy()->subDays(rand(0, 7)) : null;
                    $ends    = $hasSale ? $now->copy()->addDays(rand(3, 21)) : null;

                    CoursePrice::query()->updateOrCreate(
                        [
                            'course_id'   => (int) $course->id,
                            'currency_id' => (int) $currencyId,
                            'sort'        => (int) $sort,
                        ],
                        [
                            'price'            => $base,
                            'sale_price'       => $sale,
                            'compare_at_price' => $compare,
                            'starts_at'        => $starts,
                            'ends_at'          => $ends,
                            'activity'         => true,
                            'meta'             => ['badge' => $hasSale ? 'sale' : null],
                        ]
                    );

                    $sort += 10;
                }
            }
        });
    }
}
