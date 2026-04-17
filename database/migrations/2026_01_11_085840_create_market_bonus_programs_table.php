<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_bonus_programs', function (Blueprint $table) {
            $table->id()->comment('ID бонусной программы витрины');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)');

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность программы');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            /* =========================================================
             * IDENTIFIERS / UI
             * ========================================================= */

            $table->string('title', 255)->comment('Название программы');
            $table->string('code', 64)->comment('Код программы (например: default)');

            // Локаль (если нужно локализовать тексты программы)
            $table->string('locale', 10)->nullable()->comment('Локаль (ru/kk/en)');

            /* =========================================================
             * RULES
             * ========================================================= */

            $table->string('currency_mode', 16)
                ->default('points')
                ->comment('points (баллы) / money (денежные бонусы)');

            $table->decimal('rate_earn', 18, 6)
                ->default(0)
                ->comment('Начисление: коэффициент (0.05 = 5% от суммы)');

            $table->decimal('rate_spend', 18, 6)
                ->default(1)
                ->comment('Списание: курс (1 балл = 1 единица/или иной курс)');

            $table->unsignedInteger('earn_delay_days')
                ->default(0)
                ->comment('Задержка начисления после покупки (дней)');

            $table->unsignedInteger('expire_days')
                ->nullable()
                ->comment('Срок жизни начисления (дней), null = не сгорает');

            /* =========================================================
             * LIMITS
             * ========================================================= */

            $table->decimal('max_spend_percent', 5, 2)
                ->nullable()
                ->comment('Макс. % оплаты бонусами от суммы заказа (например 30.00)');

            $table->decimal('min_order_total', 18, 2)
                ->nullable()
                ->comment('Минимальная сумма заказа для участия');

            $table->boolean('allow_on_discounted')
                ->default(true)
                ->comment('Разрешать начисление на товары со скидкой');

            $table->boolean('allow_spend_with_discount')
                ->default(true)
                ->comment('Разрешать списание вместе со скидками');

            /* =========================================================
             * TEXT
             * ========================================================= */

            $table->string('short', 255)->nullable()->comment('Краткое описание');
            $table->text('description')->nullable()->comment('Полное описание/правила');

            $table->string('note', 255)->nullable()->comment('Заметка админа');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // tenant-safe ключ (если вдруг понадобится внешняя ссылка вида (company_id, storefront_id, bonus_program_id))
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mbp_tenant_id');

            // код уникален в пределах витрины
            $table->unique(['storefront_id', 'code'], 'uq_mbp_storefront_code');

            // если у тебя реально будет несколько локалей одной и той же программы:
            // $table->unique(['storefront_id', 'locale', 'code'], 'uq_mbp_storefront_locale_code');

            $table->index(['company_id', 'storefront_id'], 'ix_mbp_tenant');
            $table->index(['storefront_id', 'activity', 'sort'], 'ix_mbp_list');
            $table->index(['storefront_id', 'code'], 'ix_mbp_storefront_code');
            $table->index(['storefront_id', 'locale'], 'ix_mbp_storefront_locale');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            $table->foreign('company_id', 'fk_mbp_company')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            $table->foreign(['company_id', 'storefront_id'], 'fk_mbp_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            $table->comment('Маркет: настройки бонусной программы витрины (tenant-safe)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_bonus_programs');
    }
};
