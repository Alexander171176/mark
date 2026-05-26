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
        Schema::create('school_bundle_has_courses', function (Blueprint $t) {
            $t->id();

            $t->foreignId('school_bundle_id')
                ->constrained('school_bundles')
                ->cascadeOnDelete()
                ->comment('Набор курсов');

            $t->foreignId('school_course_id')
                ->constrained('school_courses')
                ->cascadeOnDelete()
                ->comment('Курс');

            $t->timestamps();

            $t->unique(['school_bundle_id', 'school_course_id'], 'uq_school_bundle_course');

            $t->index('school_bundle_id', 'idx_school_bundle_course_bundle');
            $t->index('school_course_id', 'idx_school_bundle_course_course');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_bundle_has_courses');
    }
};
