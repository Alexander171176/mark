<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('image_processor_variants', function (Blueprint $table) {
            $table->id()->comment('PK');

            $table->unsignedBigInteger('image_processor_profile_id')
                ->index('image_processor_variants_profile_id_idx')
                ->comment('Профиль обработки (image_processor_profiles.id)');

            $table->foreign('image_processor_profile_id', 'image_processor_variants_profile_id_fk')
                ->references('id')
                ->on('image_processor_profiles')
                ->cascadeOnDelete();

            $table->string('key', 100)
                ->comment('Ключ варианта: desktop, mobile, square, circle');

            $table->string('name', 255)
                ->comment('Название варианта');

            $table->string('description', 500)
                ->nullable()
                ->comment('Описание варианта');

            $table->boolean('activity')
                ->default(true)
                ->index('image_processor_variants_activity_idx')
                ->comment('Активность варианта');

            $table->unsignedInteger('width')
                ->comment('Ширина изображения в пикселях');

            $table->unsignedInteger('height')
                ->comment('Высота изображения в пикселях');

            $table->boolean('allow_rotate')
                ->default(false)
                ->comment('Разрешить разворот пропорций ширина/высота');

            $table->unsignedTinyInteger('quality')
                ->default(85)
                ->comment('Качество сжатия изображения от 1 до 100');

            $table->string('format', 20)
                ->default('webp')
                ->comment('Формат файла: webp, jpg, png');

            $table->string('fit', 50)
                ->default('crop')
                ->comment('Тип обработки: crop, contain, stretch');

            $table->string('shape', 50)
                ->default('rectangle')
                ->comment('Форма отображения: rectangle, square, circle');

            $table->string('background_light', 50)
                ->nullable()
                ->comment('Цвет фона для светлой темы');

            $table->string('background_dark', 50)
                ->nullable()
                ->comment('Цвет фона для тёмной темы');

            $table->boolean('keep_original')
                ->default(true)
                ->comment('Сохранять оригинальный файл');

            $table->unsignedInteger('sort')
                ->default(0)
                ->index('image_processor_variants_sort_idx')
                ->comment('Сортировка');

            $table->timestamps();

            $table->unique(
                ['image_processor_profile_id', 'key'],
                'image_processor_variants_profile_key_unique'
            );

            $table->index(
                ['image_processor_profile_id', 'activity', 'sort'],
                'image_processor_variants_profile_activity_sort_idx'
            );

            $table->comment('Модуль обработки изображений: варианты обработки профилей');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_processor_variants');
    }
};
