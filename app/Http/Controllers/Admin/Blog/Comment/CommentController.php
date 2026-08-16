<?php

namespace App\Http\Controllers\Admin\Blog\Comment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Blog\Comment\ApproveCommentRequest;
use App\Http\Requests\Admin\Blog\Comment\CommentRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Resources\Admin\Blog\Comment\CommentResource;
use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use App\Models\Admin\Blog\Comment\Comment;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления Комментариями (Blog) в админке.
 *
 * Паттерн как у TagController:
 * - index без пагинации (коллекция)
 * - isAdmin пробрасывается в Inertia
 * - owner-ограничение (если нужно) — по аналогии (ниже оставлено выключенным)
 * - activity (single + bulk)
 * - moderation (approve/reject) только для admin — один в один как у тегов
 *
 * Важно: локали нет.
 */
class CommentController extends Controller
{
    /**
     * Базовый query (если позже решишь ограничивать “владелец/админ” — включишь тут).
     * Сейчас возвращаем все комментарии всем, как было.
     */
    private function baseQuery(): Builder
    {
        return Comment::query();

        // Если захочешь как у тегов (автор видит только свои):
        // $q = Comment::query();
        // $user = auth()->user();
        // if ($user && !$user->hasRole('admin')) {
        //     $q->where('user_id', $user->id);
        // }
        // return $q;
    }

    /** Список комментариев */
    public function index(
        Request $request
    ): Response {
        $locale = app()->getLocale();

        $settings = app(
            AdminSettingsService::class
        );

        $perPage = $settings->int(
            'adminCommentsPerPage',
            6
        );

        $defaultSort = $settings->string(
            'adminCommentsDefaultSort',
            'idDesc'
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
            'adminCommentsProcessingMode',
            'frontend'
        );

        $commentsCount = $this->baseQuery()
            ->count();

        $useServerProcessing = app(
            ProcessingModeService::class
        )->shouldUseServer(
            $processingMode,
            $commentsCount,
            300
        );

        $user = auth()->user();

        $isAdmin = (bool) (
            $user
            && $user->hasRole('admin')
        );

        try {
            $comments = $this->getIndexComments(
                locale: $locale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            return Inertia::render(
                'Admin/Blog/Comments/Index',
                [
                    'comments' =>
                        CommentResource::collection(
                            $comments
                        ),

                    'commentsCount' =>
                        $commentsCount,

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminCommentsProcessingMode' =>
                        $processingMode,

                    'adminCommentsPerPage' =>
                        $perPage,

                    'adminCommentsDefaultSort' =>
                        $defaultSort,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'isAdmin' =>
                        $isAdmin,
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки комментариев для Index: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/Blog/Comments/Index',
                [
                    'comments' => [],
                    'commentsCount' => 0,

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminCommentsProcessingMode' =>
                        $processingMode,

                    'adminCommentsPerPage' =>
                        $perPage,

                    'adminCommentsDefaultSort' =>
                        $defaultSort,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'isAdmin' =>
                        $isAdmin,

                    'error' =>
                        __('admin/controllers.index_error'),
                ]
            );
        }
    }

    /** Форма редактирования. */
    public function edit(Comment $comment): Response
    {
        $comment->load([
            'user:id,name,email',
            'moderator:id,name,email',
            'commentable',
            'parent' => fn($q) => $q->with('user:id,name'),
        ])->loadCount('replies');

        $user = auth()->user();
        $isAdmin = (bool) ($user && $user->hasRole('admin'));

        return Inertia::render('Admin/Blog/Comments/Edit', [
            'comment' => new CommentResource($comment),
            'isAdmin' => $isAdmin,
        ]);
    }

    /** Обновление комментария (только управляемые поля). */
    public function update(CommentRequest $request, Comment $comment): RedirectResponse
    {
        $data = $request->validated();
        unset($data['_method']);

        // запрещаем менять “ключи связи”
        unset(
            $data['user_id'],
            $data['commentable_id'],
            $data['commentable_type'],
            $data['parent_id']
        );

        // moderated_* — не принимаем с фронта
        unset($data['moderated_by'], $data['moderated_at']);

        try {
            DB::beginTransaction();
            $comment->update($data);
            DB::commit();

            Log::info('Комментарий обновлен', ['id' => $comment->id]);

            return redirect()->route('admin.comments.index')
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка при обновлении комментария ID {$comment->id}: ".$e->getMessage(), ['exception' => $e]);

            return back()->withInput()->with('error', __('admin/controllers.updated_error'));
        }
    }

    /** Удаление комментария (дочерние удалятся каскадно по FK). */
    public function destroy(Comment $comment): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $comment->delete();
            DB::commit();

            Log::info('Комментарий удален', ['id' => $comment->id]);

            return redirect()->route('admin.comments.index')
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка при удалении комментария ID {$comment->id}: ".$e->getMessage(), ['exception' => $e]);

            return back()->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /** Массовое удаление (JSON) — оставляем как рабочий метод. */
    public function bulkDestroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required','array'],
            'ids.*' => ['required','integer','exists:comments,id'],
        ]);

