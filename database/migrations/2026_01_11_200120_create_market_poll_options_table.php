<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * market_poll_options
 * Варианты ответов на вопрос.
 * Tenant-safe + гарантия, что option относится к question этого survey.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_poll_options', function (Blueprint $table) {
            $table->id()->comment('ID варианта ответа');

            // tenant-safe
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mpo_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // Денормализация для быстрых выборок + жёсткая проверка связей
            $table->unsignedBigInteger('survey_id')
                ->comment('Опрос (market_poll_surveys.id)');

            $table->unsignedBigInteger('question_id')
                ->comment('Вопрос (market_poll_questions.id)');

            // ✅ Опрос должен быть в рамках tenant
            $table->foreign(['company_id', 'storefront_id', 'survey_id'], 'fk_mpo_tenant_survey')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_poll_surveys')
                ->cascadeOnDelete();

            // ✅ Вопрос должен быть в рамках tenant
            $table->foreign(['company_id', 'storefront_id', 'question_id'], 'fk_mpo_tenant_question')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_poll_questions')
                ->cascadeOnDelete();

            // ✅ Критично: гарантируем, что question действительно принадлежит survey
            // Требует UNIQUE в questions: (company_id, storefront_id, survey_id, id)
            $table->foreign(['company_id', 'storefront_id', 'survey_id', 'question_id'], 'fk_mpo_tenant_survey_question')
                ->references(['company_id', 'storefront_id', 'survey_id', 'id'])
                ->on('market_poll_questions')
                ->cascadeOnDelete();

            $table->boolean('activity')->default(true)->comment('Активность варианта');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка вариантов');

            $table->string('title', 255)->comment('Текст варианта ответа');
            $table->string('code', 64)->nullable()->comment('Код варианта (опционально)');

            $table->boolean('is_other')->default(false)->comment('Вариант “Другое”');
            $table->unsignedBigInteger('votes_count')->default(0)->comment('Сколько раз выбрали (денормализация)');

            $table->json('meta')->nullable()->comment('Доп. данные (JSON)');
            $table->timestamps();

            // Для будущих tenant-FK (если понадобятся ссылки на option tenant-safe)
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mpo_tenant_id');
            $table->unique(['storefront_id', 'survey_id', 'id'], 'uq_mpo_storefront_survey_id');

            // Уникальный code внутри вопроса (если code задан)
            $table->unique(['question_id', 'code'], 'uq_mpo_question_code');

            $table->index(['question_id', 'activity', 'sort'], 'ix_mpo_question_list');
            $table->index(['survey_id', 'activity'], 'ix_mpo_survey_active');
            $table->index(['storefront_id', 'survey_id'], 'ix_mpo_storefront_survey');
            $table->index(['company_id', 'storefront_id'], 'ix_mpo_tenant');

            $table->comment('Маркет: варианты ответов опросов, tenant-safe + защита связки survey->question.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_poll_options');
    }
};
