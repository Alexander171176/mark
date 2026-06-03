<?php

namespace App\Http\Controllers\Admin\School\SchoolBundlePrice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\SchoolBundlePrice\SchoolBundlePriceRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\School\SchoolBundle\SchoolBundleSharedResource;
use App\Http\Resources\Admin\School\SchoolBundlePrice\SchoolBundlePriceResource;
use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\School\SchoolBundle\SchoolBundle;
use App\Models\Admin\School\SchoolBundlePrice\SchoolBundlePrice;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления Прайсами набора курсов
 * (SchoolBundlePrice) в административной панели.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (одиночное и массовое)
 *
 * @version 1.1 (Улучшен с RMB, транзакциями, Form Requests)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see SchoolBundlePrice
 * @see SchoolBundlePriceRequest
 */
class SchoolBundlePriceController extends Controller
{
    /** Список цен бандлов. */
    public function index(Request $request): Response
    {
        $bundleId = $request->query('school_bundle_id');
        $currencyId = $request->query('currency_id');
        $activity = $request->query('activity');

        $adminSchoolBundlePricesPerPage = (int) config('site_settings.adminSchoolBundlePricesPerPage', 10);
        $adminSchoolBundlePricesDefaultSort = (string) config('site_settings.adminSchoolBundlePricesDefaultSort', 'idDesc');

        try {
            $query = SchoolBundlePrice::query()
                ->with([
                    'bundle.translation',
                    'bundle.translations',
                    'currency:id,code,name,symbol',
                ]);

            if ($bundleId) {
                $query->where('school_bundle_id', (int) $bundleId);
            }

            if ($currencyId) {
                $query->where('currency_id', (int) $currencyId);
            }

            if ($activity !== null && $activity !== '') {
                $query->where('activity', filter_var($activity, FILTER_VALIDATE_BOOL));
            }

            match ($adminSchoolBundlePricesDefaultSort) {
                'idAsc' => $query->orderBy('school_bundle_prices.id'),
                'idDesc' => $query->orderByDesc('school_bundle_prices.id'),

                'sortAsc' => $query->orderBy('sort')->orderByDesc('school_bundle_prices.id'),
                'sortDesc' => $query->orderByDesc('sort')->orderByDesc('school_bundle_prices.id'),

                'priceAsc' => $query->orderBy('price')->orderByDesc('school_bundle_prices.id'),
                'priceDesc' => $query->orderByDesc('price')->orderByDesc('school_bundle_prices.id'),

                'salePriceAsc' => $query->orderBy('sale_price')->orderByDesc('school_bundle_prices.id'),
                'salePriceDesc' => $query->orderByDesc('sale_price')->orderByDesc('school_bundle_prices.id'),

                'compareAtPriceAsc' => $query->orderBy('compare_at_price')->orderByDesc('school_bundle_prices.id'),
                'compareAtPriceDesc' => $query->orderByDesc('compare_at_price')->orderByDesc('school_bundle_prices.id'),

                'effectivePriceAsc' => $query
                    ->orderByRaw('CASE WHEN sale_price IS NOT NULL AND sale_price > 0 THEN sale_price ELSE price END ASC')
                    ->orderByDesc('school_bundle_prices.id'),

                'effectivePriceDesc' => $query
                    ->orderByRaw('CASE WHEN sale_price IS NOT NULL AND sale_price > 0 THEN sale_price ELSE price END DESC')
                    ->orderByDesc('school_bundle_prices.id'),

                'discountPercentAsc' => $query
                    ->orderByRaw("
                        CASE
                            WHEN compare_at_price IS NULL OR compare_at_price <= 0 THEN 0
                            WHEN compare_at_price <= (CASE WHEN sale_price IS NOT NULL AND sale_price > 0 THEN sale_price ELSE price END) THEN 0
                            ELSE ((compare_at_price - (CASE WHEN sale_price IS NOT NULL AND sale_price > 0 THEN sale_price ELSE price END)) / compare_at_price) * 100
                        END ASC
                    ")
                    ->orderByDesc('school_bundle_prices.id'),

                'discountPercentDesc' => $query
                    ->orderByRaw("
                        CASE
                            WHEN compare_at_price IS NULL OR compare_at_price <= 0 THEN 0
                            WHEN compare_at_price <= (CASE WHEN sale_price IS NOT NULL AND sale_price > 0 THEN sale_price ELSE price END) THEN 0
                            ELSE ((compare_at_price - (CASE WHEN sale_price IS NOT NULL AND sale_price > 0 THEN sale_price ELSE price END)) / compare_at_price) * 100
                        END DESC
                    ")
                    ->orderByDesc('school_bundle_prices.id'),

                'startsAtAsc' => $query->orderBy('starts_at')->orderByDesc('school_bundle_prices.id'),
                'startsAtDesc' => $query->orderByDesc('starts_at')->orderByDesc('school_bundle_prices.id'),

                'endsAtAsc' => $query->orderBy('ends_at')->orderByDesc('school_bundle_prices.id'),
                'endsAtDesc' => $query->orderByDesc('ends_at')->orderByDesc('school_bundle_prices.id'),

                'activity' => $query->where('activity', true)->orderByDesc('school_bundle_prices.id'),
                'inactive' => $query->where('activity', false)->orderByDesc('school_bundle_prices.id'),

                'bundleTitleAsc' => $query
                    ->leftJoin('school_bundle_translations as sbt', function ($join) {
                        $join->on('school_bundle_prices.school_bundle_id', '=', 'sbt.school_bundle_id')
                            ->where('sbt.locale', app()->getLocale());
                    })
                    ->orderBy('sbt.title')
                    ->orderByDesc('school_bundle_prices.id')
                    ->select('school_bundle_prices.*'),

                'bundleTitleDesc' => $query
                    ->leftJoin('school_bundle_translations as sbt', function ($join) {
                        $join->on('school_bundle_prices.school_bundle_id', '=', 'sbt.school_bundle_id')
                            ->where('sbt.locale', app()->getLocale());
                    })
                    ->orderByDesc('sbt.title')
                    ->orderByDesc('school_bundle_prices.id')
                    ->select('school_bundle_prices.*'),

                'currencyCodeAsc' => $query
                    ->join('currencies', 'school_bundle_prices.currency_id', '=', 'currencies.id')
                    ->orderBy('currencies.code')
                    ->orderByDesc('school_bundle_prices.id')
                    ->select('school_bundle_prices.*'),

                'currencyCodeDesc' => $query
                    ->join('currencies', 'school_bundle_prices.currency_id', '=', 'currencies.id')
                    ->orderByDesc('currencies.code')
                    ->orderByDesc('school_bundle_prices.id')
                    ->select('school_bundle_prices.*'),

                default => $query->orderByDesc('school_bundle_prices.id'),
            };

            $prices = $query->get();

            return Inertia::render('Admin/School/BundlePrices/Index', [
                'prices' => SchoolBundlePriceResource::collection($prices),
                'pricesCount' => $prices->count(),

                'adminSchoolBundlePricesPerPage' => $adminSchoolBundlePricesPerPage,
                'adminSchoolBundlePricesDefaultSort' => $adminSchoolBundlePricesDefaultSort,

                'filters' => [
                    'school_bundle_id' => $bundleId,
                    'currency_id' => $currencyId,
                    'activity' => $activity,
                ],

                'bundles' => $this->bundlesForSelect(),
                'currencies' => $this->currenciesForSelect(),
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки school bundle prices: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/School/BundlePrices/Index', [
                'prices' => [],
                'pricesCount' => 0,

                'adminSchoolBundlePricesPerPage' => $adminSchoolBundlePricesPerPage,
                'adminSchoolBundlePricesDefaultSort' => $adminSchoolBundlePricesDefaultSort,

                'filters' => [
                    'school_bundle_id' => $bundleId,
                    'currency_id' => $currencyId,
                    'activity' => $activity,
                ],

                'bundles' => [],
                'currencies' => [],

                'error' => 'Ошибка загрузки цен бандлов.',
            ]);
        }
    }

    /** Страница создания цены бандла. */
    public function create(Request $request): Response
    {
        return Inertia::render('Admin/School/BundlePrices/Create', [
            'bundles' => $this->bundlesForSelect(),
            'currencies' => $this->currenciesForSelect(),

            'defaultBundleId' => $request->query('school_bundle_id')
                ? (int) $request->query('school_bundle_id')
                : null,

            'defaultCurrencyId' => $request->query('currency_id')
                ? (int) $request->query('currency_id')
                : null,
        ]);
    }

    /** Сохранение новой цены бандла. */
    public function store(SchoolBundlePriceRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use (&$price, $data) {
                if (!isset($data['sort']) || $data['sort'] === null) {
                    $maxSort = SchoolBundlePrice::query()
                        ->where('school_bundle_id', $data['school_bundle_id'])
                        ->max('sort');

                    $data['sort'] = $maxSort === null ? 0 : ((int) $maxSort + 1);
                }

                $price = SchoolBundlePrice::create($data);
            });

            return redirect()
                ->route('admin.schoolBundlePrices.index')
                ->with('success', 'Цена бандла успешно создана.');
        } catch (Throwable $e) {
            Log::error('Ошибка создания school bundle price: ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => $data,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании цены бандла.');
        }
    }

