<?php

namespace App\Http\Controllers\Public\Default\Blog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Blog\Comment\CommentResource;
use App\Models\Admin\Blog\Article\Article;
use App\Models\Admin\Blog\Comment\Comment;
use App\Models\Admin\Blog\Video\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Контроллер для показа Комментариев в публичной части.
 *
 * @version 1.1
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see Comment
 * @see CommentResource
 * @see Article
 * @see Video
 */
class CommentController extends Controller
{
    /**
     * Разрешённые типы commentable (защита).
     */
    protected function allowedCommentableTypes(): array
    {
        return [
            Article::class,
            Video::class,
        ];
    }

    /**
     * Получить дерево комментариев для сущности (article/video).
     * Только публичные: approved + active.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'commentable_type' => ['required', 'string', Rule::in($this->allowedCommentableTypes())],
            'commentable_id'   => ['required', 'integer', 'min:1'],
        ]);

        $comments = Comment::query()
            ->forPublic()
            ->root()
            ->where('commentable_type', $validated['commentable_type'])
            ->where('commentable_id', $validated['commentable_id'])
            ->with([
                'user',
                'replies' => fn ($q) => $q
                    ->forPublic()
                    ->with('user')
                    ->orderBy('id', 'asc'),
            ])
            ->withCount('replies')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'data' => CommentResource::collection($comments),
        ]);
    }

    /**
     * Создать комментарий / ответ.
     * Важно: parent_id должен принадлежать этому же commentable.
     */
    public function store(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Для добавления комментария нужно авторизоваться.',
            ], 401);
        }

        $validated = $request->validate([
            'commentable_type' => ['required', 'string', Rule::in($this->allowedCommentableTypes())],
            'commentable_id'   => ['required', 'integer', 'min:1'],
            'content'          => ['required', 'string', 'max:500'],
            'parent_id'        => ['nullable', 'integer', 'min:1'],
        ]);

        try {
            // ✅ проверяем parent_id в рамках того же commentable
            if (!empty($validated['parent_id'])) {
                $parentExists = Comment::query()
                    ->where('id', $validated['parent_id'])
                    ->where('commentable_type', $validated['commentable_type'])
                    ->where('commentable_id', $validated['commentable_id'])
                    ->exists();

                if (!$parentExists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Некорректный parent_id для указанного ресурса.',
                    ], 422);
                }
            }

            $comment = Comment::create([
                'user_id'          => auth()->id(),
                'commentable_type' => $validated['commentable_type'],
                'commentable_id'   => $validated['commentable_id'],
                'parent_id'        => $validated['parent_id'] ?? null,
                'content'          => $validated['content'],

                // по умолчанию: видимый, но на модерации
                'activity'         => true,
                'moderation_status'=> 0, // pending
            ]);

            $comment->load(['user']);

            return response()->json([
                'success' => true,
                'data'    => new CommentResource($comment),
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Ошибка при создании комментария: '.$e->getMessage(), [
                'payload' => $validated,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при сохранении комментария',
            ], 500);
        }
    }

    /**
     * Показать конкретный комментарий (только публичный).
     */
    public function show(Comment $comment): JsonResponse
    {
        if (! $comment->isActive() || ! $comment->isApproved()) {
            return response()->json(['message' => 'Комментарий не найден или неактивен'], 404);
        }

        $comment->load([
            'user',
            'replies' => fn ($q) => $q->forPublic()->with('user')->orderBy('id', 'asc'),
        ])->loadCount('replies');

        return response()->json([
            'data' => new CommentResource($comment),
        ]);
    }

    /**
     * Обновить свой комментарий.
     * После редактирования отправляем на повторную модерацию.
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Нужна авторизация'], 401);
        }

        if ((int) $comment->user_id !== (int) auth()->id()) {
            return response()->json(['message' => 'Вы не можете редактировать этот комментарий'], 403);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:500'],
        ]);

        try {
            $comment->update([
                'content' => $validated['content'],
                'moderation_status' => 0, // pending после правки
                'moderated_by' => null,
                'moderated_at' => null,
                'moderation_note' => null,
            ]);

            $comment->load('user')->loadCount('replies');

            return response()->json([
                'success' => true,
                'data' => new CommentResource($comment),
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Ошибка при обновлении комментария: '.$e->getMessage(), ['id' => $comment->id]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении комментария',
            ], 500);
        }
    }

    /**
     * Удалить свой комментарий.
     */
    public function destroy(Comment $comment): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Нужна авторизация'], 401);
        }

        if ((int) $comment->user_id !== (int) auth()->id()) {
            return response()->json(['message' => 'Вы не можете удалить этот комментарий'], 403);
        }

        try {
            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Комментарий удалён',
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Ошибка при удалении комментария: '.$e->getMessage(), ['id' => $comment->id]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении комментария',
            ], 500);
        }
    }
}
