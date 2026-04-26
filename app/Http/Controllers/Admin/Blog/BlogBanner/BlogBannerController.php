<?php

namespace App\Http\Controllers\Admin\Blog\BlogBanner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Blog\BlogBanner\BlogBannerRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateLeftRequest;
use App\Http\Requests\Admin\System\UpdateMainRequest;
use App\Http\Requests\Admin\System\UpdateRightRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\Blog\BlogBanner\BlogBannerResource;
use App\Models\Admin\Blog\BlogBanner\BlogBanner;
use App\Models\Admin\Blog\BlogBanner\BlogBannerImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
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
 * @version 1.1 (Улучшен с RMB, транзакциями, Form Requests)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see BlogBanner
 * @see BlogBannerRequest
 */
class BlogBannerController extends Controller
{
    private function availableLocales(): array
    {
        return config('app.available_locales', ['ru']);
    }

    private function baseQuery(): Builder
    {
        $query = BlogBanner::query();

        $user = auth()->user();

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    private function normalizeLocale(?string $locale): string
    {
        $availableLocales = $this->availableLocales();
        $fallback = config('app.fallback_locale', 'ru');

        if (!$locale || !in_array($locale, $availableLocales, true)) {
            return $fallback;
        }

        return $locale;
    }

    private function normalizeSortParam(?string $sort): string
    {
        return match ($sort) {
            'idAsc' => 'date_asc',
            'idDesc' => 'date_desc',
            'sortAsc' => 'sort_asc',
            'sortDesc' => 'sort_desc',
            'titleAsc' => 'title_asc',
            'titleDesc' => 'title_desc',
            default => $sort ?: 'sort_asc',
        };
    }

    private function syncTranslations(BlogBanner $banner, array $translations): void
    {
        $locales = array_keys($translations);

        foreach ($translations as $locale => $translationData) {
            $banner->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title' => $translationData['title'] ?? null,
                    'link' => $translationData['link'] ?? null,
                    'short' => $translationData['short'] ?? null,
                ]
            );
        }

