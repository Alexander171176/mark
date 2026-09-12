<?php

namespace App\Services\Public\Location;

use App\Context\Location\LocationContext;
use App\Models\Admin\System\Location\Location;
use Illuminate\Http\Request;

class LocationResolverService
{
    /**
     * Определить текущую Location.
     *
     * Приоритет:
     * 1. Location из URL.
     * 2. Location по умолчанию.
     *
     * @return array{
     *     location: Location,
     *     source: string,
     *     invalid_route_location: bool
     * }|null
     */
    public function resolve(
        Request $request
    ): ?array {
        $hasRouteLocation =
            $request->route('location') !== null;

        $location = $this->resolveFromRoute(
            $request
        );

        if ($location) {
            return [
                'location' => $location,
                'source' => LocationContext::SOURCE_ROUTE,
                'invalid_route_location' => false,
            ];
        }

        $location = $this->resolveDefault();

        if ($location) {
            return [
                'location' => $location,
                'source' => LocationContext::SOURCE_DEFAULT,
                'invalid_route_location' => $hasRouteLocation,
            ];
        }

        return null;
    }

    /**
     * Определить Location из параметра маршрута.
     *
     * Поддерживает:
     * - строковый slug;
     * - готовую модель Location при route model binding.
     */
    public function resolveFromRoute(
        Request $request
    ): ?Location {
        $routeLocation = $request->route(
            'location'
        );

        if ($routeLocation instanceof Location) {
            return $routeLocation->activity
                ? $routeLocation
                : null;
        }

        if (
            !is_string($routeLocation)
            || trim($routeLocation) === ''
        ) {
            return null;
        }

        return Location::query()
            ->active()
            ->where(
                'slug',
                trim($routeLocation)
            )
            ->first();
    }

    /**
     * Получить активную Location по умолчанию.
     */
    public function resolveDefault(): ?Location
    {
        return Location::query()
            ->active()
            ->default()
            ->ordered()
            ->first();
    }
}
