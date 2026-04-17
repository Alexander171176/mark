<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_delivery_zone_coverage_rules', function (Blueprint $table) {

            /* =========================================================
             * BASE
             * ========================================================= */

            $table->id()->comment('ID правила покрытия зоны');

            /* =========================================================
             * LINKS (поля)
             * ========================================================= */

            $table->unsignedBigInteger('delivery_zone_id')
                ->comment('Зона доставки (market_delivery_zones.id)');

            /* =========================================================
             * FLAGS / SORT
             * ========================================================= */

            $table->boolean('activity')->default(true)->comment('Активность правила');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка правил внутри зоны');

            /* =========================================================
             * TYPE
             * ========================================================= */

            $table->string('type', 32)->comment('Тип правила: city|region|district|postcode|postcode_range|bbox|polygon');

            /* =========================================================
             * TEXT RULES
             * ========================================================= */

            $table->string('country_code', 2)->nullable()->comment('ISO2 страны (KZ/...)');
            $table->string('region', 128)->nullable()->comment('Область/регион');
            $table->string('city', 128)->nullable()->comment('Город');
            $table->string('district', 128)->nullable()->comment('Район/микрорайон');

            /* =========================================================
             * POSTCODE RULES
             * ========================================================= */

            $table->string('postcode', 16)->nullable()->comment('Точный индекс');
            $table->string('postcode_from', 16)->nullable()->comment('Диапазон индекса: от');
            $table->string('postcode_to', 16)->nullable()->comment('Диапазон индекса: до');

            /* =========================================================
             * BBOX RULE
             * ========================================================= */

            $table->decimal('bbox_min_lat', 10, 7)->nullable()->comment('BBox min latitude');
            $table->decimal('bbox_min_lng', 10, 7)->nullable()->comment('BBox min longitude');
            $table->decimal('bbox_max_lat', 10, 7)->nullable()->comment('BBox max latitude');
            $table->decimal('bbox_max_lng', 10, 7)->nullable()->comment('BBox max longitude');

            /* =========================================================
             * POLYGON RULE
             * ========================================================= */

            $table->json('polygon')->nullable()->comment('GeoJSON Polygon/MultiPolygon или массив координат');

            $table->string('note', 255)->nullable()->comment('Комментарий/описание правила');

            $table->timestamps();

            /* =========================================================
             * UNIQUE / INDEXES
             * ========================================================= */

            // “Опорный” уникальный ключ на случай composite-FK из других таблиц
            $table->unique(['delivery_zone_id', 'id'], 'uq_mdzcr_zone_id');

            // Основная выборка правил зоны (в порядке сортировки)
            $table->index(['delivery_zone_id', 'activity', 'sort'], 'ix_mdzcr_zone_list');

            // Фильтр по типу внутри зоны (и сортировка)
            $table->index(['delivery_zone_id', 'type', 'activity', 'sort'], 'ix_mdzcr_zone_type_list');

            // Быстрая выборка по типу (аналитика/валидаторы)
            $table->index(['type', 'activity'], 'ix_mdzcr_type_active');

            // Индексы под проверки покрытий
            $table->index(['country_code', 'city'], 'ix_mdzcr_country_city');
            $table->index(['country_code', 'region'], 'ix_mdzcr_country_region');
            $table->index(['country_code', 'region', 'city'], 'ix_mdzcr_country_region_city');
            $table->index(['postcode'], 'ix_mdzcr_postcode');

            // Для диапазонов индексов
            $table->index(['postcode_from', 'postcode_to'], 'ix_mdzcr_postcode_range');

            // bbox pre-filter
            $table->index(['bbox_min_lat', 'bbox_max_lat'], 'ix_mdzcr_bbox_lat');
            $table->index(['bbox_min_lng', 'bbox_max_lng'], 'ix_mdzcr_bbox_lng');

            /**
             * Опционально: жёстко запретить дубли sort внутри зоны.
             * Если нужно — включай.
             */
            // $table->unique(['delivery_zone_id', 'sort'], 'uq_mdzcr_zone_sort');

            /* =========================================================
             * FOREIGN KEYS (в конце)
             * ========================================================= */

            $table->foreign('delivery_zone_id', 'fk_mdzcr_zone')
                ->references('id')
                ->on('market_delivery_zones')
                ->cascadeOnDelete();

            $table->comment('Маркет: правила покрытия зоны доставки (город/регион/индекс/bbox/polygon)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_delivery_zone_coverage_rules');
    }
};
