<?php

namespace App\Http\Controllers\Admin\Blog\BlogTag;

use App\Http\Controllers\Admin\Blog\BaseBlogAdminController;
use App\Http\Requests\Admin\Blog\BlogTag\BlogTagRequest;
use App\Http\Resources\Admin\Blog\BlogTag\BlogTagResource;
use App\Http\Resources\Admin\Blog\BlogTag\BlogTagSharedResource;
use App\Models\Admin\Blog\BlogTag\BlogTag;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
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

    /** Список тегов */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int('adminBlogTagsPerPage', 6);
        $defaultSort = $settings->string('adminBlogTagsDefaultSort', 'idDesc');

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $processingMode = $settings->string('adminBlogTagsProcessingMode', 'frontend');

        $tagsCount = $this->baseQuery()->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $tagsCount,
                300
            );

        try {
            $tags = $this->getIndexTags(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            return Inertia::render('Admin/Blog/BlogTags/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminBlogTagsPerPage' => $perPage,
                'adminBlogTagsDefaultSort' => $defaultSort,
                'adminBlogTagsProcessingMode' => $processingMode,

                'tags' => BlogTagSharedResource::collection($tags),
                'tagsCount' => $tagsCount,

                'sortParam' => $sortParam,
                'search' => $search,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка blog tags: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/Blog/BlogTags/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminBlogTagsPerPage' => $perPage,
                'adminBlogTagsDefaultSort' => $defaultSort,
                'adminBlogTagsProcessingMode' => $processingMode,

                'tags' => [],
                'tagsCount' => 0,

                'sortParam' => $sortParam,
                'search' => $search,

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
                /**
                 * Для Edit нужны все переводы:
                 * TranslationTabs позволяет динамически
                 * переключать, добавлять и удалять локали.
                 */
                'translations',
            ])
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

    /**
     * Базовый запрос списка тегов.
     */
    private function indexTagsQuery(
        string $locale
    ): Builder {
        return $this->baseQuery()
            ->with([
                /**
                 * Автор нужен UI,
                 * frontend-поиску и сортировке.
                 */
                'owner',

                /**
                 * Модератор пока нужен
                 * frontend-поиску.
                 */
                'moderator',

                /**
                 * Только выбранная локаль.
                 */
                'translations' => fn ($query) =>
                $query->where(
                    'locale',
                    $locale
                ),
            ])
            ->withCount([
                'articles',
            ]);
    }

    /** Список тегов по режиму обработки */
    private function getIndexTags(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = '',
    ) {
        $query = $this->indexTagsQuery(
            $locale
        );

        /**
         * Server:
         * поиск, фильтрация, сортировка
         * и пагинация выполняются в SQL.
         */
        if ($useServerProcessing) {
            return $query
                ->search(
                    $search,
                    $locale
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
         * Frontend:
         * отдаём всю доступную коллекцию.
         *
         * Поиск, фильтрацию, сортировку
         * и пагинацию выполняет Vue.
         */
        return $query
            ->ordered(
                $locale
            )
            ->get();
    }
}
