<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * market_poll_results_cache
 * Денормализованный кеш результатов.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_poll_results_cache', function (Blueprint $table) {
            $table->id()->comment('ID записи кеша результата');

            /* TENANT */
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mprc_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('survey_id')
                ->comment('Опрос (market_poll_surveys.id)');

            $table->unsignedBigInteger('question_id')
                ->nullable()
                ->comment('Вопрос (market_poll_questions.id), если кеш по вопросу');

            /* survey tenant-safe */
            $table->foreign(['company_id', 'storefront_id', 'survey_id'], 'fk_mprc_tenant_survey')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_poll_surveys')
                ->cascadeOnDelete();

            /**
             * ⚠️ ВАЖНО:
             * Нельзя ON DELETE SET NULL для композитного FK (company_id, storefront_id, question_id),
             * потому что company_id/storefront_id NOT NULL.
             * Поэтому: RESTRICT (или NO ACTION).
             */
            $table->foreign(['company_id', 'storefront_id', 'question_id'], 'fk_mprc_tenant_question')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_poll_questions')
                ->restrictOnDelete();

            /**
             * ✅ Жёстко: question принадлежит этому survey (если question_id указан)
             * FK: (company_id, storefront_id, survey_id, question_id) -> questions(company_id, storefront_id, survey_id, id)
             * И здесь ТОЖЕ нельзя SET NULL по той же причине → RESTRICT.
             */
            $table->foreign(['company_id', 'storefront_id', 'survey_id', 'question_id'], 'fk_mprc_tenant_survey_question')
                ->references(['company_id', 'storefront_id', 'survey_id', 'id'])
                ->on('market_poll_questions')
                ->restrictOnDelete();

            $table->unsignedSmallInteger('version')->default(1)->comment('Версия структуры JSON результата');
            $table->json('result_json')->comment('Результаты (JSON): counts/percents/total');

            $table->unsignedBigInteger('total_votes')->default(0)->comment('Общее количество голосов');

            $table->timestamp('calculated_at')->nullable()->comment('Когда пересчитано');
            $table->timestamps();

            // Один кеш на (survey, question|null) в рамках tenant
            $table->unique(['company_id', 'storefront_id', 'survey_id', 'question_id'], 'uq_mprc_tenant_survey_question');

            $table->index(['storefront_id', 'survey_id'], 'ix_mprc_storefront_survey');
            $table->index(['survey_id', 'calculated_at'], 'ix_mprc_survey_calc_time');
            $table->index(['company_id', 'storefront_id'], 'ix_mprc_tenant');

            $table->comment('Маркет: кеш результатов опросов (быстрый UI), tenant-safe + строгая связь survey->question.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_poll_results_cache');
    }
};
