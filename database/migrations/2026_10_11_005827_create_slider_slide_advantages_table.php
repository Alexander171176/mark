<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slider_slide_advantages', function (Blueprint $table) {
            // Основные данные самостоятельного преимущества.
            $table->id(); // Уникальный идентификатор преимущества.
            $table->boolean('activity')->default(true); // Глобальная активность во всех слайдах.

            // Настройки иконки.
            $table->string('icon_type', 30)->default('lucide'); // lucide, heroicon, class, none.
            $table->string('icon', 100)->nullable(); // Название иконки или CSS-класс.
            $table->string('icon_color', 30)->nullable(); // Индивидуальный цвет иконки.
            $table->string('style', 50)->nullable(); // default, card, compact и другие.
            $table->json('settings')->nullable(); // Расширенные параметры оформления преимущества.

            // Модерация преимущества.
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('slider_slide_advantages_moderation_status_idx')
                ->comment('Модерация: 0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('slider_slide_advantages_moderated_by_idx')
                ->comment('Модератор (users.id)');

            $table->foreign('moderated_by', 'slider_slide_advantages_moderated_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('slider_slide_advantages_moderated_at_idx')
                ->comment('Дата модерации');

            $table->text('moderation_note')
                ->nullable()
                ->comment('Комментарий модератора');

            $table->unsignedInteger('sort')->default(0); // Глобальный порядок сортировки в админке.

            // Системные поля.
            $table->timestamps(); // Даты создания и последнего изменения.

            // Индексы.
            $table->index(['activity', 'moderation_status'], 'slider_slide_advantages_public_idx');
            $table->index(['sort', 'id'], 'slider_slide_advantages_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slider_slide_advantages');
    }
};
