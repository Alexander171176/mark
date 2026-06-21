<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('image_processor_profiles', function (Blueprint $table) {
            $table->id()->comment('PK');

            $table->string('key', 100)
                ->unique('image_processor_profiles_key_unique')
                ->comment('Ключ профиля: article_image, company_logo, banner');

            $table->string('name', 255)
                ->comment('Название профиля');

            $table->string('description', 500)
                ->nullable()
                ->comment('Описание профиля');

            $table->boolean('activity')
                ->default(true)
                ->index('image_processor_profiles_activity_idx')
                ->comment('Активность профиля');

            $table->unsignedInteger('sort')
                ->default(0)
                ->index('image_processor_profiles_sort_idx')
                ->comment('Сортировка');

            $table->timestamps();

            $table->comment('Модуль обработки изображений: профили обработки');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_processor_profiles');
    }
};
