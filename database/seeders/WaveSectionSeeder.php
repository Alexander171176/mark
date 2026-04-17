<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WaveSectionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // RU
        $leftRu = <<<'TXT'
Vulk создан на базе Vue 3 и его новой Composition API.
Одно из ключевых изменений — возможность функционального стиля написания компонентов
(вдохновлено React Hooks). Это позволяет собирать компоненты за считанные минуты.
TXT;

        $rightRu = <<<'TXT'
Философия Vulk — ультра-модульность и высокая производительность.
Проект также следует стандартам веб-доступности. Хотя используется Sass,
все переменные — нативные CSS-переменные, что упрощает темизацию.
TXT;

        // EN
        $leftEn = <<<'TXT'
Vulk is built on top of Vue 3 and its new Composition API.
One of the most significant changes is that this new API allows function-based ways
of writing your components, inspired by React Hooks. It makes it easy to create components
in a matter of minutes.
TXT;

        $rightEn = <<<'TXT'
Vulk's philosophy is ultra modular and optimized for better performance.
It also provides other quality standards such as web accessibility. Although it is built with Sass,
all used variables are native CSS variables, which makes it a lot easier to theme.
TXT;

        // KK
        $leftKk = <<<'TXT'
Vulk Vue 3 және оның жаңа Composition API интерфейсінің негізінде жасалған.
Негізгі өзгерістердің бірі - компоненттерді функционалды стильде жазу мүмкіндігі (React Hooks шабыттандырған).
Бұл сізге компоненттерді бірнеше минут ішінде жинауға мүмкіндік береді.
TXT;

        $rightKk = <<<'TXT'
Vulk философиясы - ультра модульдік және жоғары өнімділік.
Жоба сонымен қатар веб-қолжетімділік стандарттарына сәйкес келеді. Sass қолданылғанымен,
барлық айнымалылар тақырыптарды жеңілдететін CSS айнымалылары болып табылады.
TXT;

        $rows = [
            [
                'locale'     => 'ru',
                'title'      => 'Идеальный Web UI Kit',
                'subtitle'   => 'Красиво, мощно, надёжно',
                'left_text'  => $leftRu,
                'right_text' => $rightRu,
                'sort'       => 0,
                'is_dark'    => false,
                'activity'   => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'locale'     => 'en',
                'title'      => 'The Utimate Web UI Kit',
                'subtitle'   => 'Beautiful, Powerful, Reliable',
                'left_text'  => $leftEn,
                'right_text' => $rightEn,
                'sort'       => 0,
                'is_dark'    => false,
                'activity'   => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'locale'     => 'kk',
                'title'      => 'Мінсіз веб-интерфейс жинағы',
                'subtitle'   => 'Әдемі, күшті, сенімді',
                'left_text'  => $leftKk,
                'right_text' => $rightKk,
                'sort'       => 0,
                'is_dark'    => false,
                'activity'   => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('wave_sections')->upsert(
            $rows,
            ['locale'],
            ['title','subtitle','left_text','right_text','sort','is_dark','activity','updated_at']
        );
    }
}
