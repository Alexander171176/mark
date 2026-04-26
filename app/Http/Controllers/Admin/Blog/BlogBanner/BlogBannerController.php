<?php

namespace App\Http\Controllers\Admin\Blog\BlogBanner;

use App\Http\Controllers\Admin\Blog\Base\BaseBlogAdminController;
use App\Http\Requests\Admin\Blog\BlogBanner\BlogBannerRequest;
use App\Http\Resources\Admin\Blog\BlogBanner\BlogBannerResource;
use App\Models\Admin\Blog\BlogBanner\BlogBanner;
use App\Models\Admin\Blog\BlogBanner\BlogBannerImage;
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
    protected string $modelClass = BlogBanner::class;

    protected string $entityLabel = 'баннеров';

    protected array $translationFields = [
        'title',
        'link',
        'short',
    ];

    protected string $imageModelClass = BlogBannerImage::class;

    protected string $imageMediaCollection = 'images';

    /**
     * Список баннеров:
     * - поиск
     * - сортировка
     * - текущая локаль
     */
    public function index(Request $request): Response
    {
        $adminCountBanners = (int) config('site_settings.AdminCountBanners', 15);
        $adminSortBanners = (string) config('site_settings.AdminSortBanners', 'idDesc');

        $currentLocale = $this->normalizeLocale($request->query('locale'));
        $search = trim((string) $request->query('search', ''));
        $sortParam = $this->normalizeSortParam($request->query('sort', $adminSortBanners));

        try {
            $banners = $this->baseQuery()
                ->with([
                    'owner',
                    'moderator',
                    'translations',
                    'images',
                ])
                ->withCount(['images'])
                ->search($search, $currentLocale)
                ->sortByParam($sortParam, $currentLocale)
                ->get();

            return Inertia::render('Admin/Blog/BlogBanners/Index', [
                'banners' => BlogBannerResource::collection($banners),
                'bannersCount' => $this->baseQuery()->count(),

                'adminCountBanners' => $adminCountBanners,
                'adminSortBanners' => $adminSortBanners,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
                'search' => $search,
                'sortParam' => $sortParam,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки blog banners: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/Blog/BlogBanners/Index', [
                'banners' => [],
                'bannersCount' => 0,

                'adminCountBanners' => $adminCountBanners,
                'adminSortBanners' => $adminSortBanners,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
                'search' => $search,
                'sortParam' => $sortParam,
                'error' => 'Ошибка загрузки баннеров.',
            ]);
        }
    }

    /**
     * Страница создания баннера
     */
    public function create(Request $request): Response
    {
        $targetLocale = $this->normalizeLocale($request->query('locale'));

        return Inertia::render('Admin/Blog/BlogBanners/Create', [
            'targetLocale' => $targetLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /**
     * Создание баннера:
     * - основная запись
     * - переводы
     */
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

    /**
     * Редирект на страницу редактирования
     */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route('admin.blogBanners.edit', $id);
    }

    /**
     * Страница редактирования баннера:
     * - основная запись
     * - переводы
     * - счётчики
     */
    public function edit(int $blogBanner, Request $request): Response
    {
        $banner = $this->baseQuery()
            ->with([
                'owner',
                'moderator',
                'translations',
                'images',
            ])
            ->withCount(['images'])
            ->findOrFail($blogBanner);

        $targetLocale = $this->normalizeLocale($request->query('locale'));

        return Inertia::render('Admin/Blog/BlogBanners/Edit', [
            'banner' => new BlogBannerResource($banner),
            'targetLocale' => $targetLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /**
     * Обновление баннера:
     * - основная запись
     * - синхронизация переводов
     */
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

    /**
     * Удаление баннера:
     * - detach статей
     * - удаление переводов
     * - удаление основной записи
     */
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

    /**
     * Массовое удаление баннеров
     */
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
}
