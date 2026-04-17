<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // SEO-метаданные для любых сущностей (полиморфная связь)
    public function up(): void
    {
        Schema::create('seo_metas', function (Blueprint $t) {
            $t->id();

            // Полиморфная привязка: к какой сущности относятся метаданные
            $t->nullableMorphs('seoable'); // создаст seoable_type (varchar) + seoable_id (bigint unsigned)

            // Базовые поля SEO
            $t->string('title')->nullable();           // <title>
            $t->text('description')->nullable();       // <meta name="description">
            $t->string('keywords')->nullable();        // <meta name="keywords"> (опционально)

            // Индексация/каноникал
            $t->boolean('robots_noindex')->default(false);   // noindex
            $t->boolean('robots_nofollow')->default(false);  // nofollow
            $t->string('canonical_url')->nullable();         // <link rel="canonical">

            // OpenGraph
            $t->string('og_title')->nullable();
            $t->text('og_description')->nullable();
            $t->string('og_image_url')->nullable();
            $t->string('og_type', 50)->nullable()->default('article');

            // Twitter
            $t->string('twitter_card', 20)->nullable()->default('summary_large_image');

            // Локаль и флаги
            $t->string('locale', 10)->nullable();      // ru, en, ...
            $t->boolean('activity')->default(true);   // использовать ли эти метаданные

            // Произвольные данные/структурированная разметка
            $t->json('json_ld')->nullable();           // JSON-LD блок(и)
            $t->json('meta')->nullable();              // любые доп. мета

            $t->timestamps();
            $t->softDeletes();

            // Частые индексы
            $t->index(['seoable_type', 'seoable_id', 'locale'], 'idx_seoable_locale');

            // Если хотите уникальность на сущность+локаль — раскомментируйте:
            // $t->unique(['seoable_type', 'seoable_id', 'locale'], 'uniq_seoable_locale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_metas');
    }
};
