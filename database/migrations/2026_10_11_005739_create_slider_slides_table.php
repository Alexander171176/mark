
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slider_slides', function (Blueprint $table) {

            // Основные данные слайда.
            $table->id(); // Уникальный идентификатор слайда.

            $table->foreignId('slider_id')
                ->constrained('sliders')
                ->cascadeOnDelete(); // Слайдер, которому принадлежит слайд.

            // Управление отображением.
            $table->boolean('activity')
                ->default(true); // Активность слайда.

            $table->unsignedInteger('sort')
                ->default(0); // Порядок слайда внутри слайдера (Drag & Drop).

            $table->boolean('is_main')
                ->default(false); // Использовать заголовок h1 вместо h2.

            // Публикация.
            $table->string('status', 30)
                ->default('draft'); // Статус: draft, published, archived.

            $table->timestamp('published_at')
                ->nullable(); // Дата публикации слайда.

            $table->timestamp('show_from_at')
                ->nullable(); // Начало периода отображения.

            $table->timestamp('show_to_at')
                ->nullable(); // Окончание периода отображения.

            // Фон и оформление.
            $table->string('background_color', 30)
                ->nullable(); // Цвет фона слайда.

            $table->string('text_color', 30)
                ->nullable(); // Основной цвет текста.

            $table->string('accent_color', 30)
                ->nullable(); // Цвет акцентного текста.

            $table->string('overlay_color', 30)
                ->nullable(); // Цвет затемняющего слоя поверх изображения.

            $table->unsignedTinyInteger('overlay_opacity')
                ->nullable(); // Прозрачность затемнения от 0 до 100%.

            $table->string('image_position', 100)
                ->default('object-center'); // Позиция фонового изображения: object-center, object-top, object-bottom, object-left, object-right.

            $table->string('content_position', 30)
                ->default('left'); // Расположение контента: left, center, right.

            // Анимация содержимого слайда.
            $table->string('animation_type', 50)
                ->default('fade-up'); // Общий эффект появления содержимого.

            $table->unsignedInteger('animation_duration')
                ->default(700); // Длительность анимации в миллисекундах.

            $table->unsignedInteger('animation_delay')
                ->default(0); // Начальная задержка анимации в миллисекундах.

            $table->unsignedInteger('animation_stagger')
                ->default(150); // Интервал появления последовательных элементов.

            $table->boolean('animation_once')
                ->default(false); // Анимировать только при первом показе слайда.

            $table->json('animation_settings')
                ->nullable(); // Индивидуальная анимация заголовка, описания, кнопок и преимуществ.

            // Дополнительные настройки.
            $table->json('settings')
                ->nullable(); // Прочие параметры оформления конкретного слайда.

            // Системные поля.
            $table->timestamps(); // Даты создания и последнего изменения.

            // Индексы.
            $table->index(
                ['slider_id', 'sort', 'id'],
                'slider_slides_sort_idx'
            ); // Быстрая сортировка слайдов внутри слайдера.

            $table->index(
                ['slider_id', 'activity', 'status'],
                'slider_slides_public_idx'
            ); // Выборка активных опубликованных слайдов.

            $table->index(
                ['activity', 'show_from_at', 'show_to_at'],
                'slider_slides_period_idx'
            ); // Фильтрация по периоду показа.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slider_slides');
    }
};
