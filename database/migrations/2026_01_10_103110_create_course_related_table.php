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
        Schema::create('course_related', function (Blueprint $table) {
            // Используем foreignId для краткости
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('related_course_id')->constrained('courses')->onDelete('cascade'); // Указываем ту же таблицу 'courses'

            $table->primary(['course_id', 'related_course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_related');
    }
};
