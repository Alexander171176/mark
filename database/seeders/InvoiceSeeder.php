<?php

namespace Database\Seeders;

use App\Models\Admin\Finance\Invoice\Invoice;
use App\Models\Admin\Finance\Order\Order;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('invoices') || !Schema::hasTable('orders')) {
            $this->command?->warn('Нет таблиц invoices/orders — пропускаю InvoiceSeeder.');
            return;
        }

        $orders = Order::query()->with('user:id,name,email')->get();
        if ($orders->isEmpty()) {
            $this->command?->warn('Заказы не найдены — сперва посей OrderSeeder.');
            return;
        }

        $faker = fake();

        foreach ($orders as $order) {
            $number = $this->invoiceNumber($order->id);

            $status = match ($order->status) {
                'paid'                     => 'paid',
                'pending'                  => 'issued',
                'cancelled', 'failed'      => 'void',
                'refunded', 'partial_refund' => 'refunded',
                default                    => 'draft',
            };

            $issuedAt = $order->created_at?->copy() ?? now()->subDays(rand(5, 15));
            $dueAt    = $issuedAt?->copy()->addDays(7);
            $paidAt   = $order->paid_at;

            if ($status === 'paid') {
                if (!$paidAt) $paidAt = $issuedAt->copy()->addDays(rand(0, 5));
                if ($dueAt && $dueAt->lt($issuedAt)) $dueAt = $issuedAt->copy()->addDays(7);
            }

            // ---- Адрес без state()/secondaryAddress()
            $billToName     = $order->user->name ?? $faker->name();
            $billToEmail    = $order->user->email ?? $faker->safeEmail();
            $billToAddress1 = $faker->streetAddress();
            $billToAddress2 = $faker->optional(0.3)->passthrough('кв. ' . rand(1, 300));
            $billToCity     = $faker->city();
            $billToCountry  = $faker->randomElement(['US','GB','DE','PL','KZ','RU']);

            $regionsByCountry = [
                'US' => ['California','New York','Texas','Florida','Illinois'],
                'GB' => ['England','Scotland','Wales','Northern Ireland'],
                'DE' => ['Bayern','Berlin','Nordrhein‑Westfalen','Sachsen'],
                'PL' => ['Mazowieckie','Małopolskie','Wielkopolskie','Dolnośląskie'],
                'KZ' => ['Almaty','Astana','Aqmola','Qaraghandy'],
                'RU' => ['Moscow Oblast','Saint Petersburg','Tatarstan','Sverdlovsk Oblast'],
            ];
            $billToRegion   = $faker->randomElement($regionsByCountry[$billToCountry] ?? ['—']);
            $billToPostcode = $faker->postcode();

            Invoice::updateOrCreate(
                ['number' => $number],
                [
                    'order_id'         => $order->id,
                    'status'           => $status,
                    'currency'         => $order->currency ?? 'USD',
                    'subtotal'         => $order->subtotal ?? 0,
                    'discount_total'   => $order->discount_total ?? 0,
                    'tax_total'        => $order->tax_total ?? 0,
                    'total'            => $order->total ?? 0,
                    'issued_at'        => $status === 'draft' ? null : $issuedAt,
                    'due_at'           => in_array($status, ['issued','paid','refunded'], true) ? $dueAt : null,
                    'paid_at'          => in_array($status, ['paid','refunded'], true) ? $paidAt : null,

                    'bill_to_name'     => $billToName,
                    'bill_to_tax_id'   => null,
                    'bill_to_email'    => $billToEmail,
                    'bill_to_address1' => $billToAddress1,
                    'bill_to_address2' => $billToAddress2,
                    'bill_to_city'     => $billToCity,
                    'bill_to_region'   => $billToRegion,
                    'bill_to_postcode' => $billToPostcode,
                    'bill_to_country'  => $billToCountry,

                    'notes'            => $this->makeNote($status),
                    'meta'             => [
                        'seeded'   => true,
                        'seed_run' => now()->toDateTimeString(),
                        'source'   => 'InvoiceSeeder',
                    ],
                ]
            );
        }

        $this->command?->info('Invoices seeded/updated (идемпотентно).');
    }

    private function invoiceNumber(int $orderId): string
    {
        return sprintf('INV-%s-%06d', now()->format('Y'), $orderId);
    }

    private function makeNote(string $status): ?string
    {
        return match ($status) {
            'paid'     => 'Инвойс оплачен.',
            'issued'   => 'Счёт выставлен, ожидает оплаты.',
            'void'     => 'Инвойс аннулирован вследствие отмены/ошибки заказа.',
            'refunded' => 'Инвойс возвращён (полностью или частично).',
            default    => null,
        };
    }
}
