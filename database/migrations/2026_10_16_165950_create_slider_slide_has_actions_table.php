
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slider_slide_has_actions', function (Blueprint $table) {

            // Основные данные связи.
            $table->id(); // Уникальный идентификатор связи.

            $table->foreignId('slider_slide_id')
                ->constrained('slider_slides')
                ->cascadeOnDelete(); // Слайд, в котором используется кнопка.

            $table->foreignId('slider_slide_action_id')
                ->constrained('slider_slide_actions')
                ->cascadeOnDelete(); // Подключённая кнопка.

            // Управление отображением внутри слайда.
            $table->unsignedInteger('sort')
                ->default(0); // Порядок кнопки внутри конкретного слайда.

            $table->boolean('activity')
                ->default(true); // Локальная активность кнопки в этом слайде.

            $table->boolean('is_primary')
                ->default(false); // Основная кнопка в данном слайде.

            // Системные поля.
            $table->timestamps(); // Даты создания и изменения связи.

            // Уникальность связи.
            $table->unique(
                ['slider_slide_id', 'slider_slide_action_id'],
                'slider_slide_has_actions_unique'
            ); // Одна кнопка подключается к одному слайду только один раз.

            // Индексы.
            $table->index(
                ['slider_slide_id', 'activity', 'sort'],
                'slider_slide_has_actions_display_idx'
            ); // Выборка активных кнопок в нужном порядке.

            $table->index(
                ['slider_slide_action_id'],
                'slider_slide_has_actions_action_idx'
            ); // Поиск слайдов, использующих конкретную кнопку.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slider_slide_has_actions');
    }
};
