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
        Schema::create('school_hashtaggables', function (Blueprint $t) {
            $t->id();

            $t->foreignId('school_hashtag_id')
                ->constrained('school_hashtags')
                ->cascadeOnDelete();

            $t->unsignedBigInteger('hashtaggable_id');
            $t->string('hashtaggable_type');

            $t->timestamps();

            $t->unique(
                ['school_hashtag_id', 'hashtaggable_type', 'hashtaggable_id'],
                'uq_school_hashtag_hashtaggable'
            );

            $t->index(['hashtaggable_type', 'hashtaggable_id'], 'idx_school_hashtaggable_pair');
            $t->index('school_hashtag_id', 'idx_school_hashtag_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_hashtaggables');
    }
};
