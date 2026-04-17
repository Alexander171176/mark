<?php

namespace Database\Seeders;

use App\Models\Admin\Finance\Order\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // sanity checks
        foreach (['users', 'orders'] as $tbl) {
            if (!Schema::hasTable($tbl)) {
                $this->command?->warn("Таблица {$tbl} отсутствует — пропускаю OrderSeeder.");
                return;
            }
        }

        $users = User::query()->select('id', 'name', 'email')->get();
        if ($users->isEmpty()) {
            $this->command?->warn('Пользователи не найдены — сначала создайте пользователей.');
            return;
        }

        $currencies = ['USD', 'EUR', 'RUB'];

        foreach ($users as $user) {
            $ordersCount = rand(0, 6);

            for ($i = 0; $i < $ordersCount; $i++) {
                $currency  = $currencies[array_rand($currencies)];

                // Базовая корзина (цифровые товары/курсы)
                $subtotal  = $this->money(rand(2, 20) * rand(10, 50)); // от ~20 до ~1000
                $discount  = $this->money($subtotal * (rand(0, 100) < 35 ? (rand(5, 30) / 100) : 0));
                $taxRate   = $this->taxRateFor($currency);
                $tax       = $this->money(($subtotal - $discount) * $taxRate);
                $total     = $this->money($subtotal - $discount + $tax);

                // Статус оплаты + оплачено/не оплачено
                [$paymentStatus, $isPaid] = $this->randomPaymentState();

                // Статус заказа (логика "поверх" статуса оплаты)
                $status = $this->statusForPayment($paymentStatus);

                // Даты
                $createdAt = now()->subDays(rand(1, 120))->setTime(rand(9, 22), rand(0, 59));
                $paidAt    = $isPaid
                    ? (clone $createdAt)->addHours(rand(1, 72))
                    : null;

                // Номер заказа (уникальный)
                $number = $this->uniqueOrderNumber();

                // Немного фейковых данных покупателя (для онлайн-школы)
                $buyerName  = $user->name;
                $buyerEmail = $user->email;
                $buyerPhone = '+7 707 ' . rand(100, 999) . ' ' . rand(10, 99) . ' ' . rand(10, 99);

                // Фейковые мета/техданные
                $metaInfo = $this->fakeMeta(); // ip, user_agent, channel

                // Идемпотентно по number
                Order::firstOrCreate(
                    ['number' => $number],
                    [
                        'user_id'   => $user->id,

                        // Покупатель
                        'buyer_name'   => $buyerName,
                        'buyer_email'  => $buyerEmail,
                        'buyer_phone'  => $buyerPhone,
                        'billing_company' => null,
                        'billing_tax_id'  => null,
                        'billing_address' => null,

                        // Адрес и доставка (для онлайн-школы почти всегда пусто)
                        'shipping_address'       => null,
                        'shipping_address_parts' => null,
                        'delivery_method_id'     => null,
                        'delivery_cost'          => 0,
                        'delivery_options'       => null,
                        'delivery_interval'      => null,
                        'warehouse'              => null,
                        'delivery_date'          => null,

                        // Оплата
                        'is_paid'            => $isPaid,
                        'paid_at'            => $paidAt,
                        'payment_method_id'  => null,
                        'payment_method'     => 'card',        // для онлайн-школы по умолчанию
                        'payment_provider'   => 'test_gateway',
                        'payment_reference'  => null,
                        'confirmation_code'  => null,
                        'confirmation_status'=> 'confirmed',    // упрощённо
                        'failure_reason'     => null,

                        // Валюта/суммы
                        'currency'              => $currency,
                        'subtotal'              => $subtotal,
                        'discount_total'        => $discount,
                        'tax_total'             => $tax,
                        'total'                 => $total,
                        'total_shop_currency'   => $total,     // пока считаем, что валюта магазина = валюте заказа
                        'delivery_shop_currency'=> 0,

                        // Статусы
                        'status'         => $status,
                        'payment_status' => $paymentStatus,

                        // Контент/мета (JSON)
                        'items' => null, // нормализованные позиции идут в order_items, здесь можно хранить снапшот, если нужно
                        'meta'  => [
                            'channel' => $metaInfo['channel'],
                            // можно сюда же позже добавить купоны, промо и т.п.
                        ],

                        // Комментарии
                        'user_comment'    => null,
                        'manager_comment' => $this->maybeManagerComment(),

                        // Интеграции
                        'external_id' => null,
                        'exported_at' => null,

                        // Техданные
                        'client_ip'   => $metaInfo['ip'],
                        'user_agent'  => $metaInfo['user_agent'],
                        'public_hash' => hash('sha256', $number),

                        'created_at'  => $createdAt,
                        'updated_at'  => $createdAt,
                    ]
                );
            }
        }
    }

    /* ================= helpers ================= */

    private function money(float $v): string
    {
        return number_format(max(0, round($v, 2)), 2, '.', '');
    }

    private function taxRateFor(string $currency): float
    {
        // упрощённые ставки для примера
        return match ($currency) {
            'EUR' => 0.20,
            'RUB' => 0.20,
            default => 0.07, // USD
        };
    }

    /**
     * Возвращает [payment_status, is_paid].
     */
    private function randomPaymentState(): array
    {
        // 55% paid, 25% pending, 10% failed, 7% refunded, 3% partial
        $r = rand(1, 100);

        return match (true) {
            $r <= 55 => ['paid', true],
            $r <= 80 => ['pending', false],
            $r <= 90 => ['failed', false],
            $r <= 97 => ['refunded', true],
            default  => ['partial', true],
        };
    }

    /**
     * Маппинг статуса заказа от статуса оплаты.
     */
    private function statusForPayment(string $paymentStatus): string
    {
        return match ($paymentStatus) {
            'paid'     => rand(0, 1) ? 'processing' : 'completed',
            'pending'  => rand(0, 1) ? 'new' : 'processing',
            'failed'   => 'cancelled',
            'refunded' => 'refunded',
            'partial'  => 'processing',
            default    => 'new',
        };
    }

    private function uniqueOrderNumber(): string
    {
        // пример: ORD-202510-AB12CD34
        do {
            $number = 'ORD-' . now()->format('Ym') . '-' . strtoupper(Str::random(8));
        } while (Order::where('number', $number)->exists());

        return $number;
    }

    private function maybeManagerComment(): ?string
    {
        if (rand(0, 100) < 15) {
            return collect([
                'Клиент попросил выслать программу курса на email.',
                'Нужно уточнить, есть ли доступ к предыдущему потоку.',
                'Скидка по акции онлайн-школы.',
                'Оплата частями через провайдера.',
                'Выслать сертификат после завершения курса.',
            ])->random();
        }
        return null;
    }

    private function fakeMeta(): array
    {
        return [
            'ip'         => long2ip(random_int(0, 2_147_483_647)),
            'user_agent' => collect([
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'Mozilla/5.0 (X11; Linux x86_64)',
                'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X)',
            ])->random(),
            'channel'    => collect(['web', 'mobile', 'partner'])->random(),
        ];
    }
}
