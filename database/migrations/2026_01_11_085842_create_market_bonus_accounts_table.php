<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_bonus_accounts', function (Blueprint $table) {
            $table->id()->comment('ID бонусного счёта пользователя (кошелёк)');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)');

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * OWNER
             * ========================================================= */

            $table->foreignId('user_id')
                ->comment('Покупатель (users.id)');

            /* =========================================================
             * PROGRAM
             * ========================================================= */

            $table->foreignId('program_id')
                ->comment('Бонусная программа (market_bonus_programs.id)');

            /* =========================================================
             * STATE
             * ========================================================= */

            $table->boolean('activity')
                ->default(true)
                ->comment('Активность кошелька');

            /* =========================================================
             * BALANCES
             * ========================================================= */

            $table->decimal('balance', 18, 2)
                ->default(0)
                ->comment('Доступный баланс');

            $table->decimal('hold_balance', 18, 2)
                ->default(0)
                ->comment('Заморожено (hold)');

            $table->decimal('lifetime_earned', 18, 2)
                ->default(0)
                ->comment('Всего начислено за всё время');

            $table->decimal('lifetime_spent', 18, 2)
                ->default(0)
                ->comment('Всего списано за всё время');

            $table->timestamp('last_operation_at')
                ->nullable()
                ->comment('Дата последней операции');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            /**
             * 1 кошелёк на пользователя
             * в рамках витрины и бонусной программы
             */
            $table->unique(
                ['storefront_id', 'program_id', 'user_id'],
                'uq_mba_storefront_program_user'
            );

            /**
             * tenant-safe FK support
             * (если понадобится ссылаться как (id, company_id, storefront_id))
             */
            $table->unique(
                ['id', 'company_id', 'storefront_id'],
                'uq_mba_id_tenant'
            );

            $table->index(
                ['company_id', 'storefront_id'],
                'ix_mba_tenant'
            );

            $table->index(
                ['storefront_id', 'user_id', 'activity'],
                'ix_mba_storefront_user_active'
            );

            $table->index(
                ['program_id', 'activity'],
                'ix_mba_program_active'
            );

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            $table->foreign('company_id', 'fk_mba_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            $table->foreign(
                ['company_id', 'storefront_id'],
                'fk_mba_company_storefront'
            )
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            $table->foreign('user_id', 'fk_mba_user')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('program_id', 'fk_mba_program')
                ->references('id')
                ->on('market_bonus_programs')
                ->cascadeOnDelete();

            $table->comment('Маркет: бонусный кошелёк пользователя (tenant-safe)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_bonus_accounts');
    }
};
