
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slider_has_slides', function (Blueprint $table) {

            // Основные данные связи.
            $table->id(); // Уникальный идентификатор связи.

            $table->foreignId('slider_id')
                ->constrained('sliders')
                ->cascadeOnDelete(); // Слайдер, к которому подключён слайд.

            $table->foreignId('slider_slide_id')
                ->constrained('slider_slides')
                ->cascadeOnDelete(); // Подключённый слайд.

            // Управление отображением внутри слайдера.
            $table->unsignedInteger('sort')
                ->default(0); // Порядок слайда в конкретном слайдере (Drag & Drop).

            $table->boolean('activity')
                ->default(true); // Локальная активность слайда в этом слайдере.

            $table->boolean('is_main')
                ->default(false); // Использовать h1 вместо h2 в данном размещении.

            // Системные поля.
            $table->timestamps(); // Даты создания и изменения связи.

            // Уникальность связи.
            $table->unique(
                ['slider_id', 'slider_slide_id'],
                'slider_has_slides_unique'
            ); // Один слайд подключается к одному слайдеру только один раз.

            // Индексы.
            $table->index(
                ['slider_id', 'activity', 'sort'],
                'slider_has_slides_display_idx'
            ); // Выборка активных слайдов в нужном порядке.

            $table->index(
                ['slider_slide_id'],
                'slider_has_slides_slide_idx'
            ); // Поиск слайдеров, использующих конкретный слайд.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slider_has_slides');
    }
};
