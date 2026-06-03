<?php

namespace Database\Seeders;

use App\Models\Admin\School\SchoolSubscriptionPlan\SchoolSubscriptionPlanTranslation;
use Illuminate\Database\Seeder;

class SchoolSubscriptionPlanTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            1 => [
                'ru' => ['Стартовый', 'Базовый доступ для знакомства с онлайн-школой.'],
                'en' => ['Starter', 'Basic access to explore the online school.'],
                'kk' => ['Бастапқы', 'Онлайн мектеппен танысуға арналған базалық қолжетімділік.'],
            ],
            2 => [
                'ru' => ['Стандарт', 'Оптимальный тариф для регулярного обучения.'],
                'en' => ['Standard', 'An optimal plan for regular learning.'],
                'kk' => ['Стандарт', 'Тұрақты оқуға арналған оңтайлы тариф.'],
            ],
            3 => [
                'ru' => ['Pro', 'Расширенный доступ ко всем основным материалам.'],
                'en' => ['Pro', 'Extended access to all core materials.'],
                'kk' => ['Pro', 'Барлық негізгі материалдарға кеңейтілген қолжетімділік.'],
            ],
            4 => [
                'ru' => ['Командный', 'Тариф для командного обучения и корпоративных групп.'],
                'en' => ['Team', 'A plan for team learning and corporate groups.'],
                'kk' => ['Командалық', 'Командалық оқу және корпоративтік топтарға арналған тариф.'],
            ],
            5 => [
                'ru' => ['Стандарт на год', 'Годовой доступ к стандартному обучению со скидкой.'],
                'en' => ['Annual Standard', 'Yearly access to standard learning with a discount.'],
                'kk' => ['Жылдық стандарт', 'Жеңілдікпен стандартты оқуға жылдық қолжетімділік.'],
            ],
            6 => [
                'ru' => ['Pro на год', 'Годовой Pro-доступ для интенсивного обучения.'],
                'en' => ['Annual Pro', 'Yearly Pro access for intensive learning.'],
                'kk' => ['Жылдық Pro', 'Қарқынды оқуға арналған жылдық Pro қолжетімділік.'],
            ],
        ];

        foreach ($data as $planId => $translations) {
            foreach ($translations as $locale => [$title, $short]) {
                SchoolSubscriptionPlanTranslation::updateOrCreate(
                    [
                        'school_subscription_plan_id' => $planId,
                        'locale' => $locale,
                    ],
                    [
                        'title' => $title,
                        'subtitle' => $short,
                        'short' => $short,
                        'description' => $short . ' Тариф подходит для изучения IT, программирования и веб-разработки в удобном темпе.',
                        'meta_title' => $title,
                        'meta_keywords' => 'IT, программирование, онлайн школа, подписка, тариф',
                        'meta_desc' => $short,
                    ]
                );
            }
        }
    }
}
