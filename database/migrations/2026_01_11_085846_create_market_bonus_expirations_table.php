<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_bonus_expirations', function (Blueprint $table) {
            $table->id()->comment('ID партии начисления (для учёта сгорания/FIFO)');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)');

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * LINKS (tenant-safe)
             * ========================================================= */

            $table->unsignedBigInteger('account_id')
                ->comment('Бонусный счёт (market_bonus_accounts.id)');

            // Операция начисления, которая создала “партию”
            $table->unsignedBigInteger('earn_operation_id')
                ->comment('Операция начисления (market_bonus_operations.id)');

            /* =========================================================
             * AMOUNTS / DATES
             * ========================================================= */

            $table->decimal('earned_amount', 18, 2)->comment('Начислено в партии');
            $table->decimal('remaining_amount', 18, 2)->comment('Остаток в партии');

            $table->timestamp('earned_at')->nullable()->comment('Когда начислено (проведено)');
            $table->timestamp('expires_at')->nullable()->comment('Когда сгорает, null = не сгорает');

            $table->string('status', 32)
                ->default('active')
                ->comment('active/expired/consumed/cancelled');

            $table->timestamps();

            /* =========================================================
             * INDEXES / UNIQUE
             * ========================================================= */

            $table->index(['company_id', 'storefront_id'], 'ix_mbe_tenant');

            // очередь списания/сгорания по аккаунту
            $table->index(
                ['account_id', 'status', 'expires_at'],
                'ix_mbe_account_status_expires'
            );

            $table->index(['expires_at', 'status'], 'ix_mbe_expire_queue');

            // 1 earn_operation -> 1 партия
            $table->unique(['earn_operation_id'], 'uq_mbe_earn_operation');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            $table->foreign('company_id', 'fk_mbe_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            $table->foreign(['company_id', 'storefront_id'], 'fk_mbe_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            $table->foreign(['company_id', 'storefront_id', 'account_id'], 'fk_mbe_account_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_bonus_accounts')
                ->cascadeOnDelete();

            $table->foreign(['company_id', 'storefront_id', 'earn_operation_id'], 'fk_mbe_earn_op_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_bonus_operations')
                ->cascadeOnDelete();

            $table->comment('Маркет: партии начислений для корректного списания и сгорания (FIFO), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_bonus_expirations');
    }
};
