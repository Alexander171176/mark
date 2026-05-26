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
        Schema::create('school_course_has_tracks', function (Blueprint $t) {
            $t->id();

            // Курс
            $t->foreignId('school_course_id')
                ->constrained('school_courses')
                ->cascadeOnDelete()
                ->comment('Курс');

            // Категория/трек обучения
            $t->foreignId('school_track_id')
                ->constrained('school_tracks')
                ->cascadeOnDelete()
                ->comment('Трек обучения');

            $t->timestamps();

            $t->unique(['school_course_id', 'school_track_id'], 'uq_school_course_track');

            $t->index('school_course_id', 'idx_school_course_track_course');
            $t->index('school_track_id', 'idx_school_course_track_track');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_course_has_tracks');
    }
};
