<?php

namespace Database\Seeders;

use App\Models\Admin\Market\MarketAttribute\MarketAttribute;
use App\Models\Admin\Market\MarketAttributeGroup\MarketAttributeGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MarketAttributeSeeder extends Seeder
{
    /**
     * Запуск сидера.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            foreach ($this->attributes() as $index => $item) {
                $group = MarketAttributeGroup::query()
                    ->where('code', $item['group_code'])
                    ->first();

                if (! $group) {
                    $this->command?->warn(
                        "MarketAttributeSeeder: группа {$item['group_code']} не найдена."
                    );

                    continue;
                }

                $attribute = MarketAttribute::query()
                    ->updateOrCreate(
                        [
                            'code' => $item['code'],
                        ],
                        [
                            'market_attribute_group_id' => $group->id,
                            'user_id' => 1,

                            'icon' => null,
                            'color' => $item['color'],
                            'type' => $item['type'],
                            'unit' => $item['unit'],

                            'required' => $item['required'],
                            'filterable' => $item['filterable'],

                            /**
                             * Разрешение использовать характеристику
                             * при формировании вариантов товара.
                             */
                            'use_for_variants' =>
                                $item['use_for_variants'],

                            'visible' => true,

                            'sort' => $index,
                            'activity' => true,

                            'status' => 'published',

                            'moderation_status' => 1,
                            'moderated_by' => 1,
                            'moderated_at' => now(),
                            'moderation_note' => null,

                            'published_at' => Carbon::now()
                                ->subDays($index),

                            'show_from_at' => Carbon::now()
                                ->subDays($index)
                                ->startOfDay(),

                            'show_to_at' => Carbon::now()
                                ->addYear()
                                ->endOfDay(),
                        ]
                    );

                foreach (
                    $item['translations'] as $locale => $translation
                ) {
                    $attribute->translations()->updateOrCreate(
                        [
                            'locale' => $locale,
                        ],
                        [
                            'title' => $translation['title'],
                            'subtitle' => $translation['subtitle'],
                            'short' => $translation['short'],
                            'description' =>
                                $translation['description'],
                        ]
                    );
                }
            }
        });

        $this->command?->info(
            'Характеристики товаров успешно созданы.'
        );
    }

    /**
     * Данные характеристик.
     *
     * Для вариантов товаров используются:
     * - body-material;
     * - color.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function attributes(): array
    {
        return [
            $this->attributeBlueprint(
                groupCode: 'general',
                code: 'brand-country',
                type: 'select',
                unit: null,
                color: '#3b82f6',
                required: true,
                filterable: true,
                titleRu: 'Страна бренда',
                titleEn: 'Brand country',
                titleKk: 'Бренд елі'
            ),

            $this->attributeBlueprint(
                groupCode: 'general',
                code: 'product-series',
                type: 'string',
                unit: null,
                color: '#3b82f6',
                required: false,
                filterable: true,
                titleRu: 'Серия товара',
                titleEn: 'Product series',
                titleKk: 'Тауар сериясы'
            ),

            $this->attributeBlueprint(
                groupCode: 'dimensions',
                code: 'weight',
                type: 'decimal',
                unit: 'kg',
                color: '#14b8a6',
                required: false,
                filterable: true,
                titleRu: 'Вес',
                titleEn: 'Weight',
                titleKk: 'Салмақ'
            ),

            $this->attributeBlueprint(
                groupCode: 'dimensions',
                code: 'length',
                type: 'decimal',
                unit: 'mm',
                color: '#14b8a6',
                required: false,
                filterable: true,
                titleRu: 'Длина',
                titleEn: 'Length',
                titleKk: 'Ұзындығы'
            ),

            $this->attributeBlueprint(
                groupCode: 'dimensions',
                code: 'width',
                type: 'decimal',
                unit: 'mm',
                color: '#14b8a6',
                required: false,
                filterable: true,
                titleRu: 'Ширина',
                titleEn: 'Width',
                titleKk: 'Ені'
            ),

            /**
             * Характеристика используется
             * для формирования вариантов.
             */
            $this->attributeBlueprint(
                groupCode: 'materials',
                code: 'body-material',
                type: 'select',
                unit: null,
                color: '#f97316',
                required: false,
                filterable: true,
                titleRu: 'Материал корпуса',
                titleEn: 'Body material',
                titleKk: 'Корпус материалы',
                useForVariants: true
            ),

            /**
             * Основная характеристика вариантов.
             */
            $this->attributeBlueprint(
                groupCode: 'materials',
                code: 'color',
                type: 'select',
                unit: null,
                color: '#f97316',
                required: false,
                filterable: true,
                titleRu: 'Цвет',
                titleEn: 'Color',
                titleKk: 'Түс',
                useForVariants: true
            ),

            $this->attributeBlueprint(
                groupCode: 'electrical',
                code: 'voltage',
                type: 'decimal',
                unit: 'V',
                color: '#eab308',
                required: false,
                filterable: true,
                titleRu: 'Напряжение',
                titleEn: 'Voltage',
                titleKk: 'Кернеу'
            ),

            $this->attributeBlueprint(
                groupCode: 'electrical',
                code: 'power',
                type: 'decimal',
                unit: 'W',
                color: '#eab308',
                required: false,
                filterable: true,
                titleRu: 'Мощность',
                titleEn: 'Power',
                titleKk: 'Қуат'
            ),

            $this->attributeBlueprint(
                groupCode: 'electrical',
                code: 'frequency',
                type: 'decimal',
                unit: 'Hz',
                color: '#eab308',
                required: false,
                filterable: true,
                titleRu: 'Частота',
                titleEn: 'Frequency',
                titleKk: 'Жиілік'
            ),

            $this->attributeBlueprint(
                groupCode: 'operation',
                code: 'protection-class',
                type: 'select',
                unit: null,
                color: '#22c55e',
                required: false,
                filterable: true,
                titleRu: 'Степень защиты',
                titleEn: 'Protection class',
                titleKk: 'Қорғаныс дәрежесі'
            ),

            $this->attributeBlueprint(
                groupCode: 'operation',
                code: 'working-temperature',
                type: 'string',
                unit: '°C',
                color: '#22c55e',
                required: false,
                filterable: false,
                titleRu: 'Рабочая температура',
                titleEn: 'Working temperature',
                titleKk: 'Жұмыс температурасы'
            ),

            $this->attributeBlueprint(
                groupCode: 'package-warranty',
                code: 'warranty-period',
                type: 'integer',
                unit: 'months',
                color: '#8b5cf6',
                required: false,
                filterable: true,
                titleRu: 'Гарантийный срок',
                titleEn: 'Warranty period',
                titleKk: 'Кепілдік мерзімі'
            ),

            $this->attributeBlueprint(
                groupCode: 'package-warranty',
                code: 'package-included',
                type: 'text',
                unit: null,
                color: '#8b5cf6',
                required: false,
                filterable: false,
                titleRu: 'Комплектация',
                titleEn: 'Package included',
                titleKk: 'Жинақ құрамы'
            ),
        ];
    }

    /**
     * Шаблон характеристики.
     *
     * @return array<string, mixed>
     */
    protected function attributeBlueprint(
        string $groupCode,
        string $code,
        string $type,
        ?string $unit,
        ?string $color,
        bool $required,
        bool $filterable,
        string $titleRu,
        string $titleEn,
        string $titleKk,
        bool $useForVariants = false
    ): array {
        return [
            'group_code' => $groupCode,
            'code' => $code,
            'type' => $type,
            'unit' => $unit,
            'color' => $color,

            'required' => $required,
            'filterable' => $filterable,

            /**
             * По умолчанию характеристика
             * не участвует в вариантах.
             */
            'use_for_variants' => $useForVariants,

            'translations' => [
                'ru' => [
                    'title' => $titleRu,
                    'subtitle' => 'Характеристика товара',

                    'short' =>
                        'Характеристика "'
                        . $titleRu
                        . '" используется для описания товара.',

                    'description' =>
                        'Характеристика "'
                        . $titleRu
                        . '" помогает структурировать данные товара, '
                        . 'использовать фильтры и выводить важную '
                        . 'информацию в карточке товара.',
                ],

                'en' => [
                    'title' => $titleEn,
                    'subtitle' => 'Product attribute',

                    'short' =>
                        'The "'
                        . $titleEn
                        . '" attribute is used to describe a product.',

                    'description' =>
                        'The "'
                        . $titleEn
                        . '" attribute helps structure product data, '
                        . 'use filters and display important '
                        . 'information in the product card.',
                ],

                'kk' => [
                    'title' => $titleKk,
                    'subtitle' => 'Тауар сипаттамасы',

                    'short' =>
                        '"'
                        . $titleKk
                        . '" сипаттамасы тауарды сипаттау үшін қолданылады.',

                    'description' =>
                        '"'
                        . $titleKk
                        . '" сипаттамасы тауар деректерін құрылымдауға, '
                        . 'сүзгілерді қолдануға және карточкада маңызды '
                        . 'ақпаратты көрсетуге көмектеседі.',
                ],
            ],
        ];
    }
}
