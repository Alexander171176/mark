<?php

namespace App\Http\Controllers\Admin\Finance\BundlePrice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Finance\BundlePrice\BundlePriceRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\Finance\BundlePrice\BundlePriceResource;
use App\Models\Admin\Finance\BundlePrice\BundlePrice;
use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\School\Bundle\Bundle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления Прайсами бандлов в административной панели.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (одиночное и массовое)
 *
 * Логика полностью повторяет CoursePriceController, только для сущности BundlePrice.
 *
 * @version 1.1 (Улучшен с RMB, транзакциями, Form Requests)
 * @author Александр Косолапов
 *
 * @see BundlePrice
 * @see BundlePriceRequest
 */
class BundlePriceController extends Controller
{
    /**
     * Общий список прайсов бандлов.
     * Пагинация/поиск/сортировка — на фронте.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $adminCountBundlePrices = (int) config('site_settings.AdminCountBundlePrices', 10);
        $adminSortBundlePrices  = config('site_settings.AdminSortBundlePrices', 'idDesc');

        $prices = collect();
        $pricesCount = 0;

        try {
            $prices = BundlePrice::query()
                ->with([
                    'bundle:id,title,slug,locale',
                    'currency:id,code,name,symbol',
                ])
                ->orderBy('sort')
                ->orderBy('id')
                ->get();

            $pricesCount = BundlePrice::query()->count();

        } catch (Throwable $e) {
            Log::error('Ошибка загрузки bundle_prices: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            session()->flash('error', __('admin/controllers.index_error'));
        }

        return Inertia::render('Admin/Finance/BundlePrices/Index', [
            'prices' => BundlePriceResource::collection($prices)->resolve(),
            'pricesCount' => $pricesCount,
            'adminCountBundlePrices' => $adminCountBundlePrices,
            'adminSortBundlePrices' => $adminSortBundlePrices,
        ]);
    }

    /**
     * Форма создания прайса бандла.
     *
     * @return Response
     */
    public function create(): Response
    {
        $bundles = Bundle::query()
            ->select(['id', 'title', 'slug', 'locale'])
            ->orderBy('locale', 'desc')
            ->orderBy('title')
            ->get();

        $currencies = Currency::query()
            ->orderBy('code')
            ->get(['id', 'code', 'name']);

        return Inertia::render('Admin/Finance/BundlePrices/Create', [
            'bundles' => $bundles,
            'currencies' => $currencies,
        ]);
    }

    /**
     * Сохранение нового прайса бандла.
     *
     * @param BundlePriceRequest $request
     * @return RedirectResponse
     */
    public function store(BundlePriceRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $price = BundlePrice::create($data);

            DB::commit();

            Log::info('BundlePrice created', [
                'id' => $price->id,
                'bundle_id' => $price->bundle_id,
                'currency_id' => $price->currency_id,
            ]);

            return redirect()
                ->route('admin.bundlePrices.index')
                ->with('success', __('admin/controllers.created_success'));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('BundlePrice store failed: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $data,
            ]);

            return back()
                ->withInput()
                ->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * show — редирект на edit (как у курсов).
     *
     * @param BundlePrice $bundlePrice
     * @return RedirectResponse
     */
    public function show(BundlePrice $bundlePrice): RedirectResponse
    {
        return redirect()->route('admin.bundlePrices.edit', $bundlePrice);
    }

    /**
     * Форма редактирования прайса бандла.
     *
     * @param BundlePrice $bundlePrice
     * @return Response
     */
    public function edit(BundlePrice $bundlePrice): Response
    {
        $bundlePrice->load([
            'bundle:id,title,slug,locale',
            'currency:id,code,name',
        ]);

        $bundles = Bundle::query()
            ->select(['id', 'title', 'slug', 'locale'])
            ->orderBy('locale', 'desc')
            ->orderBy('title')
            ->get();

        $currencies = Currency::query()
            ->orderBy('code')
            ->get(['id', 'code', 'name']);

        return Inertia::render('Admin/Finance/BundlePrices/Edit', [
            'bundlePrice' => (new BundlePriceResource($bundlePrice))->resolve(),
            'bundles' => $bundles,
            'currencies' => $currencies,
        ]);
    }

    /**
     * Обновление прайса бандла.
     *
     * @param BundlePriceRequest $request
     * @param BundlePrice $bundlePrice
     * @return RedirectResponse
     */
    public function update(BundlePriceRequest $request, BundlePrice $bundlePrice): RedirectResponse
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $bundlePrice->update($data);

            DB::commit();

            Log::info('BundlePrice updated', ['id' => $bundlePrice->id]);

            return redirect()
                ->route('admin.bundlePrices.index')
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('BundlePrice update failed: ' . $e->getMessage(), [
                'exception' => $e,
                'id' => $bundlePrice->id,
                'data' => $data,
            ]);

