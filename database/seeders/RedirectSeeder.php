<?php

namespace Database\Seeders;

use App\Models\Admin\System\Redirect\Redirect;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RedirectSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('redirects')) {
            $this->command?->warn('Нет таблицы redirects — пропускаю RedirectSeeder.');
            return;
        }

        $now = now();

        // Базовые примеры редиректов. Можно смело править под свой проект.
        // Важно: from_path — относительный путь без домена и query string.
        $rows = [
            // Главная/старая главная
            [
                'from_path'      => '/home',
                'to_url'         => '/',
                'code'           => 301,
                'preserve_query' => false,
                'locale'         => null,
                'activity'      => true,
                'notes'          => 'Старая главная → корень',
                'meta'           => ['reason' => 'legacy'],
            ],
            [
                'from_path'      => '/index.php',
                'to_url'         => '/',
                'code'           => 301,
                'preserve_query' => true, // переносим ?utm_* и прочее
                'locale'         => null,
                'activity'      => true,
                'notes'          => 'Убираем index.php из URL',
                'meta'           => ['preserve' => 'query'],
            ],

            // Блог/новости
            [
                'from_path'      => '/news',
                'to_url'         => '/blog',
                'code'           => 301,
                'preserve_query' => false,
                'locale'         => null,
                'activity'      => true,
                'notes'          => 'news → blog',
                'meta'           => ['module' => 'blog'],
            ],
            [
                'from_path'      => '/ru/novosti',
                'to_url'         => '/blog',
                'code'           => 301,
                'preserve_query' => false,
                'locale'         => 'ru',
                'activity'      => true,
                'notes'          => 'lokalizованный редирект RU',
                'meta'           => ['i18n' => true],
            ],
            [
                'from_path'      => '/en/news',
                'to_url'         => '/blog',
                'code'           => 301,
                'preserve_query' => false,
                'locale'         => 'en',
                'activity'      => true,
                'notes'          => 'localized redirect EN',
                'meta'           => ['i18n' => true],
            ],

            // Курсы (пример переноса старого слага)
            [
                'from_path'      => '/old-course/laravel-5',
                'to_url'         => '/courses/laravel-11-beginners',
                'code'           => 301,
                'preserve_query' => false,
                'locale'         => null,
                'activity'      => true,
                'notes'          => 'Миграция курсов со старых URL',
                'meta'           => ['migrated_at' => $now->toDateTimeString()],
            ],

            // Пример временного (302) промо-редиректа
            [
                'from_path'      => '/promo/summer',
                'to_url'         => '/specials/summer-2025',
                'code'           => 302,
                'preserve_query' => true,
                'locale'         => null,
                'activity'      => true,
                'notes'          => 'Временная акция',
                'meta'           => ['campaign' => 'summer-2025'],
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($rows as $row) {
            $from = $this->normalizePath($row['from_path'] ?? '');
            if ($from === null) {
                // пропускаем некорректные
                $this->command?->warn('Пропуск: пустой/некорректный from_path.');
                continue;
            }

            $where = [
                'from_path' => $from,
                'locale'    => $row['locale'] ?? null,
            ];

            $payload = [
                'to_url'         => $row['to_url'],
                'code'           => (int) ($row['code'] ?? 301),
                'preserve_query' => (bool) ($row['preserve_query'] ?? false),
                'activity'      => (bool) ($row['activity'] ?? true),
                'notes'          => $row['notes'] ?? null,
                'meta'           => $row['meta'] ?? null,   // массив — Eloquent сам закодирует в JSON
                'updated_at'     => $now,
            ];

            /** @var Redirect|null $model */
            $model = Redirect::query()->where($where)->first();

            if ($model) {
                $model->fill($payload)->save();
                $updated++;
            } else {
                $create = array_merge($where, $payload, ['created_at' => $now]);
                Redirect::query()->create($create);
                $created++;
            }
        }

        $this->command?->info("Redirects upserted: created {$created}, updated {$updated}.");
    }

    /**
     * Нормализует относительный путь:
     * - пустые строки → null
     * - гарантирует ведущий слеш
     * - убирает query string и хэш
     * - убирает домен, если передали абсолютный URL
     */
    private function normalizePath(?string $path): ?string
    {
        if (!$path) return null;

        // Если абсолютный URL — вытаскиваем только path
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $parsed = parse_url($path);
            $path = $parsed['path'] ?? '/';
        }

        // Срезаем query и hash, если вдруг пришли
        $qPos = strpos($path, '?');
        if ($qPos !== false) $path = substr($path, 0, $qPos);
        $hPos = strpos($path, '#');
        if ($hPos !== false) $path = substr($path, 0, $hPos);

        $path = trim($path);
        if ($path === '') return null;

        // Добавляем ведущий слеш
        if (!str_starts_with($path, '/')) {
            $path = '/'.$path;
        }

        // Убираем лишние слеши в конце, кроме корня
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        return $path;
    }
}
