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
        Schema::create('school_tracks', function (Blueprint $t) {
            $t->id();

            // Родительская категория обучения
            $t->foreignId('parent_id')
                ->nullable()
                ->constrained('school_tracks')
                ->nullOnDelete()
                ->comment('Родительская категория обучения');

            // Служебные поля
            $t->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');
            $t->boolean('activity')->default(true)->comment('Активность');

            // Непереводимые поля
            $t->string('slug')->comment('Уникальный ЧПУ');

            // Метрики
            $t->unsignedBigInteger('views')->default(0)->comment('Количество просмотров');

            $t->timestamps();

            // Уникальность
            $t->unique('slug', 'uq_school_track_slug');

            // Индексы
            $t->index(['activity', 'sort'], 'idx_school_track_activity_sort');
            $t->index(['parent_id', 'sort'], 'idx_school_track_parent_sort');
            $t->index('sort', 'idx_school_track_sort');
            $t->index('views', 'idx_school_track_views');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_tracks');
    }
};
