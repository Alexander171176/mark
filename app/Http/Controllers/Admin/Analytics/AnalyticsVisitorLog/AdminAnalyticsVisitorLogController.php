<?php

namespace App\Http\Controllers\Admin\Analytics\AnalyticsVisitorLog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Analytics\AnalyticsVisitorLog\AnalyticsVisitorLogResource;
use App\Models\Admin\Analytics\AnalyticsVisitorLog\AnalyticsVisitorLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminAnalyticsVisitorLogController extends Controller
{
    /**
     * Список посещений.
     */
    public function index(Request $request): Response
    {
        $query = AnalyticsVisitorLog::query();

        $query
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('visited_at', '>=', $request->input('date_from'));
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('visited_at', '<=', $request->input('date_to'));
            })
            ->when($request->filled('module'), function ($query) use ($request) {
                $query->where('module', $request->input('module'));
            })
            ->when($request->filled('event_type'), function ($query) use ($request) {
                $query->where('event_type', $request->input('event_type'));
            })
            ->when($request->filled('visitor_uuid'), function ($query) use ($request) {
                $query->where('visitor_uuid', $request->input('visitor_uuid'));
            })
            ->when($request->filled('user_id'), function ($query) use ($request) {
                $query->where('user_id', $request->input('user_id'));
            })
            ->when($request->filled('url'), function ($query) use ($request) {
                $query->where('url', 'like', '%' . $request->input('url') . '%');
            })
            ->when($request->filled('country'), function ($query) use ($request) {
                $query->where('country', $request->input('country'));
            })
            ->when($request->filled('city'), function ($query) use ($request) {
                $query->where('city', $request->input('city'));
            })
            ->when($request->filled('device_type'), function ($query) use ($request) {
                $query->where('device_type', $request->input('device_type'));
            })
            ->when($request->filled('browser'), function ($query) use ($request) {
                $query->where('browser', $request->input('browser'));
            })
            ->when($request->filled('os'), function ($query) use ($request) {
                $query->where('os', $request->input('os'));
            });

        $perPage = (int) $request->input('per_page', 50);

        if (! in_array($perPage, [10, 25, 50, 100, 250], true)) {
            $perPage = 50;
        }

        $sortDirection = $request->input('sort_direction', 'desc');

        if (! in_array($sortDirection, ['asc', 'desc'], true)) {
            $sortDirection = 'desc';
        }

        $visitorLogs = $query
            ->orderBy('id', $sortDirection)
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Admin/Analytics/AnalyticsVisitorLog/Index', [
            'visitorLogs' => AnalyticsVisitorLogResource::collection($visitorLogs),
            'filters' => $request->only([
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
            ]),
        ]);
    }

    /**
     * Просмотр одной записи посещения.
     */
    public function show(AnalyticsVisitorLog $analyticsVisitorLog): Response
    {
        return Inertia::render('Admin/Analytics/AnalyticsVisitorLog/Show', [
            'visitorLog' => new AnalyticsVisitorLogResource($analyticsVisitorLog),
        ]);
    }
}
