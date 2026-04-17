<?php

namespace Database\Seeders;

use App\Models\Admin\Finance\PaymentMethod\PaymentMethod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('payment_methods')) {
            $this->command?->warn('Таблица payment_methods отсутствует — пропускаю PaymentMethodSeeder.');
            return;
        }

        // Базовый набор способов оплаты
        $rows = [
            [
                'code' => 'card',
                'name' => 'Банковская карта',
                'provider' => 'manual',
                'type' => 'card',
                'supports_refund' => true,
                'supports_recurring' => true,
                'activity' => true,
                'sort' => 10,
                'meta' => [
                    'display_icons' => ['visa','mastercard','mir'],
                    'note' => 'Фиктивный локальный провайдер карт',
                ],
            ],
            [
                'code' => 'stripe',
                'name' => 'Stripe',
                'provider' => 'stripe',
                'type' => 'card',
                'supports_refund' => true,
                'supports_recurring' => true,
                'activity' => true,
                'sort' => 20,
                'meta' => [
                    'mode' => 'test',
                    'webhook' => 'https://example.test/webhooks/stripe',
                    'capabilities' => ['card','apple_pay','google_pay'],
                ],
            ],
            [
                'code' => 'yookassa',
                'name' => 'ЮKassa',
                'provider' => 'yookassa',
                'type' => 'ewallet',
                'supports_refund' => true,
                'supports_recurring' => true,
                'activity' => true,
                'sort' => 30,
                'meta' => [
                    'mode' => 'test',
                    'methods' => ['bank_card','yoo_money','sbp'],
                ],
            ],
            [
                'code' => 'paypal',
                'name' => 'PayPal',
                'provider' => 'paypal',
                'type' => 'ewallet',
                'supports_refund' => true,
                'supports_recurring' => true,
                'activity' => true,
                'sort' => 40,
                'meta' => [
                    'mode' => 'sandbox',
                ],
            ],
            [
                'code' => 'bank_transfer',
                'name' => 'Банковский перевод',
                'provider' => 'manual',
                'type' => 'bank_transfer',
                'supports_refund' => false,
                'supports_recurring' => false,
                'activity' => true,
                'sort' => 50,
                'meta' => [
                    'iban' => 'DE00 0000 0000 0000 0000 00',
                    'bic'  => 'ABCDEFGHXXX',
                    'beneficiary' => 'Demo LLC',
                    'instructions' => 'Оплата по счету в течение 3 рабочих дней.',
                ],
            ],
            [
                'code' => 'invoice',
                'name' => 'Счёт на оплату',
                'provider' => 'manual',
                'type' => 'invoice',
                'supports_refund' => false,
                'supports_recurring' => false,
                'activity' => true,
                'sort' => 60,
                'meta' => [
                    'issue_pdf' => true,
                    'auto_close_days' => 7,
                ],
            ],
            [
                'code' => 'cash',
                'name' => 'Наличные (офлайн)',
                'provider' => 'manual',
                'type' => 'cash',
                'supports_refund' => false,
                'supports_recurring' => false,
                'activity' => false, // по умолчанию скрыт
                'sort' => 70,
                'meta' => [
                    'note' => 'Использовать только для офлайн-мероприятий',
                ],
            ],
            [
                'code' => 'apple_pay',
                'name' => 'Apple Pay (через Stripe)',
                'provider' => 'stripe',
                'type' => 'ewallet',
                'supports_refund' => true,
                'supports_recurring' => true,
                'activity' => true,
                'sort' => 80,
                'meta' => [
                    'via' => 'stripe',
                ],
            ],
            [
                'code' => 'google_pay',
                'name' => 'Google Pay (через Stripe)',
                'provider' => 'stripe',
                'type' => 'ewallet',
                'supports_refund' => true,
                'supports_recurring' => true,
                'activity' => true,
                'sort' => 90,
                'meta' => [
                    'via' => 'stripe',
                ],
            ],
        ];

        foreach ($rows as $data) {
            PaymentMethod::query()->updateOrCreate(
                ['code' => $data['code']],     // уникальный ключ
                $data                          // атрибуты для обновления/создания
            );
        }

        $this->command?->info('Payment methods seeded/updated (идемпотентно).');
    }
}
