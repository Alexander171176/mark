
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slider_slide_advantages', function (Blueprint $table) {

            // Основные данные преимущества.
            $table->id(); // Уникальный идентификатор преимущества.

            $table->foreignId('slider_slide_id')
                ->constrained('slider_slides')
                ->cascadeOnDelete(); // Слайд, которому принадлежит преимущество.

            // Управление отображением.
            $table->unsignedInteger('sort')
                ->default(0); // Порядок преимущества внутри слайда (Drag & Drop).

            $table->boolean('activity')
                ->default(true); // Активность преимущества: показывать или скрывать.

            // Настройки иконки.
            $table->string('icon_type', 30)
                ->default('lucide'); // Тип иконки: lucide, heroicon, class, none.

            $table->string('icon', 100)
                ->nullable(); // Название иконки или CSS-класс.

            $table->string('icon_color', 30)
                ->nullable(); // Индивидуальный цвет иконки.

            // Внешний вид преимущества.
            $table->string('style', 50)
                ->nullable(); // Вариант оформления: default, card, compact и другие.

            // Дополнительные настройки.
            $table->json('settings')
                ->nullable(); // Расширенные параметры оформления преимущества.

            // Системные поля.
            $table->timestamps(); // Даты создания и последнего изменения.

            // Индексы.
            $table->index(
                ['slider_slide_id', 'sort', 'id'],
                'slider_slide_advantages_order_idx'
            ); // Быстрая сортировка преимуществ внутри слайда.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slider_slide_advantages');
    }
};
