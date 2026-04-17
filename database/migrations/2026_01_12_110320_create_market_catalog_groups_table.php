<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_catalog_groups', function (Blueprint $table) {
            $table->id()->comment('ID глобальной группы каталога');

            $table->boolean('activity')->default(true)->comment('Активность группы (глобально)');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка групп (глобально)');

            /**
             * code — системный идентификатор (top_100/hits_week)
             * slug — человекочитаемый URL-ключ (если понадобится public route)
             */
            $table->string('code', 64)->nullable()->comment('Системный код группы (top_100/hits_week)');
            $table->string('slug', 191)->nullable()->comment('Slug группы (если используешь на публичке)');

            $table->string('title', 255)->comment('Название группы');
            $table->string('subtitle', 255)->nullable()->comment('Подзаголовок');
            $table->text('description')->nullable()->comment('Описание/правила отображения');

            $table->string('type', 32)->default('manual')->comment('Тип группы: manual/rule');

            $table->json('rules_json')->nullable()->comment('Правила построения группы (для type=rule)');
            $table->json('ui_meta')->nullable()->comment('UI-мета: иконки/баннеры/цвета/настройки блока');

            $table->foreignId('created_by_user_id')
                ->nullable()
                ->comment('Кто создал (users.id), опционально')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // UNIQUE / INDEXES
            $table->unique(['code'], 'uq_mcg_code');
            $table->unique(['slug'], 'uq_mcg_slug');

            $table->index(['activity', 'sort'], 'ix_mcg_list');
            $table->index(['type', 'activity'], 'ix_mcg_type_active');

            $table->comment('Маркет: глобальные группы каталога (подборки товаров для всех витрин).');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_catalog_groups');
    }
};
