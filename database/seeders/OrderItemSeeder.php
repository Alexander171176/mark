<?php

namespace Database\Seeders;

use App\Models\Admin\Finance\BundlePrice\BundlePrice;
use App\Models\Admin\Finance\CoursePrice\CoursePrice;
use App\Models\Admin\Finance\Order\Order;
use App\Models\Admin\Finance\OrderItem\OrderItem;
use App\Models\Admin\Finance\SubscriptionPlan\SubscriptionPlan;
use App\Models\Admin\School\Bundle\Bundle;
use App\Models\Admin\School\Course\Course;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('orders') || !Schema::hasTable('order_items')) {
            $this->command?->warn('Нет таблиц orders / order_items — пропускаю OrderItemSeeder.');
            return;
        }

        $orders = Order::query()->get();
        if ($orders->isEmpty()) {
            $this->command?->warn('Заказы не найдены — сначала посей OrderSeeder.');
            return;
        }

        // Сущности (могут быть пустыми — тогда тип просто не будет выбран)
        $courses = class_exists(Course::class)
            ? Course::query()->get(['id', 'title'])
            : collect();

        $bundles = class_exists(Bundle::class)
            ? Bundle::query()->get(['id', 'title'])
            : collect();

        $plans = class_exists(SubscriptionPlan::class)
            ? SubscriptionPlan::query()
                ->with('currency:id,code')   // нужна связь currency()
                ->get(['id', 'title', 'currency_id', 'price'])
            : collect();

        $seeded = 0;

        foreach ($orders as $order) {
            // Идемпотентность
            if (method_exists($order, 'items') && $order->items()->exists()) {
                continue;
            }

            DB::transaction(function () use ($order, $courses, $bundles, $plans, &$seeded) {
                $itemsPayload = $this->buildRandomPositions($courses, $bundles, $plans);

                $subtotal      = 0.0;
                $discountTotal = 0.0;
                $currencyCode  = $this->normalizeCurrencyCode((string) ($order->currency ?? 'USD'));

                foreach ($itemsPayload as $payload) {
                    // Финальная страховка: currency всегда ISO-3
                    $payload['currency'] = $this->normalizeCurrencyCode((string) ($payload['currency'] ?? 'USD'));

                    $item = new OrderItem($payload);
                    $item->order_id = $order->id;
                    $item->save();

                    // Валюта заказа = валюта первой позиции (или оставляем что было)
                    $currencyCode = $currencyCode ?: $item->currency;
                    if ($currencyCode === 'USD' && $item->currency && $item->currency !== 'USD') {
                        $currencyCode = $item->currency;
                    }

                    $subtotal      += (float) $item->unit_price * (int) $item->quantity;
                    $discountTotal += (float) $item->discount;
                }

                // Пересчёт заказа
                $order->currency       = $currencyCode ?: 'USD';
                $order->subtotal       = $subtotal;
                $order->discount_total = $discountTotal;
                $order->tax_total      = 0;
                $order->total          = max(0, $order->subtotal - $order->discount_total + $order->tax_total);
                $order->save();

                $seeded++;
            });
        }

        $this->command?->info("OrderItemSeeder: обработано заказов (без позиций): {$seeded}");
    }

    private function buildRandomPositions($courses, $bundles, $plans): array
    {
        $availableTypes = collect();

        if ($courses->isNotEmpty()) $availableTypes->push('course');
        if ($bundles->isNotEmpty()) $availableTypes->push('bundle');
        if ($plans->isNotEmpty())   $availableTypes->push('subscription_plan');

        // минимум 1 позиция
        $count = max(1, min(3, $availableTypes->count() ? rand(1, 3) : 1));

        $items = [];

        for ($i = 0; $i < $count; $i++) {
            if ($availableTypes->isEmpty()) {
                $items[] = $this->fakeFallbackItem();
                continue;
            }

            $type = $availableTypes->random();

            if ($type === 'course') {
                $items[] = $this->buildCourseItem($courses->random());
            } elseif ($type === 'bundle') {
                $items[] = $this->buildBundleItem($bundles->random());
            } else {
                $items[] = $this->buildPlanItem($plans->random());
            }
        }

        return $items;
    }

    private function buildCourseItem($course): array
    {
        $priceRow = CoursePrice::query()
            ->where('course_id', $course->id)
            ->actual()
            ->with('currency:id,code')
            ->orderBy('sort')
            ->first();

        $currencyCode = $this->normalizeCurrencyCode(
            (string) ($priceRow?->currency?->code ?? 'USD')
        );

        $unitPrice = (float) ($priceRow?->effective_price ?? $priceRow?->price ?? rand(19, 99));
        $quantity  = 1;
        $discount  = $this->randomDiscount($unitPrice);

        return [
            'purchasable_type' => $this->morphType('course', Course::class),
            'purchasable_id'   => $course->id,

            'title'            => (string) $course->title,
            'sku'              => null,
            'unit_name'        => 'course',

            'currency'         => $currencyCode,
            'quantity'         => $quantity,
            'unit_price'       => $unitPrice,
            'discount'         => $discount,
            'total'            => max(0, $unitPrice * $quantity - $discount),

            'attributes'       => ['kind' => 'course'],
            'meta'             => [
                'snapshot'  => 'course',
                'course_id' => $course->id,
                'price_id'  => $priceRow?->id,
            ],
        ];
    }

    private function buildBundleItem($bundle): array
    {
        $priceRow = BundlePrice::query()
            ->where('bundle_id', $bundle->id)
            ->actual()
            ->with('currency:id,code')
            ->orderBy('sort')
            ->first();

        $currencyCode = $this->normalizeCurrencyCode(
            (string) ($priceRow?->currency?->code ?? 'USD')
        );

        $unitPrice = (float) ($priceRow?->effective_price ?? $priceRow?->price ?? rand(99, 299));
        $quantity  = 1;
        $discount  = $this->randomDiscount($unitPrice);

        return [
            'purchasable_type' => $this->morphType('bundle', Bundle::class),
            'purchasable_id'   => $bundle->id,

            'title'            => (string) $bundle->title,
            'sku'              => null,
            'unit_name'        => 'bundle',

            'currency'         => $currencyCode,
            'quantity'         => $quantity,
            'unit_price'       => $unitPrice,
            'discount'         => $discount,
            'total'            => max(0, $unitPrice * $quantity - $discount),

            'attributes'       => ['kind' => 'bundle'],
            'meta'             => [
                'snapshot'  => 'bundle',
                'bundle_id' => $bundle->id,
                'price_id'  => $priceRow?->id,
            ],
        ];
    }

    private function buildPlanItem($plan): array
    {
        $currencyCode = $this->normalizeCurrencyCode(
            (string) ($plan->currency?->code ?? 'USD')
        );

        $unitPrice = (float) ($plan->price ?? rand(9, 49));
        $quantity  = 1;
        $discount  = $this->randomDiscount($unitPrice, 30);

        return [
            'purchasable_type' => $this->morphType('subscription_plan', SubscriptionPlan::class),
            'purchasable_id'   => $plan->id,

            'title'            => (string) ($plan->title ?? 'Subscription Plan'),
            'sku'              => null,
            'unit_name'        => 'subscription',

            'currency'         => $currencyCode,
            'quantity'         => $quantity,
            'unit_price'       => $unitPrice,
            'discount'         => $discount,
            'total'            => max(0, $unitPrice * $quantity - $discount),

            'attributes'       => [
                'kind'           => 'subscription_plan',
                'billing_period' => $plan->billing_period,
                'interval'       => (int) $plan->interval,
                'trial_days'     => (int) $plan->trial_days,
                'auto_renew'     => (bool) $plan->auto_renew,
            ],
            'meta'             => [
                'snapshot'             => 'subscription_plan',
                'subscription_plan_id' => $plan->id,
            ],
        ];
    }

    private function fakeFallbackItem(): array
    {
        $unitPrice = rand(10, 50);
        $quantity  = 1;

        return [
            'purchasable_type' => $this->morphType('course', Course::class),
            'purchasable_id'   => null,

            'title'     => 'Demo Item',
            'sku'       => null,
            'unit_name' => 'item',

            'currency'   => 'USD',
            'quantity'   => $quantity,
            'unit_price' => $unitPrice,
            'discount'   => 0,
            'total'      => $unitPrice * $quantity,

            'attributes' => ['fallback' => true],
            'meta'       => ['fallback' => true],
        ];
    }

    /* ==================== Utils ==================== */

    private function randomDiscount(float $unitPrice, int $maxPercent = 20): float
    {
        if (rand(0, 100) > 35) return 0.0;
        $percent = rand(5, $maxPercent);
        return round($unitPrice * ($percent / 100), 2);
    }

    /**
     * Возвращает корректный purchasable_type:
     * - если есть morphMap и алиас зарегистрирован — вернёт алиас
     * - иначе вернёт FQCN (безопасно для morphTo)
     */
    private function morphType(string $alias, string $fqcn): string
    {
        $map = Relation::morphMap();

        if (empty($map)) {
            return $fqcn;
        }

        if (isset($map[$alias]) && $map[$alias] === $fqcn) {
            return $alias;
        }

        $foundAlias = array_search($fqcn, $map, true);
        return $foundAlias ?: $fqcn;
    }

    /**
     * Всегда ISO-3 (USD/EUR/KZT...), иначе USD.
     */
    private function normalizeCurrencyCode(string $code): string
    {
        $code = strtoupper(trim($code));
        $code = substr($code, 0, 3);
        return preg_match('/^[A-Z]{3}$/', $code) ? $code : 'USD';
    }
}
