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
        Schema::create('school_qa_threads', function (Blueprint $t) {
            $t->id();

            // Автор темы
            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('Автор темы');

            // К чему относится тема (Course / Module / Lesson / Bundle)
            $t->morphs('threadable');

            // Контент
            $t->string('title')->comment('Заголовок темы');
            $t->text('body')->nullable()->comment('Первое сообщение');

            // Статусы
            $t->string('status', 16)
                ->default('open')
                ->comment('open|closed|archived');

            $t->boolean('is_locked')->default(false)->comment('Запрет сообщений');
            $t->boolean('is_pinned')->default(false)->comment('Закреплена');

            // Метрики
            $t->unsignedInteger('replies_count')->default(0);
            $t->timestamp('last_reply_at')->nullable();
            $t->timestamp('last_activity_at')->nullable();

            $t->json('meta')->nullable();

            $t->timestamps();

            // Индексы
            $t->index(['status', 'is_locked', 'is_pinned'], 'idx_school_thread_flags');
            $t->index(['last_activity_at'], 'idx_school_thread_last_activity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_qa_threads');
    }
};
