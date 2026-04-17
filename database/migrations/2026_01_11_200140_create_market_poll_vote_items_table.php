<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * market_poll_vote_items
 * Ответы по вопросам внутри одного голосования (vote).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_poll_vote_items', function (Blueprint $table) {
            $table->id()->comment('ID ответа на вопрос (в рамках голосования)');

            /* TENANT */
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_market_poll_vote_items_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('vote_id')->comment('Голосование (market_poll_votes.id)');
            $table->unsignedBigInteger('survey_id')->comment('Опрос (market_poll_surveys.id)');
            $table->unsignedBigInteger('question_id')->comment('Вопрос (market_poll_questions.id)');

            /* vote tenant-safe */
            $table->foreign(['company_id', 'storefront_id', 'vote_id'], 'fk_mpvi_tenant_vote')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_poll_votes')
                ->cascadeOnDelete();

            /* survey tenant-safe */
            $table->foreign(['company_id', 'storefront_id', 'survey_id'], 'fk_mpvi_tenant_survey')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_poll_surveys')
                ->cascadeOnDelete();

            /* question tenant-safe */
            $table->foreign(['company_id', 'storefront_id', 'question_id'], 'fk_mpvi_tenant_question')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_poll_questions')
                ->cascadeOnDelete();

            /* жёстко: question принадлежит survey */
            $table->foreign(['company_id', 'storefront_id', 'survey_id', 'question_id'], 'fk_mpvi_tenant_survey_question')
                ->references(['company_id', 'storefront_id', 'survey_id', 'id'])
                ->on('market_poll_questions')
                ->cascadeOnDelete();

            /* жёстко: vote принадлежит survey */
            $table->foreign(['company_id', 'storefront_id', 'survey_id', 'vote_id'], 'fk_mpvi_tenant_survey_vote')
                ->references(['company_id', 'storefront_id', 'survey_id', 'id'])
                ->on('market_poll_votes')
                ->cascadeOnDelete();

            /* option (может быть null, если “Другое”) */
            $table->unsignedBigInteger('option_id')
                ->nullable()
                ->comment('Вариант ответа (market_poll_options.id), опционально');

            /**
             * ВАЖНО:
             * Нельзя SET NULL на композитном FK с company_id/storefront_id NOT NULL.
             * Поэтому RESTRICT: option нельзя удалить, если есть ответы.
             */
            $table->foreign(['company_id', 'storefront_id', 'option_id'], 'fk_mpvi_tenant_option')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_poll_options')
                ->restrictOnDelete();

            // “Другое”
            $table->string('other_text', 500)->nullable()->comment('Текст “Другое”');

            $table->timestamps();

            /* UNIQUE / INDEXES */

            // Один ответ на один вопрос в рамках одного vote
            $table->unique(['vote_id', 'question_id'], 'uq_mpvi_vote_question');

            $table->index(['vote_id', 'created_at'], 'ix_mpvi_vote_time');
            $table->index(['survey_id', 'question_id'], 'ix_mpvi_survey_question');
            $table->index(['question_id', 'option_id'], 'ix_mpvi_question_option');
            $table->index(['storefront_id', 'survey_id'], 'ix_mpvi_storefront_survey');
            $table->index(['company_id', 'storefront_id'], 'ix_mpvi_tenant');

            $table->comment('Маркет: ответы по вопросам в опросах (vote items), tenant-safe + строгие связи vote/survey/question/option.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_poll_vote_items');
    }
};
