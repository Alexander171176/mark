<?php

namespace App\Http\Controllers\Admin\Market\MarketStorefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Market\MarketStorefront\MarketStorefrontRequest;
use App\Http\Resources\Admin\Finance\Currency\CurrencyResource;
use App\Http\Resources\Admin\Market\MarketCompany\MarketCompanyResource;
use App\Http\Resources\Admin\Market\MarketStorefront\MarketStorefrontResource;
use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\Market\MarketCompany\MarketCompany;
use App\Models\Admin\Market\MarketStorefront\MarketStorefront;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class MarketStorefrontController extends Controller
{
    /**
     * Базовый query:
     * - admin видит все витрины
     * - обычный пользователь только витрины своих компаний
     */
    private function baseQuery(): Builder
    {
        $query = MarketStorefront::query();

        $user = auth()->user();

        if ($user && !$user->hasRole('admin')) {
            $query->whereHas('company', function ($q) use ($user) {
                $q->where('owner_user_id', $user->id);
            });
        }

        return $query;
    }

    /**
     * Базовый query компаний для селектов:
     * - admin видит все компании
     * - обычный пользователь только свои
     */
    private function companyBaseQuery(): Builder
    {
        $query = MarketCompany::query();

        $user = auth()->user();

        if ($user && !$user->hasRole('admin')) {
            $query->where('owner_user_id', $user->id);
        }

        return $query;
    }

    /**
     * Список витрин
     */
    public function index(Request $request): Response
    {
        $adminCountMarketStorefronts = (int) config('site_settings.AdminCountMarketStorefronts', 15);
        $adminSortMarketStorefronts  = (string) config('site_settings.AdminSortMarketStorefronts', 'idDesc');

        try {
            $storefronts = $this->baseQuery()
                ->with([
                    'company:id,name,brand_name,slug',
                    'defaultCurrency:id,title,code,symbol',
                ])
                ->orderBy('sort', 'asc')
                ->orderBy('id', 'desc')
                ->get();

            return Inertia::render('Admin/Market/Storefronts/Index', [
                'storefronts' => MarketStorefrontResource::collection($storefronts),
                'storefrontsCount' => $storefronts->count(),
                'adminCountMarketStorefronts' => $adminCountMarketStorefronts,
                'adminSortMarketStorefronts'  => $adminSortMarketStorefronts,
            ]);

        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка витрин: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/Market/Storefronts/Index', [
                'storefronts' => [],
                'storefrontsCount' => 0,
                'adminCountMarketStorefronts' => $adminCountMarketStorefronts,
                'adminSortMarketStorefronts'  => $adminSortMarketStorefronts,
                'error' => 'Ошибка загрузки списка витрин.',
            ]);
        }
    }

    /**
     * Страница создания витрины
     */
    public function create(): Response
    {
        $companies = $this->companyBaseQuery()
            ->orderBy('name')
            ->get();

        $currencies = Currency::query()
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Market/Storefronts/Create', [
            'companies' => MarketCompanyResource::collection($companies),
            'currencies' => CurrencyResource::collection($currencies),
        ]);
    }

    /**
     * Сохранение витрины
     */
    public function store(MarketStorefrontRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $allowedCompany = $this->companyBaseQuery()
            ->whereKey($data['company_id'])
            ->exists();

        if (!$allowedCompany) {
            return back()
                ->withInput()
                ->withErrors([
                    'company_id' => 'Вы не можете использовать выбранную компанию.',
                ]);
        }

        try {
            DB::beginTransaction();

            if (!array_key_exists('sort', $data) || is_null($data['sort'])) {
                $maxSort = MarketStorefront::where('company_id', $data['company_id'])->max('sort');
                $data['sort'] = is_null($maxSort) ? 0 : $maxSort + 1;
            }

            $storefront = MarketStorefront::create($data);

            if (!empty($data['is_main'])) {
                MarketStorefront::query()
                    ->where('company_id', $data['company_id'])
                    ->where('id', '!=', $storefront->id)
                    ->update(['is_main' => false]);
            }

            DB::commit();

            return redirect()
                ->route('admin.marketStorefronts.index')
                ->with('success', 'Витрина успешно создана.');

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Ошибка при создании витрины: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $data,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании витрины.');
        }
    }

    /**
     * Просмотр витрины
     * Пока как JSON Resource
     */
    public function show(int $marketStorefront): MarketStorefrontResource
    {
        $storefront = $this->baseQuery()
            ->with([
                'company:id,name,brand_name,slug',
                'defaultCurrency:id,title,code,symbol',
            ])
            ->findOrFail($marketStorefront);

        return new MarketStorefrontResource($storefront);
    }

    /**
     * Страница редактирования витрины
     */
    public function edit(int $marketStorefront): Response
    {
        $storefront = $this->baseQuery()
            ->with([
                'company:id,name,brand_name,slug',
                'defaultCurrency:id,title,code,symbol',
            ])
            ->findOrFail($marketStorefront);

        $companies = $this->companyBaseQuery()
            ->orderBy('name')
            ->get();

        $currencies = Currency::query()
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Market/Storefronts/Edit', [
            'storefront' => new MarketStorefrontResource($storefront),
            'companies' => MarketCompanyResource::collection($companies),
            'currencies' => CurrencyResource::collection($currencies),
        ]);
    }

    /**
     * Обновление витрины
     */
    public function update(MarketStorefrontRequest $request, int $marketStorefront): RedirectResponse
    {
        $storefront = $this->baseQuery()->findOrFail($marketStorefront);

        $data = $request->validated();

        $allowedCompany = $this->companyBaseQuery()
            ->whereKey($data['company_id'])
            ->exists();

        if (!$allowedCompany) {
            return back()
                ->withInput()
                ->withErrors([
                    'company_id' => 'Вы не можете использовать выбранную компанию.',
                ]);
        }

        try {
            DB::beginTransaction();

            $storefront->update($data);

            if (!empty($data['is_main'])) {
                MarketStorefront::query()
                    ->where('company_id', $data['company_id'])
                    ->where('id', '!=', $storefront->id)
                    ->update(['is_main' => false]);
            }

            DB::commit();

            return redirect()
                ->route('admin.marketStorefronts.index')
                ->with('success', 'Витрина успешно обновлена.');

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка при обновлении витрины ID {$storefront->id}: " . $e->getMessage(), [
                'exception' => $e,
                'data' => $data,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении витрины.');
        }
    }

    /**
     * Удаление витрины
     */
    public function destroy(int $marketStorefront): RedirectResponse
    {
        $storefront = $this->baseQuery()->findOrFail($marketStorefront);

        try {
            DB::beginTransaction();

            $storefront->delete();

            DB::commit();

            return redirect()
                ->route('admin.marketStorefronts.index')
                ->with('success', 'Витрина успешно удалена.');

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка при удалении витрины ID {$storefront->id}: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении витрины.');
        }
    }

    /**
     * Массовое удаление
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:market_storefronts,id'],
        ]);

        $ids = $validated['ids'];
        $count = count($ids);

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== $count) {
            return back()->with('error', 'Не все выбранные витрины доступны для удаления.');
        }

        try {
            DB::beginTransaction();

            MarketStorefront::whereIn('id', $allowedIds)->delete();

            DB::commit();

            return redirect()
                ->route('admin.marketStorefronts.index')
                ->with('success', "Успешно удалено витрин: {$count}.");

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Ошибка массового удаления витрин: ' . $e->getMessage(), [
                'exception' => $e,
                'ids' => $allowedIds,
            ]);

            return back()->with('error', 'Ошибка при массовом удалении витрин.');
        }
    }

    /**
     * Обновление активности одной витрины
     */
    public function updateActivity(Request $request, int $marketStorefront): RedirectResponse
    {
        $validated = $request->validate([
            'activity' => ['required', 'boolean'],
        ]);

        $storefront = $this->baseQuery()->findOrFail($marketStorefront);

        try {
            DB::beginTransaction();

            $storefront->activity = $validated['activity'];
            $storefront->save();

            DB::commit();

            return back()->with('success', 'Активность витрины успешно обновлена.');

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка updateActivity витрины ID {$storefront->id}: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при обновлении активности витрины.');
        }
    }

    /**
     * Массовое обновление активности
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:market_storefronts,id'],
            'activity' => ['required', 'boolean'],
        ]);

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $validated['ids'])
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($validated['ids'])) {
            $message = 'Не все выбранные витрины доступны для изменения активности.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 403)
                : back()->with('error', $message);
        }

        try {
            $updatedCount = MarketStorefront::whereIn('id', $allowedIds)
                ->update(['activity' => $validated['activity']]);

            $message = 'Активность витрин успешно обновлена.';

            return $request->expectsJson()
                ? response()->json([
                    'message' => $message,
                    'updatedCount' => $updatedCount,
                ])
                : back()->with('success', $message);

        } catch (Throwable $e) {
            Log::error('Ошибка bulkUpdateActivity витрин: ' . $e->getMessage(), [
                'exception' => $e,
                'ids' => $allowedIds,
            ]);

            $message = 'Ошибка при массовом обновлении активности витрин.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }

    /**
     * Обновление sort одной витрины
     */
    public function updateSort(Request $request, int $marketStorefront): RedirectResponse
    {
        $validated = $request->validate([
            'sort' => ['required', 'integer', 'min:0'],
        ]);

        $storefront = $this->baseQuery()->findOrFail($marketStorefront);

        try {
            $storefront->sort = $validated['sort'];
            $storefront->save();

            return back()->with('success', 'Сортировка витрины успешно обновлена.');

        } catch (Throwable $e) {
            Log::error("Ошибка updateSort витрины ID {$storefront->id}: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при обновлении сортировки витрины.');
        }
    }

    /**
     * Массовое обновление сортировки
     *
     * Поддержка payload:
     * - items: [{id, sort}]
     * - storefronts: [{id, sort}]
     */
    public function updateSortBulk(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['required_without:storefronts', 'array'],
            'items.*.id' => ['required_with:items', 'integer', 'exists:market_storefronts,id'],
            'items.*.sort' => ['required_with:items', 'integer', 'min:0'],

            'storefronts' => ['required_without:items', 'array'],
            'storefronts.*.id' => ['required_with:storefronts', 'integer', 'exists:market_storefronts,id'],
            'storefronts.*.sort' => ['required_with:storefronts', 'integer', 'min:0'],
        ]);

        $data = $validated['items'] ?? $validated['storefronts'];

        try {
            DB::transaction(function () use ($data) {
                $ids = array_column($data, 'id');

                $allowedIds = $this->baseQuery()
                    ->whereIn('id', $ids)
                    ->pluck('id')
                    ->toArray();

                if (count($allowedIds) !== count($ids)) {
                    abort(403, 'Не все выбранные витрины доступны для сортировки.');
                }

                foreach ($data as $row) {
                    MarketStorefront::whereKey($row['id'])->update([
                        'sort' => (int) $row['sort'],
                    ]);
                }
            });

            $message = 'Сортировка витрин успешно обновлена.';

            return $request->expectsJson()
                ? response()->json(['message' => $message])
                : back()->with('success', $message);

        } catch (Throwable $e) {
            Log::error('Ошибка массового обновления сортировки витрин: ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => $data,
            ]);

            $message = 'Ошибка при массовом обновлении сортировки витрин.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }
}
