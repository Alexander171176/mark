<?php

namespace Database\Seeders;

use App\Models\Admin\Constructor\HomePage\Quality\QualitySection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class QualitySectionSeeder extends Seeder
{
    public function run(): void
    {
        // Файлы лежат в resources/images/vulk/
        $base      = resource_path('images/vulk');
        $lightPath = $base.'/vulk-page-1.png';
        $darkPath  = $base.'/vulk-page-1-dark.png';

        $payloads = [
            'ru' => [
                'subtitle' => 'Carefully Handcrafted',
                'title'    => 'Vulk is a Top-Tier product',
                'screenshot_alt' => 'Длинный скриншот',
            ],
            'en' => [
                'subtitle' => 'Carefully Handcrafted',
                'title'    => 'Vulk is a Top-Tier product',
                'screenshot_alt' => 'Long screenshot',
            ],
            'kk' => [
                'subtitle' => 'Carefully Handcrafted',
                'title'    => 'Vulk is a Top-Tier product',
                'screenshot_alt' => 'Длинный скриншот',
            ],
        ];

        foreach ($payloads as $locale => $data) {
            /** @var QualitySection $section */
            $section = QualitySection::query()->firstOrCreate(
                ['locale' => $locale],
                array_merge($data, ['sort' => 0, 'activity' => true])
            );

            $section->fill($data)->save();

            // Медиа (light / dark) — singleFile, перезаписываем
            if (File::exists($lightPath)) {
                $section->clearMediaCollection(QualitySection::MEDIA_COLLECTION_LIGHT);
                $section->addMedia($lightPath)
                    ->preservingOriginal()
                    ->toMediaCollection(QualitySection::MEDIA_COLLECTION_LIGHT);
            } else {
                $this->warnIfPossible("Light image not found: {$lightPath}");
            }

            if (File::exists($darkPath)) {
                $section->clearMediaCollection(QualitySection::MEDIA_COLLECTION_DARK);
                $section->addMedia($darkPath)
                    ->preservingOriginal()
                    ->toMediaCollection(QualitySection::MEDIA_COLLECTION_DARK);
            } else {
                $this->warnIfPossible("Dark image not found: {$darkPath}");
            }
        }
    }

    private function warnIfPossible(string $msg): void
    {
        if (property_exists($this, 'command') && $this->command) {
            $this->command->warn($msg);
        }
    }
}
