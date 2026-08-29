<?php

namespace App\Http\Controllers\Admin\Cms\CmsPage;

use App\Http\Controllers\Admin\Cms\BaseCmsAdminController;
use App\Http\Requests\Admin\Cms\CmsPage\CmsPageRequest;
use App\Http\Resources\Admin\Cms\CmsPage\CmsPageResource;
use App\Http\Resources\Admin\Cms\CmsPage\CmsPageSharedResource;
use App\Models\Admin\Cms\CmsPage\CmsPage;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use InvalidArgumentException;
use Throwable;

/**
 * Контроллер для управления страницами CMS (CmsPage) в админке.
 *
 * Паттерн:
 * - Поиск, Пагинация, сортировка (режимы: frontend | auto | server )
 * - CRUD
 * - delete (single + bulk)
 * - activity (single + bulk)
 * - sort + drag&drop (bulk) в карточках
 * - переключатели: activity, in_menu, in_footer, show_content, show_seo
 * - вид деревом и карточками
 *
 * @version 1.1 (мультиязычеая архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 */
class CmsPageController extends BaseCmsAdminController
{
    /** Основная модель */
    protected string $modelClass = CmsPage::class;

    /** Название сущности */
    protected string $entityLabel = 'CMS страниц';

    /** Поля переводов */
    protected array $translationFields = [
        'title',
        'subtitle',
        'short',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    /** Максимальный уровень вложенности */
    protected int $maxPageLevel = 3;

    /* ======================== Index ======================== */

    /** Список страниц */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int('adminCmsPagesPerPage', 6);
        $defaultSort = $settings->string('adminCmsPagesDefaultSort', 'idDesc');

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $processingMode = $settings->string(
            'adminCmsPagesProcessingMode',
            'frontend'
        );

        $pagesCount = $this->baseQuery()->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $pagesCount,
                300
            );

