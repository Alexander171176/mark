
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slider_slide_has_advantages', function (Blueprint $table) {

            // Основные данные связи.
            $table->id(); // Уникальный идентификатор связи.

            $table->foreignId('slider_slide_id')
                ->constrained('slider_slides')
                ->cascadeOnDelete(); // Слайд, в котором используется преимущество.

            $table->foreignId('slider_slide_advantage_id')
                ->constrained(
                    'slider_slide_advantages',
                    indexName: 'slider_has_advantages_fk'
                )
                ->cascadeOnDelete(); // Подключённое преимущество.

            // Управление отображением внутри слайда.
            $table->unsignedInteger('sort')
                ->default(0); // Порядок преимущества в конкретном слайде.

            $table->boolean('activity')
                ->default(true); // Локальная активность преимущества в этом слайде.

            // Системные поля.
            $table->timestamps(); // Даты создания и изменения связи.

            // Уникальность связи.
            $table->unique(
                ['slider_slide_id', 'slider_slide_advantage_id'],
                'slider_has_advantages_unique'
            ); // Одно преимущество подключается к одному слайду только один раз.

            // Индексы.
            $table->index(
                ['slider_slide_id', 'activity', 'sort'],
                'slider_has_advantages_display_idx'
            ); // Выборка активных преимуществ в нужном порядке.

            $table->index(
                ['slider_slide_advantage_id'],
                'slider_has_advantages_advantage_idx'
            ); // Поиск слайдов, использующих конкретное преимущество.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slider_slide_has_advantages');
    }
};
