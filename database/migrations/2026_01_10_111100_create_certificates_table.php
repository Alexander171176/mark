<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Сертификаты об окончании курса
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $t) {
            $t->id();

            // Владелец сертификата
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Какой курс завершён
            $t->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            // Конкретное зачисление (если ведёте историю наборов/покупок)
            $t->foreignId('enrollment_id')
                ->nullable()
                ->constrained('enrollments')
                ->nullOnDelete();

            // Уникальные идентификаторы для проверки
            $t->string('number', 32)->unique();          // человеко-читаемый номер (для печати)
            $t->string('verification_code', 64)->unique();// код/хэш для публичной верификации

            // Данные о выдаче
            $t->timestamp('issued_at')->nullable();       // когда выдан
            $t->timestamp('expires_at')->nullable();      // когда истекает (если есть срок)

            // Итоги обучения (опционально)
            $t->unsignedTinyInteger('score')->nullable()->comment('Итоговый процент/оценка 0..100');
            $t->decimal('hours', 5, 2)->nullable()->comment('Кол-во академических часов');

            // Статус жизненного цикла
            $t->string('status', 16)->default('issued')->comment('draft|issued|revoked|expired');
            $t->timestamp('revoked_at')->nullable()->comment('Когда был отозван, если отозван');

            // Доп. сведения
            $t->string('name_on_certificate')->nullable()->comment('Имя, пропечатываемое на сертификате');
            $t->text('notes')->nullable();
            $t->json('meta')->nullable();

            $t->timestamps();
            $t->softDeletes();

            // Один сертификат на пользователя по одному курсу (если нужна такая политика)
            $t->unique(['user_id', 'course_id'], 'uniq_user_course');

            // Частые выборки
            $t->index(['user_id', 'status', 'issued_at'], 'idx_user_status_issued');
            $t->index(['course_id', 'issued_at'], 'idx_course_issued');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
