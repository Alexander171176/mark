<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Добавляем поле is_private
     */
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {

            $table->boolean('is_private')
                ->default(false)
                ->index('videos_is_private_idx')
                ->after('activity')
                ->comment('Приватное видео (не показывать в публичной галерее блога)');
        });
    }

    /**
     * Откат
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropIndex('videos_is_private_idx');
            $table->dropColumn('is_private');
        });
    }
};
