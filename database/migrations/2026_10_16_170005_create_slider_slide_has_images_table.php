
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slider_slide_has_images', function (Blueprint $table) {

            $table->unsignedBigInteger('slider_slide_id')
                ->comment('Слайд (slider_slides.id)');

            $table->foreign(
                'slider_slide_id',
                'slider_slide_has_images_slide_fk'
            )
                ->references('id')
                ->on('slider_slides')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('slider_slide_image_id')
                ->comment('Изображение (slider_slide_images.id)');

            $table->foreign(
                'slider_slide_image_id',
                'slider_slide_has_images_image_fk'
            )
                ->references('id')
                ->on('slider_slide_images')
                ->cascadeOnDelete();

            $table->string('purpose', 30)
                ->default('desktop')
                ->comment('Назначение изображения: desktop, mobile и другие');

            $table->unsignedInteger('order')
                ->default(0)
                ->comment('Порядок отображения изображения');

            $table->primary(
                ['slider_slide_id', 'slider_slide_image_id'],
                'slider_slide_has_images_pk'
            );

            $table->index(
                ['slider_slide_id', 'purpose', 'order'],
                'slider_slide_has_images_purpose_idx'
            );

            $table->comment(
                'Связь слайдов с изображениями для разных устройств.'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slider_slide_has_images');
    }
};
