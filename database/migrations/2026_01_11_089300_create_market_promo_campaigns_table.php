<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_promo_campaigns', function (Blueprint $table) {
            $table->id()->comment('ID промо-кампании');

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
            $table->foreign(['company_id', 'storefront_id'], 'fk_mpc_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность кампании');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');
            $table->boolean('is_public')->default(true)->comment('Показывать в витрине/ЛК (если нужно)');

            /* =========================================================
             * LOCALE / IDENTIFIERS
             * ========================================================= */

            $table->string('locale', 10)->nullable()
                ->comment('Локаль кампании (ru/kk/en), если контент локализуется');

            $table->string('title', 255)->comment('Название кампании');
            $table->string('slug', 191)->comment('Slug кампании');

            /* =========================================================
             * TYPE / CODES
             * ========================================================= */

            $table->string('type', 32)->default('promo')
                ->comment('Тип: promo/coupon (для UI/логики)');

            $table->string('code_mode', 32)->default('manual')
                ->comment('Режим кодов: manual/generated/single_use');

            // если нужен “один общий код”, можно хранить тут (для manual)
            $table->string('code', 64)->nullable()
                ->comment('Код промо (если code_mode=manual и один общий код)');

            /* =========================================================
             * BENEFIT (что даёт кампания)
             * ========================================================= */

            $table->string('benefit_type', 32)->default('discount')
                ->comment('Преимущество: discount/bonus/mixed');

            // discount
            $table->string('discount_type', 32)->nullable()
                ->comment('Тип скидки: percent/fixed');

            $table->decimal('discount_value', 18, 2)->nullable()
                ->comment('Значение скидки (% или сумма)');

            // bonus
            $table->decimal('bonus_value', 18, 2)->nullable()
                ->comment('Значение бонуса (баллы или сумма)');

            $table->foreignId('currency_id')
                ->nullable()
                ->comment('Валюта (currencies.id), если фиксированная сумма/денежные бонусы')
                ->constrained('currencies')
                ->nullOnDelete();

            /* =========================================================
             * CONSTRAINTS / LIMITS
             * ========================================================= */

            $table->decimal('min_order_total', 18, 2)->nullable()
                ->comment('Мин. сумма заказа для применения');

            $table->decimal('max_discount_amount', 18, 2)->nullable()
                ->comment('Потолок скидки (если discount_type=percent)');

            $table->boolean('is_stackable')->default(false)
                ->comment('Можно совмещать с другими скидками/промо');

            // лимиты использования
            $table->unsignedInteger('max_uses_total')->nullable()
                ->comment('Лимит использований общий');

            $table->unsignedInteger('max_uses_per_user')->nullable()
                ->comment('Лимит использований на пользователя');

            // счётчики (удобно для аналитики/быстрых проверок)
            $table->unsignedInteger('uses_total')->default(0)
                ->comment('Сколько раз использовано всего (счётчик)');

            /* =========================================================
             * DATES / ACTIVE WINDOW
             * ========================================================= */

            $table->timestamp('starts_at')->nullable()->comment('Начало действия');
            $table->timestamp('ends_at')->nullable()->comment('Окончание действия');

            /* =========================================================
             * APPLIES TO / SCOPE
             * ========================================================= */

            $table->string('applies_to', 32)->default('order')
                ->comment('Применение: order/items/shipping (на заказ/товары/доставку)');

            $table->json('scope')->nullable()
                ->comment('Область применения (json): product_ids/category_ids/brand_ids и т.д.');

            /* =========================================================
             * TECH
             * ========================================================= */

            $table->string('note', 255)->nullable()->comment('Заметка админа');
            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */
            $table->unique(['company_id','storefront_id','id'], 'uq_mpc_tenant_id');

            // slug уникален в рамках витрины (+локаль, если используешь локализацию)
            $table->unique(['storefront_id', 'locale', 'slug'], 'uq_mpc_storefront_locale_slug');

            // если используешь единый код (manual) — защитим от дублей в рамках витрины
            $table->unique(['storefront_id', 'code'], 'uq_mpc_storefront_code');

            $table->index(['company_id', 'storefront_id'], 'ix_mpc_tenant');
            $table->unique(['storefront_id','id'], 'uq_mpc_storefront_id');
            $table->index(['storefront_id', 'activity', 'sort'], 'ix_mpc_list');

            // быстрый поиск “активных сейчас”
            $table->index(['storefront_id', 'activity', 'starts_at', 'ends_at'], 'ix_mpc_active_window');

            $table->index(['storefront_id', 'type'], 'ix_mpc_type');
            $table->index(['storefront_id', 'code_mode'], 'ix_mpc_code_mode');
            $table->index(['storefront_id', 'benefit_type'], 'ix_mpc_benefit');
            $table->index(['storefront_id', 'applies_to'], 'ix_mpc_applies_to');

            $table->comment('Маркет: промо-кампании/купоны (скидки и/или бонусы), tenant-safe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_promo_campaigns');
    }
};