        $ids = $validated['ids'];

        try {
            DB::beginTransaction();
            Comment::whereIn('id', $ids)->delete();
            DB::commit();

            Log::info('Комментарии удалены массово', ['ids' => $ids]);

            return response()->json([
                'success' => true,
                'message' => __('admin/controllers.bulk_deleted_success'),
                'reload'  => true,
            ]);

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка при массовом удалении комментариев: ".$e->getMessage(), [
                'ids' => $ids,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => __('admin/controllers.bulk_deleted_error'),
            ], 500);
        }
    }

    /** Переключение activity (JSON) — оставляем как рабочий метод. */
    public function updateActivity(UpdateActivityRequest $request, Comment $comment): JsonResponse
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();
            $comment->activity = (bool) $validated['activity'];
            $comment->save();
            DB::commit();

            Log::info("Обновлена активность комментария", [
                'id' => $comment->id,
                'activity' => $comment->activity
            ]);

            return response()->json([
                'success'  => true,
                'activity' => (bool) $comment->activity,
                'message'  => $comment->activity
                    ? __('admin/controllers.activated_success')
                    : __('admin/controllers.deactivated_success'),
            ]);

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка updateActivity комментария ID {$comment->id}: ".$e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => __('admin/controllers.activity_updated_error'),
            ], 500);
        }
    }

    /** Массовое обновление activity (redirect) — оставляем. */
    public function bulkUpdateActivity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids'      => ['required','array'],
            'ids.*'    => ['required','integer','exists:comments,id'],
            'activity' => ['required','boolean'],
        ]);

        try {
            DB::beginTransaction();

            Comment::whereIn('id', $validated['ids'])
                ->update(['activity' => (bool) $validated['activity']]);

            DB::commit();

            Log::info('Массово обновлена активность комментариев', [
                'count' => count($validated['ids']),
                'activity' => (bool) $validated['activity'],
            ]);

            return back()->with('success', __('admin/controllers.bulk_activity_updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка bulkUpdateActivity комментариев: ".$e->getMessage(), ['exception' => $e]);

            return back()->with('error', __('admin/controllers.bulk_activity_updated_error'));
        }
    }

    /**
     * ✅ Модерация (approve/reject) — ОДИН В ОДИН как у тегов.
     * PUT/POST /admin/actions/comments/{comment}/approve
     *
     * Принимает:
     * - moderation_status: 0|1|2 (required)
     * - moderation_note: string|max:500 (nullable)
     *
     * Ставит:
     * - moderated_by = admin id
     * - moderated_at = now()
     */
    public function approve(Request $request, Comment $comment): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'moderation_status' => ['required', 'integer', Rule::in([0, 1, 2])],
            'moderation_note'   => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $comment->update([
                'moderation_status' => (int) $validated['moderation_status'],
                'moderation_note'   => $validated['moderation_note'] ?? null,
                'moderated_by'      => $user->id,
                'moderated_at'      => now(),
            ]);

            $msg = __('admin/controllers.updated_success');

            return $request->expectsJson()
                ? response()->json([
                    'message' => $msg,
                    'comment' => new CommentResource(
                        $comment->load(['user:id,name,email','moderator:id,name,email','commentable','parent.user:id,name'])
                    ),
                ])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error("Ошибка approve комментария {$comment->id}: ".$e->getMessage(), ['exception' => $e]);

            $msg = __('admin/controllers.updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Оставил метод approve с типизированным реквестом (если он тебе нужен где-то),
     * но логика модерации должна быть одинаковой с тегами — поэтому основная approve выше.
     *
     * Если ты хочешь строго использовать ApproveCommentRequest — скажи, и я заменю validate() на $request->validated().
     */
    public function approveWithRequest(ApproveCommentRequest $request, Comment $comment): RedirectResponse|JsonResponse
    {
        // Этот метод можно удалить, если нигде не используется.
        $user = auth()->user();
        if (!$user || !$user->hasRole('admin')) {
            abort(403);
        }

        $validated = $request->validated();

        try {
            $comment->update([
                'moderation_status' => (int) $validated['moderation_status'],
                'moderation_note'   => $validated['moderation_note'] ?? null,
                'moderated_by'      => $user->id,
                'moderated_at'      => now(),
            ]);

            $msg = __('admin/controllers.updated_success');

            return $request->expectsJson()
                ? response()->json([
                    'message' => $msg,
                    'comment' => new CommentResource($comment->load(['user','moderator','commentable','parent.user'])),
                ])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error("Ошибка approveWithRequest комментария {$comment->id}: ".$e->getMessage(), ['exception' => $e]);

            $msg = __('admin/controllers.updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /** Базовый запрос списка комментариев */
    private function indexCommentsQuery(
        string $locale
    ): Builder {
        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $locales = array_values(
            array_unique([
                $locale,
                $fallbackLocale,
            ])
        );

        return $this->baseQuery()
            ->with([
                /**
                 * Автор нужен таблице,
                 * карточкам и frontend-поиску.
                 */
                'user:id,name,email',

                /**
                 * Модератор нужен
                 * frontend-поиску.
                 */
                'moderator:id,name,email',

                /**
                 * Родительский комментарий.
                 */
                'parent' => fn ($query) =>
                $query->with([
                    'user:id,name',
                ]),

                /**
                 * Полиморфный объект комментария.
                 *
                 * Для поддерживаемых Blog-сущностей
                 * заранее загружаем только:
                 *
                 * current locale + fallback ru.
                 */
                'commentable' => function (
                    MorphTo $morphTo
                ) use ($locales) {
                    $morphTo->morphWith([
                        BlogArticle::class => [
                            'translations' =>
                                fn ($query) =>
                                $query->whereIn(
                                    'locale',
                                    $locales
                                ),
                        ],
                        BlogVideo::class => [
                            'translations' =>
                                fn ($query) =>
                                $query->whereIn(
                                    'locale',
                                    $locales
                                ),
                        ],
                    ]);
                },
            ])
            ->withCount([
                'replies',
            ]);
    }

    /** Получение списка комментариев по режиму обработки */
    private function getIndexComments(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = '',
    ) {
        $query = $this->indexCommentsQuery(
            $locale
        );

        /**
         * Server mode:
         *
         * поиск, фильтрация, сортировка
         * и пагинация выполняются SQL.
         */
        if ($useServerProcessing) {
            return $query
                ->search(
                    $search
                )
                ->sortByParam(
                    $sort,
                    $locale
                )
                ->paginate(
                    $perPage
                )
                ->withQueryString();
        }

        /**
         * Frontend mode:
         *
         * отдаём полную коллекцию.
         * Поиск, фильтрацию, сортировку
         * и локальную пагинацию выполняет Vue.
         */
        return $query
            ->orderByDesc(
                'comments.id'
            )
            ->get();
    }
}
