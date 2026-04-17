<?php

namespace Database\Seeders;

use App\Models\Admin\Constructor\HomePage\Demo\DemoSection;
use Illuminate\Database\Seeder;

class DemoSectionSeeder extends Seeder
{
    public function run(): void
    {
        // Заголовки секции для RU/EN/KK
        $sectionsByLocale = [
            'ru' => [
                'title'    => 'Красивые демо-страницы',
                'subtitle' => 'Премиальные дизайны',
                'sort'     => 0,
                'is_dark'  => false,
                'activity' => true,
            ],
            'en' => [
                'title'    => 'Beautiful demo pages',
                'subtitle' => 'Premium Designs',
                'sort'     => 0,
                'is_dark'  => false,
                'activity' => true,
            ],
            'kk' => [
                'title'    => 'Әдемі демо беттер',
                'subtitle' => 'Премиум дизайндар',
                'sort'     => 0,
                'is_dark'  => false,
                'activity' => true,
            ],
        ];

        foreach ($sectionsByLocale as $locale => $payload) {
            DemoSection::updateOrCreate(
                ['locale' => $locale],
                $payload
            );
        }
    }
}
