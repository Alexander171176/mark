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
        Schema::create('school_bundles', function (Blueprint $t) {
            $t->id();

            $t->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');
            $t->boolean('activity')->default(true)->comment('Активность набора');

            $t->string('slug')->unique()->comment('Уникальный ЧПУ набора');

            $t->timestamp('published_at')->nullable()->comment('Дата публикации');

            $t->unsignedBigInteger('views')->default(0)->comment('Просмотры');
            $t->unsignedBigInteger('likes')->default(0)->comment('Лайки');

            $t->json('meta')->nullable()->comment('Дополнительные метаданные');

            $t->timestamps();

            $t->index(['activity', 'published_at'], 'idx_school_bundle_active_pub');
            $t->index(['activity', 'sort'], 'idx_school_bundle_active_sort');
            $t->index('sort', 'idx_school_bundle_sort');
            $t->index('views', 'idx_school_bundle_views');
            $t->index('likes', 'idx_school_bundle_likes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_bundles');
    }
};
