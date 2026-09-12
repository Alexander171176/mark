<?php

namespace App\Http\Controllers\Admin\Analytics\AnalyticsVisitorLog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Analytics\AnalyticsVisitorLog\AnalyticsVisitorLogResource;
use App\Http\Resources\Admin\Analytics\AnalyticsVisitorLog\AnalyticsVisitorLogSharedResource;
use App\Models\Admin\Analytics\AnalyticsVisitorLog\AnalyticsVisitorLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminAnalyticsVisitorLogController extends Controller
{
    /**
     * Допустимое количество записей на странице.
     *
     * @var array<int, int>
     */
    private const PER_PAGE_OPTIONS = [
        10,
        25,
        50,
        100,
        250,
    ];

    /**
     * Количество записей на странице по умолчанию.
     */
    private const DEFAULT_PER_PAGE = 50;

    /**
     * Направление сортировки по умолчанию.
     */
    private const DEFAULT_SORT_DIRECTION = 'desc';

    /**
     * Допустимые направления сортировки.
     *
     * @var array<int, string>
     */
    private const SORT_DIRECTIONS = [
        'asc',
        'desc',
    ];

    /**
     * Фильтры списка.
     *
     * @var array<int, string>
     */
    private const FILTERS = [
        'date_from',
        'date_to',
        'module',
        'event_type',
        'visitor_uuid',
        'user_id',
        'url',
        'country',
        'city',
        'device_type',
        'browser',
        'os',
        'per_page',
        'sort_direction',
    ];

    /**
     * Список посещений.
     */
    public function index(Request $request): Response
    {
        $query = AnalyticsVisitorLog::query();

        $this->applyFilters(
            $query,
            $request
        );

        $perPage = $this->resolvePerPage(
            $request
        );

        $sortDirection =
            $this->resolveSortDirection(
                $request
            );

        $visitorLogs = $query
            ->orderBy(
                'id',
                $sortDirection
            )
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render(
            'Admin/Analytics/AnalyticsVisitorLog/Index',
            [
                'visitorLogs' =>
                    AnalyticsVisitorLogSharedResource::collection(
                        $visitorLogs
                    ),

                'filters' =>
                    $request->only(
                        self::FILTERS
                    ),
            ]
        );
    }

    /**
     * Просмотр одной записи посещения.
     */
    public function show(
        AnalyticsVisitorLog $analyticsVisitorLog
    ): Response {
        return Inertia::render(
            'Admin/Analytics/AnalyticsVisitorLog/Show',
            [
                'visitorLog' =>
                    new AnalyticsVisitorLogResource(
                        $analyticsVisitorLog
                    ),
            ]
        );
    }

    /**
     * Применение фильтров.
     */
    private function applyFilters(
        Builder $query,
        Request $request
    ): void {
        $query
            ->when(
                $request->filled('date_from'),
                fn (Builder $query) =>
                $query->whereDate(
                    'visited_at',
                    '>=',
                    $request->input(
                        'date_from'
                    )
                )
            )
            ->when(
                $request->filled('date_to'),
                fn (Builder $query) =>
                $query->whereDate(
                    'visited_at',
                    '<=',
                    $request->input(
                        'date_to'
                    )
                )
            )
            ->when(
                $request->filled('module'),
                fn (Builder $query) =>
                $query->where(
                    'module',
                    $request->input(
                        'module'
                    )
                )
            )
            ->when(
                $request->filled('event_type'),
                fn (Builder $query) =>
                $query->where(
                    'event_type',
                    $request->input(
                        'event_type'
                    )
                )
            )
            ->when(
                $request->filled('visitor_uuid'),
                fn (Builder $query) =>
                $query->where(
                    'visitor_uuid',
                    $request->input(
                        'visitor_uuid'
                    )
                )
            )
            ->when(
                $request->filled('user_id'),
                fn (Builder $query) =>
                $query->where(
                    'user_id',
                    $request->input(
                        'user_id'
                    )
                )
            )
            ->when(
                $request->filled('url'),
                fn (Builder $query) =>
                $query->where(
                    'url',
                    'like',
                    '%' .
                    $request->input('url') .
                    '%'
                )
            )
            ->when(
                $request->filled('country'),
                fn (Builder $query) =>
                $query->where(
                    'country',
                    $request->input(
                        'country'
                    )
                )
            )
            ->when(
                $request->filled('city'),
                fn (Builder $query) =>
                $query->where(
                    'city',
                    $request->input(
                        'city'
                    )
                )
            )
            ->when(
                $request->filled(
                    'device_type'
                ),
                fn (Builder $query) =>
                $query->where(
                    'device_type',
                    $request->input(
                        'device_type'
                    )
                )
            )
            ->when(
                $request->filled('browser'),
                fn (Builder $query) =>
                $query->where(
                    'browser',
                    $request->input(
                        'browser'
                    )
                )
            )
            ->when(
                $request->filled('os'),
                fn (Builder $query) =>
                $query->where(
                    'os',
                    $request->input(
                        'os'
                    )
                )
            );
    }

    /**
     * Получение количества записей на странице.
     */
    private function resolvePerPage(
        Request $request
    ): int {
        $perPage = (int) $request->input(
            'per_page',
            self::DEFAULT_PER_PAGE
        );

        return in_array(
            $perPage,
            self::PER_PAGE_OPTIONS,
            true
        )
            ? $perPage
            : self::DEFAULT_PER_PAGE;
    }

    /**
     * Получение направления сортировки.
     */
    private function resolveSortDirection(
        Request $request
    ): string {
        $sortDirection =
            strtolower(
                (string) $request->input(
                    'sort_direction',
                    self::DEFAULT_SORT_DIRECTION
                )
            );

        return in_array(
            $sortDirection,
            self::SORT_DIRECTIONS,
            true
        )
            ? $sortDirection
            : self::DEFAULT_SORT_DIRECTION;
    }
}
