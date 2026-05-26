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
        Schema::create('school_course_schedule_has_images', function (Blueprint $t) {
            $t->unsignedBigInteger('school_course_schedule_id')
                ->comment('Поток курса');

            $t->unsignedBigInteger('image_id')
                ->comment('Изображение');

            $t->foreign('school_course_schedule_id', 'fk_school_sched_img_schedule')
                ->references('id')
                ->on('school_course_schedules')
                ->cascadeOnDelete();

            $t->foreign('image_id', 'fk_school_sched_img_image')
                ->references('id')
                ->on('school_course_schedule_images')
                ->cascadeOnDelete();

            $t->unsignedInteger('order')->default(0)->comment('Порядок отображения');

            $t->primary(
                ['school_course_schedule_id', 'image_id'],
                'pk_school_schedule_image'
            );

            $t->index(
                ['school_course_schedule_id', 'order'],
                'idx_school_schedule_image_order'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_course_schedule_has_images');
    }
};
