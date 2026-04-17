<?php

namespace App\Http\Controllers\Admin\Blog\Banner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Blog\Banner\BannerRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateLeftRequest;
use App\Http\Requests\Admin\System\UpdateMainRequest;
use App\Http\Requests\Admin\System\UpdateRightRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\Blog\Banner\BannerResource;
use App\Models\Admin\Blog\Banner\Banner;
use App\Models\Admin\Blog\Banner\BannerImage;
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
 * @see Banner
 * @see BannerRequest
 */
class BannerController extends Controller
{
    /**
     * Разрешённые локали.
     *
     * @var array|string[]
     */
    protected array $availableLocales = ['ru', 'en', 'kk'];

    /**
     * Локаль с БД
     *
     * @param string|null $locale
     * @return string
     */
    private function normalizeLocale(?string $locale): string
    {
        $locale = $locale ?: config('app.fallback_locale', 'ru');

        return in_array($locale, $this->availableLocales, true)
            ? $locale
            : config('app.fallback_locale', 'ru');
    }

    /**
     * Базовый query с ограничением “владелец/админ”.
     * Автор видит только свои. Admin — все.
     *
     * @return Builder
     */
    private function baseQuery(): Builder
    {
        $q = Banner::query();

        $user = auth()->user();
        if ($user && ! $user->hasRole('admin')) {
            $q->where('user_id', $user->id);
        }

        return $q;
    }

    /**
     * Общие данные для страниц.
     *
     * @param string|null $locale
     * @return array
     */
    private function sharedSelects(?string $locale = null): array
    {
        $locale = $this->normalizeLocale($locale);

        return [
            'currentLocale'    => $locale,
            'availableLocales' => $this->availableLocales,
        ];
    }

