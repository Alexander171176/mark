<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HeroSectionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $rows = [
            // EN
            [
                'locale'               => 'en',
                'title'                => 'Hello, I am Vulk.',
                'subtitle'             => 'Vue 3 Landing Page UI - v2.1.2',
                'badge_text'           => 'SSG/SSR Ready',
                'description'          => 'Vulk is a premium toolkit that lets you build powerful, SSR-Ready memorable websites.',
                'primary_btn_text'     => 'Demos',
                'primary_btn_url'      => '#',
                'primary_btn_target'   => '_self',
                'secondary_btn_text'   => 'Components',
                'secondary_btn_url'    => '#',
                'secondary_btn_target' => '_self',
                'is_dark'              => false,
                'activity'             => true,
                'created_at'           => $now,
                'updated_at'           => $now,
            ],

            // RU
            [
                'locale'               => 'ru',
                'title'                => 'Привет, я Vulk.',
                'subtitle'             => 'UI лендинга на Vue 3 — v2.1.2',
                'badge_text'           => 'Готов к SSG/SSR',
                'description'          => 'Vulk — это премиальный набор инструментов для создания мощных и запоминающихся сайтов с поддержкой SSR.',
                'primary_btn_text'     => 'Демо',
                'primary_btn_url'      => '#',
                'primary_btn_target'   => '_self',
                'secondary_btn_text'   => 'Компоненты',
                'secondary_btn_url'    => '#',
                'secondary_btn_target' => '_self',
                'is_dark'              => false,
                'activity'             => true,
                'created_at'           => $now,
                'updated_at'           => $now,
            ],

            // KK
            [
                'locale'               => 'kk',
                'title'                => 'Сәлем, мен Vulk.',
                'subtitle'             => 'Vue 3 Landing Page UI — v2.1.2',
                'badge_text'           => 'SSG/SSR Дайын',
                'description'          => 'Vulk — қуатты, SSR-ға дайын, есте қаларлық сайттарды тез жасауға арналған премиум құралдар жиынтығы.',
                'primary_btn_text'     => 'Демо',
                'primary_btn_url'      => '#',
                'primary_btn_target'   => '_self',
                'secondary_btn_text'   => 'Компоненттер',
                'secondary_btn_url'    => '#',
                'secondary_btn_target' => '_self',
                'is_dark'              => false,
                'activity'             => true,
                'created_at'           => $now,
                'updated_at'           => $now,
            ],
        ];

        DB::table('hero_sections')->upsert(
            $rows,
            ['locale'],
            [
                'title','subtitle','badge_text','description',
                'primary_btn_text','primary_btn_url','primary_btn_target',
                'secondary_btn_text','secondary_btn_url','secondary_btn_target',
                'is_dark','activity','updated_at'
            ]
        );
    }
}
