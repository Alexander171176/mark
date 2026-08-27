<?php

namespace App\Http\Controllers\Admin\School\SchoolSubscriptionPlan;

use App\Http\Controllers\Admin\School\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\SchoolSubscriptionPlan\SchoolSubscriptionPlanRequest;
use App\Http\Resources\Admin\School\SchoolSubscriptionPlan\SchoolSubscriptionPlanResource;
use App\Http\Resources\Admin\School\SchoolSubscriptionPlan\SchoolSubscriptionPlanSharedResource;
use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\School\SchoolSubscriptionPlan\SchoolSubscriptionPlan;
use App\Models\Admin\School\SchoolSubscriptionPlan\SchoolSubscriptionPlanImage;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления
 * тарифными планами школы.
 *
 * @version 1.2
 * @author Александр Косолапов
 */
class SchoolSubscriptionPlanController extends BaseSchoolAdminController
{
    /**
     * Основная модель.
     */
    protected string $modelClass =
        SchoolSubscriptionPlan::class;

    /**
     * Модель изображений.
     */
    protected string $imageModelClass =
        SchoolSubscriptionPlanImage::class;

    /**
     * Media collection.
     */
    protected string $imageMediaCollection =
        'images';

    /**
     * Название сущности.
     */
    protected string $entityLabel =
        'тарифных планов';

