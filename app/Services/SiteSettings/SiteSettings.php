<?php

namespace App\Services\SiteSettings;

use App\Models\Admin\System\Setting\Setting;
use Illuminate\Support\Facades\File;

/**
 * Сервис работы сайта на сформированных снапшотах настроек
 */
class SiteSettings
{
    /**
     * Показать настройки
     *
     * @param string $key
     * @param mixed|null $default
     * @param string $category
     * @return mixed
     */
    public static function get(string $key, mixed $default = null, string $category = 'public'): mixed
    {
        $all = self::all($category);
        return $all[$key] ?? $default;
    }

    /**
     * @param string $category
     * @return array
     */
    public static function all(string $category = 'public'): array
    {
        $driver = config('site_settings.driver', 'snapshot');

        return match ($driver) {
            'db'       => self::fromDb($category),
            // 'cache'  => self::fromCache($category), // добавим позже
            default    => self::fromSnapshot($category),
        };
    }

    /**
     * @param string $category
     * @return array
     */
    protected static function fromSnapshot(string $category): array
    {
        $path = $category === 'admin'
            ? config('site_settings.snapshot.admin_path')
            : config('site_settings.snapshot.public_path');

        if (!File::exists($path)) {
            return $category === 'public'
                ? []
                : self::fromDb($category);
        }

        $data = require $path;
        if (!is_array($data)) return [];

        // чтобы updated_at не считался настройкой:
        unset($data['updated_at']);

        return $data;
    }

    /**
     * @param string $category
     * @return array
     */
    protected static function fromDb(string $category): array
    {
        // ВАЖНО: импорт модели здесь, чтобы сервис был автономен
        $modelClass = Setting::class;

        $q = $modelClass::query()
            ->select(['option', 'value', 'category'])
            ->where('category', $category);

        // для public можно ограничить ключи (по желанию)
        if ($category === 'public') {
            $keys = config('site_settings.public_keys', []);
            if (is_array($keys) && count($keys)) {
                $q->whereIn('option', $keys);
            }
        }

        $rows = $q->get();

        // option => value
        $out = [];
        foreach ($rows as $row) {
            $out[$row->option] = self::castValue($row->value);
        }

        return $out;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected static function castValue(mixed $value): mixed
    {
        // типичный кейс: "true"/"false"/числа
        if (!is_string($value)) return $value;

        $v = trim($value);

        if ($v === 'true') return true;
        if ($v === 'false') return false;

        // int?
        if (preg_match('/^-?\d+$/', $v)) return (int) $v;

        // float?
        if (preg_match('/^-?\d+\.\d+$/', $v)) return (float) $v;

        return $value;
    }
}
