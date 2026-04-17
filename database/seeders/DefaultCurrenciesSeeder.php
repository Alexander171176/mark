<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultCurrenciesSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CurrencySeeder::class,
            CurrencyRateSeeder::class,
        ]);

        // гарантируем единственный default: USD
        DB::table('currencies')->update([
            'is_default'     => false,
            'set_default_at' => null,
            'updated_at'     => now(),
        ]);

        DB::table('currencies')
            ->where('code', 'USD')
            ->update([
                'is_default'     => true,
                'set_default_at' => now(),
                'updated_at'     => now(),
            ]);
    }
}
