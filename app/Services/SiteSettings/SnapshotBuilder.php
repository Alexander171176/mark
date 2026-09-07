<?php

namespace App\Services\SiteSettings;

use App\Models\Admin\System\Setting\Setting;
use Illuminate\Support\Facades\File;

/**
 * Сервис формирования снапшотов системных настроек из БД.
 */
class SnapshotBuilder
{
    /**
     * Формирует снапшот настроек публичной части.
     *
     * @return string
     */
    public static function buildPublic(): string
    {
        return self::buildToPath('public');
    }

    /**
     * Формирует снапшот настроек административной части.
     *
     * @return string
     */
    public static function buildAdmin(): string
    {
        return self::buildToPath('admin');
    }

    /**
     * Формирует файл снапшота для указанной категории.
     *
     * @param string $category
     * @return string
     */
    protected static function buildToPath(string $category): string
    {
        $data = self::loadFromDbStrict($category);

        $data['updated_at'] = now()->toDateTimeString();

        $path = $category === 'admin'
            ? (
            config('site_settings.snapshot.admin_path')
                ?: storage_path('app/settings/admin.php')
            )
            : (
            config('site_settings.snapshot.public_path')
                ?: storage_path('app/settings/public.php')
            );

        if (!is_string($path) || $path === '') {
            throw new \RuntimeException(
                "Snapshot path not configured for {$category}"
            );
        }

        File::ensureDirectoryExists(dirname($path));

        $php = "<?php\n\nreturn "
            . var_export($data, true)
            . ";\n";

        // Атомарная запись файла.
        $tmp = $path . '.tmp';

        File::put($tmp, $php);

        @chmod($tmp, 0664);

        rename($tmp, $path);

        return $path;
    }

    /**
     * Загружает настройки указанной категории напрямую из БД.
     *
     * @param string $category
     * @return array
     */
    protected static function loadFromDbStrict(string $category): array
    {
        $rows = Setting::query()
            ->select(['option', 'value'])
            ->where('category', $category)
            ->get();

        $out = [];

        foreach ($rows as $row) {
            $out[$row->option] = self::castValue($row->value);
        }

        return $out;
    }

    /**
     * Преобразует строковое значение настройки
     * в подходящий скалярный тип.
     *
     * @param mixed $value
     * @return mixed
     */
    protected static function castValue(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $value = trim($value);

        if ($value === 'true') {
            return true;
        }

        if ($value === 'false') {
            return false;
        }

        if (preg_match('/^-?\d+$/', $value)) {
            return (int) $value;
        }

        if (preg_match('/^-?\d+\.\d+$/', $value)) {
            return (float) $value;
        }

        return $value;
    }
}
