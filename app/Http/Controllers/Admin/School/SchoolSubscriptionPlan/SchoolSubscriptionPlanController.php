<?php

namespace App\Http\Controllers\Admin\School\SchoolSubscriptionPlan;

use App\Http\Controllers\Admin\School\Base\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\SchoolSubscriptionPlan\SchoolSubscriptionPlanRequest;
use App\Http\Resources\Admin\School\SchoolSubscriptionPlan\SchoolSubscriptionPlanResource;
use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\School\SchoolSubscriptionPlan\SchoolSubscriptionPlan;
use App\Models\Admin\School\SchoolSubscriptionPlan\SchoolSubscriptionPlanImage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления тарифными планами в административной панели.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (одиночное)
 *
 * @version 1.1 (мультиязычная архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see SchoolSubscriptionPlan
 * @see SchoolSubscriptionPlanRequest
 */
class SchoolSubscriptionPlanController extends BaseSchoolAdminController
{
    /** Основная модель */
    protected string $modelClass = SchoolSubscriptionPlan::class;

    /** Модель изображений */
    protected string $imageModelClass = SchoolSubscriptionPlanImage::class;

    /** Коллекция изображений */
    protected string $imageMediaCollection = 'images';

    /** Название сущности */
    protected string $entityLabel = 'тарифных планов';

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

    /** Расширение сортировки для тарифных планов */
    protected function extendedSortMap(): array
    {
        return [
            'availabilityNowFirst' => 'availability_now_first',

            'publishedAtDesc' => 'published_at_desc',
            'publishedAtAsc' => 'published_at_asc',

            'availableFromDesc' => 'available_from_desc',
            'availableFromAsc' => 'available_from_asc',

            'availableUntilDesc' => 'available_until_desc',
            'availableUntilAsc' => 'available_until_asc',

            'billingPeriodAsc' => 'billing_period_asc',
            'billingPeriodDesc' => 'billing_period_desc',

            'intervalDesc' => 'interval_desc',

            'currencyAsc' => 'currency_asc',

            'priceDesc' => 'price_desc',
            'priceAsc' => 'price_asc',

            'trialDaysDesc' => 'trial_days_desc',
            'trialDaysAsc' => 'trial_days_asc',

            'autoRenew' => 'auto_renew',
            'noAutoRenew' => 'no_auto_renew',

            'providerAsc' => 'provider_asc',

            'activity' => 'activity',
            'inactive' => 'inactive',
        ];
    }

    /** Список тарифных планов */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $adminSchoolSubscriptionPlansPerPage = (int) config(
            'site_settings.adminSchoolSubscriptionPlansPerPage',
            10
        );

        $adminSchoolSubscriptionPlansDefaultSort = (string) config(
            'site_settings.adminSchoolSubscriptionPlansDefaultSort',
            'idDesc'
        );

        $sort = $this->normalizeSortParam($adminSchoolSubscriptionPlansDefaultSort);

        try {
            $subscriptionPlans = $this->baseQuery()
                ->with([
                    'translation',
                    'translations',
                    'images',
                    'currency:id,code,name,symbol',
                ])
                ->withCount([
                    'images',
                ])
                ->when($sort === 'activity', fn ($query) => $query->where('activity', true))
                ->when($sort === 'inactive', fn ($query) => $query->where('activity', false))
                ->when($sort === 'auto_renew', fn ($query) => $query->where('auto_renew', true))
                ->when($sort === 'no_auto_renew', fn ($query) => $query->where('auto_renew', false))

                ->when($sort === 'published_at_desc', fn ($query) => $query->orderByDesc('published_at')->orderByDesc('id'))
                ->when($sort === 'available_from_desc', fn ($query) => $query->orderByDesc('available_from')->orderByDesc('id'))
                ->when($sort === 'available_until_desc', fn ($query) => $query->orderByDesc('available_until')->orderByDesc('id'))

                ->when($sort === 'billing_period_asc', fn ($query) => $query->orderBy('billing_period')->orderByDesc('id'))
                ->when($sort === 'interval_desc', fn ($query) => $query->orderByDesc('interval')->orderByDesc('id'))
                ->when($sort === 'price_desc', fn ($query) => $query->orderByDesc('price')->orderByDesc('id'))
                ->when($sort === 'trial_days_desc', fn ($query) => $query->orderByDesc('trial_days')->orderByDesc('id'))
                ->when($sort === 'provider_asc', fn ($query) => $query->orderBy('provider')->orderByDesc('id'))

                ->when($sort === 'currency_asc', fn ($query) => $query
                    ->leftJoin('currencies', 'school_subscription_plans.currency_id', '=', 'currencies.id')
                    ->orderBy('currencies.code')
                    ->orderByDesc('school_subscription_plans.id')
                    ->select('school_subscription_plans.*')
                )

                ->when($sort === 'title_asc', fn ($query) => $query
                    ->leftJoin('school_subscription_plan_translations as spt', function ($join) use ($currentLocale) {
                        $join->on('school_subscription_plans.id', '=', 'spt.school_subscription_plan_id')
                            ->where('spt.locale', $currentLocale);
                    })
                    ->orderBy('spt.title')
                    ->orderByDesc('school_subscription_plans.id')
                    ->select('school_subscription_plans.*')
                )

                ->when($sort === 'title_desc', fn ($query) => $query
                    ->leftJoin('school_subscription_plan_translations as spt', function ($join) use ($currentLocale) {
                        $join->on('school_subscription_plans.id', '=', 'spt.school_subscription_plan_id')
                            ->where('spt.locale', $currentLocale);
                    })
                    ->orderByDesc('spt.title')
                    ->orderByDesc('school_subscription_plans.id')
                    ->select('school_subscription_plans.*')
                )

                ->when($sort === 'sort_asc', fn ($query) => $query->orderBy('sort')->orderByDesc('id'))
                ->when($sort === 'sort_desc', fn ($query) => $query->orderByDesc('sort')->orderByDesc('id'))
                ->when($sort === 'date_asc', fn ($query) => $query->orderBy('id'))
                ->when($sort === 'date_desc', fn ($query) => $query->orderByDesc('id'))
                ->get();

            return Inertia::render('Admin/School/SubscriptionPlans/Index', [
                'subscriptionPlans' => SchoolSubscriptionPlanResource::collection($subscriptionPlans),
                'plansCount' => $this->baseQuery()->count(),

                'adminSchoolSubscriptionPlansPerPage' => $adminSchoolSubscriptionPlansPerPage,
                'adminSchoolSubscriptionPlansDefaultSort' => $adminSchoolSubscriptionPlansDefaultSort,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'currencies' => $this->currenciesForSelect(),
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка school subscription plans: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/School/SubscriptionPlans/Index', [
                'subscriptionPlans' => [],
                'plansCount' => 0,

                'adminSchoolSubscriptionPlansPerPage' => $adminSchoolSubscriptionPlansPerPage,
                'adminSchoolSubscriptionPlansDefaultSort' => $adminSchoolSubscriptionPlansDefaultSort,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'currencies' => [],
                'error' => 'Ошибка загрузки тарифных планов.',
            ]);
        }
    }

    /** Страница создания тарифного плана */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/School/SubscriptionPlans/Create', [
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'currencies' => $this->currenciesForSelect(),
        ]);
    }

    /** Сохранение тарифного плана */
    public function store(SchoolSubscriptionPlanRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages']
        );

        try {
            DB::transaction(function () use ($request, $data, $translations, $imagesData) {
                if (!isset($data['sort']) || is_null($data['sort'])) {
                    $maxSort = SchoolSubscriptionPlan::query()->max('sort');
                    $data['sort'] = is_null($maxSort) ? 1 : $maxSort + 1;
                }

                $plan = SchoolSubscriptionPlan::create($data);

                $this->syncTranslations($plan, $translations);

                $this->syncImages($plan, $request, $imagesData);
            });

            return redirect()
                ->route('admin.schoolSubscriptionPlans.index')
                ->with('success', 'Тарифный план успешно создан.');
        } catch (Throwable $e) {
            Log::error('Ошибка создания school subscription plan: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании тарифного плана.');
        }
    }

    /** Редирект на редактирование */
    public function show(int $schoolSubscriptionPlan): RedirectResponse
    {
        return redirect()->route('admin.schoolSubscriptionPlans.edit', $schoolSubscriptionPlan);
    }

    /** Страница редактирования тарифного плана */
    public function edit(int $schoolSubscriptionPlan, Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $subscriptionPlan = $this->baseQuery()
            ->with([
                'translation',
                'translations',
                'images',
                'currency:id,code,name,symbol',
            ])
            ->withCount([
                'images',
            ])
            ->findOrFail($schoolSubscriptionPlan);

        return Inertia::render('Admin/School/SubscriptionPlans/Edit', [
            'subscriptionPlan' => new SchoolSubscriptionPlanResource($subscriptionPlan),

            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'currencies' => $this->currenciesForSelect(),
        ]);
    }

    /** Обновление тарифного плана */
    public function update(SchoolSubscriptionPlanRequest $request, int $schoolSubscriptionPlan): RedirectResponse
    {
        $subscriptionPlan = $this->baseQuery()
            ->with('images')
            ->findOrFail($schoolSubscriptionPlan);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages'],
            $data['_method']
        );

        try {
            DB::transaction(function () use (
                $request,
                $subscriptionPlan,
                $data,
                $translations,
                $imagesData,
                $deletedImageIds
            ) {
                $subscriptionPlan->update($data);

                $this->syncTranslations($subscriptionPlan, $translations);

                $this->syncImages($subscriptionPlan, $request, $imagesData, $deletedImageIds);
            });

            return redirect()
                ->route('admin.schoolSubscriptionPlans.index')
                ->with('success', 'Тарифный план успешно обновлён.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления school subscription plan ID ' .
                $subscriptionPlan->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении тарифного плана.');
        }
    }

    /** Удаление тарифного плана */
    public function destroy(int $schoolSubscriptionPlan): RedirectResponse
    {
        $subscriptionPlan = $this->baseQuery()
            ->with('images')
            ->findOrFail($schoolSubscriptionPlan);

        try {
            DB::transaction(function () use ($subscriptionPlan) {
                $imageIds = $subscriptionPlan->images()
                    ->pluck('school_subscription_plan_images.id')
                    ->toArray();

                if (!empty($imageIds)) {
                    $subscriptionPlan->images()->detach();

                    $this->deleteImages($imageIds);
                }

                $subscriptionPlan->translations()->delete();

                $subscriptionPlan->delete();
            });

            return redirect()
                ->route('admin.schoolSubscriptionPlans.index')
                ->with('success', 'Тарифный план успешно удалён.');
        } catch (Throwable $e) {
            Log::error('Ошибка удаления school subscription plan ID ' .
                $subscriptionPlan->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении тарифного плана.');
        }
    }

    /** Валюты для select */
    private function currenciesForSelect(): Collection|array
    {
        return Currency::query()
            ->select('id', 'code', 'name', 'symbol')
            ->orderBy('code')
            ->get();
    }
}
