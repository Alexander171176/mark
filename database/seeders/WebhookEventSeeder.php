<?php

namespace Database\Seeders;

use App\Models\Admin\Finance\Order\Order;
use App\Models\Admin\Finance\Payment\Payment;
use App\Models\Admin\Finance\Subscription\Subscription;
use App\Models\Admin\Finance\WebhookEvent\WebhookEvent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class WebhookEventSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('webhook_events')) {
            $this->command?->warn('Нет таблицы webhook_events — пропускаю WebhookEventSeeder.');
            return;
        }

        $faker = fake();
        $now   = now();

        $orders        = Schema::hasTable('orders')        ? Order::query()->get(['id','number','status','currency','total','created_at']) : collect();
        $payments      = Schema::hasTable('payments')      ? Payment::query()->get(['id','order_id','provider','status','currency','amount','created_at']) : collect();
        $subscriptions = Schema::hasTable('subscriptions') ? Subscription::query()->get(['id','user_id','status','provider','current_period_start','current_period_end']) : collect();

        $providers = ['stripe','paypal','yookassa','manual'];

        $inserted = 0;
        $updated  = 0;

        /* ===== 1) События по платежам ===== */
        foreach ($payments as $p) {
            $provider = $p->provider ?: $faker->randomElement($providers);

            // Успешный платёж
            [$ins, $upd] = $this->upsertEvent(
                provider: $provider,
                eventType: 'payment.succeeded',
                externalId: "evt_{$provider}_pay_{$p->id}",
                idempotencyKey: null,
                payload: [
                    'id'         => "pay_{$p->id}",
                    'object'     => 'payment',
                    'status'     => 'succeeded',
                    'amount'     => (float)$p->amount,
                    'currency'   => $p->currency,
                    'order_id'   => $p->order_id,
                    'payment_id' => $p->id,
                ],
                headers: $this->fakeHeaders($provider),
                status: 'processed',
                deliveredAt: ($p->created_at ?? $now)->copy()->addMinutes(5),
                orderId: $p->order_id,
                paymentId: $p->id,
                subscriptionId: null
            );
            $inserted += $ins; $updated += $upd;

            // Иногда приходит возврат
            if ($faker->boolean(20)) {
                [$ins2, $upd2] = $this->upsertEvent(
                    provider: $provider,
                    eventType: 'payment.refunded',
                    externalId: "evt_{$provider}_refund_{$p->id}",
                    idempotencyKey: null,
                    payload: [
                        'id'         => "refund_{$p->id}",
                        'object'     => 'refund',
                        'status'     => 'succeeded',
                        'amount'     => (float) round(((float)$p->amount) * 0.25, 2),
                        'currency'   => $p->currency,
                        'payment_id' => $p->id,
                        'order_id'   => $p->order_id,
                    ],
                    headers: $this->fakeHeaders($provider),
                    status: 'processed',
                    deliveredAt: ($p->created_at ?? $now)->copy()->addHours(2),
                    orderId: $p->order_id,
                    paymentId: $p->id,
                    subscriptionId: null
                );
                $inserted += $ins2; $updated += $upd2;
            }
        }

        /* ===== 2) События по подпискам ===== */
        foreach ($subscriptions as $s) {
            $provider = $s->provider ?: $faker->randomElement($providers);

            [$ins1, $upd1] = $this->upsertEvent(
                provider: $provider,
                eventType: 'invoice.payment_succeeded',
                externalId: "evt_{$provider}_sub_inv_paid_{$s->id}",
                idempotencyKey: null,
                payload: [
                    'id'                => "inv_{$s->id}",
                    'object'            => 'invoice',
                    'status'            => 'paid',
                    'subscription_id'   => $s->id,
                    'period_start'      => optional($s->current_period_start)->toIso8601String(),
                    'period_end'        => optional($s->current_period_end)->toIso8601String(),
                ],
                headers: $this->fakeHeaders($provider),
                status: 'processed',
                deliveredAt: ($s->current_period_start ?? $now)->copy()->addMinutes(15),
                orderId: null,
                paymentId: null,
                subscriptionId: $s->id
            );
            $inserted += $ins1; $updated += $upd1;

            // Дубликат без external_id, но с idem key
            if ($faker->boolean(25)) {
                [$ins2, $upd2] = $this->upsertEvent(
                    provider: $provider,
                    eventType: 'invoice.payment_succeeded',
                    externalId: null,
                    idempotencyKey: "idem_{$provider}_sub_{$s->id}",
                    payload: [
                        'id'              => "inv_{$s->id}_dup",
                        'object'          => 'invoice',
                        'status'          => 'paid',
                        'subscription_id' => $s->id,
                    ],
                    headers: $this->fakeHeaders($provider),
                    status: 'processed',
                    deliveredAt: ($s->current_period_start ?? $now)->copy()->addMinutes(16),
                    orderId: null,
                    paymentId: null,
                    subscriptionId: $s->id
                );
                $inserted += $ins2; $updated += $upd2;
            }
        }

        /* ===== 3) Синтетика, если база пуста ===== */
        if ($payments->isEmpty() && $subscriptions->isEmpty()) {
            for ($i = 1; $i <= 5; $i++) {
                $prov = $faker->randomElement($providers);
                [$ins, $upd] = $this->upsertEvent(
                    provider: $prov,
                    eventType: $faker->randomElement([
                        'payment.succeeded', 'payment.failed', 'invoice.payment_succeeded', 'invoice.payment_failed'
                    ]),
                    externalId: "evt_{$prov}_synthetic_{$i}",
                    idempotencyKey: null,
                    payload: [
                        'id'       => "synthetic_{$i}",
                        'object'   => 'event',
                        'status'   => $faker->randomElement(['succeeded','failed','processed']),
                        'amount'   => $faker->randomFloat(2, 5, 500),
                        'currency' => $faker->randomElement(['USD','EUR','GBP']),
                    ],
                    headers: $this->fakeHeaders($prov),
                    status: $faker->randomElement(['received','processed','failed']),
                    deliveredAt: $now->copy()->subMinutes(rand(1, 120)),
                    orderId: null,
                    paymentId: null,
                    subscriptionId: null
                );
                $inserted += $ins; $updated += $upd;
            }
        }

        $this->command?->info("Webhook events upserted: created {$inserted}, updated {$updated}.");
    }

    /**
     * Upsert по (provider, external_id) или по idempotency_key (если external_id нет).
     * Возвращает массив: [inserted(0|1), updated(0|1)].
     */
    private function upsertEvent(
        string $provider,
        string $eventType,
        ?string $externalId,
        ?string $idempotencyKey,
        array $payload,
        ?array $headers,
        string $status,
        $deliveredAt,
        ?int $orderId,
        ?int $paymentId,
        ?int $subscriptionId
    ): array {
        $now = now();

        // Ключ идемпотентности, если ничего не пришло
        if (!$externalId && !$idempotencyKey) {
            $idempotencyKey = 'idem_' . substr(sha1($provider.$eventType.json_encode($payload)), 0, 24);
        }

        $where = $externalId
            ? ['provider' => $provider, 'external_id' => $externalId]
            : ['idempotency_key' => $idempotencyKey];

        $data = [
            'provider'        => $provider,
            'event_type'      => $eventType,
            'external_id'     => $externalId,
            'idempotency_key' => $idempotencyKey,
            'signature'       => $this->fakeSignature($provider),
            'order_id'        => $orderId,
            'payment_id'      => $paymentId,
            'subscription_id' => $subscriptionId,
            'payload'         => $payload,
            'headers'         => $headers,
            'status'          => $status,
            'attempts'        => 1,
            'error_message'   => null,
            'delivered_at'    => $deliveredAt,
            'processed_at'    => $status === 'processed' ? ($deliveredAt ?? $now) : null,
        ];

        $existing = WebhookEvent::query()->where($where)->first();

        if ($existing) {
            $existing->fill($data);
            $existing->save();
            return [0, 1];
        }

        WebhookEvent::query()->create(array_merge($where, $data));
        return [1, 0];
    }

    private function fakeHeaders(string $provider): array
    {
        $faker = fake();
        return [
            'User-Agent'      => $faker->userAgent(),
            'X-Provider'      => $provider,
            'Content-Type'    => 'application/json',
            'X-Request-Id'    => (string) Str::uuid(),
            'X-Signature'     => $this->fakeSignature($provider),
            'X-Forwarded-For' => $faker->ipv4(),
        ];
    }

    private function fakeSignature(string $provider): string
    {
        return hash_hmac('sha256', $provider.'|'.now()->timestamp, Str::random(16));
    }
}
