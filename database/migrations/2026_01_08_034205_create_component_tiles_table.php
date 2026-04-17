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
        Schema::create('component_tiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tab_id')
                ->constrained('component_tabs')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('href', 1024); // ссылка на страницу блока
            $table->string('title');      // подпись под иконкой

            // ALT'ы для картинок (сами изображения — через Spatie Media Library)
            $table->string('light_alt')->nullable();
            $table->string('dark_alt')->nullable();

            $table->unsignedInteger('sort')->default(0);
            $table->boolean('activity')->default(true);

            $table->timestamps();

            $table->index(['tab_id', 'activity', 'sort']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('component_tiles');
    }
};
