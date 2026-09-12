<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();

            // Родительская локация для построения иерархии:
            // страна -> область -> город -> район
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete();

            // Тип локации: country, region, city, district и т.д.
            $table->string('type', 50)->index();

            // Уникальный URL-идентификатор локации
            $table->string('slug')->unique();

            // Внешний/системный код локации для интеграций
            $table->string('code', 100)->nullable()->index();

            // Географические координаты
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Часовой пояс, например Asia/Almaty
            $table->string('timezone', 100)->nullable();

            // Активность локации
            $table->boolean('activity')->default(true)->index();

            // Локация по умолчанию
            $table->boolean('is_default')->default(false)->index();

            // Порядок сортировки
            $table->unsignedInteger('sort')->default(0);

            $table->timestamps();

            $table->index(['parent_id', 'activity', 'sort']);
            $table->index(['type', 'activity', 'sort']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