    /**
     * Поля переводов.
     */
    protected array $translationFields = [
        'title',
        'subtitle',
        'short',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    /**
     * =========================================================
     * INDEX
     * =========================================================
     */

    /**
     * Список тарифных планов.
     */
    public function index(
        Request $request
    ): Response {
        $currentLocale =
            $this->resolveLocale(
                $request
            );

        $settings =
            app(
                AdminSettingsService::class
            );

        $perPage =
            $settings->int(
                'adminSchoolSubscriptionPlansPerPage',
                6
            );

        $defaultSort =
            $settings->string(
                'adminSchoolSubscriptionPlansDefaultSort',
                'idDesc'
            );

        $sortParam =
            (string) $request->query(
                'sort',
                $defaultSort
            );

        $search =
            trim(
                (string) $request->query(
                    'search',
                    ''
                )
            );

        $processingMode =
            $settings->string(
                'adminSchoolSubscriptionPlansProcessingMode',
                'frontend'
            );

        /**
         * Для определения processing mode
         * используем лёгкий запрос
         * без eager loading.
         */
        $plansCount =
            $this->indexBaseQuery()
                ->count();

        $useServerProcessing =
            app(
                ProcessingModeService::class
            )->shouldUseServer(
                $processingMode,
                $plansCount,
                300
            );

        try {
            $subscriptionPlans =
                $this->getIndexSubscriptionPlans(
                    locale:
                    $currentLocale,

                    useServerProcessing:
                    $useServerProcessing,

                    perPage:
                    $perPage,

                    sort:
                    $sortParam,

                    search:
                    $search,
                );

            return Inertia::render(
                'Admin/School/SchoolSubscriptionPlans/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolSubscriptionPlansPerPage' =>
                        $perPage,

                    'adminSchoolSubscriptionPlansDefaultSort' =>
                        $defaultSort,

                    'adminSchoolSubscriptionPlansProcessingMode' =>
                        $processingMode,

                    /**
                     * Index использует
                     * компактный SharedResource.
                     */
                    'subscriptionPlans' =>
                        SchoolSubscriptionPlanSharedResource::collection(
                            $subscriptionPlans
                        ),

                    'plansCount' =>
                        $plansCount,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    /**
                     * Currency не переводимая.
                     */
                    'currencies' =>
                        $this->currenciesForSelect(),
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки списка school subscription plans: '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,
                ]
            );

            return Inertia::render(
                'Admin/School/SchoolSubscriptionPlans/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolSubscriptionPlansPerPage' =>
                        $perPage,

                    'adminSchoolSubscriptionPlansDefaultSort' =>
                        $defaultSort,

                    'adminSchoolSubscriptionPlansProcessingMode' =>
                        $processingMode,

                    'subscriptionPlans' =>
                        [],

                    'plansCount' =>
                        0,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'currencies' =>
                        [],

                    'error' =>
                        'Ошибка загрузки тарифных планов.',
                ]
            );
        }
    }

    /**
     * =========================================================
     * CREATE / STORE
     * =========================================================
     */

    /**
     * Страница создания тарифного плана.
     */
    public function create(
        Request $request
    ): Response {
        $currentLocale =
            $this->resolveLocale(
                $request
            );

        return Inertia::render(
            'Admin/School/SchoolSubscriptionPlans/Create',
            [
                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                /**
                 * Currency не переводимая.
                 */
                'currencies' =>
                    $this->currenciesForSelect(),
            ]
        );
    }

    /**
     * Создание тарифного плана.
     */
    public function store(
        SchoolSubscriptionPlanRequest $request
    ): RedirectResponse {
        $data =
            $request->validated();

        $translations =
            $data['translations']
            ?? [];

        $imagesData =
            $data['images']
            ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages']
        );

        try {
            DB::transaction(
                function () use (
                    $request,
                    $data,
                    $translations,
                    $imagesData
                ) {
                    /**
                     * Автоматический sort.
                     */
                    if (
                        !isset($data['sort'])
                        || is_null($data['sort'])
                    ) {
                        $maxSort =
                            SchoolSubscriptionPlan::query()
                                ->max(
                                    'sort'
                                );

                        $data['sort'] =
                            is_null($maxSort)
                                ? 1
                                : $maxSort + 1;
                    }

                    $plan =
                        SchoolSubscriptionPlan::create(
                            $data
                        );

                    /**
                     * Переводы.
                     */
                    $this->syncTranslations(
                        $plan,
                        $translations
                    );

                    /**
                     * Изображения через
                     * общую Spatie-логику.
                     */
                    $this->syncImages(
                        $plan,
                        $request,
                        $imagesData
                    );
                }
            );

            return redirect()
                ->route(
                    'admin.schoolSubscriptionPlans.index'
                )
                ->with(
                    'success',
                    'Тарифный план успешно создан.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка создания school subscription plan: '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ошибка при создании тарифного плана.'
                );
        }
    }

    /**
     * =========================================================
     * SHOW / EDIT / UPDATE
     * =========================================================
     */

    /**
     * Редирект на редактирование.
     */
    public function show(
        int $schoolSubscriptionPlan
    ): RedirectResponse {
        return redirect()->route(
            'admin.schoolSubscriptionPlans.edit',
            $schoolSubscriptionPlan
        );
    }

    /**
     * Страница редактирования
     * тарифного плана.
     */
    public function edit(
        int $schoolSubscriptionPlan,
        Request $request
    ): Response {
        $currentLocale =
            $this->resolveLocale(
                $request
            );

        $subscriptionPlan =
            $this->baseQuery()
                ->with([
                    /**
                     * Для Edit нужны
                     * все переводы самого Plan.
                     */
                    'translations',

                    /**
                     * Изображения через
                     * базовую Spatie-модель.
                     */
                    'images.media',

                    /**
                     * Currency не переводимая.
                     */
                    'currency:id,code,name,symbol',
                ])
                ->withCount([
                    'images',
                ])
                ->findOrFail(
                    $schoolSubscriptionPlan
                );

        return Inertia::render(
            'Admin/School/SchoolSubscriptionPlans/Edit',
            [
                'subscriptionPlan' =>
                    new SchoolSubscriptionPlanResource(
                        $subscriptionPlan
                    ),

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'currencies' =>
                    $this->currenciesForSelect(),
            ]
        );
    }

    /**
     * Обновление тарифного плана.
     */
    public function update(
        SchoolSubscriptionPlanRequest $request,
        int $schoolSubscriptionPlan
    ): RedirectResponse {
        /**
         * Для syncImages необходимо
         * загрузить images.media.
         */
        $subscriptionPlan =
            $this->baseQuery()
                ->with([
                    'images.media',
                ])
                ->findOrFail(
                    $schoolSubscriptionPlan
                );

        $data =
            $request->validated();

        $translations =
            $data['translations']
            ?? [];

        $imagesData =
            $data['images']
            ?? [];

        $deletedImageIds =
            $data['deletedImages']
            ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages'],
            $data['_method']
        );

        try {
            DB::transaction(
                function () use (
                    $request,
                    $subscriptionPlan,
                    $data,
                    $translations,
                    $imagesData,
                    $deletedImageIds
                ) {
                    $subscriptionPlan->update(
                        $data
                    );

                    /**
                     * Обновляем переводы.
                     */
                    $this->syncTranslations(
                        $subscriptionPlan,
                        $translations
                    );

                    /**
                     * Обновляем изображения.
                     */
                    $this->syncImages(
                        $subscriptionPlan,
                        $request,
                        $imagesData,
                        $deletedImageIds
                    );
                }
            );

            return redirect()
                ->route(
                    'admin.schoolSubscriptionPlans.index'
                )
                ->with(
                    'success',
                    'Тарифный план успешно обновлён.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка обновления school subscription plan ID '
                . $subscriptionPlan->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ошибка при обновлении тарифного плана.'
                );
        }
    }

    /**
     * =========================================================
     * DELETE
     * =========================================================
     */

    /**
     * Удаление тарифного плана.
     */
    public function destroy(
        int $schoolSubscriptionPlan
    ): RedirectResponse {
        $subscriptionPlan =
            $this->baseQuery()
                ->with([
                    'images.media',
                ])
                ->findOrFail(
                    $schoolSubscriptionPlan
                );

        try {
            DB::transaction(
                function () use (
                    $subscriptionPlan
                ) {
                    /**
                     * Получаем IDs связанных
                     * моделей изображений.
                     */
                    $imageIds =
                        $subscriptionPlan
                            ->images()
                            ->pluck(
                                'school_subscription_plan_images.id'
                            )
                            ->toArray();

                    if (
                        !empty($imageIds)
                    ) {
                        /**
                         * Сначала detach pivot.
                         */
                        $subscriptionPlan
                            ->images()
                            ->detach();

                        /**
                         * Затем удаляем сами
                         * image-модели + media
                         * через базовую логику.
                         */
                        $this->deleteImages(
                            $imageIds
                        );
                    }

                    /**
                     * Удаляем переводы.
                     */
                    $subscriptionPlan
                        ->translations()
                        ->delete();

                    /**
                     * Удаляем Plan.
                     */
                    $subscriptionPlan
                        ->delete();
                }
            );

            return redirect()
                ->route(
                    'admin.schoolSubscriptionPlans.index'
                )
                ->with(
                    'success',
                    'Тарифный план успешно удалён.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка удаления school subscription plan ID '
                . $subscriptionPlan->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,
                ]
            );

            return back()
                ->with(
                    'error',
                    'Ошибка при удалении тарифного плана.'
                );
        }
    }

    /**
     * =========================================================
     * SELECT HELPERS
     * =========================================================
     */

    /**
     * Валюты для select.
     *
     * Currency не переводимая,
     * locale не требуется.
     */
    private function currenciesForSelect(): Collection
    {
        return Currency::query()
            ->select(
                'id',
                'code',
                'name',
                'symbol'
            )
            ->orderBy(
                'code'
            )
            ->get();
    }

    /**
     * =========================================================
     * INDEX QUERIES
     * =========================================================
     */

    /**
     * Лёгкий базовый запрос.
     *
     * Используется для count().
     */
    private function indexBaseQuery(): Builder
    {
        return $this->baseQuery();
    }

    /**
     * Основной Admin Index Query.
     *
     * Загружаем только currentLocale.
     */
    private function indexQuery(
        string $locale
    ): Builder {
        return $this->indexBaseQuery()
            ->with([
                /**
                 * Только перевод
                 * выбранной locale.
                 */
                'translations' =>
                    fn ($query) =>
                    $query->where(
                        'locale',
                        $locale
                    ),

                /**
                 * Controller обязан загрузить
                 * images.media.
                 */
                'images.media',

                /**
                 * Currency не переводимая.
                 */
                'currency:id,code,name,symbol',
            ])
            ->withCount([
                'images',
            ]);
    }

    /**
     * Получение тарифных планов
     * по текущему processing mode.
     */
    private function getIndexSubscriptionPlans(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = '',
    ) {
        $query =
            $this->indexQuery(
                $locale
            );

        /**
         * SERVER:
         * поиск + сортировка
         * + SQL pagination.
         */
        if (
            $useServerProcessing
        ) {
            return $query
                ->search(
                    $search,
                    $locale
                )
                ->sortByParam(
                    $sort,
                    $locale
                )
                ->paginate(
                    $perPage
                )
                ->withQueryString();
        }

        /**
         * FRONTEND:
         * полный dataset.
         */
        return $query
            ->sortByParam(
                $sort,
                $locale
            )
            ->get();
    }
}
