
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slider_slide_actions', function (Blueprint $table) {

            // Основные данные действия.
            $table->id(); // Уникальный идентификатор действия.

            $table->foreignId('slider_slide_id')
                ->constrained('slider_slides')
                ->cascadeOnDelete(); // Слайд, которому принадлежит кнопка.

            // Управление отображением.
            $table->unsignedInteger('sort')
                ->default(0); // Порядок кнопки внутри слайда (Drag & Drop).

            $table->boolean('activity')
                ->default(true); // Активность кнопки: показывать или скрывать.

            $table->boolean('is_primary')
                ->default(false); // Основная кнопка с акцентным оформлением.

            // Тип и назначение действия.
            $table->string('action_type', 30)
                ->default('url'); // Тип действия: url, route, form, anchor.

            $table->string('action_value', 2048)
                ->nullable(); // URL, имя маршрута, код формы или якорь.

            $table->json('route_params')
                ->nullable(); // Параметры именованного маршрута Laravel.

            $table->string('target', 20)
                ->default('_self'); // Способ открытия ссылки: _self или _blank.

            // Внешний вид кнопки.
            $table->string('style', 50)
                ->nullable(); // Вариант оформления: primary, secondary, outline и др.

            $table->string('icon', 100)
                ->nullable(); // Название иконки, отображаемой на кнопке.

            $table->string('icon_position', 20)
                ->default('left'); // Расположение иконки: left или right.

            $table->string('css_class', 255)
                ->nullable(); // Дополнительный CSS-класс оформления кнопки.

            // Дополнительные настройки.
            $table->json('settings')
                ->nullable(); // Расширенные настройки поведения и оформления.

            // Системные поля.
            $table->timestamps(); // Даты создания и изменения действия.

            // Индексы.
            $table->index(
                ['slider_slide_id', 'sort', 'id'],
                'slider_slide_actions_order_idx'
            ); // Быстрая сортировка кнопок внутри конкретного слайда.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slider_slide_actions');
    }
};
