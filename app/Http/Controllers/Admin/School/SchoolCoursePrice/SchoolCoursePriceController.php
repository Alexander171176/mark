<?php

namespace App\Http\Controllers\Admin\School\SchoolCoursePrice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\SchoolCoursePrice\SchoolCoursePriceRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolCoursePrice\SchoolCoursePriceResource;
use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\School\SchoolCoursePrice\SchoolCoursePrice;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
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
 * Контроллер для управления Прайсами курсов
 * (SchoolCoursePrice) в административной панели.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (одиночное и массовое)
 *
 * @version 1.1 (Улучшен с RMB, транзакциями, Form Requests)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see SchoolCoursePrice
 * @see SchoolCoursePriceRequest
 */
class SchoolCoursePriceController extends Controller
{
    /** Список цен курсов. */
    public function index(Request $request): Response
    {
        $courseId = $request->query('school_course_id');
        $currencyId = $request->query('currency_id');
        $activity = $request->query('activity');

        $currentLocale = app()->getLocale();

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int('adminSchoolCoursePricesPerPage', 6);
        $defaultSort = $settings->string('adminSchoolCoursePricesDefaultSort', 'idDesc');

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $processingMode = $settings->string(
            'adminSchoolCoursePricesProcessingMode',
            'frontend'
        );

        $pricesCount = $this->indexQuery(
            courseId: $courseId,
            currencyId: $currencyId,
            activity: $activity,
        )->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $pricesCount,
                300
            );

