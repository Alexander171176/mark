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
        Schema::create('school_coupon_has_courses', function (Blueprint $t) {
            $t->foreignId('school_coupon_id')
                ->constrained('school_coupons')
                ->cascadeOnDelete()
                ->comment('Купон');

            $t->foreignId('school_course_id')
                ->constrained('school_courses')
                ->cascadeOnDelete()
                ->comment('Курс');

            $t->primary(
                ['school_coupon_id', 'school_course_id'],
                'pk_school_coupon_course'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_coupon_has_courses');
    }
};
