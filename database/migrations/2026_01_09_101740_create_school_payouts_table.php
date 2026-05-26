<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('school_payouts', function (Blueprint $t) {
            $t->id();

            // Преподаватель
            $t->foreignId('school_instructor_profile_id')
                ->constrained('school_instructor_profiles')
                ->cascadeOnDelete()
                ->comment('Преподаватель');

            // Аккаунт провайдера
            $t->foreignId('school_provider_account_id')
                ->nullable()
                ->constrained('school_provider_accounts')
                ->nullOnDelete()
                ->comment('Аккаунт платёжного провайдера');

            // Номер выплаты
            $t->string('number', 32)->unique()->comment('Номер выплаты');

            // Период выплаты
            $t->date('period_start')->nullable()->comment('Начало периода');
            $t->date('period_end')->nullable()->comment('Конец периода');

            // Суммы
            $t->char('currency', 3)->default('USD')->comment('Валюта');
            $t->decimal('amount_gross', 18, 2)->default(0)->comment('Начислено брутто');
            $t->decimal('fee_total', 18, 2)->default(0)->comment('Комиссии');
            $t->decimal('tax_total', 18, 2)->default(0)->comment('Налоги/удержания');
            $t->decimal('amount_net', 18, 2)->default(0)->comment('К выплате нетто');

            // Статус и метод
            $t->string('status', 20)
                ->default('pending')
                ->comment('pending|processing|paid|failed|cancelled');

            $t->string('method', 32)
                ->nullable()
                ->comment('manual|bank_wire|stripe_transfer|paypal_payout');

            // Дата оплаты
            $t->timestamp('paid_at')->nullable()->comment('Дата выплаты');

            // Служебные поля
            $t->text('notes')->nullable()->comment('Заметки');
            $t->json('meta')->nullable()->comment('Дополнительные данные');

            // Аудит
            $t->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Кто создал');

            $t->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Кто обновил');

            $t->timestamps();

            // Индексы
            $t->index(
                ['school_instructor_profile_id', 'status'],
                'idx_school_payout_instructor_status'
            );

            $t->index(['status', 'paid_at'], 'idx_school_payout_status_paid');
            $t->index(['period_start', 'period_end'], 'idx_school_payout_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_payouts');
    }
};
