<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_jobs', function (Blueprint $table) {
            $table->id()->comment('ID вакансии');

            /* TENANT / STOREFRONT */
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mj_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* CATEGORY (tenant-safe) */
            $table->unsignedBigInteger('job_category_id')
                ->nullable()
                ->comment('Категория вакансии (market_job_categories.id)');

            /**
             * ❗ SET NULL для композитного FK использовать нельзя,
             * потому что company_id/storefront_id NOT NULL.
             * Поэтому ставим RESTRICT (безопасно) или CASCADE (если хочешь удалять jobs вместе с категорией).
             */
            $table->foreign(['company_id', 'storefront_id', 'job_category_id'], 'fk_mj_category_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_job_categories')
                ->restrictOnDelete();

            /* FLAGS / SORT / PUBLICATION */
            $table->boolean('activity')->default(true)->comment('Активность вакансии');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            $table->string('status', 32)->default('draft')
                ->comment('Статус: draft/published/archived/closed');

            $table->timestamp('published_at')->nullable()->comment('Дата публикации');
            $table->timestamp('closed_at')->nullable()->comment('Дата закрытия');

            /* CONTENT / LOCALE */
            $table->string('locale', 10)->comment('Локаль (ru/kk/en/...)');

            $table->string('title', 255)->comment('Название вакансии');
            $table->string('slug', 191)->comment('Slug вакансии');

            $table->string('short', 255)->nullable()->comment('Краткое описание');
            $table->longText('description')->nullable()->comment('Полное описание/обязанности/требования');
            $table->longText('conditions')->nullable()->comment('Условия/бонусы/соцпакет');
            $table->longText('requirements')->nullable()->comment('Требования');

            /* FILTERS */
            $table->string('employment_type', 32)->nullable()->comment('full_time/part_time/contract/intern');
            $table->string('work_format', 32)->nullable()->comment('office/remote/hybrid');
            $table->string('experience_level', 32)->nullable()->comment('no_exp/1_3/3_6/6_plus');
            $table->string('schedule', 64)->nullable()->comment('5_2/2_2/flexible/...');

            /* LOCATION */
            $table->string('country_code', 2)->nullable()->comment('Страна ISO2');
            $table->string('region', 128)->nullable()->comment('Регион/область');
            $table->string('city', 128)->nullable()->comment('Город');
            $table->string('address', 255)->nullable()->comment('Адрес');
            $table->decimal('lat', 10, 7)->nullable()->comment('Широта');
            $table->decimal('lng', 10, 7)->nullable()->comment('Долгота');

            /* SALARY */
            $table->decimal('salary_from', 18, 2)->nullable()->comment('ЗП от');
            $table->decimal('salary_to', 18, 2)->nullable()->comment('ЗП до');

            $table->foreignId('currency_id')
                ->nullable()
                ->comment('Валюта зарплаты (currencies.id)')
                ->constrained('currencies')
                ->nullOnDelete();

            $table->boolean('salary_gross')->default(false)->comment('ЗП gross');

            /* CONTACTS */
            $table->string('contact_name', 255)->nullable()->comment('Контактное лицо');
            $table->string('contact_phone', 50)->nullable()->comment('Телефон контакта');
            $table->string('contact_email', 255)->nullable()->comment('Email контакта');
            $table->string('contact_messenger_type', 32)->nullable()->comment('whatsapp/telegram/...');
            $table->string('contact_messenger_contact', 255)->nullable()->comment('номер/username/ссылка');

            /* SEO */
            $table->string('meta_title', 255)->nullable()->comment('SEO title');
            $table->string('meta_keywords', 255)->nullable()->comment('SEO keywords');
            $table->text('meta_desc')->nullable()->comment('SEO description');
            $table->string('canonical_url', 2048)->nullable()->comment('Canonical URL');
            $table->boolean('noindex')->default(false)->comment('Запрет индексации');

            /* METRICS / SERVICE */
            $table->unsignedBigInteger('views')->default(0)->comment('Просмотры');
            $table->string('note', 255)->nullable()->comment('Заметка админа');

            $table->timestamps();

            /* UNIQUE / INDEXES */
            $table->unique(['storefront_id', 'locale', 'slug'], 'uq_mj_storefront_locale_slug');

            // ✅ ключи для tenant-safe ссылок (applications/messages/media)
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mj_tenant_id');
            $table->unique(['storefront_id', 'id'], 'uq_mj_storefront_id');

            $table->index(['company_id', 'storefront_id', 'locale'], 'ix_mj_tenant_locale');
            $table->index(['storefront_id', 'locale', 'activity', 'sort'], 'ix_mj_list');
            $table->index(['storefront_id', 'status', 'published_at'], 'ix_mj_status_published');
            $table->index(['storefront_id', 'job_category_id', 'activity'], 'ix_mj_category_active');

            $table->index(['storefront_id', 'city', 'activity'], 'ix_mj_city_active');
            $table->index(['employment_type', 'activity'], 'ix_mj_employment_active');
            $table->index(['work_format', 'activity'], 'ix_mj_work_format_active');
            $table->index(['experience_level', 'activity'], 'ix_mj_experience_active');

            $table->index('views', 'ix_mj_views');
            $table->index('locale', 'ix_mj_locale');

            $table->comment('Маркет: вакансии витрины, tenant-safe, локаль + SEO + фильтры.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_jobs');
    }
};
