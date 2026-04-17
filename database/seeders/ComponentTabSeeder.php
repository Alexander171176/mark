<?php

namespace Database\Seeders;

use App\Models\Admin\Constructor\HomePage\Component\ComponentSection;
use App\Models\Admin\Constructor\HomePage\Component\ComponentTab;
use Illuminate\Database\Seeder;

class ComponentTabSeeder extends Seeder
{
    public function run(): void
    {
        $locales = ['en', 'ru', 'kk'];

        // Получаем секции по локалям
        $sections = ComponentSection::whereIn('locale', $locales)->get()->keyBy('locale');

        // Слоты вкладок со slug и сортировкой (slug — общий для всех языков)
        $tabs = [
            ['slug' => 'base',         'sort' => 1],
            ['slug' => 'intermediate', 'sort' => 2],
            ['slug' => 'blocks',       'sort' => 3],
            ['slug' => 'advanced',     'sort' => 4],
            ['slug' => 'utilities',    'sort' => 5],
        ];

        // Локализованные подписи (если не нужно — можно вернуть английские)
        $labels = [
            'en' => [
                'base'         => 'Base',
                'intermediate' => 'Intermediate',
                'blocks'       => 'Blocks',
                'advanced'     => 'Advanced',
                'utilities'    => 'Utilities',
            ],
            'ru' => [
                'base'         => 'База',
                'intermediate' => 'Средний',
                'blocks'       => 'Блоки',
                'advanced'     => 'Продвинутые',
                'utilities'    => 'Утилиты',
            ],
            'kk' => [
                'base'         => 'Негізгі',
                'intermediate' => 'Орта',
                'blocks'       => 'Блоктар',
                'advanced'     => 'Кеңейтілген',
                'utilities'    => 'Құралдар',
            ],
        ];

        foreach ($locales as $loc) {
            $section = $sections[$loc] ?? null;
            if (!$section) continue;

            foreach ($tabs as $t) {
                $label = $labels[$loc][$t['slug']] ?? $labels['en'][$t['slug']];

                ComponentTab::updateOrCreate(
                    ['section_id' => $section->id, 'slug' => $t['slug']], // ключ ниже уровня секции
                    [
                        'label'    => $label,
                        'sort'     => $t['sort'],
                        'activity' => true,
                    ]
                );
            }
        }
    }
}
