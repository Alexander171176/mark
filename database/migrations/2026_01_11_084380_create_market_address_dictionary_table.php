<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_address_dictionary', function (Blueprint $table) {
            $table->id()->comment('ID записи справочника адресов');

            $table->boolean('activity')->default(true)->comment('Активность записи');
            $table->unsignedInteger('sort')->default(0)->comment('Сортировка');

            /**
             * Иерархия (регион → город → район → улица ...)
             * ЕДИНЫЙ ПОДХОД: если удалён родитель — удаляем всю ветку (cascade)
             */
            $table->unsignedBigInteger('parent_id')
                ->nullable()
                ->comment('Родитель (market_address_dictionary.id)');

            $table->string('type', 32)->default('city')
                ->comment('Тип: country/region/city/district/settlement/street/...');

            $table->string('country_code', 2)->comment('ISO2 страны (KZ/...)');
            $table->string('region', 128)->nullable()->comment('Область/регион');
            $table->string('city', 128)->nullable()->comment('Город');
            $table->string('district', 128)->nullable()->comment('Район/микрорайон/округ');
            $table->string('settlement', 128)->nullable()->comment('Населённый пункт (опционально)');

            // Почтовые индексы
            $table->string('postcode', 16)->nullable()->comment('Почтовый индекс (точный)');
            $table->string('postcode_from', 16)->nullable()->comment('Диапазон индекса: от');
            $table->string('postcode_to', 16)->nullable()->comment('Диапазон индекса: до');

            // Координаты
            $table->decimal('lat', 10, 7)->nullable()->comment('Широта');
            $table->decimal('lng', 10, 7)->nullable()->comment('Долгота');

            // Внешний ключ из сторонних справочников/провайдеров
            $table->string('external_id', 64)->nullable()->comment('Внешний ID (провайдер/импорт)');

            $table->string('title', 255)->nullable()->comment('Человекочитаемое имя');
            $table->json('meta')->nullable()->comment('Доп. данные (fias/kladr/geojson/алиасы/...)');

            $table->timestamps();

            /**
             * Индексы / уникальности
             */
            $table->unique(['country_code', 'external_id'], 'uq_market_addr_country_external');

            $table->unique(
                ['country_code', 'type', 'region', 'city', 'district', 'settlement', 'postcode'],
                'uq_market_addr_path'
            );

            $table->index(['activity', 'sort'], 'ix_market_addr_activity_sort');
            $table->index(['parent_id', 'activity', 'sort'], 'ix_market_addr_parent_activity_sort');

            $table->index(['country_code', 'type'], 'ix_market_addr_country_type');
            $table->index(['country_code', 'region', 'city'], 'ix_market_addr_country_region_city');
            $table->index(['country_code', 'city'], 'ix_market_addr_country_city');
            $table->index(['country_code', 'district'], 'ix_market_addr_country_district');

            $table->index('postcode', 'ix_market_addr_postcode');

            /**
             * SELF FK — в конце (как и в категориях)
             * parent_id nullable, но поведение удаления — CASCADE (удаляем ветку)
             */
            $table->foreign('parent_id', 'fk_market_addr_parent')
                ->references('id')
                ->on('market_address_dictionary')
                ->cascadeOnDelete();

            $table->comment('Маркет: справочник адресов (иерархия), удаление родителя удаляет всю ветку (cascade)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_address_dictionary');
    }
};
