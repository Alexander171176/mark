<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * market_feedback_forms
 * Конструктор форм обратной связи для витрин (несколько форм на одну витрину).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_feedback_forms', function (Blueprint $table) {
            $table->id()->comment('ID формы обратной связи');

            // tenant-safe
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mff_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            $table->boolean('activity')->default(true)->comment('Активность формы');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            $table->string('locale', 10)->nullable()->comment('Локаль формы (ru/kk/en), если формы локализуемые');

            $table->string('title', 255)->comment('Название формы (для админки/витрины)');
            $table->string('slug', 191)->comment('Slug формы (внутри витрины уникален)');

            $table->string('type', 32)->default('contact')
                ->comment('Тип формы: contact/support/partner/wholesale/return/other');

            $table->text('description')->nullable()->comment('Описание формы (для витрины)');
            $table->string('success_title', 255)->nullable()->comment('Заголовок успешной отправки');
            $table->text('success_message')->nullable()->comment('Текст успешной отправки');

            $table->string('handler', 64)->default('default')
                ->comment('Обработчик: default/ticket/email/webhook (логика приложения)');

            $table->json('settings')->nullable()->comment('Настройки формы (получатели, webhook url, шаблоны, флаги)');

            $table->boolean('requires_auth')->default(false)->comment('Требовать авторизацию для отправки');
            $table->boolean('has_attachments')->default(false)->comment('Разрешить вложения (если подключишь media)');

            $table->string('note', 255)->nullable()->comment('Заметка админа');
            $table->timestamps();

            /**
             * UNIQUE / INDEXES
             */

            // если slug НЕ зависит от locale — оставь так:
            //$table->unique(['storefront_id', 'slug'], 'uq_mff_storefront_slug');

            // если slug зависит от locale (часто так удобнее) — используй вместо строки выше:
            $table->unique(['storefront_id', 'locale', 'slug'], 'uq_mff_storefront_locale_slug');

            // критично для tenant-safe FK (если будут ссылки form_id из других таблиц)
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mff_tenant_id');

            $table->index(['company_id', 'storefront_id'], 'ix_mff_tenant');
            $table->index(['storefront_id', 'activity', 'sort'], 'ix_mff_storefront_list');
            $table->index(['storefront_id', 'type', 'activity'], 'ix_mff_storefront_type_active');
            $table->index(['storefront_id', 'locale', 'activity'], 'ix_mff_storefront_locale_active');

            $table->comment('Маркет: формы обратной связи (конструктор), tenant-safe.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_feedback_forms');
    }
};
