<?php

namespace App\Http\Controllers\Admin\School\SchoolSubscriptionPlan;

use App\Http\Controllers\Admin\School\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\SchoolSubscriptionPlan\SchoolSubscriptionPlanRequest;
use App\Http\Resources\Admin\School\SchoolSubscriptionPlan\SchoolSubscriptionPlanResource;
use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\School\SchoolSubscriptionPlan\SchoolSubscriptionPlan;
use App\Models\Admin\School\SchoolSubscriptionPlan\SchoolSubscriptionPlanImage;
use App\Services\SiteSettings\AdminSettingsService;
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

    /** Список тарифных планов */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);
        $adminSchoolSubscriptionPlansPerPage = $settings->int('site_settings.adminSchoolSubscriptionPlansPerPage', 6);

        $adminSchoolSubscriptionPlansDefaultSort =
            $settings->string('site_settings.adminSchoolSubscriptionPlansDefaultSort', 'idDesc');

        $sort = (string) $request->query('sort', $adminSchoolSubscriptionPlansDefaultSort);

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
                ->ordered()
                ->get();

            return Inertia::render('Admin/School/SchoolSubscriptionPlans/Index', [
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

            return Inertia::render('Admin/School/SchoolSubscriptionPlans/Index', [
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

        return Inertia::render('Admin/School/SchoolSubscriptionPlans/Create', [
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

        return Inertia::render('Admin/School/SchoolSubscriptionPlans/Edit', [
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
