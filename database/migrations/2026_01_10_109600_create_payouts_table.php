<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Выплаты преподавателю за период
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $t) {
            $t->id();

            // Кому платим
            $t->foreignId('instructor_profile_id')
                ->constrained('instructor_profiles')
                ->cascadeOnDelete();

            // Через какой провайдер/аккаунт (необязательно)
            $t->foreignId('provider_account_id')
                ->nullable()
                ->constrained('provider_accounts')
                ->nullOnDelete();

            // Человеко-читаемый номер/код (для актов/выписок)
            $t->string('number', 32)->unique();

            // Период, за который идёт выплата (для отчётности)
            $t->date('period_start')->nullable();
            $t->date('period_end')->nullable();

            // Суммы
            $t->char('currency', 3)->default('USD');     // ISO 4217
            $t->decimal('amount_gross', 18, 2)->default(0)->comment('Начислено брутто');
            $t->decimal('fee_total', 18, 2)->default(0)->comment('Комиссии провайдера/платёжки');
            $t->decimal('tax_total', 18, 2)->default(0)->comment('Налоги/удержания');
            $t->decimal('amount_net', 18, 2)->default(0)->comment('К выплате (нетто)');

            // Статус и метод
            $t->string('status', 20)->default('pending')
                ->comment('pending|processing|paid|failed|cancelled');
            $t->string('method', 32)->nullable()
                ->comment('manual|bank_wire|stripe_transfer|paypal_payout|...');

            // Метки жизненного цикла
            $t->timestamp('paid_at')->nullable();

            // Служебные поля
            $t->text('notes')->nullable();
            $t->json('meta')->nullable();

            // Кто создал/обновил (необязательно)
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $t->timestamps();
            $t->softDeletes();

            // Индексы на частые выборки
            $t->index(['instructor_profile_id', 'status'], 'idx_payout_instructor_status');
            $t->index(['status', 'paid_at'], 'idx_payout_status_paid');
            $t->index(['period_start', 'period_end'], 'idx_payout_period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
