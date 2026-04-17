<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Полиморфная связующая таблица "хештеги ↔ любые hashtagable-сущности"
     *
     * Пример использования:
     *  - Course::class
     *  - Module::class
     *  - Lesson::class
     *  - Article::class
     *  - Video::class
     */
    public function up(): void
    {
        Schema::create('hashtagables', function (Blueprint $t) {
            $t->id();

            $t->foreignId('hashtag_id')
                ->constrained('hashtags')
                ->cascadeOnDelete();

            $t->unsignedBigInteger('hashtagable_id');
            $t->string('hashtagable_type');

            $t->timestamps();

            $t->unique(['hashtag_id', 'hashtagable_type', 'hashtagable_id'], 'uq_hashtag_hashtagable');

            $t->index(['hashtagable_type', 'hashtagable_id'], 'idx_hashtagable_pair');
            $t->index('hashtag_id', 'idx_hashtag_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hashtagables');
    }
};
