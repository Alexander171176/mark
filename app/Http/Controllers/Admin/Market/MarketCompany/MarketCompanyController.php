<?php

namespace App\Http\Controllers\Admin\Market\MarketCompany;

use App\Http\Controllers\Admin\Market\BaseMarketAdminController;
use App\Http\Requests\Admin\Market\MarketCompany\MarketCompanyRequest;
use App\Http\Resources\Admin\Market\MarketCompany\MarketCompanyResource;
use App\Models\Admin\Market\MarketCompany\MarketCompany;
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
 * Контроллер для управления Фирмами (Market) в админке.
 *
 * Паттерн:
 * - Поиск, Пагинация, сортировка (режимы: frontend | auto | server )
 * - CRUD
 * - owner/ограничение “владелец/админ”
 * - activity/left/main/right (single + bulk)
 * - activity (single + bulk)
 * - sort + drag&drop (bulk)
 * - moderation (approve/reject) только для admin
 * - images single
 *
 * @version 1.1 (мультиязычеая архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 */
class MarketCompanyController extends BaseMarketAdminController
{
    /** Основная модель контроллера */
    protected string $modelClass = MarketCompany::class;

    /** Название сущности для сообщений */
    protected string $entityLabel = 'компаний';

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

    /** Список компаний (серверный / клиентский режим) */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int('adminMarketCompaniesPerPage', 6);
        $defaultSort = $settings->string('adminMarketCompaniesDefaultSort', 'idDesc');

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $processingMode = $settings->string('adminMarketCompaniesProcessingMode', 'frontend');

        $companiesCount = $this->baseQuery()->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer($processingMode, $companiesCount, 300);

        try {
            $companies = $this->getIndexCompanies(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            return Inertia::render('Admin/Market/MarketCompanies/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminMarketCompaniesPerPage' => $perPage,
                'adminMarketCompaniesDefaultSort' => $defaultSort,
                'adminMarketCompaniesProcessingMode' => $processingMode,

                'companies' => MarketCompanyResource::collection($companies),
                'companiesCount' => $companiesCount,

                'sortParam' => $sortParam,
                'search' => $search,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка market companies: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/Market/MarketCompanies/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminMarketCompaniesPerPage' => $perPage,
                'adminMarketCompaniesDefaultSort' => $defaultSort,
                'adminMarketCompaniesProcessingMode' => $processingMode,

                'companies' => [],
                'companiesCount' => 0,

                'sortParam' => $sortParam,
                'search' => $search,

                'error' => 'Ошибка загрузки компаний.',
            ]);
        }
    }

    /** Страница создания компании */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/Market/MarketCompanies/Create', [
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /** Создание компании */
    public function store(MarketCompanyRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];

        unset($data['translations']);
        unset($data['logo'], $data['signature'], $data['stamp']);

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
            DB::transaction(function () use (&$company, $data, $translations) {
                if (!isset($data['sort']) || is_null($data['sort'])) {
                    $maxSort = MarketCompany::query()->max('sort');
                    $data['sort'] = is_null($maxSort) ? 0 : $maxSort + 1;
                }

                $company = MarketCompany::create($data);

                $this->syncTranslations($company, $translations);
            });

            if ($request->hasFile('logo')) {
                $company->update([
                    'logo' => $request->file('logo')->store('market/companies/logos', 'public'),
                ]);
            }

            if ($request->hasFile('signature')) {
                $company->update([
                    'signature' => $request->file('signature')->store('market/companies/signatures', 'public'),
                ]);
            }

            if ($request->hasFile('stamp')) {
                $company->update([
                    'stamp' => $request->file('stamp')->store('market/companies/stamps', 'public'),
                ]);
            }

            return redirect()
                ->route('admin.marketCompanies.index')
                ->with('success', 'Компания успешно создана.');
        } catch (Throwable $e) {
            Log::error('Ошибка при создании market company: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании компании.');
        }
    }

    /** Перенаправление просмотра на редактирование */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route('admin.marketCompanies.edit', $id);
    }

    /** Страница редактирования компании */
    public function edit(int $marketCompany, Request $request): Response
    {
        $company = $this->baseQuery()
            ->with([
                'owner',
                'moderator',
                'translations',
            ])
            ->findOrFail($marketCompany);

        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/Market/MarketCompanies/Edit', [
            'company' => new MarketCompanyResource($company),
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /** Обновление компании */
    public function update(MarketCompanyRequest $request, int $marketCompany): RedirectResponse
    {
        $company = $this->baseQuery()->findOrFail($marketCompany);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];

        unset(
            $data['translations'],
            $data['_method'],
            $data['logo'],
            $data['signature'],
            $data['stamp']
        );

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
            DB::transaction(function () use ($company, $data, $translations) {
                $company->update($data);

                $this->syncTranslations($company, $translations);
            });

            if ($request->hasFile('logo')) {
                $company->update([
                    'logo' => $request->file('logo')->store('market/companies/logos', 'public'),
                ]);
            }

            if ($request->hasFile('signature')) {
                $company->update([
                    'signature' => $request->file('signature')->store('market/companies/signatures', 'public'),
                ]);
            }

            if ($request->hasFile('stamp')) {
                $company->update([
                    'stamp' => $request->file('stamp')->store('market/companies/stamps', 'public'),
                ]);
            }

            return redirect()
                ->route('admin.marketCompanies.index')
                ->with('success', 'Компания успешно обновлена.');
        } catch (Throwable $e) {
            Log::error('Ошибка при обновлении market company ID ' . $company->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении компании.');
        }
    }

    /** Удаление компании */
    public function destroy(int $marketCompany): RedirectResponse
    {
        $company = $this->baseQuery()
            ->with('translations')
            ->findOrFail($marketCompany);

        try {
            DB::transaction(function () use ($company) {
                $company->translations()->delete();
                $company->delete();
            });

            return redirect()
                ->route('admin.marketCompanies.index')
                ->with('success', 'Компания успешно удалена.');
        } catch (Throwable $e) {
            Log::error('Ошибка при удалении market company ID ' . $company->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении компании.');
        }
    }

    /** Массовое удаление компаний */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:market_companies,id'],
        ]);

        $ids = $validated['ids'];

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            return back()->with('error', 'Часть компаний недоступна для удаления.');
        }

        try {
            DB::transaction(function () use ($allowedIds) {
                $companies = MarketCompany::query()
                    ->whereIn('id', $allowedIds)
                    ->with('translations')
                    ->get();

                foreach ($companies as $company) {
                    $company->translations()->delete();
                    $company->delete();
                }
            });

            return back()->with('success', 'Выбранные компании успешно удалены.');
        } catch (Throwable $e) {
            Log::error('Ошибка bulkDestroy market companies: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при массовом удалении компаний.');
        }
    }

    /** Базовый запрос списка компаний */
    private function indexQuery(): Builder
    {
        return $this->baseQuery()
            ->with([
                'owner',
                'moderator',
                'translations',
            ]);
    }

    /** Получение списка компаний для индекса */
    private function getIndexCompanies(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query = $this->indexQuery();

        if ($useServerProcessing) {
            return $query
                ->search($search, $locale)
                ->sortByParam($sort, $locale)
                ->paginate($perPage)
                ->withQueryString();
        }

        return $query
            ->ordered()
            ->get();
    }
}
