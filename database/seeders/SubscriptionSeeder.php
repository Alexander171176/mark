<?php

namespace Database\Seeders;

use App\Models\Admin\Finance\Order\Order;
use App\Models\Admin\Finance\Subscription\Subscription;
use App\Models\Admin\Finance\SubscriptionPlan\SubscriptionPlan;
use App\Models\Admin\Finance\UserPaymentMethod\UserPaymentMethod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (
            !Schema::hasTable('subscriptions') ||
            !Schema::hasTable('subscription_plans') ||
            !Schema::hasTable('users')
        ) {
            $this->command?->warn('Нет необходимых таблиц (subscriptions/subscription_plans/users) — пропускаю SubscriptionSeeder.');
            return;
        }

        $plans = SubscriptionPlan::query()
            ->where('activity', true)
            ->orderBy('sort')
            ->get();

        if ($plans->isEmpty()) {
            $this->command?->warn('Активные тарифные планы отсутствуют — сначала запусти SubscriptionPlanSeeder.');
            return;
        }

        $users = User::query()->get(['id','name','email']);
        if ($users->isEmpty()) {
            $this->command?->warn('Пользователи не найдены — пропускаю SubscriptionSeeder.');
            return;
        }

        $faker = fake();

        foreach ($users as $user) {
            // ~65% пользователей получат подписки
            if (rand(0, 100) > 65) {
                continue;
            }

            // Сколько подписок дать пользователю (1..2), но не больше числа планов
            $count = min($plans->count(), max(1, rand(1, 2)));
            $pickedPlans = $plans->random($count)->values();

            foreach ($pickedPlans as $planIdx => $plan) {
                // Выберем один сохранённый способ оплаты (дефолтный, если есть)
                $upm = UserPaymentMethod::query()
                    ->where('user_id', $user->id)
                    ->where('activity', true)
                    ->orderByDesc('is_default')
                    ->orderBy('id')
                    ->first();

                // Попробуем найти оплаченный заказ пользователя с позицией по этому плану
                $orderId = $this->findPaidOrderIdForPlan($user->id, $plan->id);

                // Сгенерируем «сценарий» подписки: trialing/active/past_due/cancelled/expired/paused/incomplete/pending
                $scenario = $this->randomScenario(); // возвращает массив с 'status', 'trial', 'cancel_at_period_end', 'ageMonths'
                $now = now();

                // Начало подписки: от -6 до 0 месяцев (зависит от сценария)
                $startedAt = (clone $now)->subMonths($scenario['ageMonths'])->startOfDay();

                // Триал
                $trialDays = (int) $plan->trial_days;
                $trialEndsAt = null;
                $status = $scenario['status'];

                if ($trialDays > 0 && in_array($status, ['trialing', 'active'])) {
                    // 70% случаев — триал был и уже закончился к текущему моменту
                    if (rand(0,100) < 70) {
                        $trialEndsAt = (clone $startedAt)->addDays($trialDays);
                        if ($trialEndsAt->gt($now)) {
                            // триал ещё идёт
                            $status = 'trialing';
                        }
                    } else {
                        // триал ещё активен
                        $trialEndsAt = (clone $now)->addDays(min($trialDays, rand(1, $trialDays)));
                        $status = 'trialing';
                    }
                }

                // Текущий период
                $curStart = $this->alignPeriodStart($startedAt, $plan->billing_period, (int)$plan->interval, $now);
                $curEnd   = $this->addPeriod($curStart, $plan->billing_period, (int)$plan->interval);

                // Если триал активен — сделаем текущий период = триальный
                if ($status === 'trialing' && $trialEndsAt) {
                    $curStart = clone $startedAt;
                    $curEnd   = (clone $trialEndsAt);
                }

                // Даты завершения/отмены
                $cancelAtPeriodEnd = $scenario['cancel_at_period_end'];
                $cancelledAt = null;
                $endsAt      = null;

                if (in_array($status, ['cancelled','expired','paused'])) {
                    // Случайная дата отмены/окончания (между началом и сейчас)
                    $cancelledAt = $status === 'cancelled' ? (clone $now)->subDays(rand(1, 30)) : null;
                    $endsAt      = $status === 'expired'   ? (clone $now)->subDays(rand(1, 15)) : null;
                } elseif ($cancelAtPeriodEnd) {
                    // Планируем отменить в конце текущего периода
                    $endsAt = null; // ещё активна до даты current_period_end
                }

                // Следующее списание (если активная/триал заканчивается/и т.п.)
                $nextBillingAt = null;
                if (in_array($status, ['active','trialing','past_due','incomplete'])) {
                    // Если trialing и trialEndsAt в будущем — следующее списание тогда
                    if ($status === 'trialing' && $trialEndsAt && $trialEndsAt->gt($now)) {
                        $nextBillingAt = (clone $trialEndsAt)->addDay(); // условно на следующий день после триала
                    } else {
                        $nextBillingAt = (clone $curEnd)->startOfDay();
                    }
                }

                // Снапшоты из плана
                $currency   = $plan->currency;
                $price      = $plan->price;
                $features   = $plan->features;
                $limits     = $plan->limits;

                // Провайдер/привязка платёжного метода (если есть)
                $provider = $upm?->provider;
                $providerSubId = $upm ? ('sub_'.$upm->provider.'_'.$user->id.'_'.$plan->id) : null;

                // Идемпотентный ключ (user + plan + started_at)
                $where = [
                    'user_id'               => $user->id,
                    'subscription_plan_id'  => $plan->id,
                    'started_at'            => $startedAt, // ключ фиксирует «экземпляр» подписки
                ];

                $payload = [
                    'order_id'               => $orderId,
                    'user_payment_method_id' => $upm?->id,

                    'currency'        => $currency,
                    'price'           => $price,
                    'billing_period'  => $plan->billing_period,
                    'interval'        => (int)$plan->interval,

                    'trial_days'      => $trialDays,
                    'trial_ends_at'   => $trialEndsAt,

                    'current_period_start' => $curStart,
                    'current_period_end'   => $curEnd,

                    'ends_at'         => $endsAt,
                    'cancelled_at'    => $cancelledAt,
                    'cancel_at_period_end' => $cancelAtPeriodEnd,

                    'status'          => $status,

                    'provider'                 => $provider,
                    'provider_subscription_id' => $providerSubId,
                    'last_paid_at'    => $this->maybeLastPaidAt($status, $curStart, $now),
                    'next_billing_at' => $nextBillingAt,
                    'renewal_attempts'=> in_array($status, ['past_due','incomplete']) ? rand(1,3) : 0,

                    'features'        => $features,
                    'limits'          => $limits,
                    'meta'            => [
                        'seeded'   => true,
                        'seed_run' => now()->toDateTimeString(),
                        'note'     => 'Fake subscription seeded',
                    ],
                ];

                // started_at — уже есть в $where; установим также явное значение (на случай update)
                $payload['started_at'] = $startedAt;

                Subscription::query()->updateOrCreate($where, $payload);
            }
        }

        $this->command?->info('Subscriptions seeded/updated (идемпотентно).');
    }

    /**
     * Вернуть id оплаченного заказа с позицией нужного плана (если есть).
     */
    private function findPaidOrderIdForPlan(int $userId, int $planId): ?int
    {
        if (!Schema::hasTable('orders') || !Schema::hasTable('order_items')) {
            return null;
        }

        // Берём алиас из morphMap, чтобы не хардкодить FQCN
        $map = Relation::morphMap();
        $alias = 'subscription_plan';

        if (! isset($map[$alias])) {
            // morphMap ещё не настроен — безопасно возвращаем null
            return null;
        }

        /** @var Order|null $order */
        $order = Order::query()
            ->where('user_id', $userId)
            // заказ должен быть оплачен: либо флаг, либо статус оплаты
            ->where(function ($q) {
                $q->where('is_paid', true)
                    ->orWhere('payment_status', 'paid');
            })
            ->whereHas('items', function ($q) use ($alias, $planId) {
                $q->where('purchasable_type', $alias)
                    ->where('purchasable_id', $planId);
            })
            ->orderByDesc('paid_at')
            ->first(['id']);

        return $order?->id;
    }

    /**
     * Случайный сценарий для подписки.
     * @return array{status:string, trial:bool, cancel_at_period_end:bool, ageMonths:int}
     */
    private function randomScenario(): array
    {
        // Распределение статусов
        $pool = [
            ['status' => 'active',    'trial' => false, 'cancel_at_period_end' => false, 'ageMonths' => rand(0, 6)],
            ['status' => 'trialing',  'trial' => true,  'cancel_at_period_end' => false, 'ageMonths' => rand(0, 1)],
            ['status' => 'past_due',  'trial' => false, 'cancel_at_period_end' => false, 'ageMonths' => rand(0, 3)],
            ['status' => 'incomplete','trial' => false, 'cancel_at_period_end' => false, 'ageMonths' => rand(0, 1)],
            ['status' => 'paused',    'trial' => false, 'cancel_at_period_end' => false, 'ageMonths' => rand(0, 6)],
            ['status' => 'cancelled', 'trial' => false, 'cancel_at_period_end' => (bool)rand(0,1), 'ageMonths' => rand(0, 6)],
            ['status' => 'expired',   'trial' => false, 'cancel_at_period_end' => false, 'ageMonths' => rand(1, 6)],
            ['status' => 'pending',   'trial' => false, 'cancel_at_period_end' => false, 'ageMonths' => rand(0, 1)],
        ];

        return collect($pool)->random();
    }

    /**
     * Добавить период к дате.
     */
    private function addPeriod(Carbon $start, string $period, int $interval): Carbon
    {
        $end = $start->copy();
        return match ($period) {
            'day'   => $end->addDays($interval),
            'week'  => $end->addWeeks($interval),
            'month' => $end->addMonthsNoOverflow($interval),
            'year'  => $end->addYearsNoOverflow($interval),
            default => $end->addMonthsNoOverflow($interval),
        };
    }

    /**
     * Вычислить "начало текущего периода" относительно now.
     * Примерная логика: идём от started_at, прибавляя период, пока не превысим now.
     */
    private function alignPeriodStart(Carbon $startedAt, string $period, int $interval, Carbon $now): Carbon
    {
        $cur = $startedAt->copy();
        while ($this->addPeriod($cur->copy(), $period, $interval)->lte($now)) {
            $cur = $this->addPeriod($cur, $period, $interval);
        }
        return $cur;
    }

    /**
     * Когда в последний раз платили (для активных и родственных статусов).
     */
    private function maybeLastPaidAt(string $status, Carbon $curStart, Carbon $now): ?Carbon
    {
        if (!in_array($status, ['active','trialing','past_due','incomplete'])) {
            return null;
        }

        // Для trialing оплата ещё не списана
        if ($status === 'trialing') {
            return null;
        }

        // В остальных случаях — между началом текущего периода и сейчас
        $randDays = max(0, min($now->diffInDays($curStart), rand(0, 10)));
        return $curStart->copy()->addDays($randDays);
    }
}
