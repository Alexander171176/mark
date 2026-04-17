<?php

namespace App\Http\Controllers\Admin\Finance\SubscriptionPlan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Finance\SubscriptionPlan\SubscriptionPlanRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\Finance\Currency\CurrencyResource;
use App\Http\Resources\Admin\Finance\SubscriptionPlan\SubscriptionPlanResource;
use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\Finance\SubscriptionPlan\SubscriptionPlan;
use App\Models\Admin\Finance\SubscriptionPlan\SubscriptionPlanImage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления Тарифными планами в административной панели.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (одиночное)
 *
 * @version 1.1 (Улучшен с RMB, транзакциями, Form Requests)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see SubscriptionPlan
 * @see SubscriptionPlanRequest
 */
class SubscriptionPlanController extends Controller
{
    /**
     * Разрешённые локали
     *
     * @var array|string[]
     */
    protected array $availableLocales = ['ru', 'en', 'kk'];

    /**
     * Список тарифов (по локали).
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $adminCountSubscriptionPlans = (int) config('site_settings.AdminCountSubscriptionPlans', 10);
        $adminSortSubscriptionPlans  = (string) config('site_settings.AdminSortSubscriptionPlans', 'idDesc');

        $currentLocale = $this->normalizeLocale(
            (string) $request->query('locale', config('app.fallback_locale', 'ru'))
        );

        try {
            $subscriptionPlans = SubscriptionPlan::query()
                ->locale($currentLocale)
                ->with([
                    'currency:id,code,name,symbol',
                    'images' => fn ($q) => $q
                        ->orderBy('subscription_plan_has_images.order')
                        ->orderBy('subscription_plan_has_images.image_id', 'desc'),
                ])
                ->orderBy('sort')
                ->get();

            $plansCount = SubscriptionPlan::query()
                ->locale($currentLocale)
                ->count();

        } catch (Throwable $e) {
            Log::error("Ошибка загрузки SubscriptionPlans (locale: {$currentLocale}): " . $e->getMessage(), [
                'exception' => $e,
            ]);

            $subscriptionPlans = collect();
            $plansCount = 0;

            session()->flash('error', __('admin/controllers.index_error'));
        }

        return Inertia::render('Admin/Finance/SubscriptionPlans/Index', [
            'subscriptionPlans'           => SubscriptionPlanResource::collection($subscriptionPlans)->resolve(),
            'plansCount'                  => $plansCount,
            'adminCountSubscriptionPlans' => $adminCountSubscriptionPlans,
            'adminSortSubscriptionPlans'  => $adminSortSubscriptionPlans,
            'currentLocale'               => $currentLocale,
            'availableLocales'            => $this->availableLocales,
        ]);
    }

    /**
     * Форма создания тарифа.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $currentLocale = $this->normalizeLocale(
            (string) $request->query('locale', config('app.fallback_locale', 'ru'))
        );

        $currencies = $this->getCurrenciesForSelect();

        return Inertia::render('Admin/Finance/SubscriptionPlans/Create', [
            'currencies'       => CurrencyResource::collection($currencies)->resolve(),
            'currentLocale'    => $currentLocale,
            'availableLocales' => $this->availableLocales,
        ]);
    }

    /**
     * Создание тарифа + изображения.
     *
     * @param SubscriptionPlanRequest $request
     * @return RedirectResponse
     */
    public function store(SubscriptionPlanRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $imagesData = $data['images'] ?? [];
        unset($data['images'], $data['deletedImages'], $data['_method']);

        try {
            /** @var SubscriptionPlan $subscriptionPlan */
            $subscriptionPlan = DB::transaction(function () use ($request, $data, $imagesData) {
                $subscriptionPlan = SubscriptionPlan::create($data);

                $syncData = $this->buildImagesSyncDataFromRequest(
                    request: $request,
                    imagesData: $imagesData,
                    ownerSubscriptionPlanId: $subscriptionPlan->id
                );

                if (!empty($syncData)) {
                    $subscriptionPlan->images()->sync($syncData);
                }

                return $subscriptionPlan;
            });

            Log::info('SubscriptionPlan создан', [
                'id'     => $subscriptionPlan->id,
                'title'  => $subscriptionPlan->title,
                'locale' => $subscriptionPlan->locale,
            ]);

            return redirect()
                ->route('admin.subscriptionPlans.index', ['locale' => $subscriptionPlan->locale])
                ->with('success', __('admin/controllers.created_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при создании SubscriptionPlan: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * show — редирект на edit.
     *
     * @param SubscriptionPlan $subscriptionPlan
     * @return RedirectResponse
     */
    public function show(SubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        return redirect()->route('admin.subscriptionPlans.edit', $subscriptionPlan);
    }

    /**
     * Форма редактирования тарифа + справочники.
     *
     * @param SubscriptionPlan $subscriptionPlan
     * @return Response
     */
    public function edit(SubscriptionPlan $subscriptionPlan): Response
    {
        $currentLocale = $this->normalizeLocale((string) ($subscriptionPlan->locale ?? config('app.fallback_locale', 'ru')));

        $subscriptionPlan->load([
            'currency:id,code,name,symbol',
            'images' => fn ($q) => $q
                ->orderBy('subscription_plan_has_images.order')
                ->orderBy('subscription_plan_has_images.image_id', 'desc'),
        ]);

        $currencies = $this->getCurrenciesForSelect();

        return Inertia::render('Admin/Finance/SubscriptionPlans/Edit', [
            'subscriptionPlan' => (new SubscriptionPlanResource($subscriptionPlan))->resolve(),
            'currencies'       => CurrencyResource::collection($currencies)->resolve(),
            'currentLocale'    => $currentLocale,
            'availableLocales' => $this->availableLocales,
        ]);
    }

    /**
     * Обновление тарифа + изображения.
     *
     * @param SubscriptionPlanRequest $request
     * @param SubscriptionPlan $subscriptionPlan
     * @return RedirectResponse
     */
    public function update(SubscriptionPlanRequest $request, SubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        $data = $request->validated();

        $imagesData      = $data['images'] ?? null; // важно: отличаем "не прислали" от "пусто"
        $deletedImageIds = $data['deletedImages'] ?? [];

        unset($data['images'], $data['deletedImages'], $data['_method']);

        try {
            DB::transaction(function () use ($request, $subscriptionPlan, $data, $imagesData, $deletedImageIds) {

                // 1) удалить выбранные изображения (detach + физ. удаление)
                if (!empty($deletedImageIds)) {
                    $subscriptionPlan->images()->detach($deletedImageIds);
                    $this->deleteImages($deletedImageIds);
                }

                // 2) обновить поля тарифа
                $subscriptionPlan->update($data);

                // 3) изображения
                // Защита: если фронт почему-то не прислал key "images" — не трогаем pivot
                if ($imagesData === null) {
                    return;
                }

                // Берём разрешённые существующие id, которые реально принадлежат тарифу
                $ownedImageIds = $subscriptionPlan->images()
                    ->pluck('subscription_plan_images.id')
                    ->map(fn ($v) => (int) $v)
                    ->toArray();

                $syncData = [];

                foreach ((array) $imagesData as $index => $imageData) {
                    $fileKey = "images.{$index}.file";

                    // 3.1) existing image meta update
                    if (!empty($imageData['id'])) {
                        $imageId = (int) $imageData['id'];

                        // чужие картинки / удалённые пропускаем
                        if (!in_array($imageId, $ownedImageIds, true)) {
                            continue;
                        }
                        if (!empty($deletedImageIds) && in_array($imageId, $deletedImageIds, true)) {
                            continue;
                        }

                        $image = SubscriptionPlanImage::query()->whereKey($imageId)->first();
                        if (!$image) {
                            continue;
                        }

                        $image->update([
                            'order'   => $imageData['order']   ?? $image->order,
                            'alt'     => $imageData['alt']     ?? $image->alt,
                            'caption' => $imageData['caption'] ?? $image->caption,
                        ]);

                        // Замена файла (если вдруг MultiImageEdit когда-то начнёт отдавать file)
                        if ($request->hasFile($fileKey)) {
                            $file = $request->file($fileKey);
                            if ($file && $file->isValid()) {
                                $image->clearMediaCollection('images');
                                $image->addMedia($file)->toMediaCollection('images');
                            }
                        }

                        $syncData[$image->id] = ['order' => (int) $image->order];
                        continue;
                    }

                    // 3.2) new image
                    if ($request->hasFile($fileKey)) {
                        $file = $request->file($fileKey);

                        $image = SubscriptionPlanImage::create([
                            'order'   => (int) ($imageData['order'] ?? 0),
                            'alt'     => (string) ($imageData['alt'] ?? ''),
                            'caption' => (string) ($imageData['caption'] ?? ''),
                        ]);

                        try {
                            if ($file && $file->isValid()) {
                                $image->addMedia($file)->toMediaCollection('images');
                                $syncData[$image->id] = ['order' => (int) $image->order];
                            } else {
                                $image->delete();
                            }
                        } catch (Throwable $e) {
                            Log::error("Ошибка Spatie media-library при update SubscriptionPlan {$subscriptionPlan->id}: " . $e->getMessage(), [
                                'exception' => $e,
                            ]);
                            $image->delete();
                        }
                    }
                }

                // ВАЖНО: sync только если реально что-то собрали.
                // Иначе можно случайно снести все связи, если фронт прислал пустой массив из-за бага.
                if (!empty($syncData)) {
                    $subscriptionPlan->images()->sync($syncData);
                }
            });

            Log::info('SubscriptionPlan обновлён', [
                'id'    => $subscriptionPlan->id,
                'title' => $subscriptionPlan->title,
            ]);

            return redirect()
                ->route('admin.subscriptionPlans.index', ['locale' => $subscriptionPlan->locale])
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении SubscriptionPlan ID {$subscriptionPlan->id}: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->withErrors(['server' => __('admin/controllers.updated_error')]);
        }
    }

    /**
     * Удаление тарифа вместе с изображениями (и их медиа).
     *
     * @param SubscriptionPlan $subscriptionPlan
     * @return RedirectResponse
     */
    public function destroy(SubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        try {
            DB::transaction(function () use ($subscriptionPlan) {
                $imageIds = $subscriptionPlan->images()
                    ->pluck('subscription_plan_images.id')
                    ->map(fn ($v) => (int) $v)
                    ->toArray();

                $subscriptionPlan->images()->detach();
                $this->deleteImages($imageIds);

                $subscriptionPlan->delete();
            });

            Log::info('SubscriptionPlan удалён', ['id' => $subscriptionPlan->id]);

            return redirect()
                ->route('admin.subscriptionPlans.index', ['locale' => $subscriptionPlan->locale])
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при удалении SubscriptionPlan ID {$subscriptionPlan->id}: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /**
     * Обновление активности одного тарифа.
     *
     * @param UpdateActivityRequest $request
     * @param SubscriptionPlan $subscriptionPlan
     * @return RedirectResponse
     */
    public function updateActivity(UpdateActivityRequest $request, SubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($subscriptionPlan, $validated) {
                $subscriptionPlan->activity = (bool) $validated['activity'];
                $subscriptionPlan->save();
            });

            Log::info("Обновлено activity SubscriptionPlan ID {$subscriptionPlan->id} на {$subscriptionPlan->activity}");

            return back()->with('success', __('admin/controllers.activity_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка обновления активности SubscriptionPlan (ID: {$subscriptionPlan->id}): " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', __('admin/controllers.activity_updated_error'));
        }
    }

    /**
     * Массовое обновление активности тарифов.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids'      => ['required', 'array'],
            'ids.*'    => ['integer', 'exists:subscription_plans,id'],
            'activity' => ['required', 'boolean'],
        ]);

        $ids = $validated['ids'];
        $activity = (bool) $validated['activity'];

        if (empty($ids)) {
            $message = __('admin/controllers.bulk_updated_activity_no_selection');

            return $request->expectsJson()
                ? response()->json(['message' => $message], 400)
                : back()->with('warning', $message);
        }

        try {
            $updatedCount = SubscriptionPlan::query()
                ->whereIn('id', $ids)
                ->update(['activity' => $activity]);

            $message = __('admin/controllers.bulk_activity_updated_success');

            Log::info($message, ['ids' => $ids, 'activity' => $activity]);

            return $request->expectsJson()
                ? response()->json(['message' => $message, 'updatedCount' => $updatedCount])
                : back()->with('success', $message);

        } catch (Throwable $e) {
            Log::error("Ошибка при массовом обновлении активности SubscriptionPlan: " . $e->getMessage(), [
                'exception' => $e,
                'ids' => $ids,
            ]);

            $message = __('admin/controllers.bulk_activity_updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }

    /**
     * Обновление sort одного тарифа.
     *
     * @param UpdateSortEntityRequest $request
     * @param SubscriptionPlan $subscriptionPlan
     * @return RedirectResponse
     */
    public function updateSort(UpdateSortEntityRequest $request, SubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $originalSort = (int) $subscriptionPlan->sort;

            $subscriptionPlan->sort = (int) $validated['sort'];
            $subscriptionPlan->save();

            Log::info("Сортировка тарифа '{$subscriptionPlan->title}' (ID: {$subscriptionPlan->id}) изменена с {$originalSort} на {$subscriptionPlan->sort}");

            return back()->with('success', __('admin/controllers.sort_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении сортировки SubscriptionPlan (ID: {$subscriptionPlan->id}): " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', __('admin/controllers.sort_updated_error'));
        }
    }

    /**
     * Массовое обновление сортировки тарифов.
     * Ожидает: subscriptionPlans: [{id: 1, sort: 10}, ...]
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'subscriptionPlans'        => ['required', 'array'],
            'subscriptionPlans.*.id'   => ['required', 'integer', 'exists:subscription_plans,id'],
            'subscriptionPlans.*.sort' => ['required', 'integer', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                foreach ($validated['subscriptionPlans'] as $row) {
                    SubscriptionPlan::query()
                        ->whereKey((int) $row['id'])
                        ->update(['sort' => (int) $row['sort']]);
                }
            });

            $message = __('admin/controllers.bulk_sort_updated_success');

            return $request->expectsJson()
                ? response()->json(['message' => $message])
                : back()->with('success', $message);

        } catch (Throwable $e) {
            Log::error("Ошибка массового обновления сортировки SubscriptionPlan: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            $message = __('admin/controllers.bulk_sort_updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    /**
     * @param string $locale
     * @return string
     */
    private function normalizeLocale(string $locale): string
    {
        if (!in_array($locale, $this->availableLocales, true)) {
            $fallback = (string) config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
            return $fallback;
        }

        return $locale;
    }

    /**
     * @return Collection|array
     */
    private function getCurrenciesForSelect(): Collection|array
    {
        // ВАЖНО: Create/Edit.vue ожидают cur.name, cur.code, cur.symbol
        // Если в вашей таблице поле называется иначе — оставьте нужное.
        return Currency::query()
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'symbol']);
    }

    /**
     * Собирает данные для sync() из request files images.{i}.file
     * Возвращает массив вида: [image_id => ['order' => N], ...]
     *
     * @param Request $request
     * @param array $imagesData
     * @param int $ownerSubscriptionPlanId
     * @return array
     */
    private function buildImagesSyncDataFromRequest(Request $request, array $imagesData, int $ownerSubscriptionPlanId): array
    {
        $syncData = [];

        foreach ($imagesData as $index => $imageData) {
            $fileKey = "images.{$index}.file";

            if (!$request->hasFile($fileKey)) {
                continue;
            }

            $file = $request->file($fileKey);

            $image = SubscriptionPlanImage::create([
                'order'   => (int) ($imageData['order'] ?? 0),
                'alt'     => (string) ($imageData['alt'] ?? ''),
                'caption' => (string) ($imageData['caption'] ?? ''),
            ]);

            try {
                if ($file && $file->isValid()) {
                    $image->addMedia($file)->toMediaCollection('images');
                    $syncData[$image->id] = ['order' => (int) $image->order];
                } else {
                    Log::warning("Недопустимый файл изображения SubscriptionPlan ID {$ownerSubscriptionPlanId}, index {$index}", [
                        'fileKey' => $fileKey,
                        'error'   => $file?->getErrorMessage(),
                    ]);
                    $image->delete();
                }
            } catch (Throwable $e) {
                Log::error("Ошибка Spatie media-library SubscriptionPlan {$ownerSubscriptionPlanId}, index {$index}: " . $e->getMessage(), [
                    'exception' => $e,
                ]);
                $image->delete();
            }
        }

        return $syncData;
    }

    /**
     * Удаляем записи SubscriptionPlanImage и их медиа (Spatie).
     *
     * @param array $imageIds
     * @return void
     */
    private function deleteImages(array $imageIds): void
    {
        if (empty($imageIds)) {
            return;
        }

        $images = SubscriptionPlanImage::query()
            ->whereIn('id', $imageIds)
            ->get();

        foreach ($images as $image) {
            try {
                $image->clearMediaCollection('images');
            } catch (Throwable $e) {
                Log::warning("Не удалось очистить media SubscriptionPlanImage ID {$image->id}: " . $e->getMessage(), [
                    'exception' => $e,
                ]);
            }

            $image->delete();
        }

        Log::info('Удалены SubscriptionPlanImage и их медиа', [
            'image_ids' => $imageIds,
        ]);
    }
}
