<?php

namespace Database\Seeders;

use App\Models\Admin\School\SchoolOrder\SchoolOrder;
use App\Models\Admin\School\SchoolBundle\SchoolBundle;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolOrderItem\SchoolOrderItem;
use App\Models\Admin\School\SchoolSubscriptionPlan\SchoolSubscriptionPlan;
use Illuminate\Database\Seeder;

class SchoolOrderItemSeeder extends Seeder
{
    public function run(): void
    {
        $id = 1;

        $orders = SchoolOrder::orderBy('id')->get();

        foreach ($orders as $order) {
            $typeIndex = (($order->id - 1) % 3) + 1;

            $purchasableClass = match ($typeIndex) {
                1 => SchoolCourse::class,
                2 => SchoolBundle::class,
                default => SchoolSubscriptionPlan::class,
            };

            $purchasableId = match ($typeIndex) {
                1 => $order->school_course_id ?: (($order->id - 1) % 84) + 1,
                2 => (($order->id - 1) % 6) + 1,
                default => (($order->id - 1) % 6) + 1,
            };

            $unitPrice = match ($typeIndex) {
                1 => 79 + ($order->id * 2),
                2 => 199 + (($purchasableId - 1) * 40),
                default => match ($purchasableId) {
                    1 => 19,
                    2 => 39,
                    3 => 79,
                    4 => 149,
                    5 => 390,
                    default => 790,
                },
            };

            $discount = $order->id % 4 === 0 ? 20 : 0;
            $total = max(0, $unitPrice - $discount);

            SchoolOrderItem::updateOrCreate(
                ['id' => $id],
                [
                    'school_order_id' => $order->id,

                    'purchasable_type' => $purchasableClass,
                    'purchasable_id' => $purchasableId,

                    'title' => match ($typeIndex) {
                        1 => 'Demo course #' . $purchasableId,
                        2 => 'Demo bundle #' . $purchasableId,
                        default => 'Demo subscription plan #' . $purchasableId,
                    },

                    'sku' => match ($typeIndex) {
                        1 => 'COURSE-' . str_pad((string) $purchasableId, 4, '0', STR_PAD_LEFT),
                        2 => 'BUNDLE-' . str_pad((string) $purchasableId, 4, '0', STR_PAD_LEFT),
                        default => 'PLAN-' . str_pad((string) $purchasableId, 4, '0', STR_PAD_LEFT),
                    },

                    'unit_name' => match ($typeIndex) {
                        1 => 'course',
                        2 => 'bundle',
                        default => 'subscription',
                    },

                    'currency' => $order->currency ?? 'USD',
                    'quantity' => 1,
                    'unit_price' => $unitPrice,
                    'discount' => $discount,
                    'total' => $total,

                    'attributes' => [
                        'source' => 'seeder',
                        'demo' => true,
                    ],

                    'meta' => [
                        'order_number' => $order->number,
                        'type_index' => $typeIndex,
                    ],
                ]
            );

            $id++;
        }
    }
}
