<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_referral_codes', function (Blueprint $table) {
            $table->id()->comment('ID реферального кода пользователя');

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
            $table->foreign(['company_id', 'storefront_id'], 'fk_mrc_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * PROGRAM (tenant-safe)
             * Требование: в market_referral_programs есть uq_market_referral_programs_tenant_id
             * ========================================================= */

            $table->unsignedBigInteger('program_id')
                ->comment('Реферальная программа (market_referral_programs.id)');

            $table->foreign(['company_id', 'storefront_id', 'program_id'], 'fk_mrc_program_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_referral_programs')
                ->cascadeOnDelete();

            /* =========================================================
             * USER
             * ========================================================= */

            $table->foreignId('user_id')
                ->comment('Реферер (users.id) — владелец кода')
                ->constrained('users')
                ->cascadeOnDelete();

            /* =========================================================
             * FIELDS
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность кода');
            $table->string('code', 64)->comment('Реферальный код');

            // Денормализация метрик (для UI)
            $table->unsignedBigInteger('clicks')->default(0)->comment('Клики по ссылке');
            $table->unsignedBigInteger('signups')->default(0)->comment('Регистрации по коду');
            $table->unsignedBigInteger('first_orders')->default(0)->comment('Первые заказы по коду');

            $table->json('meta')->nullable()->comment('Доп. данные (utm/канал и т.п.)');
            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // чтобы можно было ссылаться tenant-safe FK (company_id, storefront_id, referral_code_id)
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_market_referral_codes_tenant_id');

            // уникальность кода в витрине
            $table->unique(['storefront_id', 'code'], 'uq_mrc_storefront_code');

            // 1 код на программу на пользователя (в рамках витрины)
            $table->unique(['storefront_id', 'program_id', 'user_id'], 'uq_mrc_program_user');

            $table->index(['company_id', 'storefront_id'], 'ix_mrc_tenant');
            $table->index(['storefront_id', 'program_id', 'activity'], 'ix_mrc_program_active');
            $table->index(['storefront_id', 'user_id', 'activity'], 'ix_mrc_user_active');

            $table->comment('Маркет: реферальные коды пользователей (1 код на программу), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_referral_codes');
    }
};
