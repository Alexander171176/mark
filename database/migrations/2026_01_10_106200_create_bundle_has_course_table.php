<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    // M:N Bundle <-> Course
    public function up(): void {
        Schema::create('bundle_has_course', function (Blueprint $t) {
            $t->id();
            $t->foreignId('bundle_id')->constrained()->cascadeOnDelete();
            $t->foreignId('course_id')->constrained()->cascadeOnDelete();
            $t->timestamps();

            $t->unique(['bundle_id','course_id'], 'u_bundle_course');
        });
    }
    public function down(): void {
        Schema::dropIfExists('bundle_has_course');
    }
};
