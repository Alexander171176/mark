<?php

namespace App\Http\Controllers\Admin\Market\MarketCompany;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Market\MarketCompany\MarketCompanyRequest;
use App\Http\Resources\Admin\Market\MarketCompany\MarketCompanyResource;
use App\Models\Admin\Market\MarketCompany\MarketCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class MarketCompanyController extends Controller
{
    /**
     * Базовый query:
     * - admin видит все компании
     * - обычный пользователь только свои
     */
    private function baseQuery(): Builder
    {
        $query = MarketCompany::query();

        $user = auth()->user();

        if ($user && !$user->hasRole('admin')) {
            $query->where('owner_user_id', $user->id);
        }

        return $query;
    }

    /**
     * Список компаний
     */
    public function index(Request $request): Response
    {
        $adminCountMarketCompanies = (int) config('site_settings.AdminCountMarketCompanies', 15);
        $adminSortMarketCompanies  = (string) config('site_settings.AdminSortMarketCompanies', 'idDesc');

        try {
            $companies = $this->baseQuery()
                ->with(['owner:id,name,email'])
                ->orderBy('sort', 'asc')
                ->orderBy('id', 'desc')
                ->get();

            return Inertia::render('Admin/Market/Companies/Index', [
                'companies' => MarketCompanyResource::collection($companies),
                'companiesCount' => $companies->count(),
                'adminCountMarketCompanies' => $adminCountMarketCompanies,
                'adminSortMarketCompanies'  => $adminSortMarketCompanies,
            ]);

        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка компаний: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/Market/Companies/Index', [
                'companies' => [],
                'companiesCount' => 0,
                'error' => 'Ошибка загрузки списка компаний.',
            ]);
        }
    }

    /**
     * Страница создания компании
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Market/Companies/Create');
    }

    /**
     * Сохранение компании
     */
    public function store(MarketCompanyRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = auth()->user();

        try {
            DB::beginTransaction();

            $data['owner_user_id'] = $user?->id;

            // Если sort не передан, оставляем логику автоподстановки
            if (!array_key_exists('sort', $data) || is_null($data['sort'])) {
                $maxSort = MarketCompany::max('sort');
                $data['sort'] = is_null($maxSort) ? 0 : $maxSort + 1;
            }

            MarketCompany::create($data);

            DB::commit();

            return redirect()
                ->route('admin.marketCompanies.index')
                ->with('success', 'Компания успешно создана.');

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Ошибка при создании компании: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $data,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании компании.');
        }
    }

    /**
     * Просмотр компании
     * Пока оставляем как JSON Resource.
     * Если позже понадобится отдельная Show.vue страница, легко переделаем на Inertia::render().
     */
    public function show(int $marketCompany): MarketCompanyResource
    {
        $company = $this->baseQuery()
            ->with(['owner:id,name,email'])
            ->findOrFail($marketCompany);

        return new MarketCompanyResource($company);
    }

    /**
     * Страница редактирования компании
     */
    public function edit(int $marketCompany): Response
    {
        $company = $this->baseQuery()
            ->with(['owner:id,name,email'])
            ->findOrFail($marketCompany);

        return Inertia::render('Admin/Market/Companies/Edit', [
            'company' => new MarketCompanyResource($company),
        ]);
    }

    /**
     * Обновление компании
     */
    public function update(MarketCompanyRequest $request, int $marketCompany): RedirectResponse
    {
        $company = $this->baseQuery()->findOrFail($marketCompany);

        $data = $request->validated();

        // owner_user_id нельзя менять через update
        unset($data['owner_user_id']);

        try {
            DB::beginTransaction();

            $company->update($data);

            DB::commit();

            return redirect()
                ->route('admin.marketCompanies.index')
                ->with('success', 'Компания успешно обновлена.');

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка при обновлении компании ID {$company->id}: " . $e->getMessage(), [
                'exception' => $e,
                'data' => $data,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении компании.');
        }
    }

    /**
     * Удаление компании
     */
    public function destroy(int $marketCompany): RedirectResponse
    {
        $company = $this->baseQuery()->findOrFail($marketCompany);

        try {
            DB::beginTransaction();

            $company->delete();

            DB::commit();

            return redirect()
                ->route('admin.marketCompanies.index')
                ->with('success', 'Компания успешно удалена.');

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка при удалении компании ID {$company->id}: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении компании.');
        }
    }

    /**
     * Массовое удаление
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:market_companies,id'],
        ]);

        $ids = $validated['ids'];
        $count = count($ids);

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== $count) {
            return back()->with('error', 'Не все выбранные компании доступны для удаления.');
        }

        try {
            DB::beginTransaction();

            MarketCompany::whereIn('id', $allowedIds)->delete();

            DB::commit();

            return redirect()
                ->route('admin.marketCompanies.index')
                ->with('success', "Успешно удалено компаний: {$count}.");

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Ошибка массового удаления компаний: ' . $e->getMessage(), [
                'exception' => $e,
                'ids' => $allowedIds,
            ]);

            return back()->with('error', 'Ошибка при массовом удалении компаний.');
        }
    }

    /**
     * Обновление активности одной компании
     */
    public function updateActivity(Request $request, int $marketCompany): RedirectResponse
    {
        $validated = $request->validate([
            'activity' => ['required', 'boolean'],
        ]);

        $company = $this->baseQuery()->findOrFail($marketCompany);

        try {
            DB::beginTransaction();

            $company->activity = $validated['activity'];
            $company->save();

            DB::commit();

            return back()->with('success', 'Активность компании успешно обновлена.');

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка updateActivity компании ID {$company->id}: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при обновлении активности компании.');
        }
    }

    /**
     * Массовое обновление активности
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:market_companies,id'],
            'activity' => ['required', 'boolean'],
        ]);

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $validated['ids'])
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($validated['ids'])) {
            $message = 'Не все выбранные компании доступны для изменения активности.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 403)
                : back()->with('error', $message);
        }

        try {
            $updatedCount = MarketCompany::whereIn('id', $allowedIds)
                ->update(['activity' => $validated['activity']]);

            $message = 'Активность компаний успешно обновлена.';

            return $request->expectsJson()
                ? response()->json([
                    'message' => $message,
                    'updatedCount' => $updatedCount,
                ])
                : back()->with('success', $message);

        } catch (Throwable $e) {
            Log::error('Ошибка bulkUpdateActivity компаний: ' . $e->getMessage(), [
                'exception' => $e,
                'ids' => $allowedIds,
            ]);

            $message = 'Ошибка при массовом обновлении активности компаний.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }

    /**
     * Обновление sort одной компании
     */
    public function updateSort(Request $request, int $marketCompany): RedirectResponse
    {
        $validated = $request->validate([
            'sort' => ['required', 'integer', 'min:0'],
        ]);

        $company = $this->baseQuery()->findOrFail($marketCompany);

        try {
            $company->sort = $validated['sort'];
            $company->save();

            return back()->with('success', 'Сортировка компании успешно обновлена.');

        } catch (Throwable $e) {
            Log::error("Ошибка updateSort компании ID {$company->id}: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при обновлении сортировки компании.');
        }
    }

    /**
     * Массовое обновление сортировки
     *
     * Поддержка payload:
     * - items: [{id, sort}]
     * - companies: [{id, sort}]
     */
    public function updateSortBulk(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['required_without:companies', 'array'],
            'items.*.id' => ['required_with:items', 'integer', 'exists:market_companies,id'],
            'items.*.sort' => ['required_with:items', 'integer', 'min:0'],

            'companies' => ['required_without:items', 'array'],
            'companies.*.id' => ['required_with:companies', 'integer', 'exists:market_companies,id'],
            'companies.*.sort' => ['required_with:companies', 'integer', 'min:0'],
        ]);

        $data = $validated['items'] ?? $validated['companies'];

        try {
            DB::transaction(function () use ($data) {
                $ids = array_column($data, 'id');

                $allowedIds = $this->baseQuery()
                    ->whereIn('id', $ids)
                    ->pluck('id')
                    ->toArray();

                if (count($allowedIds) !== count($ids)) {
                    abort(403, 'Не все выбранные компании доступны для сортировки.');
                }

                foreach ($data as $row) {
                    MarketCompany::whereKey($row['id'])->update([
                        'sort' => (int) $row['sort'],
                    ]);
                }
            });

            $message = 'Сортировка компаний успешно обновлена.';

            return $request->expectsJson()
                ? response()->json(['message' => $message])
                : back()->with('success', $message);

        } catch (Throwable $e) {
            Log::error('Ошибка массового обновления сортировки компаний: ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => $data,
            ]);

            $message = 'Ошибка при массовом обновлении сортировки компаний.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }
}
