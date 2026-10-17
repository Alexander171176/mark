<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slider_slide_actions', function (Blueprint $table) {
            // Основные данные самостоятельного действия.
            $table->id(); // Уникальный идентификатор действия.
            $table->boolean('activity')->default(true); // Глобальная активность кнопки во всех слайдах.

            // Тип и назначение действия.
            $table->string('action_type', 30)->default('url'); // url, route, form, anchor.
            $table->string('action_value', 2048)->nullable(); // URL, маршрут, код формы или якорь.
            $table->json('route_params')->nullable(); // Параметры именованного маршрута Laravel.
            $table->string('target', 20)->default('_self'); // _self или _blank.

            // Внешний вид кнопки.
            $table->string('style', 50)->nullable(); // primary, secondary, outline и др.
            $table->string('icon', 100)->nullable(); // Название иконки.
            $table->string('icon_position', 20)->default('left'); // left или right.
            $table->string('css_class', 255)->nullable(); // Дополнительный CSS-класс.
            $table->json('settings')->nullable(); // Расширенные настройки поведения и оформления.

            // Модерация действия.
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('slider_slide_actions_moderation_status_idx')
                ->comment('Модерация: 0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('slider_slide_actions_moderated_by_idx')
                ->comment('Модератор (users.id)');

            $table->foreign('moderated_by', 'slider_slide_actions_moderated_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('slider_slide_actions_moderated_at_idx')
                ->comment('Дата модерации');

            $table->text('moderation_note')
                ->nullable()
                ->comment('Комментарий модератора');

            $table->unsignedInteger('sort')->default(0); // Глобальный порядок сортировки в админке.

            // Системные поля.
            $table->timestamps(); // Даты создания и последнего изменения.

            // Индексы.
            $table->index(['activity', 'moderation_status'], 'slider_slide_actions_public_idx');
            $table->index(['sort', 'id'], 'slider_slide_actions_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slider_slide_actions');
    }
};
