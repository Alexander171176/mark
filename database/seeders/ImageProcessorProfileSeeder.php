<?php

namespace Database\Seeders;

use App\Models\Admin\System\ImageProcessor\ImageProcessorProfile;
use Illuminate\Database\Seeder;

class ImageProcessorProfileSeeder extends Seeder
{
    public function run(): void
    {
        $profiles = [

            [
                'key' => 'article_image',
                'name' => 'Изображения статей',
                'description' => 'Обработка изображений статей блога',
                'activity' => true,
                'sort' => 100,
            ],

            [
                'key' => 'company_logo',
                'name' => 'Логотип компании',
                'description' => 'Обработка логотипов компаний',
                'activity' => true,
                'sort' => 200,
            ],

            [
                'key' => 'shop_logo',
                'name' => 'Логотип магазина',
                'description' => 'Обработка логотипов магазинов',
                'activity' => true,
                'sort' => 300,
            ],

            [
                'key' => 'shop_image',
                'name' => 'Изображения магазинов',
                'description' => 'Обработка изображений магазинов',
                'activity' => true,
                'sort' => 400,
            ],

            [
                'key' => 'banner',
                'name' => 'Баннеры',
                'description' => 'Обработка баннеров сайта',
                'activity' => true,
                'sort' => 500,
            ],

            [
                'key' => 'profile_photo',
                'name' => 'Фото профиля',
                'description' => 'Обработка аватаров пользователей',
                'activity' => true,
                'sort' => 600,
            ],

        ];

        foreach ($profiles as $profile) {

            ImageProcessorProfile::updateOrCreate(
                [
                    'key' => $profile['key'],
                ],
                $profile
            );

        }
    }
}
