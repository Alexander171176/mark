<?php

namespace Database\Seeders;

use App\Models\Admin\Constructor\Cms\CmsBanner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CmsBannerSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('cms_banners')) {
            $this->command?->warn('Нет таблицы cms_banners — пропускаю CmsBannerSeeder.');
            return;
        }

        $now = now();
        $today = $now->copy()->startOfDay();

        // Набор баннеров по плейсментам
        // sort выставляется порядком в массиве
        $data = [
            'home_hero' => [
                [
                    'title'        => 'Учись сегодня — меняй карьеру завтра',
                    'subtitle'     => 'Практические курсы с обратной связью от менторов',
                    'link_type'    => 'internal', // url|route|none (используем internal=прямой URL на сайте)
                    'link_url'     => '/courses',
                    'button_label' => 'Смотреть курсы',
                    'link_target'  => '_self',
                    'starts_at'    => $today->copy()->subDays(10),
                    'ends_at'      => null,
                    'activity'    => true,
                    'meta'         => ['theme' => 'dark', 'align' => 'center'],
                    // Попробуем прикрепить такие файлы (если есть). Иначе — SVG-заглушка.
                    'image_file'        => 'home_hero_desktop.jpg',
                    'image_mobile_file' => 'home_hero_mobile.jpg',
                ],
                [
                    'title'        => 'Осенние скидки до −30%',
                    'subtitle'     => 'Только до конца месяца',
                    'link_type'    => 'internal',
                    'link_url'     => '/pricing',
                    'button_label' => 'К тарифам',
                    'link_target'  => '_self',
                    'starts_at'    => $today->copy()->subDays(5),
                    'ends_at'      => $today->copy()->addDays(20),
                    'activity'    => true,
                    'meta'         => ['badge' => 'SALE', 'theme' => 'light'],
                    'image_file'        => 'home_sale_desktop.jpg',
                    'image_mobile_file' => 'home_sale_mobile.jpg',
                ],
            ],

            'home_promo' => [
                [
                    'title'        => 'Подписка вместо разовых покупок',
                    'subtitle'     => 'Доступ ко всем курсам по одной цене',
                    'link_type'    => 'internal',
                    'link_url'     => '/subscriptions',
                    'button_label' => 'Узнать больше',
                    'link_target'  => '_self',
                    'starts_at'    => $today,
                    'ends_at'      => null,
                    'activity'    => true,
                    'meta'         => ['variant' => 'card', 'color' => '#4F46E5'],
                    'image_file'        => 'home_promo_subs.jpg',
                    'image_mobile_file' => 'home_promo_subs_m.jpg',
                ],
                [
                    'title'        => 'Сертификаты об окончании',
                    'subtitle'     => 'Подтвердите навыки официальным документом',
                    'link_type'    => 'internal',
                    'link_url'     => '/certificates/info',
                    'button_label' => 'Подробнее',
                    'link_target'  => '_self',
                    'starts_at'    => $today->copy()->subDays(1),
                    'ends_at'      => null,
                    'activity'    => true,
                    'meta'         => ['variant' => 'card', 'color' => '#059669'],
                    'image_file'        => 'home_promo_cert.jpg',
                    'image_mobile_file' => 'home_promo_cert_m.jpg',
                ],
            ],

            'course_sidebar' => [
                [
                    'title'        => 'Персональная менторская поддержка',
                    'subtitle'     => 'Разбор кода и карьерные советы',
                    'link_type'    => 'internal',
                    'link_url'     => '/mentors',
                    'button_label' => 'Найти ментора',
                    'link_target'  => '_self',
                    'starts_at'    => $today->copy()->subDays(30),
                    'ends_at'      => null,
                    'activity'    => true,
                    'meta'         => ['shape' => 'rounded', 'elevation' => 2],
                    'image_file'        => 'course_sidebar_mentors.jpg',
                    'image_mobile_file' => 'course_sidebar_mentors_m.jpg',
                ],
            ],

            'blog_top' => [
                [
                    'title'        => 'Новые статьи каждую неделю',
                    'subtitle'     => 'Подпишитесь и не пропускайте свежие материалы',
                    'link_type'    => 'internal',
                    'link_url'     => '/blog',
                    'button_label' => 'К блогу',
                    'link_target'  => '_self',
                    'starts_at'    => $today->copy()->subDays(3),
                    'ends_at'      => null,
                    'activity'    => true,
                    'meta'         => ['banner_style' => 'wide'],
                    'image_file'        => 'blog_top.jpg',
                    'image_mobile_file' => 'blog_top_m.jpg',
                ],
            ],
        ];

        $created = 0;
        $updated = 0;
        $attached = 0;

        foreach ($data as $placement => $banners) {
            $sort = 0;

            foreach ($banners as $b) {
                $sort++;

                // Идемпотентный ключ: (placement, title)
                $where = [
                    'placement' => (string) $placement,
                    'title'     => (string) ($b['title'] ?? 'Banner'),
                ];

                $payload = [
                    'subtitle'     => $b['subtitle']     ?? null,
                    'link_type'    => $b['link_type']    ?? 'url',
                    'link_url'     => $b['link_url']     ?? null,
                    'link_route'   => $b['link_route']   ?? null,
                    'link_params'  => $b['link_params']  ?? null, // cast -> array
                    'link_target'  => $b['link_target']  ?? '_self',
                    'button_label' => $b['button_label'] ?? null,
                    'starts_at'    => $b['starts_at']    ?? null,
                    'ends_at'      => $b['ends_at']      ?? null,
                    'activity'    => !isset($b['activity']) || (bool)$b['activity'],
                    'sort'     => (int) ($b['sort'] ?? $sort),
                    'meta'         => $b['meta']         ?? null, // cast -> array
                    'updated_at'   => $now,
                ];

                /** @var CmsBanner $model */
                $model = CmsBanner::query()->where($where)->first();

                if ($model) {
                    $model->fill($payload);
                    $model->save();
                    $updated++;
                } else {
                    $model = new CmsBanner($where + $payload + ['created_at' => $now]);
                    $model->save();
                    $created++;
                }

                // Медиа: сначала пробуем локальные файлы из storage/seed/cms_banners, иначе — SVG-заглушки.
                $attached += $this->attachBannerMedia($model, $b['image_file'] ?? null, $b['image_mobile_file'] ?? null);
            }
        }

        $this->command?->info("Cms banners upserted: created {$created}, updated {$updated}, media attached {$attached}.");
    }

    /**
     * Прикрепить изображения к баннеру: сначала ищем в storage/seed/cms_banners,
     * если нет — создаём SVG-заглушку.
     */
    private function attachBannerMedia(CmsBanner $model, ?string $desktop, ?string $mobile): int
    {
        $count = 0;

        // Desktop
        if ($desktop && $this->attachFromFileIfExists($model, $desktop, 'image')) {
            $count++;
        } else {
            // Заглушка
            if ($this->attachSvgPlaceholder($model, 'image', 1200, 400, $model->title)) {
                $count++;
            }
        }

        // Mobile
        if ($mobile && $this->attachFromFileIfExists($model, $mobile, 'image_mobile')) {
            $count++;
        } else {
            if ($this->attachSvgPlaceholder($model, 'image_mobile', 720, 400, $model->title)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Попытка прикрепить файл из storage/seed/cms_banners/{filename}
     */
    private function attachFromFileIfExists(CmsBanner $model, string $filename, string $collection): bool
    {
        $path = storage_path('seed/cms_banners/'.$filename);
        if (is_file($path)) {
            try {
                // singleFile() перезапишет предыдущее
                $model->addMedia($path)->preservingOriginal()->toMediaCollection($collection);
                return true;
            } catch (\Throwable $e) {
                // проглатываем, упадём на заглушку
            }
        }
        return false;
    }

    /**
     * Сгенерировать и прикрепить SVG-заглушку (однотонный фон + текст).
     */
    private function attachSvgPlaceholder(CmsBanner $model, string $collection, int $w, int $h, string $text): bool
    {
        $bg = '#E5E7EB';      // gray-200
        $fg = '#111827';      // gray-900
        $fontSize = max(14, intval($w * 0.035));
        $label = htmlspecialchars(mb_strimwidth($text, 0, 40, '…', 'UTF-8'), ENT_QUOTES, 'UTF-8');

        $svg = <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg width="{$w}" height="{$h}" viewBox="0 0 {$w} {$h}" xmlns="http://www.w3.org/2000/svg">
  <rect width="100%" height="100%" fill="{$bg}"/>
  <text x="50%" y="50%" text-anchor="middle" dominant-baseline="middle"
        font-family="Arial, Helvetica, sans-serif" font-size="{$fontSize}" fill="{$fg}">
    {$label}
  </text>
</svg>
SVG;

        try {
            $model
                ->addMediaFromString($svg)
                ->usingFileName('placeholder_'.$collection.'.svg')
                ->toMediaCollection($collection);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
