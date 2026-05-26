<?php

namespace Database\Seeders;

use App\Models\Admin\School\Price\SchoolBundlePrice;
use Illuminate\Database\Seeder;

class SchoolBundlePriceSeeder extends Seeder
{
    public function run(): void
    {
        for ($bundleId = 1; $bundleId <= 6; $bundleId++) {
            $basePrice = 199 + ($bundleId * 40);

            SchoolBundlePrice::updateOrCreate(
                ['id' => $bundleId],
                [
                    'school_bundle_id' => $bundleId,
                    'currency_id' => 1,

                    'price' => $basePrice,
                    'sale_price' => $bundleId % 2 === 0
                        ? $basePrice - 30
                        : null,
                    'compare_at_price' => $bundleId % 2 === 0
                        ? $basePrice + 60
                        : null,

                    'starts_at' => now()->subDays(30),
                    'ends_at' => null,

                    'activity' => true,
                    'sort' => $bundleId,

                    'meta' => [
                        'source' => 'seeder',
                        'demo' => true,
                        'price_type' => 'bundle',
                    ],
                ]
            );
        }
    }
}
