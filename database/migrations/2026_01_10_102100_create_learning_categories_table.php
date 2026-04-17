<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('learning_categories', function (Blueprint $t) {
            $t->id();

            $t->foreignId('parent_id')
                ->nullable()
                ->constrained('learning_categories')
                ->nullOnDelete();

            $t->unsignedInteger('sort')->default(0);
            $t->boolean('activity')->default(true)->index();

            $t->string('locale', 10);
            $t->string('name', 255);
            $t->string('slug');

            $t->string('short', 255)->nullable();
            $t->text('description')->nullable();

            $t->unsignedBigInteger('views')->default(0)->index();
            $t->string('meta_title', 255)->nullable();
            $t->string('meta_keywords', 255)->nullable();
            $t->text('meta_desc')->nullable();

            $t->timestamps();
            $t->softDeletes();

            // Уникальности в рамках локали
            $t->unique(['locale', 'name']);
            $t->unique(['locale', 'slug']);

            // Индексы для дерева/сортировки
            $t->index(['locale', 'parent_id', 'sort']);
            $t->index('sort');

            // Опционально (MySQL 8+):
            // $t->check("locale IN ('ru','en','kk')");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_categories');
    }
};