        try {
            /**
             * Дерево используется отдельным режимом представления.
             * Загружаем только перевод текущей локали.
             */
            $pagesTree = $this->getIndexPagesTree(
                $currentLocale
            );

            $this->prepareTreeChildren(
                $pagesTree
            );

            /**
             * Плоский список используется карточками
             * и server/frontend обработкой.
             */
            $pagesFlat = $this->getIndexPages(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search
            );

            return Inertia::render('Admin/Cms/CmsPages/Index', [
                /**
                 * Index использует только лёгкий Resource.
                 * Полный CmsPageResource здесь больше не нужен.
                 */
                'pagesTree' => CmsPageSharedResource::collection(
                    $pagesTree
                ),

                'pages' => CmsPageSharedResource::collection(
                    $pagesFlat
                ),

                'pagesCount' => $pagesCount,

                'useServerProcessing' => $useServerProcessing,

                'adminCmsPagesPerPage' => $perPage,
                'adminCmsPagesDefaultSort' => $defaultSort,
                'adminCmsPagesProcessingMode' => $processingMode,

                'sortParam' => $sortParam,
                'search' => $search,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
            ]);
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки списка CMS страниц: ' . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render('Admin/Cms/CmsPages/Index', [
                'pagesTree' => [],
                'pages' => [],
                'pagesCount' => 0,

                'useServerProcessing' => $useServerProcessing,

                'adminCmsPagesPerPage' => $perPage,
                'adminCmsPagesDefaultSort' => $defaultSort,
                'adminCmsPagesProcessingMode' => $processingMode,

                'sortParam' => $sortParam,
                'search' => $search,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'error' => 'Ошибка загрузки CMS страниц.',
            ]);
        }
    }

    /* ======================== Create ======================== */

    /** Страница создания страницы */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        /**
         * Для выбора родителя нужен только перевод
         * текущей локали.
         */
        $parents = $this->baseQuery()
            ->with([
                'translations' => function ($query) use ($currentLocale): void {
                    $query->where(
                        'locale',
                        $currentLocale
                    );
                },
            ])
            ->withCount([
                'children',
            ])
            ->ordered()
            ->get();

        return Inertia::render('Admin/Cms/CmsPages/Create', [
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'parents' => CmsPageSharedResource::collection(
                $parents
            ),
        ]);
    }

    /** Создание страницы */
    public function store(CmsPageRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];

        unset(
            $data['translations']
        );

        $user = auth()->user();

        $data['user_id'] = $user
        && method_exists($user, 'hasRole')
        && ! $user->hasRole('admin')
            ? $user->id
            : ($data['user_id'] ?? $user?->id);

        try {
            DB::transaction(function () use ($data, $translations): void {
                $this->ensureAllowedLevel(
                    $data['parent_id'] ?? null
                );

                $data['level'] = $this->resolveLevel(
                    $data['parent_id'] ?? null
                );

                if (! isset($data['sort']) || is_null($data['sort'])) {
                    $maxSort = CmsPage::query()
                        ->where(
                            'parent_id',
                            $data['parent_id'] ?? null
                        )
                        ->max('sort');

                    $data['sort'] = is_null($maxSort)
                        ? 0
                        : $maxSort + 1;
                }

                $page = CmsPage::create(
                    $data
                );

                $this->syncTranslations(
                    $page,
                    $translations
                );
            });

            return redirect()
                ->route('admin.cmsPages.index')
                ->with(
                    'success',
                    'CMS страница успешно создана.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при создании CMS страницы: ' . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e instanceof InvalidArgumentException
                        ? $e->getMessage()
                        : 'Ошибка при создании CMS страницы.'
                );
        }
    }

    /* ======================== Show / Edit ======================== */

    /** Редирект просмотра на редактирование */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route(
            'admin.cmsPages.edit',
            $id
        );
    }

    /** Страница редактирования страницы */
    public function edit(int $cmsPage, Request $request): Response
    {
        /**
         * Locale устанавливаем ДО построения данных,
         * чтобы CmsPageResource корректно определил
         * translation текущего языка.
         */
        $currentLocale = $this->resolveLocale($request);

        /**
         * Сама редактируемая страница получает ВСЕ
         * translations, поскольку форма редактирует
         * все языковые версии.
         *
         * У parent нужен только текущий перевод.
         */
        $page = $this->baseQuery()
            ->with([
                'owner:id,name,email,profile_photo_path',

                'translations',

                'parent' => function ($query) use ($currentLocale): void {
                    $query->with([
                        'translations' => function ($query) use ($currentLocale): void {
                            $query->where(
                                'locale',
                                $currentLocale
                            );
                        },
                    ]);
                },
            ])
            ->withCount([
                'children',
            ])
            ->findOrFail(
                $cmsPage
            );

        /**
         * Список возможных родителей — только
         * перевод currentLocale.
         */
        $parents = $this->baseQuery()
            ->where(
                'id',
                '!=',
                $page->id
            )
            ->with([
                'translations' => function ($query) use ($currentLocale): void {
                    $query->where(
                        'locale',
                        $currentLocale
                    );
                },
            ])
            ->withCount([
                'children',
            ])
            ->ordered()
            ->get();

        return Inertia::render('Admin/Cms/CmsPages/Edit', [
            /**
             * Здесь полный Resource нужен:
             * форма работает со всеми translations.
             */
            'page' => new CmsPageResource(
                $page
            ),

            'parents' => CmsPageSharedResource::collection(
                $parents
            ),

            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /* ======================== Update ======================== */

    /** Обновление страницы */
    public function update(CmsPageRequest $request, int $cmsPage): RedirectResponse
    {
        $page = $this->baseQuery()
            ->findOrFail(
                $cmsPage
            );

        $data = $request->validated();

        $translations = $data['translations'] ?? [];

        unset(
            $data['translations'],
            $data['_method']
        );

        $user = auth()->user();

        if (
            $user
            && method_exists($user, 'hasRole')
            && ! $user->hasRole('admin')
        ) {
            $data['user_id'] = $user->id;
        }

        try {
            DB::transaction(function () use ($page, $data, $translations): void {
                if (
                    ! empty($data['parent_id'])
                    && (int) $data['parent_id'] === (int) $page->id
                ) {
                    throw new InvalidArgumentException(
                        'Страница не может быть родителем самой себя.'
                    );
                }

                $this->ensureParentIsNotDescendant(
                    pageId: $page->id,
                    parentId: $data['parent_id'] ?? null
                );

                $this->ensureAllowedLevel(
                    $data['parent_id'] ?? null
                );

                $data['level'] = $this->resolveLevel(
                    $data['parent_id'] ?? null
                );

                $page->update(
                    $data
                );

                $this->syncTranslations(
                    $page,
                    $translations
                );
            });

            return redirect()
                ->route('admin.cmsPages.index')
                ->with(
                    'success',
                    'CMS страница успешно обновлена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при обновлении CMS страницы ID '
                . $page->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e instanceof InvalidArgumentException
                        ? $e->getMessage()
                        : 'Ошибка при обновлении CMS страницы.'
                );
        }
    }

    /* ======================== Delete ======================== */

    /** Удаление страницы */
    public function destroy(int $cmsPage): RedirectResponse
    {
        /**
         * Переводы заранее не загружаем:
         * для удаления они не нужны.
         */
        $page = $this->baseQuery()
            ->findOrFail(
                $cmsPage
            );

        try {
            DB::transaction(function () use ($page): void {
                if ($page->children()->exists()) {
                    throw new InvalidArgumentException(
                        'Нельзя удалить страницу: сначала удалите или переместите дочерние страницы.'
                    );
                }

                $page->translations()
                    ->delete();

                $page->delete();
            });

            return redirect()
                ->route('admin.cmsPages.index')
                ->with(
                    'success',
                    'CMS страница успешно удалена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при удалении CMS страницы ID '
                . $page->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                $e instanceof InvalidArgumentException
                    ? $e->getMessage()
                    : 'Ошибка при удалении CMS страницы.'
            );
        }
    }

    /** Массовое удаление страниц */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:cms_pages,id'],
        ]);

        $ids = $validated['ids'];

        $allowedIds = $this->baseQuery()
            ->whereIn(
                'id',
                $ids
            )
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            return back()->with(
                'error',
                'Часть CMS страниц недоступна для удаления.'
            );
        }

        try {
            DB::transaction(function () use ($allowedIds): void {
                $hasChildren = CmsPage::query()
                    ->whereIn(
                        'parent_id',
                        $allowedIds
                    )
                    ->exists();

                if ($hasChildren) {
                    throw new InvalidArgumentException(
                        'Нельзя удалить выбранные страницы, пока у них есть дочерние страницы.'
                    );
                }

                DB::table('cms_page_translations')
                    ->whereIn(
                        'cms_page_id',
                        $allowedIds
                    )
                    ->delete();

                CmsPage::query()
                    ->whereIn(
                        'id',
                        $allowedIds
                    )
                    ->delete();
            });

            return back()->with(
                'success',
                'Выбранные CMS страницы успешно удалены.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка bulkDestroy CMS pages: ' . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                $e instanceof InvalidArgumentException
                    ? $e->getMessage()
                    : 'Ошибка при массовом удалении CMS страниц.'
            );
        }
    }

    /* ======================== Tree sorting ======================== */

    /**
     * Массовое обновление сортировки дерева.
     * Переопределяет простой updateSortBulk из HasCmsSortingTrait.
     */
    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required_without:pages', 'array'],
            'items.*.id' => ['required_with:items', 'integer', 'exists:cms_pages,id'],
            'items.*.sort' => ['required_with:items', 'integer', 'min:0'],
            'items.*.parent_id' => ['nullable', 'integer', 'exists:cms_pages,id'],

            'pages' => ['required_without:items', 'array'],
            'pages.*.id' => ['required_with:pages', 'integer', 'exists:cms_pages,id'],
            'pages.*.sort' => ['required_with:pages', 'integer', 'min:0'],
            'pages.*.parent_id' => ['nullable', 'integer', 'exists:cms_pages,id'],
        ]);

        $items = $validated['items']
            ?? $validated['pages'];

        $ids = array_column(
            $items,
            'id'
        );

        $allowedIds = $this->baseQuery()
            ->whereIn(
                'id',
                $ids
            )
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            $message = 'Часть CMS страниц недоступна для изменения сортировки.';

            return $request->expectsJson()
                ? response()->json(
                    ['message' => $message],
                    400
                )
                : back()->with(
                    'error',
                    $message
                );
        }

        try {
            DB::transaction(function () use ($items): void {
                foreach ($items as $row) {
                    $pageId = (int) $row['id'];

                    $parentId = isset($row['parent_id'])
                        ? (int) $row['parent_id']
                        : null;

                    if (
                        $parentId !== null
                        && $parentId === $pageId
                    ) {
                        throw new InvalidArgumentException(
                            'Страница не может быть родителем самой себя.'
                        );
                    }

                    $this->ensureParentIsNotDescendant(
                        pageId: $pageId,
                        parentId: $parentId
                    );

                    $this->ensureAllowedLevel(
                        $parentId
                    );

                    CmsPage::query()
                        ->whereKey(
                            $pageId
                        )
                        ->update([
                            'sort' => (int) $row['sort'],
                            'parent_id' => $parentId,
                            'level' => $this->resolveLevel($parentId),
                        ]);
                }
            });

            $message = 'Сортировка дерева CMS страниц обновлена.';

            return $request->expectsJson()
                ? response()->json([
                    'message' => $message,
                ])
                : back()->with(
                    'success',
                    $message
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка updateSortBulk CMS pages: ' . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            $message = $e instanceof InvalidArgumentException
                ? $e->getMessage()
                : 'Ошибка при массовом обновлении сортировки CMS страниц.';

            return $request->expectsJson()
                ? response()->json(
                    ['message' => $message],
                    500
                )
                : back()->with(
                    'error',
                    $message
                );
        }
    }

    /* ======================== Boolean flags ======================== */

    /** Переключение показа страницы в главном меню */
    public function updateInMenu(
        Request $request,
        int $cmsPage
    ): RedirectResponse|JsonResponse {
        return $this->updateBooleanFlag(
            $request,
            $cmsPage,
            'in_menu'
        );
    }

    /** Переключение показа страницы в меню подвала */
    public function updateInFooter(
        Request $request,
        int $cmsPage
    ): RedirectResponse|JsonResponse {
        return $this->updateBooleanFlag(
            $request,
            $cmsPage,
            'in_footer'
        );
    }

    /** Переключение показа HTML контента страницы */
    public function updateShowContent(
        Request $request,
        int $cmsPage
    ): RedirectResponse|JsonResponse {
        return $this->updateBooleanFlag(
            $request,
            $cmsPage,
            'show_content'
        );
    }

    /** Переключение показа своего SEO страницы */
    public function updateShowSeo(
        Request $request,
        int $cmsPage
    ): RedirectResponse|JsonResponse {
        return $this->updateBooleanFlag(
            $request,
            $cmsPage,
            'show_seo'
        );
    }

    /** Обновление булевого флага */
    private function updateBooleanFlag(
        Request $request,
        int $cmsPage,
        string $field
    ): RedirectResponse|JsonResponse {
        $validated = $request->validate([
            $field => ['required', 'boolean'],
        ]);

        $page = $this->baseQuery()
            ->findOrFail(
                $cmsPage
            );

        $page->update([
            $field => (bool) $validated[$field],
        ]);

        $message = 'Настройки CMS страницы обновлены.';

        return $request->expectsJson()
            ? response()->json([
                'message' => $message,
            ])
            : back()->with(
                'success',
                $message
            );
    }

    /* ======================== Index queries ======================== */

    /**
     * Базовый запрос плоского списка Index.
     *
     * Для Index:
     * - translations только currentLocale;
     * - parent translations только currentLocale;
     * - owner только необходимые поля.
     */
    private function indexQuery(string $locale): Builder
    {
        return $this->baseQuery()
            ->with([
                'owner:id,name,email,profile_photo_path',

                'translations' => function ($query) use ($locale): void {
                    $query->where(
                        'locale',
                        $locale
                    );
                },

                'parent' => function ($query) use ($locale): void {
                    $query->with([
                        'translations' => function ($query) use ($locale): void {
                            $query->where(
                                'locale',
                                $locale
                            );
                        },
                    ]);
                },
            ])
            ->withCount([
                'children',
            ]);
    }

    /** Плоский список страниц */
    private function getIndexPages(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ): LengthAwarePaginator|Collection {
        $query = $this->indexQuery(
            $locale
        );

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
         * Во frontend режиме поиск и выбранная
         * сортировка выполняются в Index.vue.
         *
         * Backend отдаёт полный набор.
         */
        return $query
            ->ordered()
            ->get();
    }

    /**
     * Дерево страниц.
     *
     * Максимальная глубина дерева — 3 уровня,
     * поэтому явно загружаем currentLocale
     * и owner для каждого возможного уровня.
     *
     * childrenRecursive модели больше сам
     * не загружает translations.
     */
    private function getIndexPagesTree(string $locale): Collection
    {
        return $this->baseQuery()
            ->with([
                /** Уровень 1 */
                'owner:id,name,email,profile_photo_path',

                'translations' => function ($query) use ($locale): void {
                    $query->where(
                        'locale',
                        $locale
                    );
                },

                /** Уровень 2 */
                'childrenRecursive.owner:id,name,email,profile_photo_path',

                'childrenRecursive.translations' => function ($query) use ($locale): void {
                    $query->where(
                        'locale',
                        $locale
                    );
                },

                /** Уровень 3 */
                'childrenRecursive.childrenRecursive.owner:id,name,email,profile_photo_path',

                'childrenRecursive.childrenRecursive.translations' => function ($query) use ($locale): void {
                    $query->where(
                        'locale',
                        $locale
                    );
                },
            ])
            ->withCount([
                'children',
            ])
            ->root()
            ->ordered()
            ->get();
    }

    /* ======================== Tree helpers ======================== */

    /** Определение уровня вложенности */
    private function resolveLevel(?int $parentId): int
    {
        if (! $parentId) {
            return 1;
        }

        $parent = $this->baseQuery()
            ->select([
                'id',
                'level',
            ])
            ->find(
                $parentId
            );

        return $parent
            ? ((int) $parent->level) + 1
            : 1;
    }

    /** Проверка максимальной глубины */
    private function ensureAllowedLevel(?int $parentId): void
    {
        if ($this->resolveLevel($parentId) > $this->maxPageLevel) {
            throw new InvalidArgumentException(
                'Нельзя создавать страницу глубже '
                . $this->maxPageLevel
                . ' уровня вложенности.'
            );
        }
    }

    /** Запрет переноса страниц внутрь своих потомков */
    private function ensureParentIsNotDescendant(
        int $pageId,
        ?int $parentId
    ): void {
        if (! $parentId) {
            return;
        }

        $parent = CmsPage::query()
            ->select([
                'id',
                'parent_id',
            ])
            ->find(
                $parentId
            );

        while ($parent) {
            if ((int) $parent->id === $pageId) {
                throw new InvalidArgumentException(
                    'Страницу нельзя переместить внутрь своей дочерней страницы.'
                );
            }

            if (! $parent->parent_id) {
                break;
            }

            $parent = CmsPage::query()
                ->select([
                    'id',
                    'parent_id',
                ])
                ->find(
                    $parent->parent_id
                );
        }
    }

    /**
     * Подготовка childrenRecursive для Vue.
     *
     * SharedResource работает с relation children,
     * поэтому рекурсивно подменяем relation без
     * дополнительных SQL-запросов.
     */
    private function prepareTreeChildren(Collection $nodes): void
    {
        $nodes->each(function (CmsPage $node): void {
            if ($node->relationLoaded('childrenRecursive')) {
                $node->setRelation(
                    'children',
                    $node->childrenRecursive
                );

                $this->prepareTreeChildren(
                    $node->childrenRecursive
                );

                return;
            }

            if ($node->relationLoaded('children')) {
                $this->prepareTreeChildren(
                    $node->children
                );
            }
        });
    }
}
