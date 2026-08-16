<?php

namespace App\Http\Controllers\Admin\Blog\BlogBanner;

use App\Http\Controllers\Admin\Blog\BaseBlogAdminController;
use App\Http\Requests\Admin\Blog\BlogBanner\BlogBannerRequest;
use App\Http\Resources\Admin\Blog\BlogBanner\BlogBannerResource;
use App\Http\Resources\Admin\Blog\BlogBanner\BlogBannerSharedResource;
use App\Models\Admin\Blog\BlogBanner\BlogBanner;
use App\Models\Admin\Blog\BlogBanner\BlogBannerImage;
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
 * Контроллер для управления Баннерами (Blog) в админке.
 *
 * Паттерн:
 * - локали (табы)
 * - CRUD
 * - owner/ограничение “владелец/админ”
 * - activity/left/main/right (single + bulk)
 * - sort + drag&drop (bulk)
 * - moderation (approve/reject) только для admin
 * - images (Spatie)
 *
 * @version 1.1 (мультиязычеая архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 */
class BlogBannerController extends BaseBlogAdminController
{
    /** Основная модель */
    protected string $modelClass = BlogBanner::class;

    /** Модель изображений */
    protected string $imageModelClass = BlogBannerImage::class;

    /** Коллекция изображений */
    protected string $imageMediaCollection = 'images';

    /** Название сущности для уведомлений */
    protected string $entityLabel = 'баннеров';

    /** Поля переводов */
    protected array $translationFields = [
        'title',
        'link',
        'short',
    ];

    /** Список баннеров */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int('adminBlogBannersPerPage', 6);
        $defaultSort = $settings->string('adminBlogBannersDefaultSort', 'idDesc');

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $processingMode = $settings->string('adminBlogBannersProcessingMode', 'frontend');

        $bannersCount = $this->baseQuery()->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $bannersCount,
                300
            );

        try {
            $banners = $this->getIndexBanners(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            return Inertia::render('Admin/Blog/BlogBanners/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminBlogBannersPerPage' => $perPage,
                'adminBlogBannersDefaultSort' => $defaultSort,
                'adminBlogBannersProcessingMode' => $processingMode,

                'banners' => BlogBannerSharedResource::collection($banners),
                'bannersCount' => $bannersCount,

                'sortParam' => $sortParam,
                'search' => $search,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки blog banners: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/Blog/BlogBanners/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminBlogBannersPerPage' => $perPage,
                'adminBlogBannersDefaultSort' => $defaultSort,
                'adminBlogBannersProcessingMode' => $processingMode,

                'banners' => [],
                'bannersCount' => 0,

                'sortParam' => $sortParam,
                'search' => $search,

                'error' => 'Ошибка загрузки баннеров.',
            ]);
        }
    }

    /** Страница создания баннера */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/Blog/BlogBanners/Create', [
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /** Создание баннера */
    public function store(BlogBannerRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];

        unset($data['translations'], $data['images'], $data['deletedImages']);

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
            DB::transaction(function () use ($request, &$banner, $data, $translations, $imagesData) {
                if (!isset($data['sort']) || is_null($data['sort'])) {
                    $maxSort = BlogBanner::query()->max('sort');
                    $data['sort'] = is_null($maxSort) ? 0 : $maxSort + 1;
                }

                $banner = BlogBanner::create($data);

                $this->syncTranslations($banner, $translations);
                $this->syncImages($banner, $request, $imagesData);
            });

            return redirect()
                ->route('admin.blogBanners.index')
                ->with('success', 'Баннер успешно создан.');
        } catch (Throwable $e) {
            Log::error('Ошибка при создании blog banner: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании баннера.');
        }
    }

    /** Редирект на страницу редактирования */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route('admin.blogBanners.edit', $id);
    }

    /** Страница редактирования баннера */
    public function edit(
        int $blogBanner,
        Request $request
    ): Response {
        $banner = $this->baseQuery()
            ->with([
                /**
                 * Для Edit нужны все переводы,
                 * потому что форма работает
                 * с языковыми вкладками.
                 */
                'translations',

                /**
                 * Изображения + Spatie Media
                 * нужны форме редактирования.
                 */
                'images.media',
            ])
            ->findOrFail(
                $blogBanner
            );

        $currentLocale = $this->resolveLocale(
            $request
        );

        return Inertia::render(
            'Admin/Blog/BlogBanners/Edit',
            [
                'banner' =>
                    new BlogBannerResource(
                        $banner
                    ),

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),
            ]
        );
    }

    /** Обновление баннера */
    public function update(BlogBannerRequest $request, int $blogBanner): RedirectResponse
    {
        $banner = $this->baseQuery()
            ->with('images')
            ->findOrFail($blogBanner);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];

        unset($data['translations'], $data['images'], $data['deletedImages'], $data['_method']);

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
            DB::transaction(function () use ($request, $banner, $data, $translations, $imagesData, $deletedImageIds) {
                $banner->update($data);

                $this->syncTranslations($banner, $translations);
                $this->syncImages($banner, $request, $imagesData, $deletedImageIds);
            });

            return redirect()
                ->route('admin.blogBanners.index')
                ->with('success', 'Баннер успешно обновлён.');
        } catch (Throwable $e) {
            Log::error('Ошибка при обновлении blog banner ID ' . $banner->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении баннера.');
        }
    }

    /** Удаление баннера */
    public function destroy(int $blogBanner): RedirectResponse
    {
        $banner = $this->baseQuery()
            ->with('images')
            ->findOrFail($blogBanner);

        try {
            DB::transaction(function () use ($banner) {
                $imageIds = $banner->images()
                    ->pluck('blog_banner_images.id')
                    ->toArray();

                if (!empty($imageIds)) {
                    $banner->images()->detach();
                    $this->deleteImages($imageIds);
                }

                $banner->translations()->delete();
                $banner->delete();
            });

            return redirect()
                ->route('admin.blogBanners.index')
                ->with('success', 'Баннер успешно удалён.');
        } catch (Throwable $e) {
            Log::error('Ошибка при удалении blog banner ID ' . $banner->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении баннера.');
        }
    }

    /** Массовое удаление баннеров */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:blog_banners,id'],
        ]);

        $ids = $validated['ids'];

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            return back()->with('error', 'Часть баннеров недоступна для удаления.');
        }

        try {
            DB::transaction(function () use ($allowedIds) {
                $imageIds = BlogBannerImage::whereHas('banners', function ($query) use ($allowedIds) {
                    $query->whereIn('blog_banners.id', $allowedIds);
                })->pluck('id')->toArray();

                DB::table('blog_banner_has_images')
                    ->whereIn('banner_id', $allowedIds)
                    ->delete();

                if (!empty($imageIds)) {
                    $this->deleteImages($imageIds);
                }

                DB::table('blog_banner_translations')
                    ->whereIn('banner_id', $allowedIds)
                    ->delete();

                BlogBanner::whereIn('id', $allowedIds)->delete();
            });

            return back()->with('success', 'Выбранные баннеры успешно удалены.');
        } catch (Throwable $e) {
            Log::error('Ошибка bulkDestroy blog banners: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при массовом удалении баннеров.');
        }
    }

    /** Базовый запрос списка баннеров. */
    private function indexBannersQuery(
        string $locale
    ): Builder {
        return $this->baseQuery()
            ->with([
                /**
                 * Для Admin Index нужен только
                 * перевод выбранной локали.
                 */
                'translations' => fn ($query) =>
                $query->where(
                    'locale',
                    $locale
                ),

                /**
                 * Владелец нужен отображению,
                 * поиску и сортировке.
                 */
                'owner',

                /**
                 * Модератор нужен
                 * frontend-поиску.
                 */
                'moderator',

                /**
                 * Изображения + Spatie Media
                 * загружаются пакетно.
                 */
                'images.media',
            ])
            ->withCount([
                'images',
            ]);
    }

    /** Список баннеров по режиму обработки */
    private function getIndexBanners(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = '',
    ) {
        $query = $this->indexBannersQuery(
            $locale
        );

        /**
         * Server mode:
         * поиск, фильтрация, сортировка
         * и пагинация выполняются SQL.
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
         * Frontend mode:
         *
         * backend отдаёт полную коллекцию,
         * а Vue выполняет поиск,
         * фильтрацию, сортировку
         * и локальную пагинацию.
         */
        return $query
            ->orderBy(
                'sort',
                'asc'
            )
            ->orderByDesc(
                'id'
            )
            ->get();
    }
}
