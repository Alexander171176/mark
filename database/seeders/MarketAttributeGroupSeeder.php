<?php

namespace Database\Seeders;

use App\Models\Admin\Market\MarketAttributeGroup\MarketAttributeGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MarketAttributeGroupSeeder extends Seeder
{
    /**
     * Запуск сидера.
     */
    public function run(): void
    {
        DB::transaction(function () {
            foreach ($this->groups() as $index => $item) {
                $group = MarketAttributeGroup::updateOrCreate(
                    [
                        'code' => $item['code'],
                    ],
                    [
                        'user_id' => 1,

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
                    $group->translations()->updateOrCreate(
                        [
                            'locale' => $locale,
                        ],
                        [
                            'title' => $translation['title'],
                            'subtitle' => $translation['subtitle'],
                            'short' => $translation['short'],
                        ]
                    );
                }
            }
        });
    }

    /**
     * Данные групп характеристик.
     */
    protected function groups(): array
    {
        return [
            $this->groupBlueprint(
                'Основные характеристики',
                'general',
                '#3b82f6',
                'General attributes',
                'Негізгі сипаттамалар'
            ),

            $this->groupBlueprint(
                'Размеры и вес',
                'dimensions',
                '#14b8a6',
                'Dimensions and weight',
                'Өлшемдер мен салмақ'
            ),

            $this->groupBlueprint(
                'Материалы и конструкция',
                'materials',
                '#f97316',
                'Materials and construction',
                'Материалдар мен құрылым'
            ),

            $this->groupBlueprint(
                'Электрические параметры',
                'electrical',
                '#eab308',
                'Electrical parameters',
                'Электрлік параметрлер'
            ),

            $this->groupBlueprint(
                'Эксплуатация',
                'operation',
                '#22c55e',
                'Operation',
                'Пайдалану'
            ),

            $this->groupBlueprint(
                'Комплектация и гарантия',
                'package-warranty',
                '#8b5cf6',
                'Package and warranty',
                'Жинақ және кепілдік'
            ),
        ];
    }

    /**
     * Шаблон группы характеристик.
     */
    protected function groupBlueprint(
        string $titleRu,
        string $code,
        ?string $color = null,
        ?string $titleEn = null,
        ?string $titleKk = null
    ): array {
        $titleEn = $titleEn ?: $titleRu;
        $titleKk = $titleKk ?: $titleRu;

        return [
            'code' => $code,
            'color' => $color,

            'translations' => [
                'ru' => [
                    'title' => $titleRu,
                    'subtitle' => 'Группа характеристик маркетплейса',
                    'short' => 'Группа "' . $titleRu . '" объединяет связанные характеристики товаров.',
                ],

                'en' => [
                    'title' => $titleEn,
                    'subtitle' => 'Marketplace attribute group',
                    'short' => 'The "' . $titleEn . '" group combines related product attributes.',
                ],

                'kk' => [
                    'title' => $titleKk,
                    'subtitle' => 'Маркетплейс сипаттамалар тобы',
                    'short' => '"' . $titleKk . '" тобы тауарлардың байланысты сипаттамаларын біріктіреді.',
                ],
            ],
        ];
    }
}
