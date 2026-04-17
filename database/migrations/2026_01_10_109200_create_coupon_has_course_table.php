<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Привязка купона к конкретным курсам (если купон не универсальный)
    public function up(): void
    {
        Schema::create('coupon_has_course', function (Blueprint $t) {
            $t->unsignedBigInteger('coupon_id');
            $t->unsignedBigInteger('course_id');

            $t->foreign('coupon_id')->references('id')->on('coupons')->cascadeOnDelete();
            $t->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();

            $t->primary(['coupon_id', 'course_id']); // защита от дублей
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_has_course');
    }
};
