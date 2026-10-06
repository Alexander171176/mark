<?php

namespace App\Http\Controllers\Admin\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Notification\NotificationResource;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class NotificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    /**
     * Центр внутренних уведомлений PulsarCMS.
     *
     * Поддерживает:
     * - frontend;
     * - server;
     * - auto режимы обработки;
     * - поиск;
     * - сортировку;
     * - фильтры;
     * - серверную пагинацию.
     *
     * Пользователь с ролью admin видит все уведомления системы.
     * Остальные пользователи видят только свои уведомления.
     */
    public function index(Request $request): Response
    {
        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int(
            'adminNotificationsPerPage',
            20
        );

        $defaultSort = $settings->string(
            'adminNotificationsDefaultSort',
            'createdAtDesc'
        );

        $sortParam = (string) $request->query(
            'sort',
            $defaultSort
        );

        $search = trim(
            (string) $request->query(
                'search',
                ''
            )
        );

        $processingMode = $settings->string(
            'adminNotificationsProcessingMode',
            'auto'
        );

        /**
         * Общее количество доступных пользователю уведомлений.
         *
         * Используется для определения режима auto.
         *
         * baseQuery() уже учитывает доступ:
         * - admin видит все уведомления;
         * - остальные пользователи видят только свои.
         */
        $notificationsCount = $this->baseQuery()->count();

        $useServerProcessing = app(
            ProcessingModeService::class
        )->shouldUseServer(
            $processingMode,
            $notificationsCount,
            300
        );

        try {
            $notifications = $this->getIndexNotifications(
                request: $request,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            return Inertia::render(
                'Admin/Notifications/Index',
                [
                    'notifications' =>
                        NotificationResource::collection(
                            $notifications
                        ),

                    'notificationsCount' =>
                        $notificationsCount,

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminNotificationsProcessingMode' =>
                        $processingMode,

                    'adminNotificationsPerPage' =>
                        $perPage,

                    'adminNotificationsDefaultSort' =>
                        $defaultSort,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'filters' =>
                        $this->indexFilters($request),
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки уведомлений для Index: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/Notifications/Index',
                [
                    'notifications' => [],

                    'notificationsCount' =>
                        $notificationsCount,

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminNotificationsProcessingMode' =>
                        $processingMode,

                    'adminNotificationsPerPage' =>
                        $perPage,

                    'adminNotificationsDefaultSort' =>
                        $defaultSort,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'filters' =>
                        $this->indexFilters($request),

                    'error' =>
                        __('admin/controllers.index_error'),
                ]
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Bell
    |--------------------------------------------------------------------------
    */

    /**
     * Получить последние собственные уведомления
     * текущего авторизованного пользователя.
     *
     * Используется глобальным колокольчиком уведомлений.
     * Даже для admin возвращаются только его
     * собственные уведомления.
     */
    public function recent(
        Request $request
    ): AnonymousResourceCollection {
        $limit = min(
            max(
                (int) $request->integer(
                    'limit',
                    5
                ),
                1
            ),
            10
        );

        $notifications = $request->user()
            ->notifications()
            ->latest('created_at')
            ->limit($limit)
            ->get();

        return NotificationResource::collection(
            $notifications
        );
    }

    /**
     * Получить количество собственных
     * непрочитанных уведомлений пользователя.
     *
     * Даже для admin учитываются только его
     * собственные непрочитанные уведомления.
     */
    public function unreadCount(
        Request $request
    ): JsonResponse {
        return response()->json([
            'count' => $request->user()
                ->unreadNotifications()
                ->count(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Read
    |--------------------------------------------------------------------------
    */

    /**
     * Отметить собственное уведомление
     * текущего пользователя как прочитанное.
     *
     * Роль admin не даёт права изменять
     * состояние чужого уведомления.
     */
    public function markAsRead(
        Request $request,
        string $notification
    ): JsonResponse {
        $notification = $this->findOwnedNotification(
            $request,
            $notification
        );

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return response()->json([
            'message' =>
                'Уведомление отмечено как прочитанное.',
        ]);
    }

    /**
     * Отметить все собственные уведомления
     * текущего пользователя как прочитанные.
     *
     * Для admin чужие уведомления
     * остаются без изменений.
     */
    public function markAllAsRead(
        Request $request
    ): JsonResponse {
        $request->user()
            ->unreadNotifications()
            ->update([
                'read_at' => now(),
            ]);

        return response()->json([
            'message' =>
                'Все уведомления отмечены как прочитанные.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    /**
     * Удалить собственное уведомление
     * текущего пользователя.
     *
     * Роль admin не даёт права удалять
     * уведомления других пользователей.
     */
    public function destroy(
        Request $request,
        string $notification
    ): JsonResponse {
        $notification = $this->findOwnedNotification(
            $request,
            $notification
        );

        $notification->delete();

        return response()->json([
            'message' =>
                'Уведомление удалено.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Queries
    |--------------------------------------------------------------------------
    */

    /**
     * Базовый query с проверкой доступа.
     *
     * Пользователь с ролью admin видит
     * все уведомления системы.
     *
     * Остальные пользователи видят
     * только собственные уведомления.
     *
     * Расширенная видимость admin применяется
     * только к просмотру списка и не даёт права
     * изменять чужие уведомления.
     */
    protected function baseQuery(): Builder
    {
        $query = DatabaseNotification::query();

        $user = auth()->user();

        if (
            $user
            && method_exists(
                $user,
                'hasRole'
            )
            && !$user->hasRole('admin')
        ) {
            $query
                ->where(
                    'notifiable_type',
                    $user->getMorphClass()
                )
                ->where(
                    'notifiable_id',
                    $user->getKey()
                );
        }

        return $query;
    }

    /**
     * Получить уведомления для Admin Index.
     *
     * Frontend:
     * - структурные фильтры выполняются Laravel;
     * - поиск выполняется Vue;
     * - сортировка выполняется Vue;
     * - пагинация выполняется Vue.
     *
     * Server:
     * - структурные фильтры выполняются Laravel;
     * - поиск выполняется Laravel;
     * - сортировка выполняется Laravel;
     * - пагинация выполняется Laravel.
     */
    protected function getIndexNotifications(
        Request $request,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search
    ): Collection|LengthAwarePaginator {
        $query = $this->baseQuery()
            ->with('notifiable');

        $this->applyIndexFilters(
            $query,
            $request
        );

        if ($useServerProcessing) {
            $this->applySearch(
                $query,
                $search
            );

            $this->applySort(
                $query,
                $sort
            );

            return $query
                ->paginate($perPage)
                ->withQueryString();
        }

        return $query->get();
    }

    /**
     * Серверный поиск уведомлений.
     *
     * Основные данные уведомления хранятся
     * внутри текстового JSON-поля data.
     *
     * Поиск по data позволяет находить:
     * - category;
     * - type;
     * - level;
     * - title;
     * - message;
     * - entity_type;
     * - entity_id;
     * - вложенные данные.
     *
     * Дополнительно ищем по получателю
     * и UUID уведомления.
     */
    protected function applySearch(
        Builder $query,
        string $search
    ): void {
        if ($search === '') {
            return;
        }

        $query->where(
            function (Builder $searchQuery) use ($search) {
                $searchQuery
                    ->where(
                        'notifications.id',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'notifications.type',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'notifications.notifiable_type',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'notifications.notifiable_id',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'notifications.data',
                        'like',
                        '%' . $search . '%'
                    );
            }
        );
    }

    /**
     * Серверная сортировка уведомлений.
     */
    protected function applySort(
        Builder $query,
        string $sort
    ): void {
        switch ($sort) {
            case 'createdAtAsc':
                $query->orderBy(
                    'notifications.created_at',
                    'asc'
                );
                break;

            case 'updatedAtDesc':
                $query->orderBy(
                    'notifications.updated_at',
                    'desc'
                );
                break;

            case 'updatedAtAsc':
                $query->orderBy(
                    'notifications.updated_at',
                    'asc'
                );
                break;

            case 'categoryAsc':
                $query->orderByRaw(
                    "JSON_UNQUOTE(JSON_EXTRACT(notifications.data, '$.category')) ASC"
                );
                break;

            case 'categoryDesc':
                $query->orderByRaw(
                    "JSON_UNQUOTE(JSON_EXTRACT(notifications.data, '$.category')) DESC"
                );
                break;

            case 'levelAsc':
                $query->orderByRaw(
                    "JSON_UNQUOTE(JSON_EXTRACT(notifications.data, '$.level')) ASC"
                );
                break;

            case 'levelDesc':
                $query->orderByRaw(
                    "JSON_UNQUOTE(JSON_EXTRACT(notifications.data, '$.level')) DESC"
                );
                break;

            case 'readAsc':
                $query
                    ->orderByRaw(
                        'notifications.read_at IS NULL DESC'
                    )
                    ->orderBy(
                        'notifications.read_at',
                        'asc'
                    );
                break;

            case 'readDesc':
                $query
                    ->orderByRaw(
                        'notifications.read_at IS NULL ASC'
                    )
                    ->orderBy(
                        'notifications.read_at',
                        'desc'
                    );
                break;

            case 'createdAtDesc':
            default:
                $query->orderBy(
                    'notifications.created_at',
                    'desc'
                );
                break;
        }
    }

    /**
     * Применить фильтры списка уведомлений.
     */
    protected function applyIndexFilters(
        Builder $query,
        Request $request
    ): void {
        /**
         * Состояние прочтения.
         *
         * Поддерживает:
         * - unread;
         * - read.
         *
         * all или отсутствие параметра
         * не ограничивает выборку.
         */
        if ($request->filled('read_status')) {
            $readStatus = (string) $request->input(
                'read_status'
            );

            if ($readStatus === 'unread') {
                $query->whereNull(
                    'notifications.read_at'
                );
            } elseif ($readStatus === 'read') {
                $query->whereNotNull(
                    'notifications.read_at'
                );
            }
        }

        /**
         * Категория уведомления.
         */
        if ($request->filled('category')) {
            $query->whereRaw(
                "JSON_UNQUOTE(JSON_EXTRACT(notifications.data, '$.category')) = ?",
                [
                    (string) $request->input(
                        'category'
                    ),
                ]
            );
        }

        /**
         * Уровень уведомления.
         */
        if ($request->filled('level')) {
            $query->whereRaw(
                "JSON_UNQUOTE(JSON_EXTRACT(notifications.data, '$.level')) = ?",
                [
                    (string) $request->input(
                        'level'
                    ),
                ]
            );
        }

        /**
         * Принадлежность уведомления.
         *
         * Фильтр имеет смысл только для admin,
         * потому что обычный пользователь уже
         * ограничен собственными уведомлениями
         * через baseQuery().
         *
         * Поддерживает:
         * - mine;
         * - others.
         *
         * all или отсутствие параметра
         * не ограничивает выборку.
         */
        $user = $request->user();

        if (
            $user
            && method_exists(
                $user,
                'hasRole'
            )
            && $user->hasRole('admin')
            && $request->filled('ownership')
        ) {
            $ownership = (string) $request->input(
                'ownership'
            );

            if ($ownership === 'mine') {
                $query
                    ->where(
                        'notifications.notifiable_type',
                        $user->getMorphClass()
                    )
                    ->where(
                        'notifications.notifiable_id',
                        $user->getKey()
                    );
            } elseif ($ownership === 'others') {
                $query->where(
                    function (Builder $ownershipQuery) use ($user) {
                        $ownershipQuery
                            ->where(
                                'notifications.notifiable_type',
                                '!=',
                                $user->getMorphClass()
                            )
                            ->orWhere(
                                'notifications.notifiable_id',
                                '!=',
                                $user->getKey()
                            );
                    }
                );
            }
        }

        /**
         * Период создания уведомления.
         */
        if ($request->filled('date_from')) {
            $query->whereDate(
                'notifications.created_at',
                '>=',
                $request->input('date_from')
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'notifications.created_at',
                '<=',
                $request->input('date_to')
            );
        }
    }

    /**
     * Текущие фильтры списка уведомлений.
     *
     * @return array<string, mixed>
     */
    protected function indexFilters(
        Request $request
    ): array {
        return [
            'read_status' =>
                $request->input('read_status'),

            'category' =>
                $request->input('category'),

            'level' =>
                $request->input('level'),

            'ownership' =>
                $request->input('ownership'),

            'date_from' =>
                $request->input('date_from'),

            'date_to' =>
                $request->input('date_to'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Ownership
    |--------------------------------------------------------------------------
    */

    /**
     * Найти собственное уведомление
     * текущего пользователя.
     *
     * Метод используется исключительно
     * для действий, изменяющих состояние
     * уведомления.
     *
     * Даже пользователь с ролью admin
     * не может через этот метод получить
     * чужое уведомление.
     */
    private function findOwnedNotification(
        Request $request,
        string $notification
    ): DatabaseNotification {
        return $request->user()
            ->notifications()
            ->whereKey($notification)
            ->firstOrFail();
    }
}
