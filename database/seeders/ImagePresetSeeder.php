<?php

namespace Database\Seeders;

use App\Models\Admin\System\ImagePreset\ImagePreset;
use Illuminate\Database\Seeder;

class ImagePresetSeeder extends Seeder
{
    public function run(): void
    {
        $presets = [
            [
                'key' => 'rectangle_large',
                'description' => 'Большое прямоугольное изображение 3:2',
                'shape' => 'rectangle',
                'width' => 1200,
                'height' => 800,
                'image_rotation_enabled' => true,
                'crop_rotation_enabled' => true,
                'max_file_size_kb' => 4096,
                'keep_original' => true,
                'sort' => 100,
            ],
            [
                'key' => 'rectangle_medium',
                'description' => 'Среднее прямоугольное изображение 3:2',
                'shape' => 'rectangle',
                'width' => 600,
                'height' => 400,
                'image_rotation_enabled' => true,
                'crop_rotation_enabled' => true,
                'max_file_size_kb' => 2048,
                'keep_original' => true,
                'sort' => 200,
            ],
            [
                'key' => 'rectangle_vertical',
                'description' => 'Вертикальное прямоугольное изображение 3:4',
                'shape' => 'rectangle',
                'width' => 900,
                'height' => 1200,
                'image_rotation_enabled' => true,
                'crop_rotation_enabled' => true,
                'max_file_size_kb' => 4096,
                'keep_original' => true,
                'sort' => 300,
            ],
            [
                'key' => 'banner',
                'description' => 'Горизонтальный баннер 16:9',
                'shape' => 'rectangle',
                'width' => 1200,
                'height' => 675,
                'image_rotation_enabled' => true,
                'crop_rotation_enabled' => true,
                'max_file_size_kb' => 4096,
                'keep_original' => true,
                'sort' => 400,
            ],
            [
                'key' => 'banner_vertical',
                'description' => 'Вертикальный баннер 9:16',
                'shape' => 'rectangle',
                'width' => 675,
                'height' => 1200,
                'image_rotation_enabled' => true,
                'crop_rotation_enabled' => true,
                'max_file_size_kb' => 4096,
                'keep_original' => true,
                'sort' => 500,
            ],
            [
                'key' => 'square_large',
                'description' => 'Большое квадратное изображение',
                'shape' => 'square',
                'width' => 800,
                'height' => 800,
                'image_rotation_enabled' => true,
                'crop_rotation_enabled' => false,
                'max_file_size_kb' => 4096,
                'keep_original' => true,
                'sort' => 600,
            ],
            [
                'key' => 'square_medium',
                'description' => 'Среднее квадратное изображение',
                'shape' => 'square',
                'width' => 300,
                'height' => 300,
                'image_rotation_enabled' => true,
                'crop_rotation_enabled' => false,
                'max_file_size_kb' => 2048,
                'keep_original' => true,
                'sort' => 700,
            ],
            [
                'key' => 'circle',
                'description' => 'Круглое изображение',
                'shape' => 'circle',
                'width' => 200,
                'height' => 200,
                'image_rotation_enabled' => true,
                'crop_rotation_enabled' => false,
                'max_file_size_kb' => 1024,
                'keep_original' => false,
                'sort' => 800,
            ],
            [
                'key' => 'thumbnail',
                'description' => 'Универсальная миниатюра',
                'shape' => 'square',
                'width' => 150,
                'height' => 150,
                'image_rotation_enabled' => true,
                'crop_rotation_enabled' => false,
                'max_file_size_kb' => 1024,
                'keep_original' => false,
                'sort' => 900,
            ],
        ];

        foreach ($presets as $preset) {
            ImagePreset::updateOrCreate(
                [
                    'key' => $preset['key'],
                ],
                $preset
            );
        }
    }
}
