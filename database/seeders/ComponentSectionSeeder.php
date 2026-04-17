<?php

namespace Database\Seeders;

use App\Models\Admin\Constructor\HomePage\Component\ComponentSection;
use Illuminate\Database\Seeder;

class ComponentSectionSeeder extends Seeder
{
    public function run(): void
    {
        // Все локали, которые хотим поддерживать
        $locales = ['en', 'ru', 'kk'];

        // Тексты по локалям (можно оставить английские везде, если перевод не нужен)
        $texts = [
            'en' => [
                'subtitle' => 'Reusable Components',
                'title'    => 'Explore the Component Library',
                'cta_text' => 'All Components',
            ],
            'ru' => [
                'subtitle' => 'Многоразовые компоненты',
                'title'    => 'Исследуйте библиотеку компонентов',
                'cta_text' => 'Все компоненты',
            ],
            'kk' => [
                'subtitle' => 'Қайталанатын компоненттер',
                'title'    => 'Компоненттер кітапханасын зерттеңіз',
                'cta_text' => 'Барлық компоненттер',
            ],
        ];

        foreach ($locales as $locale) {
            $t = $texts[$locale] ?? $texts['en'];

            // Один раздел на локаль (ключ — locale)
            ComponentSection::updateOrCreate(
                ['locale' => $locale],
                [
                    'subtitle' => $t['subtitle'],
                    'title'    => $t['title'],
                    'cta_text' => $t['cta_text'],
                    'cta_url'  => '/blocks',
                    'sort'     => 0,
                    'activity' => true,
                ]
            );
        }
    }
}
