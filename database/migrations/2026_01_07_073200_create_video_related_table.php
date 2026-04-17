<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_related', function (Blueprint $table) {
            $table->foreignId('video_id')
                ->comment('Видео-источник (videos.id)')
                ->constrained('videos')
                ->cascadeOnDelete();

            $table->foreignId('related_video_id')
                ->comment('Рекомендуемое видео (videos.id)')
                ->constrained('videos')
                ->cascadeOnDelete();

            $table->unsignedInteger('sort')
                ->default(0)
                ->comment('Сортировка рекомендаций внутри видео (draggable)');

            $table->primary(['video_id', 'related_video_id']);

            // Основной индекс для выборки "рекомендованные видео"
            $table->index(['video_id', 'sort'], 'vr_video_sort_idx');

            // Для обратных связей / очисток
            $table->index('related_video_id', 'vr_related_video_idx');

            $table->comment('Связь рекомендованных видео (self many-to-many) с сортировкой.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_related');
    }
};
