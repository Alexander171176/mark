<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_review_bonus_awards', function (Blueprint $table) {
            $table->id()->comment('ID начисления бонусов за отзыв');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mrba_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * RULE (tenant-safe)
             * ✅ теперь FK идёт по (company_id, storefront_id, rule_id)
             * и использует существующий uq_mrbr_tenant_id в rules
             * ========================================================= */

            $table->unsignedBigInteger('rule_id')
                ->comment('Правило начисления (market_review_bonus_rules.id)');

            $table->foreign(['company_id', 'storefront_id', 'rule_id'], 'fk_mrba_rule_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_review_bonus_rules')
                ->cascadeOnDelete();

            /* =========================================================
             * REVIEW (tenant-safe)
             * ========================================================= */

            $table->unsignedBigInteger('review_id')
                ->comment('Отзыв (market_product_reviews.id)');

            $table->foreign(['company_id', 'storefront_id', 'review_id'], 'fk_mrba_review_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_product_reviews')
                ->cascadeOnDelete();

            /* =========================================================
             * BENEFICIARY
             * ========================================================= */

            $table->foreignId('user_id')
                ->comment('Кому начисляем (users.id)')
                ->constrained('users')
                ->cascadeOnDelete();

            /* =========================================================
             * STATUS / AMOUNTS
             * ========================================================= */

            $table->string('status', 32)->default('pending')
                ->comment('Статус: pending/hold/issued/rejected/reversed');

            $table->decimal('amount', 18, 2)->default(0)
                ->comment('Сумма/кол-во бонусов к выдаче');

            $table->foreignId('currency_id')
                ->nullable()
                ->comment('Валюта (currencies.id), если денежные бонусы')
                ->constrained('currencies')
                ->nullOnDelete();

            /* =========================================================
             * LEDGER LINK (optional)
             * ========================================================= */

            $table->foreignId('bonus_operation_id')
                ->nullable()
                ->comment('Операция бонусов (market_bonus_operations.id), если выдали/заморозили бонусы')
                ->constrained('market_bonus_operations')
                ->nullOnDelete();

            /* =========================================================
             * DATES
             * ========================================================= */

            $table->timestamp('hold_until')->nullable()->comment('До какого времени удержание (если hold)');
            $table->timestamp('issued_at')->nullable()->comment('Когда выдали');
            $table->timestamp('rejected_at')->nullable()->comment('Когда отклонили');

            $table->string('note', 255)->nullable()->comment('Заметка');
            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mrba_tenant_id');

            // На один отзыв — одно начисление (в рамках витрины)
            $table->unique(['storefront_id', 'review_id'], 'uq_mrba_storefront_review');

            $table->index(['company_id', 'storefront_id'], 'ix_mrba_tenant');
            $table->index(['storefront_id', 'status', 'created_at'], 'ix_mrba_storefront_status_time');
            $table->index(['user_id', 'created_at'], 'ix_mrba_user_time');
            $table->index(['review_id'], 'ix_mrba_review');

            $table->comment('Маркет: начисления бонусов/баллов за отзывы (с модерацией и привязкой к ledger), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_review_bonus_awards');
    }
};
