
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sliders', function (Blueprint $table) {

            // Основные данные слайдера.
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete(); // Пользователь, создавший слайдер.

            $table->string('code', 150)->unique(); // Уникальный код подключения слайдера на страницах.

            $table->string('type', 50)->default('hero'); // Тип оформления: hero, banner, carousel и другие.

            // Публикация и управление отображением.
            $table->string('status', 30)->default('draft'); // Статус: draft, published, archived.

            $table->unsignedTinyInteger('moderation_status')
                ->default(0); // Модерация: 0 — ожидает, 1 — одобрен, 2 — отклонён.

            $table->boolean('activity')->default(true); // Активность слайдера: включён или выключен.

            $table->unsignedInteger('sort')->default(0); // Порядок сортировки слайдеров в админке.

            $table->timestamp('published_at')->nullable(); // Дата публикации слайдера.

            $table->timestamp('show_from_at')->nullable(); // Начало периода показа.

            $table->timestamp('show_to_at')->nullable(); // Окончание периода показа.

            // Автоматическое переключение слайдов.
            $table->unsignedInteger('autoplay_delay')
                ->nullable(); // Интервал автопрокрутки в миллисекундах; null — выключена.

            $table->boolean('autoplay_disable_on_interaction')
                ->default(false); // Останавливать автопрокрутку после взаимодействия пользователя.

            $table->boolean('pause_on_hover')
                ->default(true); // Приостанавливать автопрокрутку при наведении мыши.

            // Анимация переключения слайдов.
            $table->string('effect', 50)
                ->default('slide'); // Эффект перехода: slide, fade, cube, coverflow, flip, creative.

            $table->unsignedInteger('speed')
                ->default(700); // Продолжительность анимации переключения в миллисекундах.

            $table->json('effect_options')
                ->nullable(); // Дополнительные параметры выбранного эффекта Swiper.

            // Управление слайдером.
            $table->boolean('loop')
                ->default(true); // Бесконечное циклическое переключение слайдов.

            $table->boolean('keyboard')
                ->default(true); // Управление стрелками клавиатуры.

            $table->boolean('allow_touch_move')
                ->default(true); // Разрешить переключение свайпами и перетаскиванием.

            $table->boolean('grab_cursor')
                ->default(false); // Показывать курсор захвата при наведении на слайдер.

            // Размеры и расположение слайдов.
            $table->unsignedDecimal('slides_per_view', 5, 2)
                ->default(1); // Количество одновременно видимых слайдов.

            $table->unsignedInteger('space_between')
                ->default(0); // Расстояние между слайдами в пикселях.

            $table->boolean('auto_height')
                ->default(false); // Автоматически изменять высоту под активный слайд.

            $table->json('breakpoints')
                ->nullable(); // Адаптивные настройки для разных размеров экрана.

            // Элементы навигации.
            $table->boolean('show_navigation')
                ->default(true); // Показывать кнопки «Назад» и «Вперёд».

            $table->boolean('show_pagination')
                ->default(true); // Показывать индикаторы страниц слайдера.

            // Дополнительные настройки.
            $table->json('settings')
                ->nullable(); // Дополнительные параметры оформления и поведения слайдера.

            // Системные поля.
            $table->timestamps(); // Даты создания и последнего изменения.

            // Индексы.
            $table->index(
                ['activity', 'status', 'moderation_status'],
                'sliders_public_status_idx'
            ); // Быстрая выборка активных опубликованных слайдеров.

            $table->index(
                ['activity', 'show_from_at', 'show_to_at'],
                'sliders_show_period_idx'
            ); // Выборка слайдеров по периоду отображения.

            $table->index(
                ['sort', 'id'],
                'sliders_sort_idx'
            ); // Сортировка слайдеров в административной панели.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
