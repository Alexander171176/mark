<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * market_feedback_form_rules
 * Антиспам/лимиты/настройки защиты по форме.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_feedback_form_rules', function (Blueprint $table) {
            $table->id()->comment('ID правил формы (антиспам/лимиты)');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mffr_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * FORM (tenant-safe)
             * Требование: в market_feedback_forms есть unique(company_id, storefront_id, id)
             * ========================================================= */

            $table->unsignedBigInteger('form_id')
                ->comment('Форма (market_feedback_forms.id)');

            $table->foreign(['company_id', 'storefront_id', 'form_id'], 'fk_mffr_form_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_feedback_forms')
                ->cascadeOnDelete();

            $table->boolean('activity')->default(true)->comment('Активность правил');

            /* =========================================================
             * ANTI-SPAM MECHANICS
             * ========================================================= */

            $table->boolean('use_honeypot')->default(true)->comment('Использовать honeypot (скрытое поле)');
            $table->string('honeypot_field', 64)->default('website')->comment('Имя honeypot поля');

            $table->boolean('use_time_trap')->default(true)->comment('Проверять время заполнения формы');
            $table->unsignedSmallInteger('min_fill_seconds')->default(3)->comment('Минимум секунд на заполнение (меньше = бот)');
            $table->unsignedSmallInteger('max_fill_seconds')->nullable()->comment('Максимум секунд (опционально)');

            $table->boolean('use_token')->default(true)->comment('Использовать одноразовый токен формы');
            $table->unsignedSmallInteger('token_ttl_seconds')->default(900)->comment('TTL токена формы (сек)');

            /* =========================================================
             * RATE LIMITS
             * ========================================================= */

            $table->unsignedSmallInteger('limit_per_ip_per_hour')->default(20)->comment('Лимит отправок с IP в час');
            $table->unsignedSmallInteger('limit_per_fingerprint_per_day')->default(30)->comment('Лимит по fingerprint в день');
            $table->unsignedSmallInteger('limit_per_email_per_day')->default(10)->comment('Лимит по email в день');

            /* =========================================================
             * CONTENT FILTERS
             * ========================================================= */

            $table->unsignedSmallInteger('min_message_length')->default(5)->comment('Мин. длина сообщения');
            $table->unsignedSmallInteger('max_message_length')->default(5000)->comment('Макс. длина сообщения');

            $table->unsignedTinyInteger('max_links')->default(2)->comment('Максимум ссылок в тексте');
            $table->boolean('block_cyrillic_only')->default(false)->comment('Пример флага: блокировать, если только кириллица (опционально)');

            $table->json('meta')->nullable()->comment('Доп. правила/настройки (blacklist words, allowlist domains и т.д.)');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // 1 запись правил на форму
            $table->unique(['form_id'], 'uq_mffr_form_one_row');

            // критично для tenant-safe ссылок на rules (если понадобится)
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mffr_tenant_id');

            $table->index(['company_id', 'storefront_id'], 'ix_mffr_tenant');
            $table->index(['company_id', 'storefront_id', 'form_id', 'activity'], 'ix_mffr_form_active');

            $table->comment('Маркет: правила защиты/лимиты форм обратной связи (антиспам), tenant-safe.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_feedback_form_rules');
    }
};
