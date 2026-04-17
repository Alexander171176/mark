<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $currencies = [
            [
                'sort' => 1,
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'precision' => 2,
                'symbol_first' => true,
                'thousands_sep' => ' ',
                'decimal_sep' => '.',
                'activity' => true,
                'is_default' => true,
                'set_default_at' => $now,
            ],
            [
                'sort' => 2,
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'precision' => 2,
                'symbol_first' => true,
                'thousands_sep' => ' ',
                'decimal_sep' => ',',
                'activity' => true,
                'is_default' => false,
                'set_default_at' => null,
            ],
            [
                'sort' => 3,
                'code' => 'KZT',
                'name' => 'Kazakhstani Tenge',
                'symbol' => '₸',
                'precision' => 2,
                'symbol_first' => false,
                'thousands_sep' => ' ',
                'decimal_sep' => '.',
                'activity' => true,
                'is_default' => false,
                'set_default_at' => null,
            ],
            [
                'sort' => 4,
                'code' => 'RUB',
                'name' => 'Russian Ruble',
                'symbol' => '₽',
                'precision' => 2,
                'symbol_first' => false,
                'thousands_sep' => ' ',
                'decimal_sep' => ',',
                'activity' => true,
                'is_default' => false,
                'set_default_at' => null,
            ],
        ];

        foreach ($currencies as $data) {
            $exists = DB::table('currencies')->where('code', $data['code'])->exists();

            if ($exists) {
                DB::table('currencies')
                    ->where('code', $data['code'])
                    ->update(array_merge($data, ['updated_at' => $now]));
            } else {
                DB::table('currencies')
                    ->insert(array_merge($data, ['created_at' => $now, 'updated_at' => $now]));
            }
        }
    }
}
