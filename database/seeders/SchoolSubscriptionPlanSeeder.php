<?php

namespace Database\Seeders;

use App\Models\Admin\School\SubscriptionPlan\SchoolSubscriptionPlan;
use Illuminate\Database\Seeder;

class SchoolSubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [1, 'starter', 'month', 1, 19.00, 7],
            [2, 'standard', 'month', 1, 39.00, 14],
            [3, 'pro', 'month', 1, 79.00, 14],
            [4, 'team', 'month', 1, 149.00, 30],
            [5, 'annual-standard', 'year', 1, 390.00, 30],
            [6, 'annual-pro', 'year', 1, 790.00, 30],
        ];

        foreach ($plans as [$id, $slug, $period, $interval, $price, $trialDays]) {
            SchoolSubscriptionPlan::updateOrCreate(
                ['id' => $id],
                [
                    'sort' => $id,
                    'activity' => true,
                    'slug' => $slug,

                    'published_at' => now()->subDays($id),
                    'available_from' => now()->subDays(30),
                    'available_until' => null,

                    'billing_period' => $period,
                    'interval' => $interval,
                    'currency_id' => 1,
                    'price' => $price,
                    'trial_days' => $trialDays,
                    'auto_renew' => true,

                    'provider' => 'stripe',
                    'provider_ref' => 'school_' . $slug,
                    'provider_payload' => [
                        'source' => 'seeder',
                        'demo' => true,
                    ],

                    'config' => [
                        'max_courses' => match ($id) {
                            1 => 3,
                            2 => 10,
                            3 => 999,
                            4 => 999,
                            5 => 10,
                            6 => 999,
                            default => 1,
                        },
                        'certificate_access' => $id >= 2,
                        'support' => $id >= 3 ? 'priority' : 'standard',
                    ],
                ]
            );
        }
    }
}
