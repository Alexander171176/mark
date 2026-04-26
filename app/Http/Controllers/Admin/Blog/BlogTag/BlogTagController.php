<?php

namespace App\Http\Controllers\Admin\Blog\BlogTag;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Blog\BlogTag\BlogTagRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\Blog\BlogTag\BlogTagResource;
use App\Models\Admin\Blog\BlogTag\BlogTag;
use Illuminate\Database\Eloquent\Builder;
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
 * Контроллер для управления Тегами (Blog) в админке.
 *
 * Паттерн:
 * - локали (табы)
 * - CRUD
 * - owner/ограничение “владелец/админ”
 * - activity (single + bulk)
 * - sort + drag&drop (bulk)
 * - moderation (approve/reject) только для admin
 *
 * @version 1.1 (Улучшен с RMB, транзакциями, Form Requests)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see BlogTag
 * @see BlogTagRequest
 */
class BlogTagController extends Controller
{
    /**
     * Берём все разрешённые языки из config/app.php
     */
    private function availableLocales(): array
    {
        return config('app.available_locales', ['ru']);
    }

    /**
     * Базовый запрос:
     * - admin видит всё
     * - обычный пользователь только свои теги
     */
    private function baseQuery(): Builder
    {
        $query = BlogTag::query();

        $user = auth()->user();

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    /**
     * Нормализация локали:
     * если локаль невалидна — fallback
     */
    private function normalizeLocale(?string $locale): string
    {
        $availableLocales = $this->availableLocales();
        $fallback = config('app.fallback_locale', 'ru');

        if (!$locale || !in_array($locale, $availableLocales, true)) {
            return $fallback;
        }

        return $locale;
    }

    /**
     * Приведение сортировки из UI к форматам модели
     */
    private function normalizeSortParam(?string $sort): string
    {
        return match ($sort) {
            'idAsc' => 'date_asc',
            'idDesc' => 'date_desc',
            'sortAsc' => 'sort_asc',
            'sortDesc' => 'sort_desc',
            'nameAsc' => 'name_asc',
            'nameDesc' => 'name_desc',
            'viewsAsc' => 'views_asc',
            'viewsDesc' => 'views_desc',
            default => $sort ?: 'sort_asc',
        };
    }

    /**
     * Синхронизация переводов:
     * - создание/обновление текущих
     * - удаление отсутствующих
     */
    private function syncTranslations(BlogTag $tag, array $translations): void
    {
        $locales = array_keys($translations);

        foreach ($translations as $locale => $translationData) {
            $tag->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'name' => $translationData['name'] ?? null,
                    'subtitle' => $translationData['subtitle'] ?? null,
                    'short' => $translationData['short'] ?? null,
                    'description' => $translationData['description'] ?? null,
                    'meta_title' => $translationData['meta_title'] ?? null,
                    'meta_keywords' => $translationData['meta_keywords'] ?? null,
                    'meta_desc' => $translationData['meta_desc'] ?? null,
                ]
            );
        }

