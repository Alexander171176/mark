<?php

namespace Database\Seeders;

use App\Models\Admin\Constructor\HomePage\Developer\DeveloperSection;
use Illuminate\Database\Seeder;

class DeveloperSectionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['ru', 'en', 'kk'] as $i => $locale) {
            DeveloperSection::updateOrCreate(
                ['locale' => $locale],
                [
                    'title'     => 'Build a professional website',
                    'subtitle'  => 'Developer Experience',
                    'sort'      => 3 + $i,
                    'is_dark'   => false,
                    'activity'  => true,
                ]
            );
        }
    }
}
