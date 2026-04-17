<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeatureSectionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $rows = [
            [
                'locale'     => 'ru',
                'title'      => 'Всё, что нужно для вашего проекта',
                'subtitle'   => 'Потрясающие возможности',
                'sort'       => 0,
                'is_dark'    => false,
                'activity'   => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'locale'     => 'en',
                'title'      => 'All you need to build your project is there',
                'subtitle'   => 'Amazing Features',
                'sort'       => 0,
                'is_dark'    => false,
                'activity'   => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'locale'     => 'kk',
                'title'      => 'Жобаңыз үшін қажет нәрсенің бәрі',
                'subtitle'   => 'Тамаша мүмкіндіктер',
                'sort'       => 0,
                'is_dark'    => false,
                'activity'   => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // unique по locale — можно гонять повторно
        DB::table('feature_sections')->upsert(
            $rows,
            ['locale'],
            ['title','subtitle','sort','is_dark','activity','updated_at']
        );
    }
}
