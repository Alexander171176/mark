<?php

namespace Database\Seeders;

use App\Models\Admin\Finance\ExchangeRate\ExchangeRate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ExchangeRateSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('exchange_rates')) {
            $this->command?->warn('Нет таблицы exchange_rates — пропускаю ExchangeRateSeeder.');
            return;
        }

        $now   = now();
        $today = $now->copy()->startOfDay();

        // Базовые ориентиры для сидов: 1 USD = rate * QUOTE
        $usdPairs = [
            'EUR' => 0.92,
            'GBP' => 0.79,
            'PLN' => 3.95,
            'KZT' => 490.00,
            'RUB' => 93.00,
        ];

        // Полный список валют для генерации I→I (=1.0) и перекрёстных курсов
        $currencies = array_values(array_unique(array_merge(['USD'], array_keys($usdPairs))));

        // 1) Прямые пары USD→X + инверсные X→USD
        $pairs = [];
        foreach ($usdPairs as $quote => $rate) {
            $pairs[] = ['base' => 'USD', 'quote' => $quote, 'rate' => $this->r2($rate)];
            if ($rate > 0) {
                $pairs[] = ['base' => $quote, 'quote' => 'USD', 'rate' => $this->r2(1 / $rate)];
            }
        }

        // Индекс для быстрых подсчётов перекрёстных курсов
        $index = [];
        foreach ($pairs as $p) {
            $index[$p['base']][$p['quote']] = $p['rate'];
        }

        // 2) Перекрёстные курсы через USD: A→B = (A→USD) * (USD→B)
        foreach ($currencies as $a) {
            foreach ($currencies as $b) {
                if ($a === $b) continue;
                if (!isset($index[$a][$b]) && isset($index[$a]['USD']) && isset($index['USD'][$b])) {
                    $rate = $this->r2($index[$a]['USD'] * $index['USD'][$b]);
                    $pairs[] = ['base' => $a, 'quote' => $b, 'rate' => $rate];
                    $index[$a][$b] = $rate;
                }
            }
        }

        // 3) Единичные курсы A→A = 1.00 (упрощает конвертацию)
        foreach ($currencies as $c) {
            $pairs[] = ['base' => $c, 'quote' => $c, 'rate' => $this->r2(1.0)];
        }

        // Для идемпотентности: закроем прошлые активные "дневные" записи и создадим/обновим "срез" на сегодня
        $created = 0; $updated = 0;

        // Чтобы не дублировать одну и ту же пару в рамках одного запуска
        $seen = [];

        foreach ($pairs as $p) {
            $base  = strtoupper($p['base']);
            $quote = strtoupper($p['quote']);
            $rate  = $this->r2((float) $p['rate']);

            $key = $base.'|'.$quote;
            if (isset($seen[$key])) {
                continue; // уже обработали эту пару в текущем запуске
            }
            $seen[$key] = true;

            // Закрыть активные записи до сегодняшнего дня (если вдруг остались "вечно активные")
            ExchangeRate::query()
                ->where('base_currency', $base)
                ->where('quote_currency', $quote)
                ->where('activity', true)
                ->where(function ($q) use ($today) {
                    $q->whereNull('valid_from')->orWhere('valid_from', '<', $today);
                })
                ->update([
                    'activity' => false,
                    'valid_to'  => $now,
                    'updated_at'=> $now,
                ]);

            // Один "срез" на день — valid_from = сегодня 00:00:00
            $where = [
                'base_currency' => $base,
                'quote_currency'=> $quote,
                'valid_from'    => $today,
            ];

            $payload = [
                'rate'       => $rate,          // ← округлённое до 2 знаков
                'provider'   => 'seed',
                'fetched_at' => $now,
                'valid_to'   => null,
                'activity'   => true,
                'meta'       => [
                    'seeded'   => true,
                    'seed_run' => $now->toDateTimeString(),
                    'method'   => 'triangulated',
                ],
            ];

            /** @var ExchangeRate|null $model */
            $model = ExchangeRate::query()->where($where)->first();

            if ($model) {
                $model->fill($payload);
                $model->save();
                $updated++;
            } else {
                $model = new ExchangeRate(array_merge($where, $payload));
                $model->save();
                $created++;
            }
        }

        $this->command?->info("Exchange rates upserted: created {$created}, updated {$updated}.");
    }

    /** Округление до 2 знаков (float для записи в DECIMAL) */
    private function r2(float $v): float
    {
        return round($v, 2);
    }
}