    /** Редирект на страницу редактирования. */
    public function show(int $schoolBundlePrice): RedirectResponse
    {
        return redirect()->route('admin.schoolBundlePrices.edit', $schoolBundlePrice);
    }

    /** Страница редактирования цены бандла. */
    public function edit(int $schoolBundlePrice): Response
    {
        $price = SchoolBundlePrice::query()
            ->with([
                'bundle.translation',
                'bundle.translations',
                'currency:id,code,name,symbol',
            ])
            ->findOrFail($schoolBundlePrice);

        return Inertia::render('Admin/School/BundlePrices/Edit', [
            'price' => new SchoolBundlePriceResource($price),

            'bundles' => $this->bundlesForSelect(),
            'currencies' => $this->currenciesForSelect(),
        ]);
    }

    /** Обновление цены бандла. */
    public function update(SchoolBundlePriceRequest $request, int $schoolBundlePrice): RedirectResponse
    {
        $price = SchoolBundlePrice::query()->findOrFail($schoolBundlePrice);
        $data = $request->validated();

        try {
            $price->update($data);

            return redirect()
                ->route('admin.schoolBundlePrices.index')
                ->with('success', 'Цена бандла успешно обновлена.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления school bundle price ID ' . $price->id . ': ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => $data,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении цены бандла.');
        }
    }

