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
        DB::transaction(function () {
            foreach ($this->attributes() as $index => $item) {
                $group = MarketAttributeGroup::where('code', $item['group_code'])->first();

                if (!$group) {
                    continue;
                }

                $attribute = MarketAttribute::updateOrCreate(
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
                        'visible' => true,

                        'sort' => $index,
                        'activity' => true,

                        'status' => 'published',
                        'moderation_status' => 1,
                        'moderated_by' => 1,
                        'moderated_at' => now(),
                        'moderation_note' => null,

                        'published_at' => Carbon::now()->subDays($index),
                        'show_from_at' => Carbon::now()->subDays($index)->startOfDay(),
                        'show_to_at' => Carbon::now()->addYear()->endOfDay(),
                    ]
                );

                foreach ($item['translations'] as $locale => $translation) {
                    $attribute->translations()->updateOrCreate(
                        [
                            'locale' => $locale,
                        ],
                        [
                            'title' => $translation['title'],
                            'subtitle' => $translation['subtitle'],
                            'short' => $translation['short'],
                            'description' => $translation['description'],
                        ]
                    );
                }
            }
        });
    }

    /**
     * Данные характеристик.
     */
    protected function attributes(): array
    {
        return [
            $this->attributeBlueprint('general', 'brand-country', 'select', null, '#3b82f6', true, true, 'Страна бренда', 'Brand country', 'Бренд елі'),
            $this->attributeBlueprint('general', 'product-series', 'string', null, '#3b82f6', false, true, 'Серия товара', 'Product series', 'Тауар сериясы'),

            $this->attributeBlueprint('dimensions', 'weight', 'decimal', 'kg', '#14b8a6', false, true, 'Вес', 'Weight', 'Салмақ'),
            $this->attributeBlueprint('dimensions', 'length', 'decimal', 'mm', '#14b8a6', false, true, 'Длина', 'Length', 'Ұзындығы'),
            $this->attributeBlueprint('dimensions', 'width', 'decimal', 'mm', '#14b8a6', false, true, 'Ширина', 'Width', 'Ені'),

            $this->attributeBlueprint('materials', 'body-material', 'select', null, '#f97316', false, true, 'Материал корпуса', 'Body material', 'Корпус материалы'),
            $this->attributeBlueprint('materials', 'color', 'select', null, '#f97316', false, true, 'Цвет', 'Color', 'Түс'),

            $this->attributeBlueprint('electrical', 'voltage', 'decimal', 'V', '#eab308', false, true, 'Напряжение', 'Voltage', 'Кернеу'),
            $this->attributeBlueprint('electrical', 'power', 'decimal', 'W', '#eab308', false, true, 'Мощность', 'Power', 'Қуат'),
            $this->attributeBlueprint('electrical', 'frequency', 'decimal', 'Hz', '#eab308', false, true, 'Частота', 'Frequency', 'Жиілік'),

            $this->attributeBlueprint('operation', 'protection-class', 'select', null, '#22c55e', false, true, 'Степень защиты', 'Protection class', 'Қорғаныс дәрежесі'),
            $this->attributeBlueprint('operation', 'working-temperature', 'string', '°C', '#22c55e', false, false, 'Рабочая температура', 'Working temperature', 'Жұмыс температурасы'),

            $this->attributeBlueprint('package-warranty', 'warranty-period', 'integer', 'months', '#8b5cf6', false, true, 'Гарантийный срок', 'Warranty period', 'Кепілдік мерзімі'),
            $this->attributeBlueprint('package-warranty', 'package-included', 'text', null, '#8b5cf6', false, false, 'Комплектация', 'Package included', 'Жинақ құрамы'),
        ];
    }

    /**
     * Шаблон характеристики.
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
        string $titleKk
    ): array {
        return [
            'group_code' => $groupCode,
            'code' => $code,
            'type' => $type,
            'unit' => $unit,
            'color' => $color,
            'required' => $required,
            'filterable' => $filterable,

            'translations' => [
                'ru' => [
                    'title' => $titleRu,
                    'subtitle' => 'Характеристика товара',
                    'short' => 'Характеристика "' . $titleRu . '" используется для описания товара.',
                    'description' => 'Характеристика "' . $titleRu . '" помогает структурировать данные товара, использовать фильтры и выводить важную информацию в карточке товара.',
                ],

                'en' => [
                    'title' => $titleEn,
                    'subtitle' => 'Product attribute',
                    'short' => 'The "' . $titleEn . '" attribute is used to describe a product.',
                    'description' => 'The "' . $titleEn . '" attribute helps structure product data, use filters and display important information in the product card.',
                ],

                'kk' => [
                    'title' => $titleKk,
                    'subtitle' => 'Тауар сипаттамасы',
                    'short' => '"' . $titleKk . '" сипаттамасы тауарды сипаттау үшін қолданылады.',
                    'description' => '"' . $titleKk . '" сипаттамасы тауар деректерін құрылымдауға, сүзгілерді қолдануға және карточкада маңызды ақпаратты көрсетуге көмектеседі.',
                ],
            ],
        ];
    }
}
