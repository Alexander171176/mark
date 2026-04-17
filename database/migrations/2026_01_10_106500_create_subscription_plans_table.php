<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Таблица тарифных планов подписок (месячные/годовые и т.д.)
     *
     * Назначение:
     * - Мультиязычные планы (locale)
     * - Мультивалютные планы (currency_id -> currencies)
     * - Универсальная поддержка любых провайдеров оплаты (provider/provider_ref/provider_payload)
     *
     * Важно:
     * - Уникальность слага обеспечивается в рамках локали: unique(locale, slug)
     * - Публикация/доступность регулируются published_at + available_from/available_until
     */
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $t) {
            /* ===================== PK ===================== */

            $t->id(); // ID тарифного плана

            /* ===================== Управление / ограничения ===================== */

            $t->unsignedInteger('sort')
                ->default(0); // Порядок сортировки в админке/на витрине

            $t->boolean('activity')
                ->default(false); // Активность: доступен ли тариф для покупки (true/false)

            /* ===================== Витрина / локаль ===================== */

            $t->string('locale', 10)
                ->default('ru'); // Локаль плана (ru/kk/en и т.д., 2 символа)

            $t->string('title'); // Название тарифа (на витрину)

            $t->string('slug'); // Слаг тарифа (уникален в пределах locale)

            $t->string('subtitle')
                ->nullable(); // Подзаголовок/оффер (маркетинговая строка)

            $t->string('short', 255)
                ->nullable(); // Краткое описание (до 255 символов, для UI/превью)

            $t->text('description')
                ->nullable(); // Полное описание тарифа (на витрину)

            /* ===================== SEO ===================== */

            $t->string('meta_title', 160)
                ->nullable(); // SEO Title (до 160 символов)

            $t->string('meta_keywords', 255)
                ->nullable(); // SEO Keywords (до 255 символов)

            $t->string('meta_desc', 255)
                ->nullable(); // SEO Description (до 255 символов)

            /* ===================== Публикация / окно доступности ===================== */

            $t->timestamp('published_at')
                ->nullable(); // Дата публикации (null = не опубликован)

            $t->timestamp('available_from')
                ->nullable(); // Доступен к покупке с (null = без ограничения снизу)

            $t->timestamp('available_until')
                ->nullable(); // Доступен к покупке до (null = без ограничения сверху)

            /* ===================== Биллинг ===================== */

            $t->enum('billing_period', ['day', 'week', 'month', 'year'])
                ->default('month'); // Единица периода биллинга (day/week/month/year)

            $t->unsignedSmallInteger('interval')
                ->default(1); // Интервал периода (1 месяц, 12 месяцев и т.д.)

            $t->foreignId('currency_id')
                ->constrained('currencies')
                ->cascadeOnUpdate()
                ->restrictOnDelete(); // Валюта тарифа (FK -> currencies.id)

            $t->decimal('price', 18, 2)
                ->default(0); // Цена за период (18,2 — как у прайсов курсов/бандлов)

            $t->unsignedSmallInteger('trial_days')
                ->default(0); // Пробный период в днях (0 = без пробного периода)

            $t->boolean('auto_renew')
                ->default(true); // Автопродление (если поддерживается провайдером / логикой платформы)

            /* ===================== Провайдер оплаты / источник покупки ===================== */

            $t->string('provider', 70)
                ->nullable(); // Провайдер оплаты: stripe/paypal/paddle/boosty/patreon/kaspi/bank_transfer/manual/other

            $t->string('provider_ref')
                ->nullable(); // Идентификатор у провайдера (plan_id/price_id/checkout_url/код/ссылка и т.п.)

            $t->json('provider_payload')
                ->nullable(); // Провайдер-специфичные данные (merchant_id, реквизиты, ссылки, payload и т.д.)

            $t->json('config')
                ->nullable(); // Конфиг тарифа (JSON): features/limits/любой UI-конфиг/метаданные

            /* ===================== Service columns ===================== */

            $t->timestamps(); // created_at / updated_at
            $t->softDeletes(); // deleted_at (soft delete)

            /* ===================== Индексы / уникальности ===================== */

            $t->unique(['locale', 'slug'], 'uq_subscription_plans_locale_slug'); // Уникальность слага в рамках локали

            $t->index(['activity', 'sort'], 'idx_plan_active_sort'); // Частый фильтр: активные + сортировка

            $t->index(['published_at', 'available_from', 'available_until'], 'idx_plan_publish_window'); // Витрина: окно доступности

            $t->index(['billing_period', 'interval'], 'idx_plan_period_interval'); // Выборка по периоду/интервалу

            $t->index(['provider', 'provider_ref'], 'idx_plan_provider_ref'); // Быстрые выборки по провайдеру/референсу

            $t->index(['currency_id'], 'idx_plan_currency'); // Список тарифов по валюте
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
