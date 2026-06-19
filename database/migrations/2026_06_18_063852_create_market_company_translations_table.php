<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_company_translations', function (Blueprint $table) {
            $table->id()->comment('PK');

            $table->unsignedBigInteger('market_company_id')
                ->comment('Компания (market_companies.id)');

            $table->foreign('market_company_id', 'market_company_translations_company_id_fk')
                ->references('id')
                ->on('market_companies')
                ->cascadeOnDelete();

            $table->string('locale', 10)
                ->index('market_company_translations_locale_idx')
                ->comment('Локаль перевода (например: ru, kk, en)');

            $table->string('title', 255)
                ->comment('Название компании');

            $table->string('subtitle', 255)
                ->nullable()
                ->comment('Подзаголовок, слоган или девиз компании');

            $table->string('short', 255)
                ->nullable()
                ->comment('Краткое описание компании');

            $table->text('description')
                ->nullable()
                ->comment('Полное описание компании');

            $table->string('meta_title', 255)
                ->nullable()
                ->comment('SEO: meta title');

            $table->string('meta_keywords', 255)
                ->nullable()
                ->comment('SEO: meta keywords');

            $table->text('meta_desc')
                ->nullable()
                ->comment('SEO: meta description');

            $table->timestamps();

            $table->unique(
                ['market_company_id', 'locale'],
                'market_company_translations_company_locale_unique'
            );

            $table->index(
                ['locale', 'title'],
                'market_company_translations_locale_title_idx'
            );

            $table->comment('Маркетплейс: переводы компаний по локалям.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_company_translations');
    }
};
