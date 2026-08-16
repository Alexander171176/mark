<?php

namespace App\Http\Controllers\Public\Default\Blog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\Blog\Comment\CommentResource;
use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use App\Models\Admin\Blog\Comment\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class CommentController extends Controller
{
    /**
     * Разрешённые сущности комментариев.
     *
     * Комментарии блога доступны только:
     * - статьям;
     * - видео.
     */
    protected function allowedCommentableTypes(): array
    {
        return [
            BlogArticle::class,
            BlogVideo::class,
        ];
    }

    /**
     * Получить публичный объект,
     * к которому добавляется комментарий.
     */
    protected function resolveCommentable(
        string $type,
        int $id
    ): ?Model {
        if (!in_array(
            $type,
            $this->allowedCommentableTypes(),
            true
        )) {
            return null;
        }

        return $type::query()
            ->forPublic()
            ->find($id);
    }

    /**
     * Получить дерево публичных комментариев.
     */
    public function index(
        Request $request
    ): JsonResponse {
        $validated = $request->validate([
            'commentable_type' => [
                'required',
                'string',
                Rule::in(
                    $this->allowedCommentableTypes()
                ),
            ],

            'commentable_id' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        /**
         * Проверяем, что сама статья/видео
         * существует и доступна публично.
         */
        $commentable = $this->resolveCommentable(
            $validated['commentable_type'],
            (int) $validated['commentable_id']
        );

        if (!$commentable) {
            return response()->json([
                'message' =>
                    'Публичный объект не найден.',
            ], 404);
        }

        $comments = Comment::query()
            ->forPublic()
            ->root()
            ->where(
                'commentable_type',
                $validated['commentable_type']
            )
            ->where(
                'commentable_id',
                $validated['commentable_id']
            )
            ->with([
                /**
                 * Автор корневого комментария.
                 */
                'user:id,name',

                /**
                 * Только публичные ответы
                 * того же объекта.
                 */
                'replies' => fn ($query) =>
                $query
                    ->forPublic()
                    ->where(
                        'commentable_type',
                        $validated['commentable_type']
                    )
                    ->where(
                        'commentable_id',
                        $validated['commentable_id']
                    )
                    ->with([
                        'user:id,name',
                    ])
                    ->orderBy(
                        'id',
                        'asc'
                    ),
            ])
            ->withCount([
                /**
                 * Count должен соответствовать
                 * именно публичным ответам.
                 */
                'replies as replies_count' =>
                    fn ($query) =>
                    $query->forPublic(),
            ])
            ->orderBy(
                'id',
                'asc'
            )
            ->get();

        return response()->json([
            'data' =>
                CommentResource::collection(
                    $comments
                ),
        ]);
    }

    /**
     * Создать комментарий или ответ.
     */
    public function store(
        Request $request
    ): JsonResponse {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Для добавления комментария нужно авторизоваться.',
            ], 401);
        }

        $validated = $request->validate([
            'commentable_type' => [
                'required',
                'string',
                Rule::in(
                    $this->allowedCommentableTypes()
                ),
            ],

            'commentable_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'content' => [
                'required',
                'string',
                'max:500',
            ],

            'parent_id' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ]);

        try {
            /**
             * Нельзя создавать комментарий
             * для несуществующей или
             * непубличной статьи/видео.
             */
            $commentable = $this->resolveCommentable(
                $validated['commentable_type'],
                (int) $validated['commentable_id']
            );

            if (!$commentable) {
                return response()->json([
                    'success' => false,
                    'message' =>
                        'Публичный объект не найден.',
                ], 404);
            }

            /**
             * Если создаётся ответ,
             * parent должен:
             *
             * - существовать;
             * - быть публичным;
             * - принадлежать тому же объекту.
             */
            if (!empty(
            $validated['parent_id']
            )) {
                $parentExists = Comment::query()
                    ->forPublic()
                    ->whereKey(
                        $validated['parent_id']
                    )
                    ->where(
                        'commentable_type',
                        $validated['commentable_type']
                    )
                    ->where(
                        'commentable_id',
                        $validated['commentable_id']
                    )
                    ->exists();

                if (!$parentExists) {
                    return response()->json([
                        'success' => false,
                        'message' =>
                            'Некорректный parent_id для указанного ресурса.',
                    ], 422);
                }
            }

            $comment = Comment::create([
                'user_id' =>
                    auth()->id(),

                'commentable_type' =>
                    $validated['commentable_type'],

                'commentable_id' =>
                    $validated['commentable_id'],

                'parent_id' =>
                    $validated['parent_id']
                    ?? null,

                'content' =>
                    $validated['content'],

                /**
                 * Комментарий активен,
                 * но ожидает модерацию.
                 */
                'activity' => true,

                'moderation_status' => 0,
            ]);

            $comment->load([
                'user:id,name',
            ]);

            return response()->json([
                'success' => true,

                'message' =>
                    'Комментарий отправлен на модерацию.',

                'data' =>
                    new CommentResource(
                        $comment
                    ),
            ], 201);
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при создании комментария: '
                . $e->getMessage(),
                [
                    'payload' => $validated,
                    'user_id' => auth()->id(),
                    'exception' => $e,
                ]
            );

            return response()->json([
                'success' => false,
                'message' =>
                    'Ошибка при сохранении комментария.',
            ], 500);
        }
    }

    /**
     * Показать конкретный
     * публичный комментарий.
     */
    public function show(
        Comment $comment
    ): JsonResponse {
        if (
            !$comment->isActive()
            || !$comment->isApproved()
        ) {
            return response()->json([
                'message' =>
                    'Комментарий не найден или неактивен.',
            ], 404);
        }

        $comment->load([
            'user:id,name',

            'replies' => fn ($query) =>
            $query
                ->forPublic()
                ->with([
                    'user:id,name',
                ])
                ->orderBy(
                    'id',
                    'asc'
                ),
        ])->loadCount([
            'replies as replies_count' =>
                fn ($query) =>
                $query->forPublic(),
        ]);

        return response()->json([
            'data' =>
                new CommentResource(
                    $comment
                ),
        ]);
    }

    /**
     * Обновить свой комментарий.
     *
     * После изменения комментарий
     * снова отправляется на модерацию.
     */
    public function update(
        Request $request,
        Comment $comment
    ): JsonResponse {
        if (!auth()->check()) {
            return response()->json([
                'message' =>
                    'Нужна авторизация.',
            ], 401);
        }

        if (
            (int) $comment->user_id
            !== (int) auth()->id()
        ) {
            return response()->json([
                'message' =>
                    'Вы не можете редактировать этот комментарий.',
            ], 403);
        }

        $validated = $request->validate([
            'content' => [
                'required',
                'string',
                'max:500',
            ],
        ]);

        try {
            $comment->update([
                'content' =>
                    $validated['content'],

                'moderation_status' => 0,
                'moderated_by' => null,
                'moderated_at' => null,
                'moderation_note' => null,
            ]);

            $comment->load([
                'user:id,name',
            ]);

            return response()->json([
                'success' => true,

                'message' =>
                    'Комментарий отправлен на повторную модерацию.',

                'data' =>
                    new CommentResource(
                        $comment
                    ),
            ]);
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при обновлении комментария: '
                . $e->getMessage(),
                [
                    'id' => $comment->id,
                    'exception' => $e,
                ]
            );

            return response()->json([
                'success' => false,
                'message' =>
                    'Ошибка при обновлении комментария.',
            ], 500);
        }
    }

    /**
     * Удалить свой комментарий.
     */
    public function destroy(
        Comment $comment
    ): JsonResponse {
        if (!auth()->check()) {
            return response()->json([
                'message' =>
                    'Нужна авторизация.',
            ], 401);
        }

        if (
            (int) $comment->user_id
            !== (int) auth()->id()
        ) {
            return response()->json([
                'message' =>
                    'Вы не можете удалить этот комментарий.',
            ], 403);
        }

        try {
            $comment->delete();

            return response()->json([
                'success' => true,
                'message' =>
                    'Комментарий удалён.',
            ]);
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при удалении комментария: '
                . $e->getMessage(),
                [
                    'id' => $comment->id,
                    'exception' => $e,
                ]
            );

            return response()->json([
                'success' => false,
                'message' =>
                    'Ошибка при удалении комментария.',
            ], 500);
        }
    }
}