    /** Удаление цены бандла. */
    public function destroy(int $schoolBundlePrice): RedirectResponse
    {
        $price = SchoolBundlePrice::query()->findOrFail($schoolBundlePrice);
        $bundleId = $price->school_bundle_id;

        try {
            $price->delete();

            return redirect()
                ->route('admin.schoolBundlePrices.index', [
                    'school_bundle_id' => $bundleId,
                ])
                ->with('success', 'Цена бандла успешно удалена.');
        } catch (Throwable $e) {
            Log::error('Ошибка удаления school bundle price ID ' . $price->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении цены бандла.');
        }
    }

    /** Обновление активности одной цены бандла. */
    public function updateActivity(UpdateActivityRequest $request, int $schoolBundlePrice): RedirectResponse
    {
        $price = SchoolBundlePrice::query()->findOrFail($schoolBundlePrice);

        try {
            $price->update([
                'activity' => $request->validated('activity'),
            ]);

            return back()->with('success', 'Активность цены бандла обновлена.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления активности school bundle price ID ' . $price->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при обновлении активности цены бандла.');
        }
    }

    /** Массовое обновление активности цен бандлов. */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:school_bundle_prices,id'],
            'activity' => ['required', 'boolean'],
        ]);

        try {
            SchoolBundlePrice::query()
                ->whereIn('id', $data['ids'])
                ->update(['activity' => $data['activity']]);

            $message = 'Активность выбранных цен бандлов успешно обновлена.';

            return $request->expectsJson()
                ? response()->json(['message' => $message])
                : back()->with('success', $message);
        } catch (Throwable $e) {
            Log::error('Ошибка массового обновления активности school bundle prices: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            $message = 'Ошибка при массовом обновлении активности цен бандлов.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }

    /** Обновление сортировки одной цены бандла. */
    public function updateSort(UpdateSortEntityRequest $request, int $schoolBundlePrice): RedirectResponse
    {
        $price = SchoolBundlePrice::query()->findOrFail($schoolBundlePrice);

        try {
            $price->update([
                'sort' => $request->validated('sort'),
            ]);

            return back()->with('success', 'Сортировка цены бандла обновлена.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления сортировки school bundle price ID ' . $price->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при обновлении сортировки цены бандла.');
        }
    }

    /** Массовое обновление сортировки цен бандлов. */
    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer', 'exists:school_bundle_prices,id'],
            'items.*.sort' => ['required', 'integer', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($data) {
                foreach ($data['items'] as $item) {
                    SchoolBundlePrice::query()
                        ->whereKey($item['id'])
                        ->update([
                            'sort' => (int) $item['sort'],
                        ]);
                }
            });

            $message = 'Сортировка цен бандлов успешно обновлена.';

            return $request->expectsJson()
                ? response()->json(['message' => $message])
                : back()->with('success', $message);
        } catch (Throwable $e) {
            Log::error('Ошибка массовой сортировки school bundle prices: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            $message = 'Ошибка при массовом обновлении сортировки цен бандлов.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }

    /** Массовое удаление цен бандлов. */
    public function bulkDestroy(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:school_bundle_prices,id'],
        ]);

        try {
            DB::transaction(function () use ($data) {
                SchoolBundlePrice::query()
                    ->whereIn('id', $data['ids'])
                    ->delete();
            });

            $message = 'Выбранные цены бандлов успешно удалены.';

            return $request->expectsJson()
                ? response()->json(['message' => $message])
                : back()->with('success', $message);
        } catch (Throwable $e) {
            Log::error('Ошибка массового удаления school bundle prices: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            $message = 'Ошибка при массовом удалении цен бандлов.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }

    /** Бандлы для select. */
    private function bundlesForSelect(): AnonymousResourceCollection
    {
        $bundles = SchoolBundle::query()
            ->with(['translation', 'translations', 'images'])
            ->orderByDesc('id')
            ->get();

        return SchoolBundleSharedResource::collection($bundles);
    }

    /** Валюты для select. */
    private function currenciesForSelect(): Collection|array
    {
        return Currency::query()
            ->select('id', 'code', 'name', 'symbol')
            ->orderBy('code')
            ->get();
    }
}
