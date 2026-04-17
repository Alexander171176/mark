<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * market_feedback_submission_fields
 * Значения полей отправки, нормализовано по field_id.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_feedback_submission_fields', function (Blueprint $table) {
            $table->id()->comment('ID значения поля отправки');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mfsf_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * SUBMISSION (tenant-safe)
             * Требование: market_feedback_submissions имеет unique(company_id, storefront_id, id)
             * ========================================================= */
            $table->unsignedBigInteger('submission_id')
                ->comment('Отправка формы (market_feedback_submissions.id)');

            $table->foreign(['company_id', 'storefront_id', 'submission_id'], 'fk_mfsf_submission_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_feedback_submissions')
                ->cascadeOnDelete();

            /* =========================================================
             * FIELD (tenant-safe)
             * Требование: market_feedback_form_fields имеет unique(company_id, storefront_id, id)
             * ========================================================= */
            $table->unsignedBigInteger('field_id')
                ->comment('Поле формы (market_feedback_form_fields.id)');

            $table->foreign(['company_id', 'storefront_id', 'field_id'], 'fk_mfsf_field_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_feedback_form_fields')
                ->cascadeOnDelete();

            /* =========================================================
             * VALUE
             * ========================================================= */
            $table->string('value', 2000)->nullable()->comment('Значение поля (строкой)');
            $table->json('value_json')->nullable()->comment('Значение в JSON (для массивов/мультиселектов)');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // Одно поле = одно значение в конкретной отправке (в рамках tenant)
            $table->unique(['submission_id', 'field_id'], 'uq_mfsf_submission_field');

            // критично для будущих tenant-safe FK (если появятся)
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mfsf_tenant_id');

            $table->index(['submission_id', 'created_at'], 'ix_mfsf_submission_time');
            $table->index(['field_id'], 'ix_mfsf_field');
            $table->index(['storefront_id', 'field_id'], 'ix_mfsf_storefront_field');

            $table->comment('Маркет: значения полей отправки формы (нормализовано), tenant-safe.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_feedback_submission_fields');
    }
};
