<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_job_application_attachments', function (Blueprint $table) {
            $table->id()->comment('ID вложения отклика (файл в Spatie Media Library)');

            // TENANT
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mjaa_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            // APPLICATION tenant-safe
            $table->unsignedBigInteger('job_application_id')
                ->comment('Отклик (market_job_applications.id)');

            // Требует UNIQUE(company_id, storefront_id, id) в applications
            $table->foreign(['company_id', 'storefront_id', 'job_application_id'], 'fk_mjaa_tenant_application')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_job_applications')
                ->cascadeOnDelete();

            // SPATIE MEDIA
            $table->unsignedBigInteger('media_id')->comment('Файл в media (Spatie)');

            $table->foreign('media_id', 'fk_mjaa_media')
                ->references('id')
                ->on('media')
                ->cascadeOnDelete();

            // META
            $table->boolean('activity')->default(true)->comment('Активность вложения');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка вложений');

            $table->string('type', 32)->default('resume')->comment('resume/portfolio/cover_letter/other');
            $table->string('title', 255)->nullable()->comment('Подпись/название файла');
            $table->string('note', 255)->nullable()->comment('Комментарий');
            $table->timestamps();

            // Один media не должен повторяться в одном отклике
            $table->unique(['job_application_id', 'media_id'], 'uq_mjaa_application_media');

            $table->index(['storefront_id', 'job_application_id', 'activity', 'sort'], 'ix_mjaa_application_list');
            $table->index(['media_id'], 'ix_mjaa_media');
            $table->index(['company_id', 'storefront_id'], 'ix_mjaa_tenant');

            $table->comment('Маркет: вложения отклика + ссылка на media (Spatie), tenant-safe.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_job_application_attachments');
    }
};
