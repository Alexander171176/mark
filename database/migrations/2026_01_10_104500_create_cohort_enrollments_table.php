<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cohort_enrollments', function (Blueprint $t) {
            $t->id();

            $t->foreignId('course_schedule_id')
                ->constrained('course_schedules')
                ->cascadeOnDelete();

            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $t->string('status', 20)
                ->default('pending')
                ->comment('pending|approved|rejected|cancelled');

            $t->timestamp('enrolled_at')->nullable();
            $t->text('notes')->nullable();

            $t->timestamps();
            $t->softDeletes(); // ← опционально

            $t->unique(['course_schedule_id', 'user_id'], 'uniq_schedule_user');
            $t->index(['status', 'enrolled_at'], 'idx_status_enrolled_at');
            $t->index(['course_schedule_id', 'status'], 'idx_schedule_status'); // ← опционально
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cohort_enrollments');
    }
};