    /**
     * Список баннеров + локали.
     * GET /admin/banners?locale=ru
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $adminCountBanners = (int) config('site_settings.AdminCountBanners', 15);
        $adminSortBanners  = (string) config('site_settings.AdminSortBanners', 'idDesc');

        $currentLocale = $this->normalizeLocale($request->query('locale'));

        // предупредим, если прилетела неизвестная локаль
        if (!in_array($request->query('locale', $currentLocale), $this->availableLocales, true)) {
            session()->flash('warning', __('admin/controllers.index_locale_error'));
        }

        try {
            $banners = $this->baseQuery()
                ->where('locale', $currentLocale)
                ->with([
                    'images' => fn ($q) => $q->orderByPivot('order', 'asc'),
                    'owner',
                    'moderator:id,name',
                ])
                ->orderBy('sort')
                ->orderBy('id')
                ->get();

            return Inertia::render('Admin/Blog/Banners/Index', [
                'banners'      => BannerResource::collection($banners),
                'bannersCount' => $banners->count(),

                'adminCountBanners' => $adminCountBanners,
                'adminSortBanners'  => $adminSortBanners,

                'currentLocale'    => $currentLocale,
                'availableLocales' => $this->availableLocales,
            ]);
        } catch (Throwable $e) {
            Log::error("Ошибка загрузки баннеров для Index (locale: {$currentLocale}): ".$e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/Blog/Banners/Index', [
                'banners'      => [],
                'bannersCount' => 0,

                'adminCountBanners' => $adminCountBanners,
                'adminSortBanners'  => $adminSortBanners,

                'currentLocale'    => $currentLocale,
                'availableLocales' => $this->availableLocales,

                'error' => __('admin/controllers.index_error'),
            ]);
        }
    }

    /**
     * Создание Баннера
     * GET /admin/banners/create?locale=ru
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $currentLocale = $this->normalizeLocale($request->query('locale'));

        return Inertia::render('Admin/Blog/Banners/Create', array_merge(
            [],
            $this->sharedSelects($currentLocale)
        ));
    }

    /**
     * Сохранение нового Баннера
     * POST /admin/banners
     *
     * @param BannerRequest $request
     * @return RedirectResponse
     */
    public function store(BannerRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $imagesData = $data['images'] ?? [];
        unset($data['images']);

        $user = auth()->user();

        // Владелец: принудительно. Admin может передать user_id, но по умолчанию тоже себе.
        if ($user && ! $user->hasRole('admin')) {
            $data['user_id'] = $user->id;
            // автор не управляет модерацией
            unset($data['moderation_status'], $data['moderation_note'], $data['moderated_by'], $data['moderated_at']);
        } else {
            $data['user_id'] = $data['user_id'] ?? ($user?->id);
        }

        // Локаль: нормализуем (если поле есть в форме)
        if (array_key_exists('locale', $data)) {
            $data['locale'] = $this->normalizeLocale($data['locale']);
        }

        try {
            DB::beginTransaction();

            $banner = Banner::create($data);

            // --- images (НЕ МЕНЯЕМ ЛОГИКУ) ---
            $imageSyncData = [];
            $imageIndex = 0;

            foreach ($imagesData as $imageData) {
                $fileKey = "images.{$imageIndex}.file";

                if ($request->hasFile($fileKey)) {
                    $image = BannerImage::create([
                        'order'   => $imageData['order']   ?? 0,
                        'alt'     => $imageData['alt']     ?? '',
                        'caption' => $imageData['caption'] ?? '',
                    ]);

                    try {
                        $file = $request->file($fileKey);

                        if ($file->isValid()) {
                            $image->addMedia($file)->toMediaCollection('images');
                            $imageSyncData[$image->id] = ['order' => $image->order];
                        } else {
                            Log::warning("Недопустимый файл изображения с индексом {$imageIndex} для баннера {$banner->id}", [
                                'fileKey' => $fileKey,
                                'error'   => $file->getErrorMessage(),
                            ]);
                            $image->delete();
                            $imageIndex++;
                            continue;
                        }
                    } catch (Throwable $e) {
                        Log::error("Ошибка Spatie media-library в баннере {$banner->id}, индекс {$imageIndex}: {$e->getMessage()}", [
                            'exception' => $e,
                        ]);
                        $image->delete();
                        $imageIndex++;
                        continue;
                    }
                }

                $imageIndex++;
            }

            $banner->images()->sync($imageSyncData);

            DB::commit();

            return redirect()->route('admin.banners.index', ['locale' => $banner->locale])
                ->with('success', __('admin/controllers.created_success'));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при создании баннера: {$e->getMessage()}", ['exception' => $e]);

            return back()->withInput()->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * Редактирование Баннера
     * GET /admin/banners/{banner}/edit?locale=ru
     *
     * @param Request $request
     * @param int $banner
     * @return Response
     */
    public function edit(Request $request, int $banner): Response
    {
        $bannerModel = $this->baseQuery()
            ->with([
                'images' => fn ($q) => $q->orderByPivot('order', 'asc'),
                'owner:id,name,email',
                'moderator:id,name',
            ])
            ->findOrFail($banner);

        $targetLocale = $this->normalizeLocale($request->query('locale', $bannerModel->locale));

        return Inertia::render('Admin/Blog/Banners/Edit', array_merge(
            [
                'banner'       => new BannerResource($bannerModel),
                'targetLocale' => $targetLocale,
            ],
            $this->sharedSelects($bannerModel->locale)
        ));
    }

    /**
     * Обновление Баннера
     * PUT/PATCH /admin/banners/{banner}
     *
     * @param BannerRequest $request
     * @param int $banner
     * @return RedirectResponse
     */
    public function update(BannerRequest $request, int $banner): RedirectResponse
    {
        $banner = $this->baseQuery()->findOrFail($banner);

        $data = $request->validated();

        $imagesData      = $data['images'] ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];

        unset($data['images'], $data['deletedImages'], $data['_method']);

        $user = auth()->user();
        if ($user && ! $user->hasRole('admin')) {
            // автор не меняет владельца и модерацию
            $data['user_id'] = $user->id;
            unset($data['moderation_status'], $data['moderation_note'], $data['moderated_by'], $data['moderated_at']);
        }

        // Локаль: нормализуем (если поле меняется)
        if (array_key_exists('locale', $data)) {
            $data['locale'] = $this->normalizeLocale($data['locale']);
        }

        try {
            DB::beginTransaction();

            // 1) удалить выбранные изображения
            if (!empty($deletedImageIds)) {
                $banner->images()->detach($deletedImageIds);
                $this->deleteImages($deletedImageIds);
            }

            // 2) обновить поля
            $banner->update($data);

            // 3) images (НЕ МЕНЯЕМ ЛОГИКУ)
            $syncData = [];
            foreach ($imagesData as $index => $imageData) {
                $fileKey = "images.{$index}.file";

                if (!empty($imageData['id'])) {
                    $img = BannerImage::find($imageData['id']);

                    if ($img && !in_array($img->id, $deletedImageIds, true)) {
                        $img->update([
                            'order'   => $imageData['order']   ?? $img->order,
                            'alt'     => $imageData['alt']     ?? $img->alt,
                            'caption' => $imageData['caption'] ?? $img->caption,
                        ]);

                        if ($request->hasFile($fileKey)) {
                            $img->clearMediaCollection('images');
                            $img->addMedia($request->file($fileKey))->toMediaCollection('images');
                        }

                        $syncData[$img->id] = ['order' => $img->order];
                    }
                } elseif ($request->hasFile($fileKey)) {
                    $new = BannerImage::create([
                        'order'   => $imageData['order']   ?? 0,
                        'alt'     => $imageData['alt']     ?? '',
                        'caption' => $imageData['caption'] ?? '',
                    ]);

                    $new->addMedia($request->file($fileKey))->toMediaCollection('images');
                    $syncData[$new->id] = ['order' => $new->order];
                }
            }

            $banner->images()->sync($syncData);

            DB::commit();

            return redirect()->route('admin.banners.index', ['locale' => $banner->locale])
                ->with('success', __('admin/controllers.updated_success'));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при обновлении баннера ID {$banner->id}: {$e->getMessage()}", ['exception' => $e]);

            return back()->withInput()->with('error', __('admin/controllers.updated_error'));
        }
    }

    /**
     * Удаление Баннера
     * DELETE /admin/banners/{banner}
     *
     * @param int $banner
     * @return RedirectResponse
     */
    public function destroy(int $banner): RedirectResponse
    {
        $banner = $this->baseQuery()->findOrFail($banner);

        try {
            DB::beginTransaction();

            $this->deleteImages($banner->images()->pluck('id')->toArray());
            $banner->delete();

            DB::commit();

            return redirect()->route('admin.banners.index', ['locale' => $banner->locale])
                ->with('success', __('admin/controllers.deleted_success'));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при удалении баннера ID {$banner->id}: ".$e->getMessage(), ['exception' => $e]);

            return back()->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /**
     * Массовое удаление
     * DELETE /admin/actions/banners/bulk-destroy
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:banners,id',
        ]);

        $ids   = $validated['ids'];
        $count = count($ids);

        // ограничение “владелец/админ”
        $allowedIds = $this->baseQuery()->whereIn('id', $ids)->pluck('id')->toArray();
        if (count($allowedIds) !== $count) {
            return back()->with('error', __('admin/controllers.bulk_deleted_error'));
        }

        try {
            DB::beginTransaction();

            $allImageIds = BannerImage::whereHas('banners', fn ($q) => $q->whereIn('banners.id', $allowedIds))
                ->pluck('id')->toArray();

            if (!empty($allImageIds)) {
                DB::table('banner_has_images')->whereIn('banner_id', $allowedIds)->delete();
                $this->deleteImages($allImageIds);
            }

            Banner::whereIn('id', $allowedIds)->delete();

            DB::commit();

            return redirect()->route('admin.banners.index')
                ->with('success', __('admin/controllers.bulk_deleted_success', ['count' => $count]));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при массовом удалении баннеров: ".$e->getMessage(), [
                'ids'       => $allowedIds,
                'exception' => $e,
            ]);

            return back()->with('error', __('admin/controllers.bulk_deleted_error'));
        }
    }

    /**
     * Обновление в левой колонке
     * PUT /admin/actions/banners/{banner}/left
     *
     * @param UpdateLeftRequest $request
     * @param int $banner
     * @return RedirectResponse
     */
    public function updateLeft(UpdateLeftRequest $request, int $banner): RedirectResponse
    {
        $banner = $this->baseQuery()->findOrFail($banner);
        $validated = $request->validated();

        try {
            DB::beginTransaction();
            $banner->left = $validated['left'];
            $banner->save();
            DB::commit();

            return back()->with('success', __('admin/controllers.left_updated_success'));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка updateLeft баннера ID {$banner->id}: ".$e->getMessage(), ['exception' => $e]);

            return back()->with('error', __('admin/controllers.left_updated_error'));
        }
    }

    /**
     * Массовое обновление в левой колонке
     * PUT /admin/actions/banners/bulk-left
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkUpdateLeft(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:banners,id',
            'left'  => 'required|boolean',
        ]);

        $allowedIds = $this->baseQuery()->whereIn('id', $data['ids'])->pluck('id')->toArray();
        if (count($allowedIds) !== count($data['ids'])) {
            return response()->json(['success' => false, 'message' => __('admin/controllers.bulk_left_updated_error')], 403);
        }

        try {
            Banner::whereIn('id', $allowedIds)->update(['left' => $data['left']]);
            return response()->json(['success' => true]);
        } catch (Throwable $e) {
            Log::error('Ошибка массового updateLeft: '.$e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => __('admin/controllers.bulk_left_updated_error')], 500);
        }
    }

    /**
     * Обновление в главном
     * PUT /admin/actions/banners/{banner}/main
     *
     * @param UpdateMainRequest $request
     * @param int $banner
     * @return RedirectResponse
     */
    public function updateMain(UpdateMainRequest $request, int $banner): RedirectResponse
    {
        $banner = $this->baseQuery()->findOrFail($banner);
        $validated = $request->validated();

        try {
            DB::beginTransaction();
            $banner->main = $validated['main'];
            $banner->save();
            DB::commit();

            return back()->with('success', __('admin/controllers.main_updated_success'));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка updateMain баннера ID {$banner->id}: ".$e->getMessage(), ['exception' => $e]);

            return back()->with('error', __('admin/controllers.main_updated_error'));
        }
    }

    /**
     * Массовое обновление в главном
     * PUT /admin/actions/banners/bulk-main
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkUpdateMain(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:banners,id',
            'main'  => 'required|boolean',
        ]);

        $allowedIds = $this->baseQuery()->whereIn('id', $data['ids'])->pluck('id')->toArray();
        if (count($allowedIds) !== count($data['ids'])) {
            return response()->json(['success' => false, 'message' => __('admin/controllers.bulk_main_updated_error')], 403);
        }

        try {
            Banner::whereIn('id', $allowedIds)->update(['main' => $data['main']]);
            return response()->json(['success' => true]);
        } catch (Throwable $e) {
            Log::error('Ошибка массового updateMain: '.$e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => __('admin/controllers.bulk_main_updated_error')], 500);
        }
    }

    /**
     * Обновление в правой колонке
     * PUT /admin/actions/banners/{banner}/right
     *
     * @param UpdateRightRequest $request
     * @param int $banner
     * @return RedirectResponse
     */
    public function updateRight(UpdateRightRequest $request, int $banner): RedirectResponse
    {
        $banner = $this->baseQuery()->findOrFail($banner);
        $validated = $request->validated();

        try {
            DB::beginTransaction();
            $banner->right = $validated['right'];
            $banner->save();
            DB::commit();

            return back()->with('success', __('admin/controllers.right_updated_success'));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка updateRight баннера ID {$banner->id}: ".$e->getMessage(), ['exception' => $e]);

            return back()->with('error', __('admin/controllers.right_updated_error'));
        }
    }

    /**
     * Массовое обновление в правой колонке
     * PUT /admin/actions/banners/bulk-right
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkUpdateRight(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:banners,id',
            'right' => 'required|boolean',
        ]);

        $allowedIds = $this->baseQuery()->whereIn('id', $data['ids'])->pluck('id')->toArray();
        if (count($allowedIds) !== count($data['ids'])) {
            return response()->json(['success' => false, 'message' => __('admin/controllers.bulk_right_updated_error')], 403);
        }

        try {
            Banner::whereIn('id', $allowedIds)->update(['right' => $data['right']]);
            return response()->json(['success' => true]);
        } catch (Throwable $e) {
            Log::error('Ошибка массового updateRight: '.$e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => __('admin/controllers.bulk_right_updated_error')], 500);
        }
    }

    /**
     * Обновление активности
     * PUT /admin/actions/banners/{banner}/activity
     *
     * @param UpdateActivityRequest $request
     * @param int $banner
     * @return RedirectResponse
     */
    public function updateActivity(UpdateActivityRequest $request, int $banner): RedirectResponse
    {
        $banner = $this->baseQuery()->findOrFail($banner);
        $validated = $request->validated();

        try {
            DB::beginTransaction();
            $banner->activity = $validated['activity'];
            $banner->save();
            DB::commit();

            return back()->with('success', __('admin/controllers.activity_updated_success'));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка обновления активности баннера ID {$banner->id}: ".$e->getMessage(), ['exception' => $e]);

            return back()->with('error', __('admin/controllers.activity_updated_error'));
        }
    }

    /**
     * Массовое обновление активности
     * PUT /admin/actions/banners/bulk-activity
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids'      => 'required|array',
            'ids.*'    => 'required|integer|exists:banners,id',
            'activity' => 'required|boolean',
        ]);

        $allowedIds = $this->baseQuery()->whereIn('id', $validated['ids'])->pluck('id')->toArray();
        if (count($allowedIds) !== count($validated['ids'])) {
            return back()->with('error', __('admin/controllers.bulk_activity_updated_error'));
        }

        try {
            DB::beginTransaction();
            Banner::whereIn('id', $allowedIds)->update(['activity' => $validated['activity']]);
            DB::commit();

            return back()->with('success', __('admin/controllers.bulk_activity_updated_success'));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка массового обновления активности баннеров: ".$e->getMessage(), ['exception' => $e]);

            return back()->with('error', __('admin/controllers.bulk_activity_updated_error'));
        }
    }

    /**
     * Обновление сортировки
     * PUT /admin/actions/banners/{banner}/sort
     *
     * @param UpdateSortEntityRequest $request
     * @param int $banner
     * @return RedirectResponse
     */
    public function updateSort(UpdateSortEntityRequest $request, int $banner): RedirectResponse
    {
        $banner = $this->baseQuery()->findOrFail($banner);
        $validated = $request->validated();

        try {
            DB::beginTransaction();
            $banner->sort = $validated['sort'];
            $banner->save();
            DB::commit();

            return back()->with('success', __('admin/controllers.sort_updated_success'));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка обновления сортировки баннера ID {$banner->id}: ".$e->getMessage(), ['exception' => $e]);

            return back()->with('error', __('admin/controllers.sort_updated_error'));
        }
    }

    /**
     * Массовое обновление активности
     * PUT /admin/actions/banners/update-sort-bulk
     *
     * Поддержка payload:
     * - items: [{id, sort}]
     * - либо старый banners: [{id, sort}]
     *
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function updateSortBulk(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['nullable', 'string', Rule::in($this->availableLocales)],

            'items'        => ['required_without:banners', 'array'],
            'items.*.id'   => ['required_with:items', 'integer', 'exists:banners,id'],
            'items.*.sort' => ['required_with:items', 'integer', 'min:0'],

            'banners'        => ['required_without:items', 'array'],
            'banners.*.id'   => ['required_with:banners', 'integer', 'exists:banners,id'],
            'banners.*.sort' => ['required_with:banners', 'integer', 'min:0'],
        ]);

        $data = $validated['items'] ?? $validated['banners'];

        try {
            DB::transaction(function () use ($data, $validated) {
                $ids = array_column($data, 'id');

                $q = $this->baseQuery()->whereIn('id', $ids);
                if (!empty($validated['locale'])) {
                    $q->where('locale', $validated['locale']);
                }

                $allowedIds = $q->pluck('id')->toArray();
                if (count($allowedIds) !== count($ids)) {
                    abort(403);
                }

                foreach ($data as $row) {
                    Banner::whereKey($row['id'])->update(['sort' => (int) $row['sort']]);
                }
            });

            $msg = __('admin/controllers.bulk_sort_updated_success');

            return $request->expectsJson()
                ? response()->json(['message' => $msg])
                : back()->with('success', $msg);
        } catch (Throwable $e) {
            Log::error("Bulk sort banners error: ".$e->getMessage(), ['exception' => $e]);

            $msg = __('admin/controllers.bulk_sort_updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Одобрение admin
     * PUT/POST /admin/actions/banners/{banner}/approve
     *
     * @param Request $request
     * @param int $banner
     * @return RedirectResponse|JsonResponse
     */
    public function approve(Request $request, int $banner): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('admin')) {
            abort(403);
        }

        $bannerModel = Banner::query()->findOrFail($banner);

        $validated = $request->validate([
            'moderation_status' => ['required', 'integer', Rule::in([0, 1, 2])],
            'moderation_note'   => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $bannerModel->update([
                'moderation_status' => (int) $validated['moderation_status'],
                'moderation_note'   => $validated['moderation_note'] ?? null,
                'moderated_by'      => $user->id,
                'moderated_at'      => now(),
            ]);

            $msg = __('admin/controllers.updated_success');

            return $request->expectsJson()
                ? response()->json([
                    'message' => $msg,
                    'banner'  => new BannerResource($bannerModel->load([
                        'images' => fn ($q) => $q->orderByPivot('order', 'asc'),
                        'owner:id,name,email',
                        'moderator:id,name',
                    ])),
                ])
                : back()->with('success', $msg);
        } catch (Throwable $e) {
            Log::error("Ошибка approve баннера {$bannerModel->id}: ".$e->getMessage(), ['exception' => $e]);

            $msg = __('admin/controllers.updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Приватный метод удаления изображений (Spatie).
     *
     * @param array $imageIds
     * @return void
     */
    private function deleteImages(array $imageIds): void
    {
        if (empty($imageIds)) return;

        $imagesToDelete = BannerImage::whereIn('id', $imageIds)->get();
        foreach ($imagesToDelete as $image) {
            $image->clearMediaCollection('images');
            $image->delete();
        }

        Log::info('Удалены записи BannerImage и их медиа', ['image_ids' => $imageIds]);
    }
}
