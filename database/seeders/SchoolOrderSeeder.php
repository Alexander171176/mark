<?php

namespace Database\Seeders;

use App\Models\Admin\School\SchoolOrder\SchoolOrder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SchoolOrderSeeder extends Seeder
{
    public function run(): void
    {
        $orders = [];

        for ($id = 1; $id <= 48; $id++) {
            $userId = (($id - 1) % 6) + 2;
            $courseId = (($id - 1) % 84) + 1;
            $scheduleId = (($id - 1) % 8) + 1;

            $subtotal = 100 + ($id * 5);
            $discount = $id % 4 === 0 ? 20 : 0;
            $tax = round(($subtotal - $discount) * 0.12, 2);
            $total = $subtotal - $discount + $tax;

            $isPaid = $id % 5 !== 0;

            $orders[] = [
                'id' => $id,
                'user_id' => $userId,
                'school_course_id' => $courseId,
                'school_course_schedule_id' => $scheduleId,
                'number' => 'SCH-ORD-' . str_pad((string) $id, 6, '0', STR_PAD_LEFT),

                'buyer_name' => 'Student ' . $userId,
                'buyer_email' => 'student' . $userId . '@example.com',
                'buyer_phone' => '+770000000' . str_pad((string) $userId, 2, '0', STR_PAD_LEFT),

                'billing_company' => $id % 3 === 0 ? 'Demo IT Company' : null,
                'billing_tax_id' => $id % 3 === 0 ? 'BIN' . str_pad((string) $id, 8, '0', STR_PAD_LEFT) : null,
                'billing_address' => $id % 3 === 0 ? 'Astana, Demo street 10' : null,

                'is_paid' => $isPaid,
                'paid_at' => $isPaid ? now()->subDays($id) : null,

                'payment_method_id' => $isPaid ? 1 : null,
                'payment_method' => $isPaid ? 'card' : null,
                'payment_provider' => $isPaid ? 'stripe' : null,
                'payment_reference' => $isPaid ? 'PAY-' . Str::upper(Str::random(12)) : null,
                'confirmation_code' => $isPaid ? Str::upper(Str::random(8)) : null,
                'confirmation_status' => $isPaid ? 'confirmed' : 'pending',
                'failure_reason' => $isPaid ? null : 'Payment is pending in demo data.',

                'currency' => 'USD',
                'subtotal' => $subtotal,
                'discount_total' => $discount,
                'tax_total' => $tax,
                'total' => $total,

                'status' => $isPaid ? 'completed' : 'processing',
                'payment_status' => $isPaid ? 'paid' : 'pending',

                'items' => [
                    [
                        'type' => 'course',
                        'course_id' => $courseId,
                        'schedule_id' => $scheduleId,
                        'title' => 'Demo course order item',
                        'quantity' => 1,
                        'price' => $subtotal,
                        'discount' => $discount,
                        'total' => $total,
                    ],
                ],
                'meta' => [
                    'source' => 'seeder',
                    'demo' => true,
                ],

                'user_comment' => $id % 2 === 0 ? 'Хочу начать обучение в ближайшем потоке.' : null,
                'manager_comment' => $isPaid ? 'Оплата подтверждена.' : 'Ожидается оплата.',

                'external_id' => 'EXT-SCHOOL-ORDER-' . $id,
                'exported_at' => $id % 4 === 0 ? now()->subDays($id - 1) : null,

                'client_ip' => '127.0.0.1',
                'user_agent' => 'Seeder Demo User Agent',
                'public_hash' => hash('sha256', 'school-order-' . $id),
            ];
        }

        foreach ($orders as $order) {
            SchoolOrder::updateOrCreate(
                ['id' => $order['id']],
                collect($order)->except('id')->toArray()
            );
        }
    }
}
