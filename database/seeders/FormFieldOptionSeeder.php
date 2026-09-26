<?php

namespace Database\Seeders;

use App\Models\Admin\Form\FormField\FormField;
use App\Models\Admin\Form\FormFieldOption\FormFieldOption;
use Illuminate\Database\Seeder;
use RuntimeException;

class FormFieldOptionSeeder extends Seeder
{
    /**
     * Заполнение вариантов выбора для полей системных форм.
     */
    public function run(): void
    {
        $fields = [

            /*
            |--------------------------------------------------------------------------
            | Подбор оборудования
            | Поле: equipment_type
            |--------------------------------------------------------------------------
            */

            [
                'form' => 'selection',
                'field' => 'equipment_type',

                'options' => [
                    [
                        'data' => [
                            'value' => 'air_handling_unit',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 100,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Приточная установка',
                                'description' => 'Приточные и приточно-вытяжные вентиляционные установки.',
                            ],
                            'kk' => [
                                'label' => 'Ауа беру қондырғысы',
                                'description' => 'Ауа беру және ауа беру-сору желдету қондырғылары.',
                            ],
                            'en' => [
                                'label' => 'Air handling unit',
                                'description' => 'Supply and supply-exhaust ventilation units.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'chiller',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 200,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Чиллер',
                                'description' => 'Оборудование для охлаждения жидкости в системах кондиционирования и холодоснабжения.',
                            ],
                            'kk' => [
                                'label' => 'Чиллер',
                                'description' => 'Кондиционерлеу және салқындату жүйелеріндегі сұйықтықты салқындатуға арналған жабдық.',
                            ],
                            'en' => [
                                'label' => 'Chiller',
                                'description' => 'Equipment for cooling liquid in air-conditioning and cooling systems.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'condensing_unit',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 300,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Компрессорно-конденсаторный блок',
                                'description' => 'ККБ для систем вентиляции и кондиционирования.',
                            ],
                            'kk' => [
                                'label' => 'Компрессорлық-конденсаторлық блок',
                                'description' => 'Желдету және кондиционерлеу жүйелеріне арналған компрессорлық-конденсаторлық блок.',
                            ],
                            'en' => [
                                'label' => 'Condensing unit',
                                'description' => 'Condensing unit for ventilation and air-conditioning systems.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'vrf',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 400,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'VRF-система',
                                'description' => 'Мультизональная система кондиционирования.',
                            ],
                            'kk' => [
                                'label' => 'VRF жүйесі',
                                'description' => 'Көп аймақты кондиционерлеу жүйесі.',
                            ],
                            'en' => [
                                'label' => 'VRF system',
                                'description' => 'Multi-zone air-conditioning system.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'fan',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 500,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Вентилятор',
                                'description' => 'Радиальные, осевые и другие вентиляторы общего и специального назначения.',
                            ],
                            'kk' => [
                                'label' => 'Желдеткіш',
                                'description' => 'Жалпы және арнайы мақсаттағы радиалды, осьтік және басқа желдеткіштер.',
                            ],
                            'en' => [
                                'label' => 'Fan',
                                'description' => 'Centrifugal, axial and other general-purpose and special-purpose fans.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'air_heater',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 600,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Калорифер',
                                'description' => 'Водяные и электрические воздухонагреватели.',
                            ],
                            'kk' => [
                                'label' => 'Калорифер',
                                'description' => 'Су және электр ауа жылытқыштары.',
                            ],
                            'en' => [
                                'label' => 'Air heater',
                                'description' => 'Water and electric air heaters.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'air_conditioner',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 700,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Кондиционер',
                                'description' => 'Бытовые, полупромышленные и промышленные системы кондиционирования.',
                            ],
                            'kk' => [
                                'label' => 'Кондиционер',
                                'description' => 'Тұрмыстық, жартылай өнеркәсіптік және өнеркәсіптік кондиционерлеу жүйелері.',
                            ],
                            'en' => [
                                'label' => 'Air conditioner',
                                'description' => 'Residential, light-commercial and industrial air-conditioning systems.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'other',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 800,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Другое оборудование',
                                'description' => 'Если нужного типа оборудования нет в списке, опишите задачу в комментарии.',
                            ],
                            'kk' => [
                                'label' => 'Басқа жабдық',
                                'description' => 'Қажетті жабдық түрі тізімде болмаса, тапсырманы түсініктемеде сипаттаңыз.',
                            ],
                            'en' => [
                                'label' => 'Other equipment',
                                'description' => 'If the required equipment is not listed, describe your requirements in the comments.',
                            ],
                        ],
                    ],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Подбор оборудования
            | Поле: object_type
            |--------------------------------------------------------------------------
            */

            [
                'form' => 'selection',
                'field' => 'object_type',

                'options' => [
                    [
                        'data' => [
                            'value' => 'residential',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 100,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Жилой объект',
                                'description' => 'Квартира, частный дом, жилой комплекс и другие жилые объекты.',
                            ],
                            'kk' => [
                                'label' => 'Тұрғын нысан',
                                'description' => 'Пәтер, жеке үй, тұрғын үй кешені және басқа тұрғын нысандар.',
                            ],
                            'en' => [
                                'label' => 'Residential',
                                'description' => 'Apartment, private house, residential complex and other residential properties.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'office',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 200,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Офис',
                                'description' => 'Офисные и административные помещения.',
                            ],
                            'kk' => [
                                'label' => 'Кеңсе',
                                'description' => 'Кеңсе және әкімшілік үй-жайлар.',
                            ],
                            'en' => [
                                'label' => 'Office',
                                'description' => 'Office and administrative premises.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'retail',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 300,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Торговый объект',
                                'description' => 'Магазин, торговый центр, шоурум и другие торговые помещения.',
                            ],
                            'kk' => [
                                'label' => 'Сауда нысаны',
                                'description' => 'Дүкен, сауда орталығы, шоурум және басқа сауда үй-жайлары.',
                            ],
                            'en' => [
                                'label' => 'Retail',
                                'description' => 'Shop, shopping centre, showroom and other retail premises.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'restaurant',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 400,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Ресторан / кафе',
                                'description' => 'Рестораны, кафе, столовые и другие предприятия общественного питания.',
                            ],
                            'kk' => [
                                'label' => 'Мейрамхана / кафе',
                                'description' => 'Мейрамханалар, кафелер, асханалар және басқа қоғамдық тамақтану нысандары.',
                            ],
                            'en' => [
                                'label' => 'Restaurant / café',
                                'description' => 'Restaurants, cafés, canteens and other food-service facilities.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'warehouse',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 500,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Склад',
                                'description' => 'Складские и логистические помещения.',
                            ],
                            'kk' => [
                                'label' => 'Қойма',
                                'description' => 'Қойма және логистикалық үй-жайлар.',
                            ],
                            'en' => [
                                'label' => 'Warehouse',
                                'description' => 'Warehouse and logistics premises.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'industrial',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 600,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Промышленный объект',
                                'description' => 'Производственные здания, цеха и другие промышленные объекты.',
                            ],
                            'kk' => [
                                'label' => 'Өнеркәсіптік нысан',
                                'description' => 'Өндірістік ғимараттар, цехтар және басқа өнеркәсіптік нысандар.',
                            ],
                            'en' => [
                                'label' => 'Industrial facility',
                                'description' => 'Production buildings, workshops and other industrial facilities.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'medical',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 700,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Медицинский объект',
                                'description' => 'Клиника, медицинский центр, лаборатория и другие медицинские помещения.',
                            ],
                            'kk' => [
                                'label' => 'Медициналық нысан',
                                'description' => 'Клиника, медициналық орталық, зертхана және басқа медициналық үй-жайлар.',
                            ],
                            'en' => [
                                'label' => 'Medical facility',
                                'description' => 'Clinic, medical centre, laboratory and other medical premises.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'education',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 800,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Образовательный объект',
                                'description' => 'Школа, колледж, университет, учебный центр и другие образовательные учреждения.',
                            ],
                            'kk' => [
                                'label' => 'Білім беру нысаны',
                                'description' => 'Мектеп, колледж, университет, оқу орталығы және басқа білім беру мекемелері.',
                            ],
                            'en' => [
                                'label' => 'Educational facility',
                                'description' => 'School, college, university, training centre and other educational facilities.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'other',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 900,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Другой объект',
                                'description' => 'Если подходящего типа объекта нет в списке, укажите информацию в комментарии.',
                            ],
                            'kk' => [
                                'label' => 'Басқа нысан',
                                'description' => 'Сәйкес нысан түрі тізімде болмаса, ақпаратты түсініктемеде көрсетіңіз.',
                            ],
                            'en' => [
                                'label' => 'Other',
                                'description' => 'If the appropriate facility type is not listed, provide details in the comments.',
                            ],
                        ],
                    ],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Поставщик
            | Поле: product_categories
            |--------------------------------------------------------------------------
            */

            [
                'form' => 'supplier',
                'field' => 'product_categories',

                'options' => [
                    [
                        'data' => [
                            'value' => 'ventilation',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 100,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Вентиляционное оборудование',
                                'description' => 'Приточные установки, вентиляторы и другое вентиляционное оборудование.',
                            ],
                            'kk' => [
                                'label' => 'Желдету жабдығы',
                                'description' => 'Ауа беру қондырғылары, желдеткіштер және басқа желдету жабдығы.',
                            ],
                            'en' => [
                                'label' => 'Ventilation equipment',
                                'description' => 'Air handling units, fans and other ventilation equipment.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'air_conditioning',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 200,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Климатическое оборудование',
                                'description' => 'Кондиционеры, VRF-системы и другое климатическое оборудование.',
                            ],
                            'kk' => [
                                'label' => 'Климаттық жабдық',
                                'description' => 'Кондиционерлер, VRF жүйелері және басқа климаттық жабдық.',
                            ],
                            'en' => [
                                'label' => 'Air-conditioning equipment',
                                'description' => 'Air conditioners, VRF systems and other climate-control equipment.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'refrigeration',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 300,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Холодильное оборудование',
                                'description' => 'Чиллеры, компрессорно-конденсаторные блоки и другое холодильное оборудование.',
                            ],
                            'kk' => [
                                'label' => 'Тоңазытқыш жабдық',
                                'description' => 'Чиллерлер, компрессорлық-конденсаторлық блоктар және басқа тоңазытқыш жабдық.',
                            ],
                            'en' => [
                                'label' => 'Refrigeration equipment',
                                'description' => 'Chillers, condensing units and other refrigeration equipment.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'heating',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 400,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Отопительное оборудование',
                                'description' => 'Калориферы, теплообменники и другое оборудование для систем отопления.',
                            ],
                            'kk' => [
                                'label' => 'Жылыту жабдығы',
                                'description' => 'Калориферлер, жылу алмастырғыштар және жылыту жүйелеріне арналған басқа жабдық.',
                            ],
                            'en' => [
                                'label' => 'Heating equipment',
                                'description' => 'Air heaters, heat exchangers and other heating-system equipment.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'automation',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 500,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Автоматика и управление',
                                'description' => 'Автоматика, контроллеры, датчики и системы управления инженерным оборудованием.',
                            ],
                            'kk' => [
                                'label' => 'Автоматика және басқару',
                                'description' => 'Автоматика, контроллерлер, датчиктер және инженерлік жабдықты басқару жүйелері.',
                            ],
                            'en' => [
                                'label' => 'Automation and controls',
                                'description' => 'Automation, controllers, sensors and engineering equipment control systems.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'ducts',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 600,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Воздуховоды и комплектующие',
                                'description' => 'Воздуховоды, фасонные изделия, решётки, клапаны и другие комплектующие.',
                            ],
                            'kk' => [
                                'label' => 'Ауа өткізгіштер және жинақтаушы бөлшектер',
                                'description' => 'Ауа өткізгіштер, фасондық бұйымдар, торлар, клапандар және басқа жинақтаушы бөлшектер.',
                            ],
                            'en' => [
                                'label' => 'Air ducts and accessories',
                                'description' => 'Air ducts, fittings, grilles, dampers and other accessories.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'filters',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 700,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Фильтры и очистка воздуха',
                                'description' => 'Воздушные фильтры и оборудование для очистки воздуха.',
                            ],
                            'kk' => [
                                'label' => 'Сүзгілер және ауаны тазарту',
                                'description' => 'Ауа сүзгілері және ауаны тазартуға арналған жабдық.',
                            ],
                            'en' => [
                                'label' => 'Filters and air purification',
                                'description' => 'Air filters and air purification equipment.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'other',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 800,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Другая продукция',
                                'description' => 'Другие категории оборудования, материалов или комплектующих.',
                            ],
                            'kk' => [
                                'label' => 'Басқа өнімдер',
                                'description' => 'Жабдықтың, материалдардың немесе жинақтаушы бөлшектердің басқа санаттары.',
                            ],
                            'en' => [
                                'label' => 'Other products',
                                'description' => 'Other categories of equipment, materials or accessories.',
                            ],
                        ],
                    ],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Школа
            | Поле: course_interest
            |--------------------------------------------------------------------------
            */

            [
                'form' => 'school',
                'field' => 'course_interest',

                'options' => [
                    [
                        'data' => [
                            'value' => 'ventilation',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 100,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Вентиляция',
                                'description' => 'Обучение по системам вентиляции и воздухообмена.',
                            ],
                            'kk' => [
                                'label' => 'Желдету',
                                'description' => 'Желдету және ауа алмасу жүйелері бойынша оқыту.',
                            ],
                            'en' => [
                                'label' => 'Ventilation',
                                'description' => 'Training in ventilation and air-exchange systems.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'air_conditioning',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 200,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Кондиционирование',
                                'description' => 'Обучение по системам кондиционирования и климатическому оборудованию.',
                            ],
                            'kk' => [
                                'label' => 'Кондиционерлеу',
                                'description' => 'Кондиционерлеу жүйелері және климаттық жабдық бойынша оқыту.',
                            ],
                            'en' => [
                                'label' => 'Air conditioning',
                                'description' => 'Training in air-conditioning systems and climate-control equipment.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'refrigeration',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 300,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Холодоснабжение',
                                'description' => 'Обучение по чиллерам, холодильным контурам и системам холодоснабжения.',
                            ],
                            'kk' => [
                                'label' => 'Салқындату жүйелері',
                                'description' => 'Чиллерлер, тоңазытқыш контурлар және салқындату жүйелері бойынша оқыту.',
                            ],
                            'en' => [
                                'label' => 'Refrigeration',
                                'description' => 'Training in chillers, refrigeration circuits and cooling systems.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'design',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 400,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Проектирование',
                                'description' => 'Проектирование инженерных систем вентиляции и кондиционирования.',
                            ],
                            'kk' => [
                                'label' => 'Жобалау',
                                'description' => 'Желдету және кондиционерлеу инженерлік жүйелерін жобалау.',
                            ],
                            'en' => [
                                'label' => 'Design',
                                'description' => 'Design of ventilation and air-conditioning engineering systems.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'installation',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 500,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Монтаж',
                                'description' => 'Практическое обучение монтажу инженерных систем и оборудования.',
                            ],
                            'kk' => [
                                'label' => 'Монтаж',
                                'description' => 'Инженерлік жүйелер мен жабдықтарды монтаждау бойынша практикалық оқыту.',
                            ],
                            'en' => [
                                'label' => 'Installation',
                                'description' => 'Practical training in the installation of engineering systems and equipment.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'commissioning',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 600,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Пусконаладка и сервис',
                                'description' => 'Настройка, запуск, диагностика и обслуживание инженерного оборудования.',
                            ],
                            'kk' => [
                                'label' => 'Іске қосу және сервис',
                                'description' => 'Инженерлік жабдықты баптау, іске қосу, диагностикалау және қызмет көрсету.',
                            ],
                            'en' => [
                                'label' => 'Commissioning and service',
                                'description' => 'Setup, commissioning, diagnostics and maintenance of engineering equipment.',
                            ],
                        ],
                    ],

                    [
                        'data' => [
                            'value' => 'other',
                            'activity' => true,
                            'is_default' => false,
                            'sort' => 700,
                            'settings' => null,
                        ],

                        'translations' => [
                            'ru' => [
                                'label' => 'Другое направление',
                                'description' => 'Если нужного направления нет в списке, укажите его в комментарии.',
                            ],
                            'kk' => [
                                'label' => 'Басқа бағыт',
                                'description' => 'Қажетті бағыт тізімде болмаса, оны түсініктемеде көрсетіңіз.',
                            ],
                            'en' => [
                                'label' => 'Other area',
                                'description' => 'If the required training area is not listed, specify it in the comments.',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Сохранение вариантов
        |--------------------------------------------------------------------------
        */

        foreach ($fields as $fieldData) {
            /*
            |--------------------------------------------------------------------------
            | Получаем поле по системному коду формы и имени поля
            |--------------------------------------------------------------------------
            */

            $field = FormField::query()
                ->where('name', $fieldData['field'])
                ->whereHas('form', function ($query) use ($fieldData) {
                    $query->where(
                        'code',
                        $fieldData['form']
                    );
                })
                ->first();

            if (!$field) {
                throw new RuntimeException(
                    "Поле [{$fieldData['field']}] "
                    . "формы [{$fieldData['form']}] не найдено. "
                    . 'Сначала выполните FormSeeder и FormFieldSeeder.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Варианты поля
            |--------------------------------------------------------------------------
            */

            foreach ($fieldData['options'] as $item) {
                $option = FormFieldOption::query()->updateOrCreate(
                    [
                        'form_field_id' => $field->id,
                        'value' => $item['data']['value'],
                    ],
                    [
                        'form_field_id' => $field->id,
                        'value' => $item['data']['value'],
                        'activity' => $item['data']['activity'],
                        'is_default' => $item['data']['is_default'],
                        'sort' => $item['data']['sort'],
                        'settings' => $item['data']['settings'],
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | Переводы варианта
                |--------------------------------------------------------------------------
                */

                foreach (
                    $item['translations']
                    as $locale => $translation
                ) {
                    $option->translations()->updateOrCreate(
                        [
                            'locale' => $locale,
                        ],
                        [
                            'label' => $translation['label'],
                            'description' => $translation['description'],
                        ]
                    );
                }
            }
        }
    }
}
