<?php

namespace Database\Seeders;

use App\Models\Admin\Finance\BundlePrice\BundlePrice;
use App\Models\Admin\School\Bundle\Bundle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BundlePriceSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * Защита от запуска сидера, если таблиц нет (например, откатывался по миграциям).
         */
        foreach (['bundles', 'bundle_prices', 'currencies'] as $table) {
            if (!Schema::hasTable($table)) {
                $this->command?->warn("Таблица {$table} отсутствует — пропускаю BundlePriceSeeder.");
                return;
            }
        }

        /**
         * Получаем все бандлы (нам нужен только id).
         */
        $bundles = Bundle::query()
            ->select(['id'])
            ->get();

        if ($bundles->isEmpty()) {
            $this->command?->warn('Бандлы не найдены — сперва посейте BundleSeeder.');
            return;
        }

        /**
         * Строим карту валют code => id (только активные).
         * CODE нормализуем в верхний регистр.
         */
        $rows = DB::table('currencies')
            ->select(['id', 'code', 'activity', 'sort'])
            ->orderBy('sort')
            ->get();

        $currencyMap = [];
        foreach ($rows as $row) {
            if ((int) $row->activity !== 1) continue;

            $code = strtoupper(trim((string) $row->code));
            if ($code === '') continue;

            $currencyMap[$code] = (int) $row->id;
        }

        /**
         * Гарантируем наличие USD (даже если она неактивна — всё равно можем посеять).
         */
        if (!isset($currencyMap['USD'])) {
            $usd = DB::table('currencies')
                ->select(['id', 'activity'])
                ->where('code', 'USD')
                ->first();

            if (!$usd) {
                $this->command?->warn('Валюта USD не найдена в currencies — пропускаю BundlePriceSeeder.');
                return;
            }

            if ((int) $usd->activity !== 1) {
                $this->command?->warn('Валюта USD найдена, но не активна. Цены всё равно будут посеяны для USD.');
            }

            $currencyMap['USD'] = (int) $usd->id;
        }

        $availableCodes = array_keys($currencyMap);
        $now = now();

        /**
         * Сеем всё в транзакции: либо полностью, либо ничего.
         */
        DB::transaction(function () use ($bundles, $availableCodes, $currencyMap, $now) {

            foreach ($bundles as $bundle) {

                /**
                 * Бандлы обычно дороже курсов:
                 * делаем базовую цену в USD в более высоком диапазоне.
                 */
                $usdBase = rand(149, 499);

                /**
                 * Всегда создаём USD-цену, плюс 0–2 случайные валюты из доступных.
                 */
                $codes = ['USD'];

                $others = array_values(array_filter($availableCodes, fn ($c) => $c !== 'USD'));
                shuffle($others);
                $take = rand(0, min(2, count($others)));
                $codes = array_merge($codes, array_slice($others, 0, $take));

                $sort = 0;

                foreach (array_values(array_unique($codes)) as $code) {
                    $currencyId = $currencyMap[$code] ?? null;
                    if (!$currencyId) continue;

                    /**
                     * Простейшие “курсы конвертации” для сидера (демо-данные).
                     * В реальности валютные курсы берутся из справочника/сервиса.
                     */
                    $mult = match ($code) {
                        'EUR' => 0.92,
                        'RUB' => 92.0,
                        'KZT' => 485.0,
                        default => 1.0,
                    };

                    // Базовая цена в валюте
                    $base = round($usdBase * $mult, 2);

                    /**
                     * compare_at_price — “старая цена” для витрины (показываем скидку).
                     * С вероятностью ~60% задаём её выше базовой.
                     */
                    $compare = rand(0, 100) < 60
                        ? round($base * rand(110, 145) / 100, 2)
                        : null;

                    /**
                     * sale_price — акционная цена. С вероятностью ~55% делаем скидку.
                     */
                    $hasSale = rand(0, 100) < 55;
                    $sale    = $hasSale ? round($base * rand(65, 92) / 100, 2) : null;

                    /**
                     * Период действия акции: если есть sale_price,
                     * то задаём starts_at/ends_at, иначе null.
                     */
                    $starts = $hasSale ? $now->copy()->subDays(rand(0, 10)) : null;
                    $ends   = $hasSale ? $now->copy()->addDays(rand(5, 28)) : null;

                    /**
                     * ВАЖНО:
                     * В ключах уникальности должны быть только bundle_id + currency_id.
                     * sort НЕ должен быть в условии поиска, иначе при изменении sort будут дубликаты.
                     */
                    BundlePrice::query()->updateOrCreate(
                        [
                            'bundle_id'   => (int) $bundle->id,
                            'currency_id' => (int) $currencyId,
                        ],
                        [
                            'price'            => $base,
                            'sale_price'       => $sale,
                            'compare_at_price' => $compare,
                            'starts_at'        => $starts,
                            'ends_at'          => $ends,
                            'activity'         => true,
                            'sort'             => (int) $sort,
                            'meta'             => ['badge' => $hasSale ? 'sale' : null],
                        ]
                    );

                    $sort += 10;
                }
            }
        });
    }
}
