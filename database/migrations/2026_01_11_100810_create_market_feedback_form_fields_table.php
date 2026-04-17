<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * market_feedback_form_fields
 * Поля формы (конструктор): тип, обязательность, порядок, валидация, варианты.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_feedback_form_fields', function (Blueprint $table) {
            $table->id()->comment('ID поля формы');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mfff_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * FORM (tenant-safe)
             * Требование: в market_feedback_forms есть unique(company_id, storefront_id, id)
             * ========================================================= */

            $table->unsignedBigInteger('form_id')
                ->comment('Форма (market_feedback_forms.id)');

            $table->foreign(['company_id', 'storefront_id', 'form_id'], 'fk_mfff_form_tenant')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_feedback_forms')
                ->cascadeOnDelete();

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность поля');
            $table->unsignedInteger('sort')->default(0)->comment('Порядок в форме');

            /* =========================================================
             * FIELD DEFINITION
             * ========================================================= */

            $table->string('key', 64)->comment('Ключ поля (например: name/email/phone/message/custom_1)');
            $table->string('label', 255)->comment('Название поля (лейбл)');
            $table->string('placeholder', 255)->nullable()->comment('Подсказка');

            $table->string('type', 32)->comment('Тип: text/textarea/email/phone/number/select/checkbox/radio/date/file/...');

            $table->boolean('required')->default(false)->comment('Обязательное поле');
            $table->boolean('is_system')->default(false)->comment('Системное поле (например email/phone)');

            // Валидация/формат
            $table->unsignedSmallInteger('min_len')->nullable()->comment('Мин. длина');
            $table->unsignedSmallInteger('max_len')->nullable()->comment('Макс. длина');
            $table->string('regex', 191)->nullable()->comment('Регулярка (опционально)');
            $table->json('rules')->nullable()->comment('Правила (Laravel-style, кастомный JSON)');

            // Варианты для select/radio
            $table->json('options')->nullable()->comment('Опции для select/radio (JSON)');

            // UI
            $table->unsignedSmallInteger('width')->default(12)->comment('Ширина поля в сетке (1..12)');
            $table->boolean('is_hidden')->default(false)->comment('Скрытое поле (например honeypot)');

            $table->string('note', 255)->nullable()->comment('Заметка');
            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // Уникальность key внутри формы
            $table->unique(['form_id', 'key'], 'uq_mfff_form_key');

            // критично для tenant-safe FK на это поле (если появятся submissions_field_values и т.п.)
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mfff_tenant_id');

            $table->index(['company_id', 'storefront_id'], 'ix_mfff_tenant');
            $table->index(['company_id', 'storefront_id', 'form_id', 'activity', 'sort'], 'ix_mfff_form_list');
            $table->index(['storefront_id', 'key'], 'ix_mfff_storefront_key');

            $table->comment('Маркет: поля форм обратной связи (конструктор), tenant-safe.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_feedback_form_fields');
    }
};
