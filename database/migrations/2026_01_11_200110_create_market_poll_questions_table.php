<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * market_poll_questions
 * Вопросы опроса.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_poll_questions', function (Blueprint $table) {
            $table->id()->comment('ID вопроса опроса');

            $table->foreignId('survey_id')
                ->comment('Опрос (market_poll_surveys.id)')
                ->constrained('market_poll_surveys')
                ->cascadeOnDelete();

            /* TENANT */
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mpq_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /**
             * Защита: вопрос должен быть внутри витрины опроса
             * FK: (storefront_id, survey_id) -> market_poll_surveys(storefront_id, id)
             * Требование: в surveys есть UNIQUE(storefront_id, id) - у тебя он есть (uq_mps_storefront_id)
             */
            $table->foreign(['storefront_id', 'survey_id'], 'fk_mpq_storefront_survey')
                ->references(['storefront_id', 'id'])
                ->on('market_poll_surveys')
                ->cascadeOnDelete();

            /* FIELDS */
            $table->boolean('activity')->default(true)->comment('Активность вопроса');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка вопросов');

            $table->string('type', 32)->default('single')
                ->comment('Тип вопроса: single (1 вариант) / multi (несколько вариантов)');

            $table->string('title', 255)->comment('Текст вопроса');
            $table->text('description')->nullable()->comment('Описание/подсказка');

            $table->unsignedSmallInteger('min_choices')->nullable()->comment('Мин. выборов (для multi)');
            $table->unsignedSmallInteger('max_choices')->nullable()->comment('Макс. выборов (для multi)');

            $table->unsignedBigInteger('votes_count')->default(0)->comment('Кол-во ответов по вопросу (денормализация)');

            $table->json('settings')->nullable()->comment('Настройки вопроса (JSON)');
            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // tenant-safe ключ для ссылок вида (company_id, storefront_id, question_id) -> (..., id)
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mpq_tenant_id');

            // для FK по витрине: (storefront_id, survey_id, question_id) -> (..., id)
            $table->unique(['storefront_id', 'survey_id', 'id'], 'uq_mpq_storefront_survey_id');

            /**
             * ✅ ВАЖНО (исправление под твою ошибку 1822):
             * Нужен уникальный/индекс для FK из options:
             * (company_id, storefront_id, survey_id, question_id) -> questions(company_id, storefront_id, survey_id, id)
             */
            $table->unique(['company_id', 'storefront_id', 'survey_id', 'id'], 'uq_mpq_tenant_survey_id');

            // Индексы
            $table->index(['survey_id', 'activity', 'sort'], 'ix_mpq_survey_list');
            $table->index(['storefront_id', 'survey_id', 'activity'], 'ix_mpq_tenant_survey_active');

            $table->index(['storefront_id', 'activity', 'sort'], 'ix_mpq_storefront_list');
            $table->index(['storefront_id', 'survey_id', 'sort'], 'ix_mpq_storefront_survey_sort');

            $table->comment('Маркет: вопросы опросов (конструктор), tenant-safe.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_poll_questions');
    }
};
