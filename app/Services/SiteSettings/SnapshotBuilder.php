<?php

namespace App\Services\SiteSettings;

use App\Models\Admin\System\Setting\Setting;
use Illuminate\Support\Facades\File;

/**
 * Сервис строительства снапшотов системных настроек из БД
 */
class SnapshotBuilder
{
    /**
     * Настройки публичной части
     *
     * @return string
     */
    public static function buildPublic(): string
    {
        return self::buildToPath('public');
    }

    /**
     * Настройки административной части
     *
     * @return string
     */
    public static function buildAdmin(): string
    {
        return self::buildToPath('admin');
    }

    /**
     * Формирование файла по указанному пути, добавление даты
     *
     * @param string $category
     * @return string
     */
    protected static function buildToPath(string $category): string
    {
        // строго из БД
        $data = self::loadFromDbStrict($category);

        // updated_at в конец единого массива
        $data['updated_at'] = now()->toDateTimeString();

        $path = $category === 'admin'
            ? (config('site_settings.snapshot.admin_path') ?: storage_path('app/settings/admin.php'))
            : (config('site_settings.snapshot.public_path') ?: storage_path('app/settings/public.php'));

        if (!is_string($path) || $path === '') {
            throw new \RuntimeException("Snapshot path not configured for {$category}");
        }

        File::ensureDirectoryExists(dirname($path));

        $php = "<?php\n\nreturn " . var_export($data, true) . ";\n";

        // атомарная запись
        $tmp = $path . '.tmp';
        File::put($tmp, $php);
        @chmod($tmp, 0664);
        rename($tmp, $path);

        return $path;
    }

    /**
     * Загрузчик из БД
     *
     * @param string $category
     * @return array
     */
    protected static function loadFromDbStrict(string $category): array
    {
        $q = Setting::query()
            ->select(['option', 'value'])
            ->where('category', $category);

        if ($category === 'public') {
            $keys = config('site_settings.public_keys', []);
            if (is_array($keys) && count($keys)) {
                $q->whereIn('option', $keys);
            }
        }

        $rows = $q->get();

        $out = [];
        foreach ($rows as $row) {
            $out[$row->option] = self::castValue($row->value);
        }

        return $out;
    }

    /**
     * кастомизация строк массива
     *
     * @param mixed $value
     * @return mixed
     */
    protected static function castValue(mixed $value): mixed
    {
        if (!is_string($value)) return $value;

        $v = trim($value);

        if ($v === 'true') return true;
        if ($v === 'false') return false;

        if (preg_match('/^-?\d+$/', $v)) return (int) $v;
        if (preg_match('/^-?\d+\.\d+$/', $v)) return (float) $v;

        return $value;
    }
}
