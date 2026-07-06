<?php

namespace App\Http\Controllers\Admin\Analytics\AnalyticsVisitorLog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Analytics\AnalyticsVisitorLog\AnalyticsVisitorLogRequest;
use App\Services\Admin\Analytics\AnalyticsFileWriterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AnalyticsVisitorLogController extends Controller
{
    /**
     * Сбор аналитики посещения/события.
     */
    public function store(
        AnalyticsVisitorLogRequest $request,
        AnalyticsFileWriterService $analyticsFileWriter
    ): JsonResponse {
        $data = $request->validated();

        $visitorUuid = $data['visitor_uuid'] ?? (string) Str::uuid();

        $payload = [
            /*
            |--------------------------------------------------------------------------
            | Пользователь
            |--------------------------------------------------------------------------
            */

            'user_id' => Auth::id(),
            'session_id' => $request->session()->getId(),
            'visitor_uuid' => $visitorUuid,

            'user_gender' => null,
            'user_age' => null,
            'user_age_group' => null,

            /*
            |--------------------------------------------------------------------------
            | Backend данные запроса
            |--------------------------------------------------------------------------
            */

            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),

            'method' => $data['method'] ?? 'GET',
            'url' => $data['url'] ?? $request->headers->get('referer') ?? $request->fullUrl(),
            'page_title' => $data['page_title'] ?? null,
            'route_name' => $data['route_name'] ?? null,

            'module' => $data['module'] ?? $this->detectModule($data['url'] ?? $request->fullUrl()),
            'entity_type' => $data['entity_type'] ?? null,
            'entity_id' => $data['entity_id'] ?? null,
            'event_type' => $data['event_type'] ?? 'page_view',

            'request_type' => $data['request_type'] ?? $this->detectRequestType($request),
            'status_code' => 200,
            'response_time' => null,

            /*
            |--------------------------------------------------------------------------
            | География
            |--------------------------------------------------------------------------
            */

            'country' => null,
            'region' => null,
            'city' => null,

            /*
            |--------------------------------------------------------------------------
            | Устройство
            |--------------------------------------------------------------------------
            */

            'device_type' => null,
            'device_name' => null,

            'browser' => null,
            'browser_version' => null,

            'os' => null,
            'os_version' => null,

            /*
            |--------------------------------------------------------------------------
            | Frontend данные
            |--------------------------------------------------------------------------
            */

            'screen_width' => $data['screen_width'] ?? null,
            'screen_height' => $data['screen_height'] ?? null,

            'browser_language' => $data['browser_language'] ?? null,
            'timezone' => $data['timezone'] ?? null,

            /*
            |--------------------------------------------------------------------------
            | Источник посещения
            |--------------------------------------------------------------------------
            */

            'referer' => $data['referer'] ?? $request->headers->get('referer'),
            'source_type' => null,
            'search_engine' => null,

            /*
            |--------------------------------------------------------------------------
            | Поведение пользователя
            |--------------------------------------------------------------------------
            */

            'time_on_page' => $data['time_on_page'] ?? null,
            'scroll_depth' => $data['scroll_depth'] ?? null,
            'clicks_count' => $data['clicks_count'] ?? 0,

            /*
            |--------------------------------------------------------------------------
            | Системные данные
            |--------------------------------------------------------------------------
            */

            'locale' => $data['locale'] ?? app()->getLocale(),
            'visited_at' => now()->toDateTimeString(),
        ];

        $analyticsFileWriter->writeVisitorLog($payload);

        return response()->json([
            'success' => true,
            'visitor_uuid' => $visitorUuid,
        ]);
    }

    /**
     * Определение типа запроса.
     */
    private function detectRequestType(Request $request): string
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return 'api';
        }

        if ($request->ajax()) {
            return 'ajax';
        }

        return 'web';
    }

    /**
     * Первичное определение модуля по URL.
     */
    private function detectModule(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);

        if (! $path) {
            return null;
        }

        return match (true) {
            str_contains($path, '/blog') => 'blog',
            str_contains($path, '/school') => 'school',
            str_contains($path, '/market') => 'market',
            str_contains($path, '/crm') => 'crm',
            str_contains($path, '/chat') => 'chat',
            str_contains($path, '/ai') => 'ai',
            str_contains($path, '/admin') => 'admin',
            default => 'public',
        };
    }
}
