<?php

namespace App\Traits\Settings;

use App\Models\Admin\System\Setting\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

trait ClearsSettingsCacheTrait
{
    /**
     * Базовые ключи кэша, которые нужно очищать всегда.
     */
    private array $baseSettingsCacheKeys = [
        'site_settings',
        'setting_locale',
        'widget_panel_settings',
        'sidebar_settings',
    ];

    /**
     * Опции, которые тоже участвуют в персональных ключах setting_{option}.
     */
    private array $extraSettingOptionsForCache = [
        'widgetHexColor',
        'widgetOpacity',
        'AdminSidebarLightColor',
        'admin_sidebar_opacity',
    ];

    /**
     * Ключ, под которым кешируем список option'ов для очистки (чтобы не дергать БД постоянно).
     * Можно держать маленький TTL, чтобы не ломать актуальность.
     */
    private string $settingsOptionsCacheKey = 'settings_options_for_cache_clear';

    /**
     * Приватный метод для очистки кэша.
     * Исправлено для совместимости с RedisStore (используем цикл forget).
     *
     * @param string|null $specificKey
     * @return void
     */
    private function clearSettingsCache(string $specificKey = null): void
    {
        $keysToForget = $this->baseSettingsCacheKeys;

        if ($specificKey) {
            $keysToForget[] = $specificKey;
        }

        // Добавляем ключи для всех настроек count/sort + спец-опций
        foreach ($this->getOptionsForCacheClear() as $option) {
            $keysToForget[] = 'setting_' . $option;
        }

        $uniqueKeys = array_values(array_unique(array_filter($keysToForget)));

        foreach ($uniqueKeys as $key) {
            Cache::forget($key);
        }

        Log::debug('Кэш настроек очищен', ['keys' => $uniqueKeys]);
    }

    /**
     * Получаем список option'ов, которые нужно очистить как setting_{option}.
     * Здесь мы избегаем постоянных запросов к БД: кешируем список на короткое время.
     *
     * @return array<int, string>
     */
    private function getOptionsForCacheClear(): array
    {
        try {
            return Cache::remember($this->settingsOptionsCacheKey, 300, function () {
                $options = Setting::query()
                    ->where('option', 'like', 'AdminCount%')
                    ->orWhere('option', 'like', 'AdminSort%')
                    ->orWhereIn('option', $this->extraSettingOptionsForCache)
                    ->pluck('option')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                return $options;
            });
        } catch (Throwable $e) {
            Log::error("Ошибка получения опций для очистки кэша: {$e->getMessage()}");
            return [];
        }
    }
}
