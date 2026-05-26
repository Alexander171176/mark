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
        Schema::create('school_instructor_profile_has_images', function (Blueprint $t) {

            $t->unsignedBigInteger('school_instructor_profile_id');
            $t->unsignedBigInteger('image_id');

            // FK с короткими именами
            $t->foreign(
                'school_instructor_profile_id',
                'fk_instr_prof_img_profile'
            )
                ->references('id')
                ->on('school_instructor_profiles')
                ->cascadeOnDelete();

            $t->foreign(
                'image_id',
                'fk_instr_prof_img_image'
            )
                ->references('id')
                ->on('school_instructor_profile_images')
                ->cascadeOnDelete();

            $t->unsignedInteger('order')->default(0);

            $t->primary(['school_instructor_profile_id', 'image_id']);

            $t->index(
                ['school_instructor_profile_id', 'order'],
                'idx_instr_prof_img_order'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_instructor_profile_has_images');
    }
};
