<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * market_poll_votes
 * Голосование пользователя/гостя в рамках опроса (контейнер).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_poll_votes', function (Blueprint $table) {
            $table->id()->comment('ID голосования (сессия)');

            /* TENANT */
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            // ✅ ВАЖНО: просто другое имя constraint, чтобы не конфликтовало
            $table->foreign(['company_id', 'storefront_id'], 'fk_market_poll_votes_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* SURVEY (tenant-safe) */
            $table->unsignedBigInteger('survey_id')
                ->comment('Опрос (market_poll_surveys.id)');

            $table->foreign(['company_id', 'storefront_id', 'survey_id'], 'fk_mpv_tenant_survey')
                ->references(['company_id', 'storefront_id', 'id'])
                ->on('market_poll_surveys')
                ->cascadeOnDelete();

            /* USER */
            $table->foreignId('user_id')
                ->nullable()
                ->comment('Пользователь (users.id), если авторизован')
                ->constrained('users')
                ->nullOnDelete();

            /* ANTI-FRAUD */
            $table->string('ip', 64)->nullable()->comment('IP голосования');
            $table->string('user_agent', 255)->nullable()->comment('User-Agent');
            $table->string('fingerprint', 64)->nullable()->comment('Fingerprint (хеш)');

            $table->string('status', 32)->default('ok')->comment('Статус: ok/blocked/invalid');

            $table->timestamp('voted_at')->nullable()->comment('Когда проголосовал');
            $table->timestamps();

            /* UNIQUE / INDEXES */

            // tenant-safe FK на votes (если появятся vote_items)
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mpv_tenant_id');
            $table->unique(['storefront_id', 'survey_id', 'id'], 'uq_mpv_storefront_survey_id');

            // 1 голос на опрос для user (если нужно — оставляем)
            $table->unique(['survey_id', 'user_id'], 'uq_mpv_survey_user');

            $table->index(['survey_id', 'fingerprint'], 'ix_mpv_survey_fingerprint');
            $table->index(['survey_id', 'ip', 'created_at'], 'ix_mpv_survey_ip_time');
            $table->index(['storefront_id', 'survey_id', 'created_at'], 'ix_mpv_storefront_survey_time');
            $table->index(['company_id', 'storefront_id'], 'ix_mpv_tenant');

            $table->comment('Маркет: голосования в опросах (сессии), tenant-safe + антинакрутка.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_poll_votes');
    }
};
