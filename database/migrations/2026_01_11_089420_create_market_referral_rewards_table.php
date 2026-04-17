<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_referral_rewards', function (Blueprint $table) {
            $table->id()->comment('ID награды по рефералке');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // Гарантия: storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_mrr_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * EVENT (tenant-safe)
             * Требование: в market_referral_events есть уникальность (company_id, storefront_id, id)
             * например: uq_market_referral_events_tenant_id
             * ========================================================= */

            $table->unsignedBigInteger('event_id')
                ->comment('Событие (market_referral_events.id), на основе которого выдали награду');

            $table->foreign(['company_id', 'storefront_id', 'event_id'], 'fk_mrr_event_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_referral_events')
                ->cascadeOnDelete();

            /* =========================================================
             * BENEFICIARY
             * ========================================================= */

            $table->foreignId('beneficiary_user_id')
                ->comment('Кому выдана награда (users.id)')
                ->constrained('users')
                ->cascadeOnDelete();

            /* =========================================================
             * REWARD DATA
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность награды');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            $table->string('reward_type', 32)->default('bonus')
                ->comment('Тип награды: bonus/discount');

            $table->decimal('amount', 18, 2)->default(0)
                ->comment('Сумма/кол-во награды');

            $table->foreignId('currency_id')
                ->nullable()
                ->comment('Валюта (currencies.id), если денежная сумма')
                ->constrained('currencies')
                ->nullOnDelete();

            $table->string('status', 32)->default('pending')
                ->comment('Статус: pending/issued/reversed/failed');

            // Если награда выдана бонусами — привязка к ledger
            $table->foreignId('bonus_operation_id')
                ->nullable()
                ->comment('Операция бонусов (market_bonus_operations.id), если награда выдана бонусами')
                ->constrained('market_bonus_operations')
                ->nullOnDelete();

            $table->string('note', 255)->nullable()->comment('Заметка');
            $table->json('meta')->nullable()->comment('Доп. данные (контекст, расчёт, причины)');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // На одно событие — одна награда конкретному пользователю
            $table->unique(['event_id', 'beneficiary_user_id'], 'uq_mrr_event_user');

            $table->index(['company_id', 'storefront_id'], 'ix_mrr_tenant');
            $table->index(['storefront_id', 'status', 'created_at'], 'ix_mrr_storefront_status_time');
            $table->index(['beneficiary_user_id', 'created_at'], 'ix_mrr_user_time');
            $table->index(['event_id', 'created_at'], 'ix_mrr_event_time');

            $table->comment('Маркет: награды по рефералке (аудит + ledger), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_referral_rewards');
    }
};