        try {
            $prices = $this->getIndexCoursePrices(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
                courseId: $courseId,
                currencyId: $currencyId,
                activity: $activity,
            );

            return Inertia::render('Admin/School/SchoolCoursePrices/Index', [
                'currentLocale' => $currentLocale,

                'useServerProcessing' => $useServerProcessing,

                'adminSchoolCoursePricesPerPage' => $perPage,
                'adminSchoolCoursePricesDefaultSort' => $defaultSort,
                'adminSchoolCoursePricesProcessingMode' => $processingMode,

                'prices' => SchoolCoursePriceResource::collection($prices),
                'pricesCount' => $pricesCount,

                'sortParam' => $sortParam,
                'search' => $search,

                'filters' => [
                    'school_course_id' => $courseId,
                    'currency_id' => $currencyId,
                    'activity' => $activity,
                    'search' => $search,
                ],

                'courses' => $this->coursesForSelect(),
                'currencies' => $this->currenciesForSelect(),
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки school course prices: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/School/SchoolCoursePrices/Index', [
                'currentLocale' => $currentLocale,

                'useServerProcessing' => $useServerProcessing,

                'adminSchoolCoursePricesPerPage' => $perPage,
                'adminSchoolCoursePricesDefaultSort' => $defaultSort,
                'adminSchoolCoursePricesProcessingMode' => $processingMode,

                'prices' => [],
                'pricesCount' => 0,

                'sortParam' => $sortParam,
                'search' => $search,

                'filters' => [
                    'school_course_id' => $courseId,
                    'currency_id' => $currencyId,
                    'activity' => $activity,
                    'search' => $search,
                ],

                'courses' => [],
                'currencies' => [],

                'error' => 'Ошибка загрузки цен курсов.',
            ]);
        }
    }

    /** Страница создания цены курса. */
    public function create(Request $request): Response
    {
        return Inertia::render('Admin/School/SchoolCoursePrices/Create', [
            'courses' => $this->coursesForSelect(),
            'currencies' => $this->currenciesForSelect(),

            'defaultCourseId' => $request->query('school_course_id')
                ? (int) $request->query('school_course_id')
                : null,

            'defaultCurrencyId' => $request->query('currency_id')
                ? (int) $request->query('currency_id')
                : null,
        ]);
    }

    /** Сохранение новой цены курса. */
    public function store(SchoolCoursePriceRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use (&$price, $data) {
                if (!isset($data['sort']) || $data['sort'] === null) {
                    $maxSort = SchoolCoursePrice::query()
                        ->where('school_course_id', $data['school_course_id'])
                        ->max('sort');

                    $data['sort'] = $maxSort === null ? 0 : ((int) $maxSort + 1);
                }

                $price = SchoolCoursePrice::create($data);
            });

            return redirect()
                ->route('admin.schoolCoursePrices.index')
                ->with('success', 'Цена курса успешно создана.');
        } catch (Throwable $e) {
            Log::error('Ошибка создания school course price: ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => $data,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании цены курса.');
        }
    }

    /** Редирект на страницу редактирования. */
    public function show(int $schoolCoursePrice): RedirectResponse
    {
        return redirect()->route('admin.schoolCoursePrices.edit', $schoolCoursePrice);
    }

    /** Страница редактирования цены курса. */
    public function edit(int $schoolCoursePrice): Response
    {
        $price = SchoolCoursePrice::query()
            ->with([
                'course.translation',
                'course.translations',
                'currency:id,code,name,symbol',
            ])
            ->findOrFail($schoolCoursePrice);

        return Inertia::render('Admin/School/SchoolCoursePrices/Edit', [
            'price' => new SchoolCoursePriceResource($price),

            'courses' => $this->coursesForSelect(),
            'currencies' => $this->currenciesForSelect(),
        ]);
    }

    /** Обновление цены курса. */
    public function update(SchoolCoursePriceRequest $request, int $schoolCoursePrice): RedirectResponse
    {
        $price = SchoolCoursePrice::query()->findOrFail($schoolCoursePrice);
        $data = $request->validated();

        try {
            $price->update($data);

            return redirect()
                ->route('admin.schoolCoursePrices.index')
                ->with('success', 'Цена курса успешно обновлена.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления school course price ID ' . $price->id . ': ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => $data,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении цены курса.');
        }
    }

    /** Удаление цены курса. */
    public function destroy(int $schoolCoursePrice): RedirectResponse
    {
        $price = SchoolCoursePrice::query()->findOrFail($schoolCoursePrice);
        $courseId = $price->school_course_id;

        try {
            $price->delete();

            return redirect()
                ->route('admin.schoolCoursePrices.index', [
                    'school_course_id' => $courseId,
                ])
                ->with('success', 'Цена курса успешно удалена.');
        } catch (Throwable $e) {
            Log::error('Ошибка удаления school course price ID ' . $price->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении цены курса.');
        }
    }

    /** Обновление активности одной цены курса. */
    public function updateActivity(UpdateActivityRequest $request, int $schoolCoursePrice): RedirectResponse
    {
        $price = SchoolCoursePrice::query()->findOrFail($schoolCoursePrice);

        try {
            $price->update([
                'activity' => $request->validated('activity'),
            ]);

            return back()->with('success', 'Активность цены курса обновлена.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления активности school course price ID ' . $price->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при обновлении активности цены курса.');
        }
    }

    /** Массовое обновление активности цен курсов. */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:school_course_prices,id'],
            'activity' => ['required', 'boolean'],
        ]);

        try {
            SchoolCoursePrice::query()
                ->whereIn('id', $data['ids'])
                ->update(['activity' => $data['activity']]);

            $message = 'Активность выбранных цен курсов успешно обновлена.';

            return $request->expectsJson()
                ? response()->json(['message' => $message])
                : back()->with('success', $message);
        } catch (Throwable $e) {
            Log::error('Ошибка массового обновления активности school course prices: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            $message = 'Ошибка при массовом обновлении активности цен курсов.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }

    /** Обновление сортировки одной цены курса. */
    public function updateSort(UpdateSortEntityRequest $request, int $schoolCoursePrice): RedirectResponse
    {
        $price = SchoolCoursePrice::query()->findOrFail($schoolCoursePrice);

        try {
            $price->update([
                'sort' => $request->validated('sort'),
            ]);

            return back()->with('success', 'Сортировка цены курса обновлена.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления сортировки school course price ID ' . $price->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при обновлении сортировки цены курса.');
        }
    }

    /** Массовое обновление сортировки цен курсов. */
    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer', 'exists:school_course_prices,id'],
            'items.*.sort' => ['required', 'integer', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($data) {
                foreach ($data['items'] as $item) {
                    SchoolCoursePrice::query()
                        ->whereKey($item['id'])
                        ->update([
                            'sort' => (int) $item['sort'],
                        ]);
                }
            });

            $message = 'Сортировка цен курсов успешно обновлена.';

            return $request->expectsJson()
                ? response()->json(['message' => $message])
                : back()->with('success', $message);
        } catch (Throwable $e) {
            Log::error('Ошибка массовой сортировки school course prices: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            $message = 'Ошибка при массовом обновлении сортировки цен курсов.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }

    /** Массовое удаление цен курсов. */
    public function bulkDestroy(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:school_course_prices,id'],
        ]);

        try {
            DB::transaction(function () use ($data) {
                SchoolCoursePrice::query()
                    ->whereIn('id', $data['ids'])
                    ->delete();
            });

            $message = 'Выбранные цены курсов успешно удалены.';

            return $request->expectsJson()
                ? response()->json(['message' => $message])
                : back()->with('success', $message);
        } catch (Throwable $e) {
            Log::error('Ошибка массового удаления school course prices: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            $message = 'Ошибка при массовом удалении цен курсов.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }

    /** Курсы для select. */
    private function coursesForSelect(): AnonymousResourceCollection
    {
        $courses = SchoolCourse::query()
            ->with(['translation', 'translations'])
            ->orderByDesc('id')
            ->get();

        return SchoolCourseSharedResource::collection($courses);
    }

    /** Валюты для select. */
    private function currenciesForSelect(): Collection|array
    {
        return Currency::query()
            ->select('id', 'code', 'name', 'symbol')
            ->orderBy('code')
            ->get();
    }

    /** Базовый запрос для списка цен курсов. */
    private function indexQuery(
        null|string|int $courseId = null,
        null|string|int $currencyId = null,
        null|string|bool $activity = null,
    ): Builder {
        return SchoolCoursePrice::query()
            ->with([
                'course.translation',
                'course.translations',
                'currency:id,code,name,symbol',
            ])
            ->when($courseId, fn (Builder $query) => $query
                ->where('school_course_id', (int) $courseId)
            )
            ->when($currencyId, fn (Builder $query) => $query
                ->where('currency_id', (int) $currencyId)
            )
            ->when($activity !== null && $activity !== '', function (Builder $query) use ($activity) {
                $query->where(
                    'activity',
                    filter_var($activity, FILTER_VALIDATE_BOOL)
                );
            });
    }

    /** Получение списка цен курсов по активному режиму обработки. */
    private function getIndexCoursePrices(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = '',
        null|string|int $courseId = null,
        null|string|int $currencyId = null,
        null|string|bool $activity = null,
    ) {
        $query = $this->indexQuery(
            courseId: $courseId,
            currencyId: $currencyId,
            activity: $activity,
        );

        if ($useServerProcessing) {
            return $query
                ->search($search, $locale)
                ->sortByParam($sort, $locale)
                ->paginate($perPage)
                ->withQueryString();
        }

        return $query
            ->sortByParam($sort, $locale)
            ->get();
    }
}
