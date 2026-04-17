<?php

namespace Database\Seeders;

use App\Models\Admin\Constructor\HomePage\Reason\ReasonItem;
use App\Models\Admin\Constructor\HomePage\Reason\ReasonSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ReasonItemSeeder extends Seeder
{
    public function run(): void
    {
        // Источник картинок: resources/images/vulk/demo-1.svg, demo-2.svg, demo-3.svg
        $img = fn (string $name) => resource_path('images/vulk/'.$name);

        $items = [
            'ru' => [
                [
                    'title' => 'Фокус на UI и CSS',
                    'text'  => 'Vulk — это шаблон, ориентированный на удобство и скорость разработки. Внутри вы найдёте множество готовых компонентов — просто копируйте и вставляйте. Их также можно использовать как основу для своих и легко расширять.',
                    'file'  => 'demo-1.svg',
                    'alt'   => 'Иллюстрация преимущества',
                    'align' => 'left',
                    'sort'  => 1,
                ],
                [
                    'title' => 'Быстрая разработка',
                    'text'  => 'Vulk поставляется с сервером разработки Vite JS, который поможет при кастомизации. Vite компилирует .vue-файлы и стили, а встроенный HMR перезагружает страницу при каждом сохранении в редакторе.',
                    'file'  => 'demo-2.svg',
                    'alt'   => 'Иллюстрация преимущества',
                    'align' => 'right',
                    'sort'  => 2,
                ],
                [
                    'title' => 'Подробная документация',
                    'text'  => 'Документация Vulk — ваш надёжный помощник: у шаблона большая кодовая база и много файлов. Внимательно изучите разделы — там всё необходимое для комфортной разработки даже для новичков.',
                    'file'  => 'demo-3.svg',
                    'alt'   => 'Иллюстрация преимущества',
                    'align' => 'left',
                    'sort'  => 3,
                ],
            ],
            'en' => [
                [
                    'title' => 'Focused on UI and CSS',
                    'text'  => "Vulk is a template that focuse on usability and development speed. You'll find inside tons of ready to use components that you simply have to copy and paste to create your layouts. You can as well use the available components as a base for your own and start extending them easily.",
                    'file'  => 'demo-1.svg',
                    'alt'   => 'Benefit image',
                    'align' => 'left',
                    'sort'  => 1,
                ],
                [
                    'title' => 'Fast Development',
                    'text'  => "Vulk ships with a Vite JS development server that will assist you when customizing the template. Vite handles project compilation of .vue files and stylesheets. The built-in HMR hot reload refreshes the page each time you hit the save trigger in your favorite editor.",
                    'file'  => 'demo-2.svg',
                    'alt'   => 'Benefit image',
                    'align' => 'right',
                    'sort'  => 2,
                ],
                [
                    'title' => 'Extensive Documentation',
                    'text'  => "Vulk's documentation will be a precious ally, as the template has a solid codebase, and a lot of files. Read carefully the docs, all you need to know for an optimal development experience is there, even if you still are a beginner.",
                    'file'  => 'demo-3.svg',
                    'alt'   => 'Benefit image',
                    'align' => 'left',
                    'sort'  => 3,
                ],
            ],
            'kk' => [
                [
                    'title' => 'UI мен CSS-ке басымдық',
                    'text'  => 'Vulk – қолдануға ыңғайлы әрі жылдам әзірлеуге бағытталған үлгі. Ішінде дайын компоненттердің көптігін табасыз — макеттерді жасау үшін жай көшіріп, қойсаңыз болды. Сондай-ақ оларды негіз ретінде пайдаланып, қажетіңізге қарай оңай кеңейте аласыз.',
                    'file'  => 'demo-1.svg',
                    'alt'   => 'Артықшылық иллюстрациясы',
                    'align' => 'left',
                    'sort'  => 1,
                ],
                [
                    'title' => 'Жылдам әзірлеу',
                    'text'  => 'Vulk құрамында Vite JS әзірлеу сервері бар, ол баптау кезінде көмектеседі. Vite .vue файлдарын және стильдерді жинайды, ал кіріккен HMR әрбір сақтаудан кейін бетті бірден жаңартады.',
                    'file'  => 'demo-2.svg',
                    'alt'   => 'Артықшылық иллюстрациясы',
                    'align' => 'right',
                    'sort'  => 2,
                ],
                [
                    'title' => 'Толық құжаттама',
                    'text'  => 'Vulk құжаттамасы — сенімді көмекшіңіз: үлгінің код базасы үлкен және файлдар саны көп. Бөлімдерді мұқият оқыңыз — жайлы әзірлеуге қажет нәрсенің бәрі сонда, тіпті жаңадан бастағандар үшін де.',
                    'file'  => 'demo-3.svg',
                    'alt'   => 'Артықшылық иллюстрациясы',
                    'align' => 'left',
                    'sort'  => 3,
                ],
            ],
        ];

        foreach ($items as $locale => $list) {
            // Находим/создаём секцию под нужную локаль
            $section = ReasonSection::where('locale', $locale)->first();

            if (!$section) {
                $this->command?->warn("ReasonSection for locale [$locale] not found — creating default.");

                // Дефолтные тексты для трёх языков
                $defaults = [
                    'ru' => [
                        'subtitle'     => 'Вам понравится этот продукт',
                        'title'        => '3 причины выбрать Vulk',
                        'cta_title'    => 'Эксклюзивно на Envato Market',
                        'cta_btn_text' => 'Купить',
                    ],
                    'en' => [
                        'subtitle'     => "You'll love this product",
                        'title'        => '3 Reasons to choose Vulk',
                        'cta_title'    => 'Exclusively on Envato Market',
                        'cta_btn_text' => 'Get It Now',
                    ],
                    'kk' => [
                        'subtitle'     => 'Бұл өнім сізге ұнайды',
                        'title'        => 'Vulk-ты таңдаудың 3 себебі',
                        'cta_title'    => 'Тек Envato Market алаңында',
                        'cta_btn_text' => 'Сатып алу',
                    ],
                ][$locale];

                $section = ReasonSection::create([
                    'locale'         => $locale,
                    'subtitle'       => $defaults['subtitle'],
                    'title'          => $defaults['title'],
                    'cta_title'      => $defaults['cta_title'],
                    'cta_btn_text'   => $defaults['cta_btn_text'],
                    'cta_btn_url'    => 'https://go.cssninja.io/buy-vulk',
                    'cta_btn_target' => '_blank',
                    'sort'           => 30,
                    'activity'       => true,
                ]);
            }

            foreach ($list as $row) {
                /** @var ReasonItem $item */
                $item = ReasonItem::updateOrCreate(
                    ['section_id' => $section->id, 'sort' => $row['sort']],
                    [
                        'align'     => $row['align'] ?? 'left',
                        'title'     => $row['title'],
                        'text'      => $row['text'],
                        'image_alt' => $row['alt'],
                        'light_alt' => $row['alt'],
                        'dark_alt'  => $row['alt'],
                        'activity'  => true,
                    ]
                );

                // Медиа: тот же файл в light и dark
                $full = $img($row['file']);
                if (! File::exists($full)) {
                    $this->command?->warn("Image not found: $full");
                    continue;
                }

                // Light
                $item->clearMediaCollection(ReasonItem::MEDIA_COLLECTION_LIGHT);
                $item->addMedia($full)
                    ->preservingOriginal()
                    ->usingName($row['alt'])
                    ->toMediaCollection(ReasonItem::MEDIA_COLLECTION_LIGHT);

                // Dark
                $item->clearMediaCollection(ReasonItem::MEDIA_COLLECTION_DARK);
                $item->addMedia($full)
                    ->preservingOriginal()
                    ->usingName($row['alt'])
                    ->toMediaCollection(ReasonItem::MEDIA_COLLECTION_DARK);
            }
        }
    }
}
