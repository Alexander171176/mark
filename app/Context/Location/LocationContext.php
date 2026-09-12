<?php

namespace App\Context\Location;

use App\Models\Admin\System\Location\Location;

class LocationContext
{
    /**
     * Возможные источники определения Location.
     */
    public const SOURCE_ROUTE = 'route';
    public const SOURCE_USER = 'user';
    public const SOURCE_GEOLOCATION = 'geolocation';
    public const SOURCE_IP = 'ip';
    public const SOURCE_DEFAULT = 'default';

    /**
     * Текущая Location.
     */
    private ?Location $location = null;

    /**
     * Источник определения Location.
     */
    private ?string $source = null;

    /**
     * Установить текущую Location.
     */
    public function set(
        Location $location,
        string $source
    ): void {
        $this->location = $location;
        $this->source = $source;
    }

    /**
     * Получить текущую Location.
     */
    public function current(): ?Location
    {
        return $this->location;
    }

    /**
     * Получить источник определения Location.
     */
    public function source(): ?string
    {
        return $this->source;
    }

    /**
     * Проверить, определена ли Location.
     */
    public function hasLocation(): bool
    {
        return $this->location !== null;
    }

    /**
     * Проверить источник Location.
     */
    public function isSource(
        string $source
    ): bool {
        return $this->source === $source;
    }

    /**
     * Очистить текущий контекст.
     */
    public function clear(): void
    {
        $this->location = null;
        $this->source = null;
    }
}