        $banner->translations()
            ->whereNotIn('locale', $locales)
            ->delete();
    }

    private function syncImages(BlogBanner $banner, Request $request, array $imagesData, array $deletedImageIds = []): void
    {
        if (!empty($deletedImageIds)) {
            $banner->images()->detach($deletedImageIds);
            $this->deleteImages($deletedImageIds);
        }

        $syncData = [];

        foreach ($imagesData as $index => $imageData) {
            $fileKey = "images.{$index}.file";

            if (!empty($imageData['id'])) {
                $image = BlogBannerImage::find($imageData['id']);

                if (!$image || in_array((int) $image->id, $deletedImageIds, true)) {
                    continue;
                }

                $image->update([
                    'order' => $imageData['order'] ?? $image->order,
                    'alt' => $imageData['alt'] ?? $image->alt,
                    'caption' => $imageData['caption'] ?? $image->caption,
                ]);

                if ($request->hasFile($fileKey)) {
                    $image->clearMediaCollection('images');
                    $image->addMedia($request->file($fileKey))->toMediaCollection('images');
                }

                $syncData[$image->id] = ['order' => $image->order];
                continue;
            }

            if ($request->hasFile($fileKey)) {
                $image = BlogBannerImage::create([
                    'order' => $imageData['order'] ?? 0,
                    'alt' => $imageData['alt'] ?? '',
                    'caption' => $imageData['caption'] ?? '',
                ]);

                $image->addMedia($request->file($fileKey))->toMediaCollection('images');

                $syncData[$image->id] = ['order' => $image->order];
            }
        }

        $existingIds = $banner->images()
            ->whereNotIn('blog_banner_images.id', $deletedImageIds)
            ->pluck('blog_banner_images.id')
            ->toArray();

        foreach ($existingIds as $existingId) {
            if (!array_key_exists($existingId, $syncData)) {
                $existingImage = BlogBannerImage::find($existingId);

                if ($existingImage) {
                    $syncData[$existingId] = ['order' => $existingImage->order];
                }
            }
        }

        $banner->images()->sync($syncData);
    }

    private function deleteImages(array $imageIds): void
    {
        if (empty($imageIds)) {
            return;
        }

        $images = BlogBannerImage::whereIn('id', $imageIds)->get();

        foreach ($images as $image) {
            $image->clearMediaCollection('images');
            $image->delete();
        }
    }

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

    public function create(Request $request): Response
    {
        $targetLocale = $this->normalizeLocale($request->query('locale'));

        return Inertia::render('Admin/Blog/BlogBanners/Create', [
            'targetLocale' => $targetLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    public function store(BlogBannerRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];

        unset($data['translations'], $data['images'], $data['deletedImages']);

        $user = auth()->user();

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            $data['user_id'] = $user->id;
            unset($data['moderation_status'], $data['moderated_by'], $data['moderated_at'], $data['moderation_note']);
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

    public function show(string $id): RedirectResponse
    {
        return redirect()->route('admin.blogBanners.edit', $id);
    }

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
            unset($data['moderation_status'], $data['moderated_by'], $data['moderated_at'], $data['moderation_note']);
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

    public function updateActivity(UpdateActivityRequest $request, int $blogBanner): RedirectResponse
    {
        $banner = $this->baseQuery()->findOrFail($blogBanner);

        $banner->update([
            'activity' => $request->validated('activity'),
        ]);

        return back()->with('success', 'Активность баннера обновлена.');
    }

    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:blog_banners,id'],
            'activity' => ['required', 'boolean'],
        ]);

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $validated['ids'])
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($validated['ids'])) {
            return back()->with('error', 'Часть баннеров недоступна для обновления активности.');
        }

        BlogBanner::whereIn('id', $allowedIds)->update([
            'activity' => $validated['activity'],
        ]);

        $message = 'Активность выбранных баннеров обновлена.';

        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : back()->with('success', $message);
    }

    public function updateLeft(UpdateLeftRequest $request, int $blogBanner): RedirectResponse
    {
        $banner = $this->baseQuery()->findOrFail($blogBanner);

        $banner->update([
            'left' => $request->validated('left'),
        ]);

        return back()->with('success', 'Позиция left обновлена.');
    }

    public function bulkUpdateLeft(Request $request): JsonResponse
    {
        return $this->bulkUpdateBooleanField($request, 'left');
    }

    public function updateMain(UpdateMainRequest $request, int $blogBanner): RedirectResponse
    {
        $banner = $this->baseQuery()->findOrFail($blogBanner);

        $banner->update([
            'main' => $request->validated('main'),
        ]);

        return back()->with('success', 'Позиция main обновлена.');
    }

    public function bulkUpdateMain(Request $request): JsonResponse
    {
        return $this->bulkUpdateBooleanField($request, 'main');
    }

    public function updateRight(UpdateRightRequest $request, int $blogBanner): RedirectResponse
    {
        $banner = $this->baseQuery()->findOrFail($blogBanner);

        $banner->update([
            'right' => $request->validated('right'),
        ]);

        return back()->with('success', 'Позиция right обновлена.');
    }

    public function bulkUpdateRight(Request $request): JsonResponse
    {
        return $this->bulkUpdateBooleanField($request, 'right');
    }

    private function bulkUpdateBooleanField(Request $request, string $field): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:blog_banners,id'],
            $field => ['required', 'boolean'],
        ]);

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $validated['ids'])
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($validated['ids'])) {
            return response()->json([
                'success' => false,
                'message' => 'Часть баннеров недоступна для обновления.',
            ], 403);
        }

        BlogBanner::whereIn('id', $allowedIds)->update([
            $field => $validated[$field],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Позиция баннеров обновлена.',
        ]);
    }

    public function updateSort(UpdateSortEntityRequest $request, int $blogBanner): RedirectResponse
    {
        $banner = $this->baseQuery()->findOrFail($blogBanner);

        $banner->update([
            'sort' => $request->validated('sort'),
        ]);

        return back()->with('success', 'Сортировка баннера обновлена.');
    }

    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required_without:banners', 'array'],
            'items.*.id' => ['required_with:items', 'integer', 'exists:blog_banners,id'],
            'items.*.sort' => ['required_with:items', 'integer', 'min:0'],

            'banners' => ['required_without:items', 'array'],
            'banners.*.id' => ['required_with:banners', 'integer', 'exists:blog_banners,id'],
            'banners.*.sort' => ['required_with:banners', 'integer', 'min:0'],
        ]);

        $items = $validated['items'] ?? $validated['banners'];
        $ids = array_column($items, 'id');

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            $message = 'Часть баннеров недоступна для изменения сортировки.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 400)
                : back()->with('error', $message);
        }

        try {
            DB::transaction(function () use ($items) {
                foreach ($items as $row) {
                    BlogBanner::whereKey($row['id'])->update([
                        'sort' => (int) $row['sort'],
                    ]);
                }
            });

            $message = 'Сортировка баннеров обновлена.';

            return $request->expectsJson()
                ? response()->json(['message' => $message])
                : back()->with('success', $message);
        } catch (Throwable $e) {
            Log::error('Ошибка updateSortBulk blog banners: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            $message = 'Ошибка при массовом обновлении сортировки баннеров.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }

    public function approve(Request $request, int $blogBanner): RedirectResponse|JsonResponse
    {
        $user = auth()->user();

        if (!$user || !method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'moderation_status' => ['required', 'integer', 'in:0,1,2'],
            'moderation_note' => ['nullable', 'string', 'max:500'],
        ]);

        $banner = $this->baseQuery()->findOrFail($blogBanner);

        $banner->update([
            'moderation_status' => $validated['moderation_status'],
            'moderated_by' => $user->id,
            'moderated_at' => now(),
            'moderation_note' => $validated['moderation_note'] ?? null,
        ]);

        $message = 'Статус модерации баннера обновлён.';

        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : back()->with('success', $message);
    }
}
