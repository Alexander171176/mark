<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'id' => 2,
                'name' => 'Алексей Смирнов',
                'email' => 'alexey.smirnov@example.com',
            ],
            [
                'id' => 3,
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
            ],
            [
                'id' => 4,
                'name' => 'Айгерим Нур',
                'email' => 'aigerim.nur@example.com',
            ],
            [
                'id' => 5,
                'name' => 'Michael Lee',
                'email' => 'michael.lee@example.com',
            ],
            [
                'id' => 6,
                'name' => 'Дана Аскар',
                'email' => 'dana.askar@example.com',
            ],
            [
                'id' => 7,
                'name' => 'Сергей Иванов',
                'email' => 'sergey.ivanov@example.com',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['id' => $user['id']],
                [
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
