<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_promo_code_batches', function (Blueprint $table) {
            $table->id()->comment('ID пачки промокодов (массовая генерация)');

            /* =========================================================
             * TENANT / STOREFRONT (tenant-safe)
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // Гарантия: storefront принадлежит company
            $table->foreign(['company_id', 'storefront_id'], 'fk_mpcb_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * CAMPAIGN LINK (tenant-safe)
             * Требование: в market_promo_campaigns есть unique на (company_id, storefront_id, id)
             * или хотя бы index/unique на (storefront_id, id) / (company_id, storefront_id, id)
             * ========================================================= */

            $table->unsignedBigInteger('campaign_id')
                ->comment('Промо-кампания (market_promo_campaigns.id)');

            // tenant-safe: кампания должна быть из этой витрины/компании
            $table->foreign(['company_id', 'storefront_id', 'campaign_id'], 'fk_mpcb_campaign_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_promo_campaigns')
                ->cascadeOnDelete();

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность пачки');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            /* =========================================================
             * GENERATION SETTINGS
             * ========================================================= */

            $table->string('title', 255)->nullable()->comment('Название пачки (для админки)');
            $table->string('prefix', 32)->nullable()->comment('Префикс кодов (например OZON-)');
            $table->unsignedSmallInteger('code_length')->default(10)->comment('Длина кода (без префикса)');
            $table->string('alphabet', 64)->nullable()->comment('Алфавит генерации (опционально)');
            $table->boolean('upper')->default(true)->comment('Приводить к верхнему регистру');

            /* =========================================================
             * COUNTERS / STATUS
             * ========================================================= */

            $table->unsignedInteger('planned_count')->default(0)->comment('Сколько планировали сгенерировать');
            $table->unsignedInteger('generated_count')->default(0)->comment('Сколько реально сгенерировано');
            $table->unsignedInteger('used_count')->default(0)->comment('Сколько использовано (денорм)');

            $table->string('generation_status', 32)->default('draft')
                ->comment('Статус генерации: draft/generating/ready/failed');

            /* =========================================================
             * DATE OVERRIDES (optional)
             * ========================================================= */

            $table->timestamp('starts_at')->nullable()->comment('Начало действия (override)');
            $table->timestamp('ends_at')->nullable()->comment('Окончание действия (override)');

            /* =========================================================
             * EXTERNAL / NOTES / ACTOR
             * ========================================================= */

            $table->string('external_id', 64)->nullable()
                ->comment('ID пачки во внешней системе (опционально)');

            $table->string('note', 255)->nullable()->comment('Заметка админа');

            $table->foreignId('created_by_user_id')
                ->nullable()
                ->comment('Кто создал пачку (users.id)')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // Чтобы можно было tenant-safe ссылаться составным FK (если понадобится где-то дальше)
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mpcb_tenant_id');

            // В рамках витрины внешний идентификатор не должен повторяться (если используешь интеграции)
            $table->unique(['storefront_id', 'external_id'], 'uq_mpcb_storefront_external_id');

            $table->index(['company_id', 'storefront_id'], 'ix_mpcb_tenant');

            $table->index(['company_id', 'storefront_id', 'campaign_id', 'activity', 'sort'], 'ix_mpcb_campaign_list');

            $table->index(['storefront_id', 'generation_status', 'created_at'], 'ix_mpcb_status_time');

            $table->index(['storefront_id', 'starts_at', 'ends_at'], 'ix_mpcb_dates');

            // быстрый поиск по названию в админке (если будешь делать LIKE)
            $table->index(['storefront_id', 'title'], 'ix_mpcb_title');

            $table->comment('Маркет: пачки промокодов (массовая генерация), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_promo_code_batches');
    }
};
