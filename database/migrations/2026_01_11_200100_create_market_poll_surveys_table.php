<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * market_poll_surveys
 * Конструктор опросов для витрин.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_poll_surveys', function (Blueprint $table) {
            $table->id()->comment('ID опроса');

            /* =========================================================
             * TENANT / STOREFRONT
             * ========================================================= */

            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mps_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            /* =========================================================
             * FIELDS
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность опроса');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            $table->string('locale', 10)->nullable()->comment('Локаль (ru/kk/en), если нужно');
            $table->string('title', 255)->comment('Название опроса');
            $table->string('slug', 191)->comment('Slug опроса (уникален в рамках витрины)');
            $table->text('description')->nullable()->comment('Описание опроса');

            /* =========================================================
             * VOTING / RESULT SETTINGS
             * ========================================================= */

            $table->string('vote_mode', 32)->default('single')
                ->comment('Режим голосования: single (1 голос на опрос) / multi (разрешить переголосование)');

            $table->boolean('requires_auth')->default(false)->comment('Требовать авторизацию для голосования');

            $table->string('result_visibility', 32)->default('after_vote')
                ->comment('Показ результатов: hidden/after_vote/always/after_end');

            $table->boolean('show_percent')->default(true)->comment('Показывать проценты');
            $table->boolean('show_counts')->default(false)->comment('Показывать количество голосов');
            $table->boolean('show_total')->default(true)->comment('Показывать общий итог голосов');

            $table->timestamp('starts_at')->nullable()->comment('Начало опроса');
            $table->timestamp('ends_at')->nullable()->comment('Окончание опроса');

            // Денормализация (быстро показать “сколько проголосовало”)
            $table->unsignedBigInteger('votes_count')->default(0)->comment('Количество голосований (денормализация)');

            $table->json('settings')->nullable()->comment('Доп. настройки (JSON)');
            $table->string('note', 255)->nullable()->comment('Заметка админа');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // tenant-safe ключ для композитных ссылок из других таблиц
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mps_tenant_id');

            // slug уникален в рамках витрины (если locale влияет на slug — тогда делай ['storefront_id','locale','slug'])
            $table->unique(['storefront_id', 'slug'], 'uq_mps_storefront_slug');

            // для FK по витрине: (storefront_id, id)
            $table->unique(['storefront_id', 'id'], 'uq_mps_storefront_id');

            $table->index(['company_id', 'storefront_id'], 'ix_mps_tenant');
            $table->index(['storefront_id', 'activity', 'sort'], 'ix_mps_storefront_list');
            $table->index(['storefront_id', 'locale', 'activity', 'sort'], 'ix_mps_storefront_locale_list');

            $table->index(['storefront_id', 'starts_at', 'ends_at'], 'ix_mps_storefront_period');
            $table->index(['storefront_id', 'result_visibility', 'activity'], 'ix_mps_storefront_result_visibility');

            $table->comment('Маркет: опросы витрины (конструктор), tenant-safe.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_poll_surveys');
    }
};
