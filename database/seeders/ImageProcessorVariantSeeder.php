<?php

namespace Database\Seeders;

use App\Models\Admin\System\ImageProcessor\ImageProcessorProfile;
use App\Models\Admin\System\ImageProcessor\ImageProcessorVariant;
use Illuminate\Database\Seeder;

class ImageProcessorVariantSeeder extends Seeder
{
    public function run(): void
    {
        $variants = [

            'article_image' => [

                [
                    'key' => 'desktop',
                    'name' => '4:3',
                    'width' => 1200,
                    'height' => 800,
                    'allow_rotate' => true,
                ],

            ],

            'company_logo' => [

                [
                    'key' => 'square',
                    'name' => 'Квадрат',
                    'width' => 400,
                    'height' => 400,
                    'allow_rotate' => false,
                ],

            ],

            'shop_logo' => [

                [
                    'key' => 'square',
                    'name' => 'Квадрат',
                    'width' => 400,
                    'height' => 400,
                    'allow_rotate' => false,
                ],

            ],

            'shop_image' => [

                [
                    'key' => 'desktop',
                    'name' => '4:3',
                    'width' => 1200,
                    'height' => 800,
                    'allow_rotate' => true,
                ],

            ],

            'banner' => [

                [
                    'key' => 'desktop',
                    'name' => 'Горизонтальный',
                    'width' => 1600,
                    'height' => 900,
                    'allow_rotate' => false,
                ],

                [
                    'key' => 'mobile',
                    'name' => 'Мобильный',
                    'width' => 900,
                    'height' => 1600,
                    'allow_rotate' => false,
                ],

            ],

            'profile_photo' => [

                [
                    'key' => 'circle',
                    'name' => 'Круг',
                    'width' => 200,
                    'height' => 200,
                    'allow_rotate' => false,
                ],

            ],

        ];

        foreach ($variants as $profileKey => $items) {

            $profile = ImageProcessorProfile::query()
                ->where('key', $profileKey)
                ->first();

            if (!$profile) {
                continue;
            }

            foreach ($items as $item) {

                ImageProcessorVariant::updateOrCreate(

                    [
                        'image_processor_profile_id' => $profile->id,
                        'key' => $item['key'],
                    ],

                    [
                        'name' => $item['name'],
                        'description' => null,

                        'activity' => true,

                        'width' => $item['width'],
                        'height' => $item['height'],

                        'allow_rotate' => $item['allow_rotate'],

                        'quality' => 85,

                        'format' => 'webp',

                        'fit' => 'crop',

                        'shape' => $item['key'] === 'circle'
                            ? 'circle'
                            : (
                            $item['key'] === 'square'
                                ? 'square'
                                : 'rectangle'
                            ),

                        'background_light' => '#FFFFFF',
                        'background_dark' => '#111827',

                        'keep_original' => true,

                        'sort' => 100,

                    ]

                );

            }

        }
    }
}
