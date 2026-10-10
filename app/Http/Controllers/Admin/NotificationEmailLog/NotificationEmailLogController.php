<?php

namespace App\Http\Controllers\Admin\NotificationEmailLog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\NotificationEmailLog\NotificationEmailLogResource;
use App\Models\Admin\NotificationEmailLog\NotificationEmailLog;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class NotificationEmailLogController extends Controller
{
    /**
     * Журнал Email Notifications.
     * Структурные фильтры выполняются в БД независимо от режима обработки.
     */
    public function index(Request $request): Response
    {
        $settings = app(AdminSettingsService::class);

        /*
        |--------------------------------------------------------------------------
        | Настройки административного списка
        |--------------------------------------------------------------------------
        */
        $perPage = max(1, $settings->int('adminEmailLogsPerPage', 20));

        $defaultSort = $settings->string('adminEmailLogsDefaultSort', 'idDesc');
        if (!in_array($defaultSort, NotificationEmailLog::SORTS, true)) {
            $defaultSort = 'idDesc';
        }

        $processingMode = $settings->string('adminEmailLogsProcessingMode', 'auto');
        if (!in_array($processingMode, ['frontend', 'server', 'auto'], true)) {
            $processingMode = 'auto';
        }

        $defaultView = $settings->string('adminEmailLogsDefaultView', 'table');
        if (!in_array($defaultView, ['table', 'grid'], true)) {
            $defaultView = 'table';
        }

        /*
        |--------------------------------------------------------------------------
        | Валидация входящих параметров
        |--------------------------------------------------------------------------
        */
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', Rule::in(NotificationEmailLog::SORTS)],
            'page' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', Rule::in(NotificationEmailLog::STATUSES)],
            'event' => ['nullable', 'string', 'max:100'],
            'recipient' => ['nullable', 'string', 'max:255'],
            'uuid' => ['nullable', 'string', 'max:255'],
            'attempts' => ['nullable', 'integer', 'min:0'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
        ]);

        $filters = $this->indexFilters($validated);
        $search = trim((string) ($validated['search'] ?? ''));
        $sortParam = $validated['sort'] ?? $defaultSort;

        /*
        |--------------------------------------------------------------------------
        | Режим обработки определяется общим количеством записей
        |--------------------------------------------------------------------------
        */
        $totalRecords = NotificationEmailLog::query()->count();

        $useServerProcessing = app(ProcessingModeService::class)->shouldUseServer(
            $processingMode,
            $totalRecords,
            300
        );

        $logs = $this->getIndexLogs(
            useServerProcessing: $useServerProcessing,
            perPage: $perPage,
            sort: $sortParam,
            search: $search,
            filters: $filters
        );

        /*
        |--------------------------------------------------------------------------
        | Статистика по всей таблице (не зависит от фильтров)
        |--------------------------------------------------------------------------
        */
        $stats = NotificationEmailLog::query()
            ->select('status', DB::raw('COUNT(*) AS total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        $stats = array_merge(
            array_fill_keys(NotificationEmailLog::STATUSES, 0),
            $stats
        );

        /*
        |--------------------------------------------------------------------------
        | Справочники для универсального компонента фильтрации
        |--------------------------------------------------------------------------
        */
        $events = NotificationEmailLog::query()
            ->whereNotNull('event')
            ->where('event', '!=', '')
            ->distinct()
            ->orderBy('event')
            ->pluck('event')
            ->values()
            ->all();

        $statuses = [
            NotificationEmailLog::STATUS_PENDING => 'В очереди',
            NotificationEmailLog::STATUS_PROCESSING => 'Обработка',
            NotificationEmailLog::STATUS_RETRYING => 'Повторная попытка',
            NotificationEmailLog::STATUS_SENT => 'Отправлено',
            NotificationEmailLog::STATUS_FAILED => 'Ошибка',
            NotificationEmailLog::STATUS_SKIPPED => 'Пропущено',
        ];

        return Inertia::render('Admin/NotificationEmailLog/Index', [
            'adminEmailLogsPerPage' => $perPage,
            'adminEmailLogsDefaultSort' => $defaultSort,
            'adminEmailLogsProcessingMode' => $processingMode,
            'adminEmailLogsDefaultView' => $defaultView,
            'useServerProcessing' => $useServerProcessing,
            'effectiveMode' => $useServerProcessing ? 'server' : 'frontend',
            'logs' => NotificationEmailLogResource::collection($logs),
            'totalRecords' => $totalRecords,
            'sortParam' => $sortParam,
            'search' => $search,
            'filters' => $filters,
            'stats' => $stats,
            'statuses' => $statuses,
            'events' => $events,
        ]);
    }

    /**
     * Формирует единый контракт структурных фильтров для Vue и модели.
     * Значение attempts=0 не должно теряться.
     */
    private function indexFilters(array $validated): array
    {
        return [
            'status' => $validated['status'] ?? '',
            'event' => $validated['event'] ?? '',
            'recipient' => trim((string) ($validated['recipient'] ?? '')),
            'uuid' => trim((string) ($validated['uuid'] ?? '')),
            'attempts' => $validated['attempts'] ?? '',
            'date_from' => $validated['date_from'] ?? '',
            'date_to' => $validated['date_to'] ?? '',
        ];
    }

    /**
     * Frontend: структурные фильтры SQL, затем все подходящие записи во Vue.
     * Server: структурные фильтры + поиск + сортировка + пагинация в SQL.
     */
    private function getIndexLogs(
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = '',
        array $filters = []
    ): Collection|LengthAwarePaginator {
        $query = NotificationEmailLog::query()->indexFilters($filters);

        if ($useServerProcessing) {
            return $query
                ->search($search)
                ->sortByParam($sort)
                ->paginate($perPage)
                ->withQueryString();
        }

        return $query
            ->sortByParam($sort)
            ->get();
    }
}
