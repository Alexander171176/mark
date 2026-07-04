<?php

namespace Database\Seeders;

use App\Models\Admin\Market\MarketAttribute\MarketAttribute;
use App\Models\Admin\Market\MarketAttributeValue\MarketAttributeValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MarketAttributeValueSeeder extends Seeder
{
    /**
     * Запуск сидера.
     */
    public function run(): void
    {
        DB::transaction(function () {
            foreach ($this->values() as $index => $item) {
                $attribute = MarketAttribute::where('code', $item['attribute_code'])->first();

                if (!$attribute) {
                    continue;
                }

                $value = MarketAttributeValue::updateOrCreate(
                    [
                        'market_attribute_id' => $attribute->id,
                        'code' => $item['code'],
                    ],
                    [
                        'icon' => null,
                        'color' => $item['color'],

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
                    $value->translations()->updateOrCreate(
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
     * Данные значений характеристик.
     */
    protected function values(): array
    {
        return [
            $this->valueBlueprint('brand-country', 'kazakhstan', '#22c55e', 'Казахстан', 'Kazakhstan', 'Қазақстан'),
            $this->valueBlueprint('brand-country', 'china', '#ef4444', 'Китай', 'China', 'Қытай'),
            $this->valueBlueprint('brand-country', 'germany', '#f97316', 'Германия', 'Germany', 'Германия'),

            $this->valueBlueprint('body-material', 'metal', '#64748b', 'Металл', 'Metal', 'Металл'),
            $this->valueBlueprint('body-material', 'plastic', '#14b8a6', 'Пластик', 'Plastic', 'Пластик'),
            $this->valueBlueprint('body-material', 'aluminum', '#94a3b8', 'Алюминий', 'Aluminum', 'Алюминий'),

            $this->valueBlueprint('color', 'black', '#111827', 'Чёрный', 'Black', 'Қара'),
            $this->valueBlueprint('color', 'white', '#f8fafc', 'Белый', 'White', 'Ақ'),
            $this->valueBlueprint('color', 'red', '#ef4444', 'Красный', 'Red', 'Қызыл'),
            $this->valueBlueprint('color', 'blue', '#3b82f6', 'Синий', 'Blue', 'Көк'),

            $this->valueBlueprint('protection-class', 'ip20', '#84cc16', 'IP20', 'IP20', 'IP20'),
            $this->valueBlueprint('protection-class', 'ip44', '#22c55e', 'IP44', 'IP44', 'IP44'),
            $this->valueBlueprint('protection-class', 'ip65', '#0ea5e9', 'IP65', 'IP65', 'IP65'),
        ];
    }

    /**
     * Шаблон значения характеристики.
     */
    protected function valueBlueprint(
        string $attributeCode,
        string $code,
        ?string $color,
        string $titleRu,
        string $titleEn,
        string $titleKk
    ): array {
        return [
            'attribute_code' => $attributeCode,
            'code' => $code,
            'color' => $color,

            'translations' => [
                'ru' => [
                    'title' => $titleRu,
                    'subtitle' => 'Значение характеристики',
                    'short' => 'Значение "' . $titleRu . '" используется в характеристиках товара.',
                    'description' => 'Значение "' . $titleRu . '" применяется для выбора, фильтрации и отображения характеристик товаров в маркетплейсе.',
                ],

                'en' => [
                    'title' => $titleEn,
                    'subtitle' => 'Attribute value',
                    'short' => 'The "' . $titleEn . '" value is used in product attributes.',
                    'description' => 'The "' . $titleEn . '" value is used for selecting, filtering and displaying product attributes in the marketplace.',
                ],

                'kk' => [
                    'title' => $titleKk,
                    'subtitle' => 'Сипаттама мәні',
                    'short' => '"' . $titleKk . '" мәні тауар сипаттамаларында қолданылады.',
                    'description' => '"' . $titleKk . '" мәні маркетплейсте тауар сипаттамаларын таңдау, сүзу және көрсету үшін қолданылады.',
                ],
            ],
        ];
    }
}
