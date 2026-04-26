<?php

namespace App\Http\Controllers\Admin\Blog\BlogTag;

use App\Http\Controllers\Admin\Blog\Base\BaseBlogAdminController;
use App\Http\Requests\Admin\Blog\BlogTag\BlogTagRequest;
use App\Http\Resources\Admin\Blog\BlogTag\BlogTagResource;
use App\Models\Admin\Blog\BlogTag\BlogTag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
 * @version 1.1 (мультиязычеая архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 */
class BlogTagController extends BaseBlogAdminController
{
    protected string $modelClass = BlogTag::class;

    protected string $entityLabel = 'тегов';

    protected array $translationFields = [
        'name',
        'subtitle',
        'short',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

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

            return Inertia::render('Admin/Blog/BlogTags/Index', [
                'tags' => BlogTagResource::collection($tags),
                'tagsCount' => $this->baseQuery()->count(),

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

            unset(
                $data['moderation_status'],
                $data['moderated_by'],
                $data['moderated_at'],
                $data['moderation_note']
            );
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

            unset(
                $data['moderation_status'],
                $data['moderated_by'],
                $data['moderated_at'],
                $data['moderation_note']
            );
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
}
