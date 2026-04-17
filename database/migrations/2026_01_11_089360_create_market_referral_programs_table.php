<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_referral_programs', function (Blueprint $table) {
            $table->id()->comment('ID реферальной программы');

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
            $table->foreign(['company_id', 'storefront_id'], 'fk_mrp_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность программы');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            /* =========================================================
             * MAIN FIELDS
             * ========================================================= */

            $table->string('title', 255)->comment('Название программы');
            $table->string('code_prefix', 32)->nullable()->comment('Префикс реф.кода (опционально)');

            // Когда действует
            $table->timestamp('starts_at')->nullable()->comment('Начало действия');
            $table->timestamp('ends_at')->nullable()->comment('Окончание действия');

            /* =========================================================
             * CONDITIONS
             * ========================================================= */

            $table->decimal('min_first_order_total', 18, 2)->nullable()
                ->comment('Мин. сумма первого заказа приглашённого для награды');

            /* =========================================================
             * REWARDS
             * ========================================================= */

            $table->string('referrer_reward_type', 32)->default('bonus')
                ->comment('Награда рефереру: bonus/discount');

            $table->decimal('referrer_reward_value', 18, 2)->nullable()
                ->comment('Значение награды рефереру (баллы/сумма/% в зависимости от типа)');

            $table->string('referee_reward_type', 32)->default('bonus')
                ->comment('Награда приглашённому: bonus/discount');

            $table->decimal('referee_reward_value', 18, 2)->nullable()
                ->comment('Значение награды приглашённому');

            $table->foreignId('currency_id')
                ->nullable()
                ->comment('Валюта (currencies.id), если награда фикс суммой')
                ->constrained('currencies')
                ->nullOnDelete();

            /* =========================================================
             * LIMITS
             * ========================================================= */

            $table->unsignedInteger('max_rewards_per_referrer')->nullable()
                ->comment('Лимит наград одному рефереру');

            $table->unsignedInteger('max_total_rewards')->nullable()
                ->comment('Общий лимит наград по программе');

            /* =========================================================
             * META
             * ========================================================= */

            $table->json('settings')->nullable()->comment('Доп. настройки (json)');
            $table->string('note', 255)->nullable()->comment('Заметка админа');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // чтобы можно было ссылаться tenant-safe FK (company_id, storefront_id, program_id)
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_market_referral_programs_tenant_id');

            $table->index(['company_id', 'storefront_id'], 'ix_mrp_tenant');
            $table->index(['storefront_id', 'activity', 'sort'], 'ix_mrp_list');
            $table->index(['storefront_id', 'starts_at', 'ends_at'], 'ix_mrp_dates');

            // Практические уникальности (можешь оставить или убрать):
            $table->unique(['storefront_id', 'title'], 'uq_mrp_storefront_title');
            $table->unique(['storefront_id', 'code_prefix'], 'uq_mrp_storefront_code_prefix');

            $table->comment('Маркет: реферальные программы (правила наград), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_referral_programs');
    }
};
