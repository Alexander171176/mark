<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencyRateSeeder extends Seeder
{
    public function run(): void
    {
        $batchAt = now();

        $map = DB::table('currencies')->pluck('id', 'code');

        $rates = [
            ['base' => 'USD', 'quote' => 'EUR', 'rate' => '0.92000000'],
            ['base' => 'USD', 'quote' => 'KZT', 'rate' => '485.00000000'],
            ['base' => 'USD', 'quote' => 'RUB', 'rate' => '93.00000000'],

            ['base' => 'EUR', 'quote' => 'USD', 'rate' => '1.09000000'],
            ['base' => 'KZT', 'quote' => 'USD', 'rate' => '0.00206000'],
            ['base' => 'RUB', 'quote' => 'USD', 'rate' => '0.01070000'],
        ];

        foreach ($rates as $r) {
            if (!isset($map[$r['base']], $map[$r['quote']])) {
                continue;
            }

            DB::table('currency_rates')->updateOrInsert(
                [
                    'base_currency_id'  => (int) $map[$r['base']],
                    'quote_currency_id' => (int) $map[$r['quote']],
                    'fetched_at'        => $batchAt,
                ],
                [
                    'rate'       => $r['rate'],
                    'provider'   => 'manual',
                    'is_manual'  => true,
                    'created_at' => $batchAt,
                    'updated_at' => $batchAt,
                ]
            );
        }
    }
}
