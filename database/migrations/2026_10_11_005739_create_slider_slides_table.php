<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slider_slides', function (Blueprint $table) {
            // Основные данные самостоятельного слайда.
            $table->id(); // Уникальный идентификатор слайда.

            // Управление отображением.
            $table->boolean('activity')->default(true); // Глобальная активность слайда во всех слайдерах.

            // Публикация.
            $table->string('status', 30)->default('draft'); // draft, published, archived.
            $table->timestamp('published_at')->nullable(); // Дата публикации слайда.
            $table->timestamp('show_from_at')->nullable(); // Начало периода отображения.
            $table->timestamp('show_to_at')->nullable(); // Окончание периода отображения.

            // Фон и оформление.
            $table->string('background_color', 30)->nullable(); // Цвет фона слайда.
            $table->string('text_color', 30)->nullable(); // Основной цвет текста.
            $table->string('accent_color', 30)->nullable(); // Цвет акцентного текста.
            $table->string('overlay_color', 30)->nullable(); // Цвет затемняющего слоя поверх изображения.
            $table->unsignedTinyInteger('overlay_opacity')->nullable(); // Прозрачность затемнения от 0 до 100%.
            $table->string('image_position', 100)->default('object-center'); // Позиция фонового изображения.
            $table->string('content_position', 30)->default('left'); // left, center, right.

            // Анимация содержимого слайда.
            $table->string('animation_type', 50)->default('fade-up'); // Общий эффект появления содержимого.
            $table->unsignedInteger('animation_duration')->default(700); // Длительность анимации в миллисекундах.
            $table->unsignedInteger('animation_delay')->default(0); // Начальная задержка анимации в миллисекундах.
            $table->unsignedInteger('animation_stagger')->default(150); // Интервал появления элементов.
            $table->boolean('animation_once')->default(false); // Анимировать только при первом показе.
            $table->json('animation_settings')->nullable(); // Анимация отдельных элементов содержимого.
            $table->json('settings')->nullable(); // Прочие параметры оформления слайда.

            // Модерация слайда.
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('slider_slides_moderation_status_idx')
                ->comment('Модерация: 0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('slider_slides_moderated_by_idx')
                ->comment('Модератор (users.id)');

            $table->foreign('moderated_by', 'slider_slides_moderated_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('slider_slides_moderated_at_idx')
                ->comment('Дата модерации');

            $table->text('moderation_note')
                ->nullable()
                ->comment('Комментарий модератора');

            $table->unsignedInteger('sort')->default(0); // Глобальный порядок сортировки в админке.

            // Системные поля.
            $table->timestamps(); // Даты создания и последнего изменения.

            // Индексы.
            $table->index(['activity', 'status', 'moderation_status'], 'slider_slides_public_idx');
            $table->index(['activity', 'show_from_at', 'show_to_at'], 'slider_slides_period_idx');
            $table->index(['sort', 'id'], 'slider_slides_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slider_slides');
    }
};
