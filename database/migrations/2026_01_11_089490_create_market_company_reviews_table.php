<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_company_reviews', function (Blueprint $table) {
            $table->id()->comment('ID отзыва о компании/продавце');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец витрины (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mcr_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * TARGET COMPANY (о какой компании отзыв)
             * ========================================================= */

            $table->unsignedBigInteger('target_company_id')
                ->comment('О какой компании отзыв (market_companies.id)');

            $table->foreign('target_company_id', 'fk_mcr_target_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            /* =========================================================
             * AUTHOR
             * ========================================================= */

            $table->foreignId('user_id')
                ->nullable()
                ->comment('Пользователь-автор (users.id), если авторизован')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('author_name', 255)->nullable()->comment('Имя автора (снапшот)');
            $table->string('author_email', 255)->nullable()->comment('Email автора (снапшот)');
            $table->string('author_phone', 50)->nullable()->comment('Телефон автора (снапшот)');

            $table->string('locale', 10)->nullable()->comment('Локаль отзыва (ru/kk/en/...)');

            /* =========================================================
             * CONTENT / MODERATION
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Отзыв активен (видимость/мягкая модерация)');

            $table->string('status', 32)->default('pending')
                ->comment('Статус модерации: pending/approved/rejected/spam');

            $table->unsignedTinyInteger('rating')->comment('Рейтинг 1..5');

            $table->string('title', 255)->nullable()->comment('Заголовок отзыва (опционально)');
            $table->text('content')->nullable()->comment('Текст отзыва');

            $table->text('pros')->nullable()->comment('Плюсы (опционально)');
            $table->text('cons')->nullable()->comment('Минусы (опционально)');

            $table->boolean('is_anonymous')->default(false)->comment('Анонимный отзыв (скрывать имя автора в UI)');

            /* =========================================================
             * VERIFICATION (order link tenant-safe)
             * ⚠️ нельзя SET NULL на композитном FK с NOT NULL company_id/storefront_id
             * ========================================================= */

            $table->unsignedBigInteger('order_id')
                ->nullable()
                ->comment('Заказ, подтверждающий покупку (market_orders.id), опционально');

            // ✅ FIX: вместо nullOnDelete()
            $table->foreign(['company_id', 'storefront_id', 'order_id'], 'fk_mcr_order_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_orders')
                ->restrictOnDelete();

            $table->boolean('is_verified_purchase')->default(false)
                ->comment('Проверенная покупка (denorm, можно выставлять при наличии order_id)');

            /* =========================================================
             * COMPANY REPLY
             * ========================================================= */

            $table->text('reply_text')->nullable()->comment('Ответ компании на отзыв');
            $table->timestamp('replied_at')->nullable()->comment('Когда компания ответила');

            $table->foreignId('replied_by_user_id')
                ->nullable()
                ->comment('Кто ответил от компании (users.id)')
                ->constrained('users')
                ->nullOnDelete();

            /* =========================================================
             * TECH / AUDIT
             * ========================================================= */

            $table->string('ip', 64)->nullable()->comment('IP автора (опционально)');
            $table->string('user_agent', 255)->nullable()->comment('User-Agent (опционально)');

            $table->json('meta')->nullable()->comment('Доп. поля/параметры (опционально)');

            $table->timestamp('moderated_at')->nullable()->comment('Когда промодерировано');

            $table->foreignId('moderated_by_user_id')
                ->nullable()
                ->comment('Кто модерировал (users.id)')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('note', 255)->nullable()->comment('Заметка админа');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mcr_tenant_id');

            // ⚠️ MySQL допускает много NULL в UNIQUE, для гостей это норм.
            $table->unique(['storefront_id', 'target_company_id', 'user_id'], 'uq_mcr_storefront_company_user');

            $table->index(['company_id', 'storefront_id'], 'ix_mcr_tenant');
            $table->index(['storefront_id', 'target_company_id', 'status', 'created_at'], 'ix_mcr_company_status_time');
            $table->index(['storefront_id', 'target_company_id', 'rating'], 'ix_mcr_company_rating');
            $table->index(['storefront_id', 'status', 'activity', 'created_at'], 'ix_mcr_moderation_queue');
            $table->index(['order_id'], 'ix_mcr_order');
            $table->index(['storefront_id', 'is_verified_purchase', 'created_at'], 'ix_mcr_verified_time');

            $table->comment('Маркет: отзывы о компании/продавце (tenant-safe), с модерацией и ответом компании');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_company_reviews');
    }
};
