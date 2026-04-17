<?php

namespace Database\Seeders;

use App\Models\Admin\Finance\SubscriptionPlan\SubscriptionPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('subscription_plans')) {
            $this->command?->warn('Таблица subscription_plans отсутствует — пропускаю SubscriptionPlanSeeder.');
            return;
        }

        if (!Schema::hasTable('currencies')) {
            $this->command?->warn('Таблица currencies отсутствует — пропускаю SubscriptionPlanSeeder (нужен currency_id).');
            return;
        }

        /**
         * Берём USD из справочника (как базовый дефолт),
         * если нет — fallback на первую валюту из таблицы.
         */
        $usdId = DB::table('currencies')->where('code', 'USD')->value('id');
        $fallbackCurrencyId = DB::table('currencies')->value('id');

        $currencyId = $usdId ?: $fallbackCurrencyId;

        if (!$currencyId) {
            $this->command?->warn('В таблице currencies нет записей — пропускаю SubscriptionPlanSeeder.');
            return;
        }

        /**
         * Демо-планы.
         * Важно: ключ уникальности — (locale, slug).
         * activity оставляем false, чтобы админ включал вручную.
         */
        $plans = [
            [
                'locale'        => 'ru',
                'title'         => 'Базовый (1 месяц)',
                'slug'          => 'basic-monthly',
                'subtitle'      => 'Для старта',
                'short'         => 'Доступ к базовым курсам и сообществу.',
                'description'   => 'Для старта: доступ к базовым курсам, сообществу и ограниченным материалам.',

                'meta_title'    => 'Базовый тариф на 1 месяц',
                'meta_keywords' => 'тариф, подписка, онлайн школа, базовый доступ',
                'meta_desc'     => 'Базовый тариф на 1 месяц: доступ к курсам и сообществу.',

                'published_at'    => now(),
                'available_from'  => null,
                'available_until' => null,

                'billing_period' => 'month',
                'interval'       => 1,
                'currency_id'    => $currencyId,
                'price'          => 19.00,
                'trial_days'     => 7,
                'auto_renew'     => true,

                'provider'         => null,
                'provider_ref'     => null,
                'provider_payload' => null,

                'activity' => false,
                'sort'     => 10,

                'config' => [
                    'features' => [
                        'Доступ к базовым курсам',
                        'Сообщество и Q&A',
                        'Ограниченные материалы',
                    ],
                    'limits' => [
                        'courses_access'      => 'basic', // basic|pro|all
                        'downloadable'        => false,
                        'concurrent_streams'  => 1,
                        'priority_support'    => false,
                    ],
                    'meta' => [
                        'badge' => null,
                    ],
                ],
            ],
            [
                'locale'        => 'ru',
                'title'         => 'ПРО (1 месяц)',
                'slug'          => 'pro-monthly',
                'subtitle'      => 'Популярный выбор',
                'short'         => 'Все курсы, материалы и приоритетная поддержка.',
                'description'   => 'Расширенные возможности: все курсы, материалы для скачивания, приоритетная поддержка, сертификаты.',

                'meta_title'    => 'ПРО тариф на 1 месяц',
                'meta_keywords' => 'про тариф, подписка, курсы, сертификаты',
                'meta_desc'     => 'ПРО тариф на 1 месяц: полный доступ к курсам и приоритетная поддержка.',

                'published_at'    => now(),
                'available_from'  => null,
                'available_until' => null,

                'billing_period' => 'month',
                'interval'       => 1,
                'currency_id'    => $currencyId,
                'price'          => 39.00,
                'trial_days'     => 7,
                'auto_renew'     => true,

                'provider'         => null,
                'provider_ref'     => null,
                'provider_payload' => null,

                'activity' => false,
                'sort'     => 20,

                'config' => [
                    'features' => [
                        'Все текущие курсы',
                        'Материалы для скачивания',
                        'Приоритетная поддержка',
                        'Сертификаты по окончании',
                    ],
                    'limits' => [
                        'courses_access'      => 'pro',
                        'downloadable'        => true,
                        'concurrent_streams'  => 2,
                        'priority_support'    => true,
                    ],
                    'meta' => [
                        'badge' => 'popular',
                    ],
                ],
            ],
            [
                'locale'        => 'ru',
                'title'         => 'ПРО (1 год)',
                'slug'          => 'pro-annual',
                'subtitle'      => 'Лучшая цена',
                'short'         => 'Годовой доступ со скидкой относительно помесячной оплаты.',
                'description'   => 'Годовой план со скидкой. Доступ ко всем текущим и новым курсам, материалы, поддержка, сертификаты.',

                'meta_title'    => 'ПРО тариф на 1 год',
                'meta_keywords' => 'годовая подписка, скидка, про тариф',
                'meta_desc'     => 'ПРО тариф на 1 год: максимальная выгода и полный доступ к обучению.',

                'published_at'    => now(),
                'available_from'  => null,
                'available_until' => null,

                'billing_period' => 'year',
                'interval'       => 1,
                'currency_id'    => $currencyId,
                'price'          => 360.00,
                'trial_days'     => 14,
                'auto_renew'     => true,

                'provider'         => null,
                'provider_ref'     => null,
                'provider_payload' => null,

                'activity' => false,
                'sort'     => 30,

                'config' => [
                    'features' => [
                        'Все текущие и новые курсы в течение года',
                        'Материалы для скачивания',
                        'Приоритетная поддержка',
                        'Сертификаты по окончании',
                        'Экономия по сравнению с помесячной оплатой',
                    ],
                    'limits' => [
                        'courses_access'      => 'pro',
                        'downloadable'        => true,
                        'concurrent_streams'  => 3,
                        'priority_support'    => true,
                    ],
                    'meta' => [
                        'badge' => 'best_value',
                    ],
                ],
            ],
            [
                'locale'        => 'ru',
                'title'         => 'Команда (1 год)',
                'slug'          => 'team-annual',
                'subtitle'      => 'Для организаций',
                'short'         => 'Командный доступ для небольшой команды.',
                'description'   => 'Командный годовой план для небольших команд. Доступ ко всем курсам, материалы, поддержка, отчёты по прогрессу.',

                'meta_title'    => 'Командный тариф на 1 год',
                'meta_keywords' => 'командная подписка, организация, обучение сотрудников',
                'meta_desc'     => 'Командный тариф на 1 год: обучение для команды с расширенными возможностями.',

                'published_at'    => now(),
                'available_from'  => null,
                'available_until' => null,

                'billing_period' => 'year',
                'interval'       => 1,
                'currency_id'    => $currencyId,
                'price'          => 1290.00,
                'trial_days'     => 14,
                'auto_renew'     => true,

                'provider'         => null,
                'provider_ref'     => null,
                'provider_payload' => null,

                'activity' => false,
                'sort'     => 40,

                'config' => [
                    'features' => [
                        'До 5 мест для команды',
                        'Все курсы и материалы',
                        'Приоритетная поддержка',
                        'Отчёты по прогрессу',
                    ],
                    'limits' => [
                        'seats'              => 5,
                        'courses_access'     => 'all',
                        'downloadable'       => true,
                        'concurrent_streams' => 5,
                        'priority_support'   => true,
                    ],
                    'meta' => [
                        'badge' => 'teams',
                    ],
                ],
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::query()->updateOrCreate(
                [
                    'locale' => $plan['locale'],
                    'slug'   => $plan['slug'],
                ],
                $plan
            );
        }

        $this->command?->info('Subscription plans seeded: ' . count($plans));
    }
}
