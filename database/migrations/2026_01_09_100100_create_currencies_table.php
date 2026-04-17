<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('sort')->default(0);

            // ISO 4217: строго 3 символа
            $table->char('code', 3)->unique();

            $table->string('name', 64);
            $table->string('symbol', 8)->nullable();

            $table->unsignedTinyInteger('precision')->default(2);
            $table->boolean('symbol_first')->default(false);

            $table->string('thousands_sep', 2)->default(' ');
            $table->string('decimal_sep', 2)->default('.');

            $table->boolean('activity')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamp('set_default_at')->nullable();

            $table->timestamps();

            $table->index(['activity', 'is_default']);
            $table->index(['activity', 'sort'], 'idx_currencies_active_sort');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
