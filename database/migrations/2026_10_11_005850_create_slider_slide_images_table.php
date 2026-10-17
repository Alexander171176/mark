
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slider_slide_images', function (Blueprint $table) {

            $table->id()
                ->comment('Уникальный идентификатор изображения');

            $table->unsignedInteger('order')
                ->default(0)
                ->index('slider_slide_images_order_idx')
                ->comment('Порядок сортировки изображения');

            $table->string('alt', 255)
                ->nullable()
                ->comment('Альтернативный текст изображения');

            $table->string('caption', 255)
                ->nullable()
                ->comment('Подпись к изображению');

            $table->timestamps();

            $table->comment(
                'Изображения слайдов. Файлы хранятся через Spatie MediaLibrary.'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slider_slide_images');
    }
};