            return back()
                ->withInput()
                ->withErrors(['server' => __('admin/controllers.updated_error')]);
        }
    }

    /**
     * Удаление прайса бандла.
     *
     * @param BundlePrice $bundlePrice
     * @return RedirectResponse
     */
    public function destroy(BundlePrice $bundlePrice): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $bundlePrice->delete();

            DB::commit();

            Log::info('BundlePrice deleted', ['id' => $bundlePrice->id]);

            return redirect()
                ->route('admin.bundlePrices.index')
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('BundlePrice delete failed: ' . $e->getMessage(), [
                'exception' => $e,
                'id' => $bundlePrice->id,
            ]);

            return back()->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /* ===================== actions  ===================== */

    /**
     * Обновление статуса активности одного прайса бандла.
     *
     * @param UpdateActivityRequest $request
     * @param BundlePrice $bundlePrice
     * @return RedirectResponse
     */
    public function updateActivity(UpdateActivityRequest $request, BundlePrice $bundlePrice): RedirectResponse
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $bundlePrice->activity = (bool) $validated['activity'];
            $bundlePrice->save();

            DB::commit();

            return back()->with('success', __('admin/controllers.activity_updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка обновления активности BundlePrice (ID: {$bundlePrice->id}): " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', __('admin/controllers.activity_updated_error'));
        }
    }

    /**
     * Массовое обновление активности прайсов бандлов.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids'      => ['required', 'array'],
            'ids.*'    => ['integer', 'exists:bundle_prices,id'],
            'activity' => ['required', 'boolean'],
        ]);

        $ids = $validated['ids'];
        $activity = (bool) $validated['activity'];

        if (empty($ids)) {
            $msg = __('admin/controllers.bulk_updated_activity_no_selection');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 400)
                : back()->with('warning', $msg);
        }

        try {
            $updatedCount = BundlePrice::whereIn('id', $ids)->update(['activity' => $activity]);
            $msg = __('admin/controllers.bulk_activity_updated_success');

            return $request->expectsJson()
                ? response()->json(['message' => $msg, 'updatedCount' => $updatedCount])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error('Ошибка bulk активности bundle_prices: ' . $e->getMessage(), [
                'exception' => $e,
                'ids' => $ids,
            ]);

            $msg = __('admin/controllers.bulk_activity_updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Обновление сортировки одного прайса бандла.
     *
     * @param UpdateSortEntityRequest $request
     * @param BundlePrice $bundlePrice
     * @return RedirectResponse
     */
    public function updateSort(UpdateSortEntityRequest $request, BundlePrice $bundlePrice): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $original = $bundlePrice->sort;

            $bundlePrice->sort = (int) $validated['sort'];
            $bundlePrice->save();

            Log::info("Sort BundlePrice {$bundlePrice->id}: {$original} -> {$bundlePrice->sort}");

            return back()->with('success', __('admin/controllers.sort_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка обновления sort BundlePrice (ID: {$bundlePrice->id}): " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', __('admin/controllers.sort_updated_error'));
        }
    }

    /**
     * Массовое обновление сортировки прайсов бандлов.
     * Ожидает массив:
     * bundlePrices: [{id: 1, sort: 10}, ...]
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'bundlePrices' => ['required', 'array'],
            'bundlePrices.*.id'   => ['required', 'integer', 'exists:bundle_prices,id'],
            'bundlePrices.*.sort' => ['required', 'integer', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                foreach ($validated['bundlePrices'] as $row) {
                    BundlePrice::whereKey($row['id'])->update([
                        'sort' => (int) $row['sort'],
                    ]);
                }
            });

            $msg = __('admin/controllers.bulk_sort_updated_success');

            return $request->expectsJson()
                ? response()->json(['message' => $msg])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error('Ошибка bulk sort bundle_prices: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            $msg = __('admin/controllers.bulk_sort_updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Массовое удаление прайсов бандлов.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function bulkDestroy(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer', 'exists:bundle_prices,id'],
        ]);

        $ids = $validated['ids'];

        if (empty($ids)) {
            $msg = __('admin/controllers.bulk_deleted_no_selection') ?? 'Не выбраны записи для удаления.';

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 400)
                : back()->with('warning', $msg);
        }

        try {
            BundlePrice::whereIn('id', $ids)->delete();

            $msg = __('admin/controllers.bulk_deleted_success') ?? 'Прайсы удалены.';

            return $request->expectsJson()
                ? response()->json(['message' => $msg])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error('Ошибка bulk delete bundle_prices: ' . $e->getMessage(), [
                'exception' => $e,
                'ids' => $ids,
            ]);

            $msg = __('admin/controllers.bulk_deleted_error') ?? 'Ошибка при массовом удалении прайсов.';

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }
}
