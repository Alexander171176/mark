<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_review_bonus_rules', function (Blueprint $table) {
            $table->id()->comment('ID правила начисления бонусов за отзыв');

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
            $table->foreign(['company_id', 'storefront_id'], 'fk_mrbr_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность правила');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            /* =========================================================
             * BASIC FIELDS
             * ========================================================= */

            $table->string('title', 255)->comment('Название правила');

            // (Опционально) удобный идентификатор для UI/логов
            $table->string('slug', 191)->nullable()->comment('Slug правила (уникален в рамках витрины), опционально');

            $table->boolean('require_purchase')->default(true)
                ->comment('Требовать подтверждённую покупку (по order/order_item)');

            $table->unsignedTinyInteger('min_rating')->nullable()
                ->comment('Минимальный рейтинг (1..5), если нужно');

            $table->unsignedInteger('moderation_delay_hours')->default(0)
                ->comment('Через сколько часов после публикации/модерации можно начислять');

            $table->unsignedInteger('max_awards_per_user_per_month')->nullable()
                ->comment('Лимит начислений пользователю в месяц');

            /* =========================================================
             * REWARD
             * ========================================================= */

            $table->string('reward_type', 32)->default('bonus')
                ->comment('Тип награды: bonus (баллы/деньги), можно расширить позже');

            $table->decimal('reward_value', 18, 2)->default(0)
                ->comment('Значение награды (баллы/сумма)');

            $table->foreignId('currency_id')
                ->nullable()
                ->comment('Валюта награды (currencies.id), если денежные бонусы')
                ->constrained('currencies')
                ->nullOnDelete();

            /* =========================================================
             * META
             * ========================================================= */

            $table->json('settings')->nullable()->comment('Доп. настройки (json)');
            $table->string('note', 255)->nullable()->comment('Заметка админа');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // Tenant-unique для безопасных составных FK из других таблиц
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mrbr_tenant_id');

            // Если используешь slug — он должен быть уникален в витрине
            $table->unique(['storefront_id', 'slug'], 'uq_mrbr_storefront_slug');

            $table->index(['company_id', 'storefront_id'], 'ix_mrbr_tenant');
            $table->index(['storefront_id', 'activity', 'sort'], 'ix_mrbr_list');

            // Частые фильтры/аналитика
            $table->index(['storefront_id', 'require_purchase', 'min_rating'], 'ix_mrbr_require_rating');
            $table->index(['storefront_id', 'reward_type'], 'ix_mrbr_reward_type');

            $table->comment('Маркет: правила начисления бонусов/баллов за отзывы (с модерацией), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_review_bonus_rules');
    }
};
