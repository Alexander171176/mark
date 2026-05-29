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
 * - CRUD+ :
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
    /** Основная модель */
    protected string $modelClass = BlogTag::class;

    /** Название сущности для уведомлений */
    protected string $entityLabel = 'тегов';

    /** Поля переводов */
    protected array $translationFields = [
        'name',
        'subtitle',
        'short',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];/** Дополнительные варианты сортировки тегов */

    /** Расширение сортировки для тегов блога. */
    protected function extendedSortMap(): array
    {
        return [
            'ownerNameAsc' => 'owner_name_asc',
            'ownerNameDesc' => 'owner_name_desc',

            'ownerEmailAsc' => 'owner_email_asc',
            'ownerEmailDesc' => 'owner_email_desc',

            'locale' => 'locale_asc',

            'viewsAsc' => 'views_asc',
            'viewsDesc' => 'views_desc',

            'activity' => 'activity',
            'inactive' => 'inactive',

            'moderation_pending' => 'moderation_pending',
            'moderation_approved' => 'moderation_approved',
            'moderation_rejected' => 'moderation_rejected',

            'moderation_statusAsc' => 'moderation_status_asc',
            'moderation_statusDesc' => 'moderation_status_desc',
        ];
    }

    /** Список тегов */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $adminBlogTagsPerPage = (int) config('site_settings.adminBlogTagsPerPage', 20);
        $adminBlogTagsDefaultSort = (string) config('site_settings.adminBlogTagsDefaultSort', 'idDesc');

        $sortParam = (string) $request->query('sort', $adminBlogTagsDefaultSort);
        $normalizedSort = $this->normalizeSortParam($sortParam);

        try {
            $tags = $this->baseQuery()
                ->with([
                    'owner',
                    'moderator',
                    'translations',
                ])
                ->withCount(['articles'])
                ->sortByParam($normalizedSort, $currentLocale)
                ->get();

            return Inertia::render('Admin/Blog/BlogTags/Index', [
                'tags' => BlogTagResource::collection($tags),
                'tagsCount' => $this->baseQuery()->count(),

                'adminBlogTagsPerPage' => $adminBlogTagsPerPage,
                'adminBlogTagsDefaultSort' => $adminBlogTagsDefaultSort,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'sortParam' => $sortParam,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка blog tags: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/Blog/BlogTags/Index', [
                'tags' => [],
                'tagsCount' => 0,

                'adminBlogTagsPerPage' => $adminBlogTagsPerPage,
                'adminBlogTagsDefaultSort' => $adminBlogTagsDefaultSort,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'sortParam' => $sortParam,
                'error' => 'Ошибка загрузки тегов.',
            ]);
        }
    }

    /** Страница создания тега */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/Blog/BlogTags/Create', [
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /** Создание тега */
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

    /** Редирект на страницу редактирования */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route('admin.blogTags.edit', $id);
    }

    /** Страница редактирования тега */
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

        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/Blog/BlogTags/Edit', [
            'tag' => new BlogTagResource($tag),
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /** Обновление тега */
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

    /** Удаление тега */
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

    /** Массовое удаление тегов */
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
