<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Пивот-таблица связь "многие ко многим" между модулями и их изображениями
     */
    public function up(): void
    {
        Schema::create('module_has_images', function (Blueprint $t) {
            // Связь с модулями
            $t->foreignId('module_id')
                ->constrained('modules')
                ->cascadeOnDelete();

            // Связь с таблицей изображений модулей
            $t->foreignId('image_id')
                ->constrained('module_images')
                ->cascadeOnDelete();

            // Порядок отображения конкретного изображения у модуля
            $t->unsignedInteger('order')->default(0);

            // Составной первичный ключ
            $t->primary(['module_id', 'image_id']);

            // Индекс для сортировки изображений внутри модуля
            $t->index(['module_id', 'order'], 'idx_module_image_order');

            // ВАЖНО:
            // - Отдельные индексы на module_id и image_id не нужны, их даёт foreignId()
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_has_images');
    }
};
