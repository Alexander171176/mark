<?php

namespace Database\Seeders;

use App\Models\Admin\Finance\OrderItem\OrderItem;
use App\Models\Admin\Finance\Payout\Payout;
use App\Models\Admin\Finance\PayoutItem\PayoutItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PayoutItemSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('payouts') || !Schema::hasTable('payout_items')) {
            $this->command?->warn('Нет таблиц payouts/payout_items — пропускаю PayoutItemSeeder.');
            return;
        }

        $payouts = Payout::query()
            ->get([
                'id','instructor_profile_id','currency',
                'period_start','period_end',
                'amount_gross','fee_total','tax_total','amount_net'
            ]);

        if ($payouts->isEmpty()) {
            $this->command?->warn('Выплаты не найдены — сначала посей PayoutSeeder.');
            return;
        }

        foreach ($payouts as $payout) {
            // Идемпотентность
            PayoutItem::query()->where('payout_id', $payout->id)->delete();

            $periodStart = $payout->period_start ? $payout->period_start.' 00:00:00' : null;
            $periodEnd   = $payout->period_end   ? $payout->period_end.' 23:59:59' : null;

            $itemsPayload = [];

            /* ---------- 1) Курсовые позиции в окне периода ---------- */
            $courseItems = collect();

            if (Schema::hasTable('order_items') && Schema::hasTable('orders') && Schema::hasTable('courses')) {
                $courseItems = OrderItem::query()
                    ->with([
                        'order:id,is_paid,payment_status,created_at,currency',
                        'purchasable', // это Course
                    ])
                    ->where('purchasable_type', 'course')
                    ->whereHas('order', function ($q) use ($periodStart, $periodEnd) {
                        // оплаченный заказ: флаг или payment_status = paid
                        $q->where(function ($q) {
                            $q->where('is_paid', true)
                                ->orWhere('payment_status', 'paid');
                        });
                        if ($periodStart) $q->where('created_at', '>=', $periodStart);
                        if ($periodEnd)   $q->where('created_at', '<=', $periodEnd);
                    })
                    ->get(['id','order_id','purchasable_id','title','currency','total']);

                // фильтруем по преподавателю уже на коллекции
                $courseItems = $courseItems
                    ->filter(fn ($oi) =>
                        optional($oi->purchasable)->instructor_profile_id === $payout->instructor_profile_id
                    )
                    ->take(20)
                    ->values();
            }

            /* ---------- 2) Если пусто — те же, но без ограничения по датам ---------- */
            if ($courseItems->isEmpty() && Schema::hasTable('order_items')) {
                $courseItems = OrderItem::query()
                    ->with([
                        'order:id,is_paid,payment_status,created_at,currency',
                        'purchasable',
                    ])
                    ->where('purchasable_type', 'course')
                    ->whereHas('order', function ($q) {
                        $q->where(function ($q) {
                            $q->where('is_paid', true)
                                ->orWhere('payment_status', 'paid');
                        });
                    })
                    ->get(['id','order_id','purchasable_id','title','currency','total']);

                $courseItems = $courseItems
                    ->filter(fn ($oi) =>
                        optional($oi->purchasable)->instructor_profile_id === $payout->instructor_profile_id
                    )
                    ->take(20)
                    ->values();
            }

            /* ---------- 3) Если всё ещё пусто — бандлы преподавателя ---------- */
            $bundleItems = collect();

            if ($courseItems->isEmpty() && Schema::hasTable('order_items') && Schema::hasTable('bundle_has_course')) {
                $bundleItems = OrderItem::query()
                    ->with([
                        'order:id,is_paid,payment_status,created_at,currency',
                        'purchasable.courses:id,instructor_profile_id', // purchasable = Bundle
                    ])
                    ->where('purchasable_type', 'bundle')
                    ->whereHas('order', function ($q) {
                        $q->where(function ($q) {
                            $q->where('is_paid', true)
                                ->orWhere('payment_status', 'paid');
                        });
                    })
                    ->get(['id','order_id','purchasable_id','title','currency','total']);

                // оставляем только те бандлы, в которых есть курсы этого инструктора
                $bundleItems = $bundleItems
                    ->filter(function ($oi) use ($payout) {
                        $bundle = $oi->purchasable;
                        if (! $bundle || ! $bundle->relationLoaded('courses')) {
                            return false;
                        }
                        return $bundle->courses
                            ->contains('instructor_profile_id', $payout->instructor_profile_id);
                    })
                    ->take(10)
                    ->values();
            }

            /* ---------- 4) Сформируем payload из найденных источников ---------- */
            if ($courseItems->isNotEmpty()) {
                foreach ($courseItems as $oi) {
                    $this->pushShareRow($itemsPayload, $payout, [
                        'order_id'       => $oi->order_id,
                        'order_item_id'  => $oi->id,
                        'course_id'      => $oi->purchasable_id, // ID курса
                        'bundle_id'      => null,
                        'subscription_id'=> null,
                        'currency'       => $oi->currency ?? $payout->currency ?? 'USD',
                        'total'          => (float)$oi->total,
                        'earned_at'      => optional($oi->order?->created_at)->format('Y-m-d H:i:s')
                            ?: optional($payout->period_end)->format('Y-m-d H:i:s'),
                        'title'          => $oi->title ?: 'Продажа курса',
                        'meta'           => ['source' => 'OrderItem(course)', 'seeded' => true],
                    ]);
                }
            }

            if ($bundleItems->isNotEmpty()) {
                foreach ($bundleItems as $oi) {
                    $bundle = $oi->purchasable;

                    // Выберем курс этого же инструктора внутри бандла (для course_id)
                    $courseOfInstructor = $bundle?->courses
                        ?->firstWhere('instructor_profile_id', $payout->instructor_profile_id);

                    $this->pushShareRow($itemsPayload, $payout, [
                        'order_id'       => $oi->order_id,
                        'order_item_id'  => $oi->id,
                        'course_id'      => $courseOfInstructor?->id, // может быть null
                        'bundle_id'      => $oi->purchasable_id,      // ID бандла
                        'subscription_id'=> null,
                        'currency'       => $oi->currency ?? $payout->currency ?? 'USD',
                        'total'          => (float)$oi->total,
                        'earned_at'      => optional($oi->order?->created_at)->format('Y-m-d H:i:s')
                            ?: optional($payout->period_end)->format('Y-m-d H:i:s'),
                        'title'          => $oi->title ?: 'Продажа бандла',
                        'meta'           => ['source' => 'OrderItem(bundle)', 'seeded' => true],
                    ]);
                }
            }

            /* ---------- 5) Если так и не нашли реальных — синтетика ---------- */
            if (empty($itemsPayload)) {
                $parts = $this->splitAmount((float)$payout->amount_net, 3);
                foreach ($parts as $idx => $net) {
                    $gross = $this->money($net / 0.85);
                    $fee   = $this->money($gross * 0.03);
                    $tax   = $this->money($gross * 0.12);
                    $net   = $this->money($gross - $fee - $tax);

                    $itemsPayload[] = [
                        'payout_id'       => $payout->id,
                        'order_id'        => null,
                        'order_item_id'   => null,
                        'course_id'       => null,
                        'bundle_id'       => null,
                        'subscription_id' => null,
                        'currency'        => $payout->currency ?? 'USD',
                        'amount_gross'    => $gross,
                        'fee_total'       => $fee,
                        'tax_total'       => $tax,
                        'amount_net'      => $net,
                        'earned_at'       => optional($payout->period_end)->format('Y-m-d H:i:s'),
                        'title'           => "Роялти за период #".($idx+1),
                        'notes'           => 'Синтетическая позиция — продаж/событий не найдено.',
                        'meta'            => json_encode(['source' => 'synthetic', 'seeded' => true], JSON_UNESCAPED_UNICODE),
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];
                }
            }

            /* ---------- 6) Выравнивание суммы нетто к payout.amount_net ---------- */
            $targetNet = $this->money((float)$payout->amount_net);
            $sumNet    = $this->money(array_sum(array_column($itemsPayload, 'amount_net')));
            $delta     = $this->money($targetNet - $sumNet);

            if (abs($delta) >= 0.01) {
                if (!empty($itemsPayload)) {
                    $last = array_pop($itemsPayload);
                    $newNet = $this->money($last['amount_net'] + $delta);
                    if ($newNet >= 0) {
                        $last['amount_net'] = $newNet;
                        $last['tax_total']  = $this->money($last['amount_gross'] - $last['fee_total'] - $newNet);
                        $last['notes']      = trim(($last['notes'] ?? '').' Корректировка для выравнивания суммы.');
                        $itemsPayload[] = $last;
                    } else {
                        $itemsPayload[] = $this->adjustmentItem($payout, $delta);
                        $itemsPayload[] = $last;
                    }
                } else {
                    $itemsPayload[] = $this->adjustmentItem($payout, $delta);
                }
            }

            if (!empty($itemsPayload)) {
                PayoutItem::query()->insert($itemsPayload);
            }
        }

        $this->command?->info('Payout items seeded/updated (реальные источники + фоллбеки, meta JSON).');
    }

    /**
     * Добавляет строку по доле автора из item.total (с комиссиями/налогами),
     * аккуратно заполняя FK и JSON.
     */
    private function pushShareRow(array &$payload, Payout $payout, array $src): void
    {
        $share = rand(60, 80) / 100; // 60–80% автору
        $gross = $this->money($src['total'] * $share);
        $fee   = $this->money($gross * (rand(20, 40) / 1000));   // 2–4%
        $tax   = $this->money($gross * (rand(100, 150) / 1000)); // 10–15%
        $net   = $this->money($gross - $fee - $tax);

        $payload[] = [
            'payout_id'       => $payout->id,
            'order_id'        => $src['order_id']       ?? null,
            'order_item_id'   => $src['order_item_id']  ?? null,
            'course_id'       => $src['course_id']      ?? null,
            'bundle_id'       => $src['bundle_id']      ?? null,
            'subscription_id' => $src['subscription_id']?? null,
            'currency'        => $src['currency']       ?? ($payout->currency ?? 'USD'),
            'amount_gross'    => $gross,
            'fee_total'       => $fee,
            'tax_total'       => $tax,
            'amount_net'      => $net,
            'earned_at'       => $src['earned_at'] ?? optional($payout->period_end)->format('Y-m-d H:i:s'),
            'title'           => $src['title'] ?? 'Продажа',
            'notes'           => null,
            'meta'            => json_encode($src['meta'] ?? ['source' => 'OrderItem', 'seeded' => true], JSON_UNESCAPED_UNICODE),
            'created_at'      => now(),
            'updated_at'      => now(),
        ];
    }

    private function money(float $v): float
    {
        return round($v, 2);
    }

    private function splitAmount(float $total, int $parts): array
    {
        $total = max(0.0, (float)$total);
        if ($parts <= 1) return [$this->money($total)];

        $weights = [];
        for ($i = 0; $i < $parts; $i++) $weights[] = rand(20, 60);
        $sumW = array_sum($weights) ?: 1;

        $out = [];
        $acc = 0.0;
        for ($i = 0; $i < $parts; $i++) {
            $share = $total * ($weights[$i] / $sumW);
            $out[] = $this->money($share);
            $acc  += $out[$i];
        }
        $delta = $this->money($total - $acc);
        if (abs($delta) >= 0.01) {
            $out[count($out)-1] = $this->money($out[count($out)-1] + $delta);
        }
        return $out;
    }

    private function adjustmentItem(Payout $payout, float $delta): array
    {
        return [
            'payout_id'       => $payout->id,
            'order_id'        => null,
            'order_item_id'   => null,
            'course_id'       => null,
            'bundle_id'       => null,
            'subscription_id' => null,
            'currency'        => $payout->currency ?? 'USD',
            'amount_gross'    => $this->money(abs($delta)),
            'fee_total'       => 0.00,
            'tax_total'       => 0.00,
            'amount_net'      => $this->money(abs($delta)),
            'earned_at'       => optional($payout->period_end)->format('Y-m-d H:i:s'),
            'title'           => 'Корректировка периода',
            'notes'           => 'Автокорректировка для согласования суммы нетто по выплате.',
            'meta'            => json_encode(['source' => 'adjustment', 'seeded' => true], JSON_UNESCAPED_UNICODE),
            'created_at'      => now(),
            'updated_at'      => now(),
        ];
    }
}
