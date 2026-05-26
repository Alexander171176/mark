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
        Schema::create('school_certificates', function (Blueprint $t) {
            $t->id();

            // Владелец сертификата
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('Пользователь');

            // Курс
            $t->foreignId('school_course_id')
                ->constrained('school_courses')
                ->cascadeOnDelete()
                ->comment('Курс');

            // Зачисление
            $t->foreignId('school_enrollment_id')
                ->nullable()
                ->constrained('school_enrollments')
                ->nullOnDelete()
                ->comment('Зачисление');

            // Идентификаторы
            $t->string('number', 32)->unique()->comment('Номер сертификата');
            $t->string('verification_code', 64)->unique()->comment('Код проверки');

            // Даты
            $t->timestamp('issued_at')->nullable()->comment('Дата выдачи');
            $t->timestamp('expires_at')->nullable()->comment('Дата истечения');

            // Итоги обучения
            $t->unsignedTinyInteger('score')->nullable()->comment('Итоговая оценка 0-100');
            $t->decimal('hours', 5, 2)->nullable()->comment('Академические часы');

            // Статус
            $t->string('status', 16)
                ->default('issued')
                ->comment('draft|issued|revoked|expired');

            $t->timestamp('revoked_at')->nullable()->comment('Дата отзыва');

            // Дополнительные данные
            $t->string('name_on_certificate')->nullable()->comment('Имя на сертификате');
            $t->text('notes')->nullable()->comment('Заметки');
            $t->json('meta')->nullable()->comment('Дополнительные данные');

            $t->timestamps();

            // Один сертификат на пользователя по одному курсу
            $t->unique(['user_id', 'school_course_id'], 'uq_school_certificate_user_course');

            // Индексы
            $t->index(['user_id', 'status', 'issued_at'], 'idx_school_cert_user_status_issued');
            $t->index(['school_course_id', 'issued_at'], 'idx_school_cert_course_issued');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_certificates');
    }
};
