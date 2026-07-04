<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Запуск миграции.
     */
    public function up(): void
    {
        Schema::create('image_presets', function (Blueprint $table) {
            $table->id()
                ->comment('PK');

            $table->string('key', 100)
                ->unique('image_presets_key_unique')
                ->comment(
                    'Системный ключ пресета: article_cover, shop_logo, avatar_circle'
                );

            $table->string('description', 500)
                ->nullable()
                ->comment(
                    'Описание пресета для администратора'
                );

            $table->string('shape', 50)
                ->default('rectangle')
                ->index('image_presets_shape_idx')
                ->comment(
                    'Форма изображения: rectangle, square, circle'
                );

            $table->unsignedInteger('width')
                ->comment(
                    'Итоговая ширина изображения в пикселях'
                );

            $table->unsignedInteger('height')
                ->comment(
                    'Итоговая высота изображения в пикселях'
                );

            $table->boolean('image_rotation_enabled')
                ->default(true)
                ->index('image_presets_image_rotation_enabled_idx')
                ->comment(
                    'Разрешить поворот самого изображения'
                );

            $table->boolean('crop_rotation_enabled')
                ->default(false)
                ->index('image_presets_crop_rotation_enabled_idx')
                ->comment(
                    'Разрешить поворот рамки обрезки'
                );

            $table->unsignedInteger('max_file_size_kb')
                ->default(2048)
                ->comment(
                    'Максимальный размер исходного файла в килобайтах'
                );

            $table->boolean('keep_original')
                ->default(true)
                ->index('image_presets_keep_original_idx')
                ->comment(
                    'Сохранять оригинальный файл'
                );

            $table->unsignedInteger('sort')
                ->default(0)
                ->index('image_presets_sort_idx')
                ->comment(
                    'Сортировка'
                );

            $table->timestamps();

            $table->index(
                ['shape', 'sort'],
                'image_presets_shape_sort_idx'
            );

            $table->comment(
                'Пресеты обработки изображений: размеры, форма, поворот, ограничения'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_presets');
    }
};
