<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_delivery_zone_geometries', function (Blueprint $table) {

            /* =========================================================
             * BASE
             * ========================================================= */

            $table->id()->comment('ID геометрии зоны доставки');

            /* =========================================================
             * LINKS (поля)
             * ========================================================= */

            $table->unsignedBigInteger('delivery_zone_id')
                ->comment('Зона доставки (market_delivery_zones.id)');

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность геометрии');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка геометрий внутри зоны');

            /* =========================================================
             * GEOJSON
             * ========================================================= */

            $table->unsignedSmallInteger('geojson_version')->default(1)->comment('Версия структуры GeoJSON');

            $table->json('geojson')->nullable()->comment('GeoJSON geometry (Polygon / MultiPolygon)');

            /* =========================================================
             * BBOX (pre-filter)
             * ========================================================= */

            $table->decimal('bbox_min_lat', 10, 7)->nullable()->comment('BBox min latitude');
            $table->decimal('bbox_min_lng', 10, 7)->nullable()->comment('BBox min longitude');
            $table->decimal('bbox_max_lat', 10, 7)->nullable()->comment('BBox max latitude');
            $table->decimal('bbox_max_lng', 10, 7)->nullable()->comment('BBox max longitude');

            /* =========================================================
             * META
             * ========================================================= */

            $table->string('title', 255)->nullable()->comment('Название геометрии (часть зоны/остров)');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // Быстрый список геометрий зоны (обычно только активные)
            $table->index(['delivery_zone_id', 'activity', 'sort'], 'ix_mdzg_zone_list');

            // Порядок геометрий внутри зоны
            $table->index(['delivery_zone_id', 'sort'], 'ix_mdzg_zone_sort');

            // bbox pre-filter
            $table->index(['bbox_min_lat', 'bbox_max_lat'], 'ix_mdzg_bbox_lat');
            $table->index(['bbox_min_lng', 'bbox_max_lng'], 'ix_mdzg_bbox_lng');

            /**
             * Опционально: защита от дублей sort внутри зоны.
             * Если хочешь разрешить одинаковый sort (черновики) — уберём.
             */
            $table->unique(['delivery_zone_id', 'sort'], 'uq_mdzg_zone_sort');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            $table->foreign('delivery_zone_id', 'fk_mdzg_zone')
                ->references('id')
                ->on('market_delivery_zones')
                ->cascadeOnDelete();

            $table->comment('Маркет: геометрии зон доставки (GeoJSON + bbox), для рисования и проверки на карте');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_delivery_zone_geometries');
    }
};
