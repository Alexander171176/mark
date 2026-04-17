<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_images', function (Blueprint $table) {
            $table->id()->comment('PK');

            $table->unsignedInteger('order')
                ->default(0)
                ->index()
                ->comment('Порядок/сортировка изображения (общий, если используется)');

            $table->string('alt', 255)
                ->nullable()
                ->comment('ALT текст изображения');

            $table->string('caption', 255)
                ->nullable()
                ->comment('Подпись к изображению');

            $table->timestamps();

            $table->comment('Изображения статей (метаданные). Файл/хранилище — отдельной логикой.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_images');
    }
};
