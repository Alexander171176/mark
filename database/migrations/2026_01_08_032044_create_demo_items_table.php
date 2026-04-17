<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('demo_items', function (Blueprint $table) {
            $table->id();

            // Родительский блок
            $table->foreignId('group_id')
                ->constrained('demo_groups')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Данные карточки
            $table->string('href', 2048);     // ссылка на демо
            $table->string('title');          // "Landing 1", "About 2", ...
            $table->string('category')->nullable(); // "Marketing", "Startup", "Pricing", ...
            $table->string('alt')->nullable();      // Alt для превью

            // Управление отображением
            $table->integer('sort')->default(0);
            $table->boolean('activity')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_items');
    }
};
