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
        Schema::create('school_course_related', function (Blueprint $t) {
            // Основной курс
            $t->foreignId('school_course_id')
                ->constrained('school_courses')
                ->cascadeOnDelete()
                ->comment('Основной курс');

            // Связанный курс
            $t->foreignId('related_school_course_id')
                ->constrained('school_courses')
                ->cascadeOnDelete()
                ->comment('Связанный курс');

            $t->primary(
                ['school_course_id', 'related_school_course_id'],
                'pk_school_course_related'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_course_related');
    }
};
