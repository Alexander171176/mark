<?php

namespace Database\Seeders;

use App\Models\Admin\Finance\ProviderAccount\ProviderAccount;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ProviderAccountSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('provider_accounts')) {
            $this->command?->warn('Нет таблицы provider_accounts — пропускаю ProviderAccountSeeder.');
            return;
        }

        $creatorId = Schema::hasTable('users')
            ? (User::query()->value('id') ?? null)
            : null;

        // Набор учёток (фейковые ключи/ID)
        $accounts = [
            // Stripe
            [
                'provider'   => 'stripe',
                'title'      => 'Stripe (Test)',
                'mode'       => 'test',
                'account_id' => 'acct_test_main',
                'public_key' => 'pk_test_'.str()->random(16),
                'secret_key' => 'sk_test_'.str()->random(32),
                'webhook_secret' => 'whsec_test_'.str()->random(24),
                'supported_currencies' => ['USD','EUR','GBP'],
                'supported_countries'  => ['US','GB','DE','PL'],
                'config'     => ['api_version' => '2022-11-15'],
                'activity'  => true,
                'is_default' => true,
            ],
            [
                'provider'   => 'stripe',
                'title'      => 'Stripe (Live)',
                'mode'       => 'live',
                'account_id' => 'acct_live_main',
                'public_key' => 'pk_live_'.str()->random(16),
                'secret_key' => 'sk_live_'.str()->random(32),
                'webhook_secret' => 'whsec_live_'.str()->random(24),
                'supported_currencies' => ['USD','EUR','GBP'],
                'supported_countries'  => ['US','GB','DE','PL'],
                'config'     => ['api_version' => '2022-11-15'],
                'activity'  => true,
                'is_default' => true,
            ],

            // PayPal
            [
                'provider'   => 'paypal',
                'title'      => 'PayPal (Sandbox)',
                'mode'       => 'test',
                'account_id' => 'merchant_test_main',
                'public_key' => 'client_id_test_'.str()->random(10),
                'secret_key' => 'client_secret_test_'.str()->random(24),
                'webhook_secret' => 'whsec_pp_test_'.str()->random(20),
                'supported_currencies' => ['USD','EUR'],
                'supported_countries'  => ['US','GB','DE','PL'],
                'config'     => ['env' => 'sandbox'],
                'activity'  => true,
                'is_default' => false,
            ],
            [
                'provider'   => 'paypal',
                'title'      => 'PayPal (Live)',
                'mode'       => 'live',
                'account_id' => 'merchant_live_main',
                'public_key' => 'client_id_live_'.str()->random(10),
                'secret_key' => 'client_secret_live_'.str()->random(24),
                'webhook_secret' => 'whsec_pp_live_'.str()->random(20),
                'supported_currencies' => ['USD','EUR'],
                'supported_countries'  => ['US','GB','DE','PL'],
                'config'     => ['env' => 'live'],
                'activity'  => true,
                'is_default' => false,
            ],

            // YooKassa
            [
                'provider'   => 'yookassa',
                'title'      => 'YooKassa (Test)',
                'mode'       => 'test',
                'account_id' => 'shop_test_001',
                'public_key' => 'shopId_test_001',
                'secret_key' => 'yk_test_secret_'.str()->random(24),
                'webhook_secret' => 'whsec_yk_test_'.str()->random(18),
                'supported_currencies' => ['RUB','KZT'],
                'supported_countries'  => ['RU','KZ'],
                'config'     => ['kassa_account' => 'test'],
                'activity'  => true,
                'is_default' => false,
            ],
            [
                'provider'   => 'yookassa',
                'title'      => 'YooKassa (Live)',
                'mode'       => 'live',
                'account_id' => 'shop_live_001',
                'public_key' => 'shopId_live_001',
                'secret_key' => 'yk_live_secret_'.str()->random(24),
                'webhook_secret' => 'whsec_yk_live_'.str()->random(18),
                'supported_currencies' => ['RUB','KZT'],
                'supported_countries'  => ['RU','KZ'],
                'config'     => ['kassa_account' => 'live'],
                'activity'  => true,
                'is_default' => false,
            ],
        ];

        // Апсертим. Ключ — provider+mode+account_id (как в uniq индексе).
        foreach ($accounts as $acc) {
            $where = [
                'provider'   => $acc['provider'],
                'mode'       => $acc['mode'],
                'account_id' => $acc['account_id'],
            ];

            $payload = array_merge($acc, [
                'created_by' => $creatorId,
                'updated_by' => $creatorId,
                // добавим минимальный meta след
                'config' => array_merge($acc['config'] ?? [], [
                    'seeded'   => true,
                    'seed_run' => now()->toDateTimeString(),
                ]),
            ]);

            // защита: не даём mass-assign `provider/mode/account_id` конфликтно измениться
            unset($payload['provider'], $payload['mode'], $payload['account_id']);

            ProviderAccount::updateOrCreate($where, $payload);
        }

        // Гарантируем, что в каждом режиме ровно ОДИН is_default = true.
        foreach (['test', 'live'] as $mode) {
            $existingDefault = ProviderAccount::query()
                ->where('mode', $mode)
                ->where('is_default', true)
                ->first();

            if (!$existingDefault) {
                // Возьмём Stripe (если есть), иначе первый активный
                $candidate = ProviderAccount::query()
                    ->where('mode', $mode)
                    ->where('activity', true)
                    ->orderByRaw("CASE WHEN provider = 'stripe' THEN 0 ELSE 1 END")
                    ->orderBy('id')
                    ->first();

                if ($candidate) {
                    ProviderAccount::query()->where('mode', $mode)->update(['is_default' => false]);
                    $candidate->is_default = true;
                    $candidate->save();
                }
            } else {
                // Сбрасываем флаг у всех прочих
                ProviderAccount::query()
                    ->where('mode', $mode)
                    ->where('id', '!=', $existingDefault->id)
                    ->update(['is_default' => false]);
            }
        }

        $this->command?->info('Provider accounts seeded/updated (идемпотентно).');
    }
}
