<?php

namespace App\Services\SiteSettings;

use App\Models\Admin\System\Setting\Setting;

class AdminSettingsService
{
    private array $settings;

    public function __construct()
    {
        $this->settings = Setting::query()
            ->where('category', 'admin')
            ->pluck('value', 'option')
            ->toArray();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }

    public function string(string $key, string $default = ''): string
    {
        return (string) $this->get($key, $default);
    }

    public function int(string $key, int $default = 0): int
    {
        return (int) $this->get($key, $default);
    }

    public function bool(string $key, bool $default = false): bool
    {
        return (bool) $this->get($key, $default);
    }

    public function all(): array
    {
        return $this->settings;
    }
}