        $tag->translations()
            ->whereNotIn('locale', $locales)
            ->delete();
    }

    /**
     * Список тегов:
     * - поиск
     * - сортировка
     * - текущая локаль
     */
    public function index(Request $request): Response
    {
        $adminCountTags = (int) config('site_settings.AdminCountTags', 15);
        $adminSortTags = (string) config('site_settings.AdminSortTags', 'idDesc');

        $currentLocale = $this->normalizeLocale($request->query('locale'));
        $search = trim((string) $request->query('search', ''));
        $sortParam = $this->normalizeSortParam($request->query('sort', $adminSortTags));

        try {
            $tags = $this->baseQuery()
                ->with([
                    'owner',
                    'moderator',
                    'translations',
                ])
                ->withCount(['articles'])
                ->search($search, $currentLocale)
                ->sortByParam($sortParam, $currentLocale)
                ->get();

            $tagsCount = $this->baseQuery()->count();

            return Inertia::render('Admin/Blog/BlogTags/Index', [
                'tags' => BlogTagResource::collection($tags),
                'tagsCount' => $tagsCount,

                'adminCountTags' => $adminCountTags,
                'adminSortTags' => $adminSortTags,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
                'search' => $search,
                'sortParam' => $sortParam,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка blog tags: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/Blog/BlogTags/Index', [
                'tags' => [],
                'tagsCount' => 0,

                'adminCountTags' => $adminCountTags,
                'adminSortTags' => $adminSortTags,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
                'search' => $search,
                'sortParam' => $sortParam,
                'error' => 'Ошибка загрузки тегов.',
            ]);
        }
    }

    /**
     * Страница создания тега
     */
    public function create(Request $request): Response
    {
        $targetLocale = $this->normalizeLocale($request->query('locale'));

        return Inertia::render('Admin/Blog/BlogTags/Create', [
            'targetLocale' => $targetLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /**
     * Создание тега:
     * - основная запись
     * - переводы
     */
    public function store(BlogTagRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];

        unset($data['translations']);

        $user = auth()->user();

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            $data['user_id'] = $user->id;
            unset($data['moderation_status'], $data['moderated_by'], $data['moderated_at'], $data['moderation_note']);
        } else {
            $data['user_id'] = $data['user_id'] ?? $user?->id;
        }

        try {
            DB::transaction(function () use (&$tag, $data, $translations) {
                if (!isset($data['sort']) || is_null($data['sort'])) {
                    $maxSort = BlogTag::query()->max('sort');
                    $data['sort'] = is_null($maxSort) ? 0 : $maxSort + 1;
                }

                $tag = BlogTag::create($data);

                $this->syncTranslations($tag, $translations);
            });

            return redirect()
                ->route('admin.blogTags.index')
                ->with('success', 'Тег успешно создан.');
        } catch (Throwable $e) {
            Log::error('Ошибка при создании blog tag: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании тега.');
        }
    }

    /**
     * Редирект на страницу редактирования
     */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route('admin.blogTags.edit', $id);
    }

    /**
     * Страница редактирования тега:
     * - основная запись
     * - переводы
     * - счётчики
     */
    public function edit(int $blogTag, Request $request): Response
    {
        $tag = $this->baseQuery()
            ->with([
                'owner',
                'moderator',
                'translations',
            ])
            ->withCount(['articles'])
            ->findOrFail($blogTag);

        $targetLocale = $this->normalizeLocale($request->query('locale'));

        return Inertia::render('Admin/Blog/BlogTags/Edit', [
            'tag' => new BlogTagResource($tag),
            'targetLocale' => $targetLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /**
     * Обновление тега:
     * - основная запись
     * - синхронизация переводов
     */
    public function update(BlogTagRequest $request, int $blogTag): RedirectResponse
    {
        $tag = $this->baseQuery()->findOrFail($blogTag);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];

        unset($data['translations']);

        $user = auth()->user();

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            $data['user_id'] = $user->id;
            unset($data['moderation_status'], $data['moderated_by'], $data['moderated_at'], $data['moderation_note']);
        }

        try {
            DB::transaction(function () use ($tag, $data, $translations) {
                $tag->update($data);

                $this->syncTranslations($tag, $translations);
            });

            return redirect()
                ->route('admin.blogTags.index')
                ->with('success', 'Тег успешно обновлён.');
        } catch (Throwable $e) {
            Log::error('Ошибка при обновлении blog tag ID ' . $tag->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении тега.');
        }
    }

    /**
     * Удаление тега:
     * - detach статей
     * - удаление переводов
     * - удаление основной записи
     */
    public function destroy(int $blogTag): RedirectResponse
    {
        $tag = $this->baseQuery()->findOrFail($blogTag);

        try {
            DB::transaction(function () use ($tag) {
                $tag->articles()->detach();
                $tag->translations()->delete();
                $tag->delete();
            });

            return redirect()
                ->route('admin.blogTags.index')
                ->with('success', 'Тег успешно удалён.');
        } catch (Throwable $e) {
            Log::error('Ошибка при удалении blog tag ID ' . $tag->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении тега.');
        }
    }

    /**
     * Массовое удаление тегов
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:blog_tags,id'],
        ]);

        $ids = $validated['ids'];

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            return back()->with('error', 'Часть тегов недоступна для удаления.');
        }

        try {
            DB::transaction(function () use ($allowedIds) {
                DB::table('blog_article_has_tag')
                    ->whereIn('tag_id', $allowedIds)
                    ->delete();

                DB::table('blog_tag_translations')
                    ->whereIn('tag_id', $allowedIds)
                    ->delete();

                BlogTag::whereIn('id', $allowedIds)->delete();
            });

            return back()->with('success', 'Выбранные теги успешно удалены.');
        } catch (Throwable $e) {
            Log::error('Ошибка bulkDestroy blog tags: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при массовом удалении тегов.');
        }
    }

    /**
     * Обновление активности одного тега
     */
    public function updateActivity(UpdateActivityRequest $request, int $blogTag): RedirectResponse
    {
        $tag = $this->baseQuery()->findOrFail($blogTag);

        $tag->update([
            'activity' => $request->validated('activity'),
        ]);

        return back()->with('success', 'Активность тега обновлена.');
    }

    /**
     * Массовое обновление активности
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:blog_tags,id'],
            'activity' => ['required', 'boolean'],
        ]);

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $validated['ids'])
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($validated['ids'])) {
            return back()->with('error', 'Часть тегов недоступна для обновления активности.');
        }

        BlogTag::whereIn('id', $allowedIds)->update([
            'activity' => $validated['activity'],
        ]);

        $message = 'Активность выбранных тегов обновлена.';

        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : back()->with('success', $message);
    }

    /**
     * Обновление сортировки одного тега
     */
    public function updateSort(UpdateSortEntityRequest $request, int $blogTag): RedirectResponse
    {
        $tag = $this->baseQuery()->findOrFail($blogTag);

        $tag->update([
            'sort' => $request->validated('sort'),
        ]);

        return back()->with('success', 'Сортировка тега обновлена.');
    }

    /**
     * Массовое обновление сортировки
     */
    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required_without:tags', 'array'],
            'items.*.id' => ['required_with:items', 'integer', 'exists:blog_tags,id'],
            'items.*.sort' => ['required_with:items', 'integer', 'min:0'],

            'tags' => ['required_without:items', 'array'],
            'tags.*.id' => ['required_with:tags', 'integer', 'exists:blog_tags,id'],
            'tags.*.sort' => ['required_with:tags', 'integer', 'min:0'],
        ]);

        $items = $validated['items'] ?? $validated['tags'];
        $ids = array_column($items, 'id');

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            $message = 'Часть тегов недоступна для изменения сортировки.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 400)
                : back()->with('error', $message);
        }

        try {
            DB::transaction(function () use ($items) {
                foreach ($items as $row) {
                    BlogTag::whereKey($row['id'])->update([
                        'sort' => (int) $row['sort'],
                    ]);
                }
            });

            $message = 'Сортировка тегов обновлена.';

            return $request->expectsJson()
                ? response()->json(['message' => $message])
                : back()->with('success', $message);
        } catch (Throwable $e) {
            Log::error('Ошибка updateSortBulk blog tags: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            $message = 'Ошибка при массовом обновлении сортировки тегов.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }

    /**
     * Модерация тега:
     * доступ только для admin
     */
    public function approve(Request $request, int $blogTag): RedirectResponse|JsonResponse
    {
        $user = auth()->user();

        if (!$user || !method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'moderation_status' => ['required', 'integer', Rule::in([0, 1, 2])],
            'moderation_note' => ['nullable', 'string', 'max:500'],
        ]);

        $tag = BlogTag::findOrFail($blogTag);

        $tag->update([
            'moderation_status' => (int) $validated['moderation_status'],
            'moderation_note' => $validated['moderation_note'] ?? null,
            'moderated_by' => $user->id,
            'moderated_at' => now(),
        ]);

        $message = 'Статус модерации тега обновлён.';

        return $request->expectsJson()
            ? response()->json([
                'message' => $message,
                'tag' => new BlogTagResource(
                    $tag->load(['owner', 'moderator', 'translations'])->loadCount(['articles'])
                ),
            ])
            : back()->with('success', $message);
    }
}
