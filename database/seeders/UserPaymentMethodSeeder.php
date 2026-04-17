<?php

namespace Database\Seeders;

use App\Models\Admin\Finance\PaymentMethod\PaymentMethod;
use App\Models\Admin\Finance\UserPaymentMethod\UserPaymentMethod;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UserPaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (
            !Schema::hasTable('users') ||
            !Schema::hasTable('payment_methods') ||
            !Schema::hasTable('user_payment_methods')
        ) {
            $this->command?->warn('Нет необходимых таблиц (users/payment_methods/user_payment_methods) — пропускаю UserPaymentMethodSeeder.');
            return;
        }

        // Берём активные способы оплаты (есть в PaymentMethodSeeder)
        $methods = PaymentMethod::query()
            ->where('activity', true)
            ->orderBy('sort')
            ->get(['id','code','provider','type']);

        if ($methods->isEmpty()) {
            $this->command?->warn('Активные payment_methods не найдены — сначала запусти PaymentMethodSeeder.');
            return;
        }

        $users = User::query()->get(['id','name','email']);
        if ($users->isEmpty()) {
            $this->command?->warn('Пользователи не найдены — пропускаю UserPaymentMethodSeeder.');
            return;
        }

        $faker = fake(); // текущая локаль проекта

        foreach ($users as $user) {
            // Сколько методов сохранить этому пользователю (1..3, но не больше доступных)
            $count = min($methods->count(), max(1, rand(1, 3)));

            // Выбираем случайные варианты без повторов
            $picked = $methods->random($count)->values();

            $createdOrUpdated = [];

            foreach ($picked as $idx => $pm) {
                // Стабильные токены под уникальный индекс (provider + provider_payment_method_id)
                $provider  = $pm->provider ?: 'manual';
                $token     = 'pm_' . $provider . '_' . $user->id . '_' . ($idx + 1); // детерминированный
                $customer  = 'cus_' . $provider . '_' . $user->id;

                // Общие поля (чтобы сид был идемпотентным — updateOrCreate по уникальным полям)
                $where = [
                    'provider'                   => $provider,
                    'provider_payment_method_id' => $token,
                ];

                $payload = [
                    'user_id'              => $user->id,
                    'payment_method_id'    => $pm->id,
                    'provider_customer_id' => $customer,
                    'activity'            => true,
                    'is_default'           => false, // выставим ниже одному из методов
                    'meta'                 => [
                        'seeded'   => true,
                        'seed_run' => now()->toDateTimeString(),
                    ],
                ];

                // Карточные реквизиты — только для type=card
                if ($pm->type === 'card') {
                    $brands = ['Visa','Mastercard','Maestro','MIR'];
                    $payload = array_merge($payload, [
                        'brand'     => $faker->randomElement($brands),
                        'last4'     => str_pad((string)rand(0, 9999), 4, '0', STR_PAD_LEFT),
                        'exp_month' => rand(1, 12),
                        'exp_year'  => (int) now()->addYears(rand(1, 5))->format('Y'),
                        'country'   => $faker->randomElement(['US','GB','DE','RU','KZ','PL']),
                    ]);
                } else {
                    // Для ewallet/bank_transfer/invoice и т.п. карточные поля чистим
                    $payload = array_merge($payload, [
                        'brand'     => null,
                        'last4'     => null,
                        'exp_month' => null,
                        'exp_year'  => null,
                        'country'   => null,
                    ]);
                }

                // ---------- Безопасные фолбэки для адреса ----------
                // region: stateAbbr отсутствует в некоторых локалях → пытаемся state(), иначе 2 заглавные буквы
                $region = method_exists($faker, 'state')
                    ? $faker->state()
                    : strtoupper(Str::random(2));

                // city/postcode также могут отсутствовать в отдельных локалях
                $city = method_exists($faker, 'city') ? $faker->city() : 'City';
                $zip  = method_exists($faker, 'postcode') ? $faker->postcode() : str_pad((string)rand(0, 99999), 5, '0', STR_PAD_LEFT);

                // Биллинг-данные
                $payload = array_merge($payload, [
                    'billing_name'   => $faker->name(),
                    'billing_email'  => $user->email,
                    'billing_phone'  => $faker->optional(0.5)->numerify('+1##########'),
                    'billing_address'=> [
                        'line1'   => $faker->streetAddress(),
                        'city'    => $city,
                        'region'  => $region,
                        'zip'     => $zip,
                        'country' => $faker->randomElement(['US','GB','DE','RU','KZ','PL']),
                    ],
                ]);

                // Идемпотентное сохранение (по уникальной паре провайдера и токена)
                $model = UserPaymentMethod::query()->updateOrCreate($where, $payload);
                $createdOrUpdated[] = $model;
            }

            // Гарантируем один default на пользователя:
            // 1) Если уже есть дефолт в БД — оставляем как есть.
            // 2) Иначе делаем дефолтным первый из текущей партии.
            $hasDefault = UserPaymentMethod::query()
                ->where('user_id', $user->id)
                ->where('is_default', true)
                ->exists();

            if (!$hasDefault && !empty($createdOrUpdated)) {
                // На всякий случай снимем дефолт со всех, затем назначим
                UserPaymentMethod::query()->where('user_id', $user->id)->update(['is_default' => false]);

                $first = $createdOrUpdated[0];
                $first->is_default = true;
                $first->save();
            }
        }

        $this->command?->info('User payment methods seeded/updated (идемпотентно).');
    }
}
