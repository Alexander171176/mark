<?php

namespace Database\Seeders;

use App\Models\Admin\Constructor\HomePage\Reason\ReasonSection;
use Illuminate\Database\Seeder;

class ReasonSectionSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'ru' => [
                'subtitle'       => 'Вам понравится этот продукт',
                'title'          => '3 причины выбрать Vulk',
                'cta_title'      => 'Эксклюзивно на Envato Market',
                'cta_btn_text'   => 'Купить сейчас',
                'cta_btn_url'    => 'https://go.cssninja.io/buy-vulk',
                'cta_btn_target' => '_blank',
            ],
            'en' => [
                'subtitle'       => "You'll love this product",          // h3
                'title'          => '3 Reasons to choose Vulk',          // h2
                'cta_title'      => 'Exclusively on Envato Market',
                'cta_btn_text'   => 'Get It Now',
                'cta_btn_url'    => 'https://go.cssninja.io/buy-vulk',
                'cta_btn_target' => '_blank',
            ],
            'kk' => [
                'subtitle'       => 'Сізге бұл өнім ұнайды',
                'title'          => 'Vulk-ты таңдаудың 3 себебі',
                'cta_title'      => 'Тек Envato Market-те',
                'cta_btn_text'   => 'Қазір сатып ал',
                'cta_btn_url'    => 'https://go.cssninja.io/buy-vulk',
                'cta_btn_target' => '_blank',
            ],
        ];

        $sort = 30;

        foreach ($data as $locale => $payload) {
            ReasonSection::updateOrCreate(
                ['locale' => $locale],
                [
                    'subtitle'       => $payload['subtitle'],
                    'title'          => $payload['title'],
                    'cta_title'      => $payload['cta_title'],
                    'cta_btn_text'   => $payload['cta_btn_text'],
                    'cta_btn_url'    => $payload['cta_btn_url'],
                    'cta_btn_target' => $payload['cta_btn_target'],
                    'sort'           => $sort,
                    'activity'       => true,
                ]
            );
        }
    }
}
