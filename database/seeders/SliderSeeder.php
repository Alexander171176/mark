<?php

namespace Database\Seeders;

use App\Models\Admin\Slider\Slider\Slider;
use App\Models\Admin\Slider\SliderSlide\SliderSlideImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SliderSeeder extends Seeder
{
    public function run(): void
    {
        // Текущий публичный HomeHero: один слайдер, три слайда.
        // Повторный запуск обновляет записи, найденные по стабильным ключам.
        DB::transaction(function (): void {
            $slider = Slider::updateOrCreate(
                ['code' => 'home-hero'],
                [
                    'type' => 'hero', 'status' => 'published',
                    'moderation_status' => 1, 'activity' => true, 'sort' => 0,
                    'published_at' => now(), 'show_from_at' => null, 'show_to_at' => null,
                    'autoplay_delay' => 7000,
                    'autoplay_disable_on_interaction' => false,
                    'pause_on_hover' => true, 'effect' => 'slide', 'speed' => 900,
                    'effect_options' => null, 'loop' => true, 'keyboard' => true,
                    'allow_touch_move' => true, 'grab_cursor' => false,
                    'slides_per_view' => 1, 'space_between' => 0,
                    'auto_height' => false, 'breakpoints' => null,
                    'show_navigation' => true, 'show_pagination' => true,
                    'settings' => ['component' => 'HomeHero'],
                ]
            );

            $this->translations($slider, [
                'ru' => ['title' => 'Главный слайдер', 'subtitle' => 'Оборудование для вентиляции и кондиционирования'],
                'kk' => ['title' => 'Басты слайдер', 'subtitle' => 'Желдету және ауа баптау жабдықтары'],
                'en' => ['title' => 'Homepage hero slider', 'subtitle' => 'Ventilation and air-conditioning equipment'],
            ]);

            foreach ($this->slides() as $index => $data) {
                $slide = $slider->slides()->updateOrCreate(
                    ['sort' => $index],
                    [
                        'activity' => true, 'sort' => $index, 'is_main' => $index === 0,
                        'status' => 'published', 'published_at' => now(),
                        'show_from_at' => null, 'show_to_at' => null,
                        'background_color' => '#020617', 'text_color' => '#FFFFFF',
                        'accent_color' => '#38BDF8', 'overlay_color' => '#020617',
                        'overlay_opacity' => 45, 'image_position' => 'object-center',
                        'content_position' => 'left', 'animation_type' => 'fade-up',
                        'animation_duration' => 700, 'animation_delay' => 0,
                        'animation_stagger' => 150, 'animation_once' => false,
                        'animation_settings' => null, 'settings' => null,
                    ]
                );
                $this->translations($slide, $data['translations']);
                $this->seedImage($slide, $data['image'], $data['alt']);

                foreach ($data['actions'] as $order => $actionData) {
                    $action = $slide->actions()->updateOrCreate(
                        ['sort' => $order],
                        [
                            'sort' => $order, 'activity' => true,
                            'is_primary' => $order === 0,
                            'action_type' => $actionData['type'],
                            'action_value' => $actionData['value'],
                            'route_params' => null, 'target' => '_self',
                            'style' => $order === 0 ? 'primary' : 'secondary',
                            'icon' => null, 'icon_position' => 'left',
                            'css_class' => null, 'settings' => null,
                        ]
                    );
                    $this->translations($action, $actionData['translations']);
                }

                foreach ($data['advantages'] as $order => $advantageData) {
                    $advantage = $slide->advantages()->updateOrCreate(
                        ['sort' => $order],
                        [
                            'sort' => $order, 'activity' => true,
                            'icon_type' => 'lucide', 'icon' => 'check',
                            'icon_color' => '#7DD3FC', 'style' => 'default',
                            'settings' => null,
                        ]
                    );
                    $this->translations($advantage, $advantageData);
                }
            }
        });
    }

    /** Переводы для слайдера, слайда, кнопки и преимущества. */
    private function translations(object $model, array $translations): void
    {
        foreach ($translations as $locale => $fields) {
            $model->translations()->updateOrCreate(
                ['locale' => $locale],
                $fields
            );
        }
    }

    /** Импорт из существующего public/images/home/hero без удаления исходника. */
    private function seedImage(object $slide, string $filename, string $alt): void
    {
        $path = public_path('images/home/hero/' . $filename);
        if (! File::isFile($path)) {
            $this->command?->warn("Изображение отсутствует: {$path}");
            return;
        }

        // Не дублируем изображение при повторном запуске сидера.
        if ($slide->desktopImages()->exists()) {
            return;
        }

        $image = SliderSlideImage::create([
            'order' => 0, 'alt' => $alt, 'caption' => null,
        ]);
        $image->addMedia($path)->preservingOriginal()->toMediaCollection('images');
        $slide->images()->attach($image->id, ['purpose' => 'desktop', 'order' => 0]);
    }

    private function slides(): array
    {
        return [
            [
                'image' => 'hero-01.png', 'alt' => 'Вентиляционное и климатическое оборудование',
                'translations' => [
                    'ru' => ['label' => 'Агровент', 'title' => 'Вентиляционное и климатическое оборудование', 'accent' => 'для объектов любой сложности', 'description' => 'Подбор, поставка и техническое сопровождение оборудования для систем вентиляции, отопления и кондиционирования.'],
                    'kk' => ['label' => 'Агровент', 'title' => 'Желдету және климаттық жабдықтар', 'accent' => 'кез келген күрделіліктегі нысандарға', 'description' => 'Желдету, жылыту және ауа баптау жүйелеріне арналған жабдықтарды таңдау, жеткізу және техникалық сүйемелдеу.'],
                    'en' => ['label' => 'Agrovent', 'title' => 'Ventilation and climate-control equipment', 'accent' => 'for projects of any complexity', 'description' => 'Equipment selection, supply and technical support for ventilation, heating and air-conditioning systems.'],
                ],
                'actions' => [
                    $this->action('route', 'public.marketProducts.index', ['Каталог оборудования', 'Жабдықтар каталогы', 'Equipment catalog']),
                    $this->action('form', 'selection', ['Подобрать оборудование', 'Жабдық таңдау', 'Select equipment']),
                    $this->action('form', 'specification', ['Отправить спецификацию', 'Спецификацияны жіберу', 'Send a specification']),
                ],
                'advantages' => [
                    $this->advantage(['Инженерный подбор', 'Инженерлік іріктеу', 'Engineering selection'], ['Под задачу и параметры объекта', 'Нысанның міндеті мен параметрлеріне сай', 'Tailored to project requirements']),
                    $this->advantage(['Комплектация проектов', 'Жобаларды жабдықтау', 'Project equipment supply'], ['Работа со спецификациями', 'Спецификациялармен жұмыс', 'Working with specifications']),
                    $this->advantage(['Поставка по Казахстану', 'Қазақстан бойынша жеткізу', 'Delivery across Kazakhstan'], ['Для коммерческих и промышленных объектов', 'Коммерциялық және өнеркәсіптік нысандарға', 'For commercial and industrial facilities']),
                ],
            ],
            [
                'image' => 'hero-02.png', 'alt' => 'Промышленная система вентиляции',
                'translations' => [
                    'ru' => ['label' => 'Промышленная вентиляция', 'title' => 'Эффективная вентиляция', 'accent' => 'для промышленных объектов', 'description' => 'Подберём оборудование для производственных, складских, технологических и коммерческих помещений.'],
                    'kk' => ['label' => 'Өнеркәсіптік желдету', 'title' => 'Тиімді желдету', 'accent' => 'өнеркәсіптік нысандарға арналған', 'description' => 'Өндірістік, қойма, технологиялық және коммерциялық үй-жайларға арналған жабдықтарды таңдаймыз.'],
                    'en' => ['label' => 'Industrial ventilation', 'title' => 'Efficient ventilation', 'accent' => 'for industrial facilities', 'description' => 'We select equipment for production, warehouse, process and commercial premises.'],
                ],
                'actions' => [
                    $this->action('route', 'public.marketProducts.index', ['Смотреть оборудование', 'Жабдықтарды көру', 'Browse equipment']),
                    $this->action('form', 'consultation', ['Получить консультацию', 'Кеңес алу', 'Get a consultation']),
                    $this->action('form', 'specification', ['Отправить спецификацию', 'Спецификацияны жіберу', 'Send a specification']),
                ],
                'advantages' => [
                    $this->advantage(['Расчёт оборудования', 'Жабдықтарды есептеу', 'Equipment sizing'], ['С учётом параметров объекта', 'Нысан параметрлерін ескере отырып', 'Based on facility parameters']),
                    $this->advantage(['Проектные решения', 'Жобалық шешімдер', 'Engineering solutions'], ['Для новых и действующих объектов', 'Жаңа және қолданыстағы нысандарға', 'For new and existing facilities']),
                    $this->advantage(['Техническая поддержка', 'Техникалық қолдау', 'Technical support'], ['На этапе подбора и поставки', 'Таңдау және жеткізу кезеңдерінде', 'During selection and delivery']),
                ],
            ],
            [
                'image' => 'hero-03.png', 'alt' => 'Климатическое оборудование для коммерческого здания',
                'translations' => [
                    'ru' => ['label' => 'Системы кондиционирования', 'title' => 'Климатические системы', 'accent' => 'для коммерческих зданий', 'description' => 'Чиллеры, ККБ, VRF-системы и другое оборудование для современных систем кондиционирования.'],
                    'kk' => ['label' => 'Ауа баптау жүйелері', 'title' => 'Климаттық жүйелер', 'accent' => 'коммерциялық ғимараттарға арналған', 'description' => 'Заманауи ауа баптау жүйелеріне арналған чиллерлер, компрессорлық-конденсаторлық блоктар, VRF жүйелері және басқа жабдықтар.'],
                    'en' => ['label' => 'Air-conditioning systems', 'title' => 'Climate-control systems', 'accent' => 'for commercial buildings', 'description' => 'Chillers, condensing units, VRF systems and other equipment for modern air-conditioning systems.'],
                ],
                'actions' => [
                    $this->action('route', 'public.marketProducts.index', ['Перейти в каталог', 'Каталогқа өту', 'Go to catalog']),
                    $this->action('form', 'selection', ['Подобрать систему', 'Жүйені таңдау', 'Select a system']),
                    $this->action('form', 'specification', ['Отправить спецификацию', 'Спецификацияны жіберу', 'Send a specification']),
                ],
                'advantages' => [
                    $this->advantage(['Чиллеры и ККБ', 'Чиллерлер мен ККБ', 'Chillers and condensing units'], ['Для систем различной мощности', 'Әртүрлі қуаттағы жүйелерге', 'For systems of different capacities']),
                    $this->advantage(['VRF-системы', 'VRF жүйелері', 'VRF systems'], ['Для коммерческих объектов', 'Коммерциялық нысандарға', 'For commercial facilities']),
                    $this->advantage(['Комплексный подбор', 'Кешенді іріктеу', 'Comprehensive selection'], ['Оборудование под проект', 'Жобаға сай жабдықтар', 'Equipment tailored to the project']),
                ],
            ],
        ];
    }

    private function action(string $type, string $value, array $labels): array
    {
        return [
            'type' => $type, 'value' => $value,
            'translations' => [
                'ru' => ['label' => $labels[0], 'title' => null, 'aria_label' => $labels[0]],
                'kk' => ['label' => $labels[1], 'title' => null, 'aria_label' => $labels[1]],
                'en' => ['label' => $labels[2], 'title' => null, 'aria_label' => $labels[2]],
            ],
        ];
    }

    private function advantage(array $titles, array $texts): array
    {
        return [
            'ru' => ['title' => $titles[0], 'text' => $texts[0]],
            'kk' => ['title' => $titles[1], 'text' => $texts[1]],
            'en' => ['title' => $titles[2], 'text' => $texts[2]],
        ];
    }
}
