<?php

namespace Database\Seeders;

use App\Models\Admin\Finance\Payout\Payout;
use App\Models\Admin\Finance\ProviderAccount\ProviderAccount;
use App\Models\Admin\School\InstructorProfile\InstructorProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PayoutSeeder extends Seeder
{
    public function run(): void
    {
        if (
            !Schema::hasTable('payouts') ||
            !Schema::hasTable('instructor_profiles')
        ) {
            $this->command?->warn('Нет таблиц payouts/instructor_profiles — пропускаю PayoutSeeder.');
            return;
        }

        // Получим активных инструкторов
        $instructors = InstructorProfile::query()
            ->where('activity', true)
            ->get(['id','user_id','title']);

        if ($instructors->isEmpty()) {
            $this->command?->warn('Активные преподаватели не найдены — пропускаю PayoutSeeder.');
            return;
        }

        // Любой пользователь как автор записей (если есть)
        $creatorId = Schema::hasTable('users')
            ? (User::query()->value('id') ?? null)
            : null;

        // Возьмём активные аккаунты провайдеров (если есть)
        $providers = Schema::hasTable('provider_accounts')
            ? ProviderAccount::query()->where('activity', true)->get(['id','provider','mode','supported_currencies'])
            : collect();

        foreach ($instructors as $instructor) {
            // Сколько месяцев сделать выплат (2..3)
            $monthsCount = rand(2, 3);

            // Последние $monthsCount месяцев, включая текущий
            for ($i = $monthsCount - 1; $i >= 0; $i--) {
                $periodStart = Carbon::now()->subMonths($i)->startOfMonth();
                $periodEnd   = Carbon::now()->subMonths($i)->endOfMonth();

                // Статус: прошлые — paid, текущий — processing/pending
                if ($periodEnd->lt(now()->startOfDay())) {
                    $status = 'paid';
                    $paidAt = $periodEnd->copy()->addDays(rand(1, 10))->startOfDay();
                } else {
                    $status = rand(0, 1) ? 'processing' : 'pending';
                    $paidAt = null;
                }

                // Выбор провайдера/аккаунта (если есть)
                $providerId = null;
                $currency   = 'USD';

                if ($providers->isNotEmpty()) {
                    $pick = $providers->random();
                    $providerId = $pick->id;
                    // попытаемся взять первую валюту из supported_currencies
                    if (is_array($pick->supported_currencies) && !empty($pick->supported_currencies)) {
                        $currency = $pick->supported_currencies[0];
                    }
                }

                // Фейковые суммы (реалистичные)
                // Брутто — «начислено за период»
                $amountGross = $this->money(rand(800, 5000));
                // Комиссии 2.0–4.0% от брутто
                $feeRate     = rand(20, 40) / 1000; // 0.020..0.040
                $feeTotal    = $this->money($amountGross * $feeRate);
                // Налог/удержания 10–15% от брутто
                $taxRate     = rand(100, 150) / 1000; // 0.10..0.15
                $taxTotal    = $this->money($amountGross * $taxRate);
                // Нетто
                $amountNet   = max(0.00, $this->money($amountGross - $feeTotal - $taxTotal));

                // Уникальный и стабильный номер выплаты (пересев не создаст дубль)
                $number = $this->payoutNumber($instructor->id, $periodStart);

                // Идемпотентное сохранение по номеру
                Payout::updateOrCreate(
                    ['number' => $number],
                    [
                        'instructor_profile_id' => (int)$instructor->id,
                        'provider_account_id'   => $providerId,
                        'period_start'          => $periodStart->toDateString(),
                        'period_end'            => $periodEnd->toDateString(),

                        'currency'              => $currency,
                        'amount_gross'          => $amountGross,
                        'fee_total'             => $feeTotal,
                        'tax_total'             => $taxTotal,
                        'amount_net'            => $amountNet,

                        'status'                => $status,
                        'method'                => $this->pickMethod($providerId),

                        'paid_at'               => $paidAt,

                        'notes'                 => $this->statusNote($status),
                        'meta'                  => [
                            'seeded'   => true,
                            'seed_run' => now()->toDateTimeString(),
                        ],

                        'created_by'            => $creatorId,
                        'updated_by'            => $creatorId,
                    ]
                );
            }
        }

        $this->command?->info('Payouts seeded/updated (идемпотентно).');
    }

    private function payoutNumber(int $instructorId, Carbon $periodStart): string
    {
        // Пример: PY-2025-03-I00012
        return sprintf(
            'PY-%s-%s-I%05d',
            $periodStart->format('Y'),
            $periodStart->format('m'),
            $instructorId
        );
    }

    private function pickMethod(?int $providerAccountId): ?string
    {
        if (!$providerAccountId) {
            return rand(0, 1) ? 'manual' : 'bank_wire';
        }
        // условно считаем, что если провайдер есть — это перевод через него
        return rand(0, 1) ? 'stripe_transfer' : 'paypal_payout';
    }

    private function statusNote(string $status): ?string
    {
        return match ($status) {
            'paid'       => 'Выплачено.',
            'processing' => 'Выплата формируется/в обработке.',
            'pending'    => 'Ожидает запуска выплаты.',
            'failed'     => 'Выплата не прошла, требуется повтор.',
            'cancelled'  => 'Выплата отменена.',
            default      => null,
        };
    }

    private function money(float $value): float
    {
        // Округляем до 2 знаков в стиле денег
        return round($value, 2);
    }
}
