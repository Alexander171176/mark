<?php

namespace App\Http\Controllers\Admin\Finance\CoursePrice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Finance\CoursePrice\CoursePriceRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\Finance\CoursePrice\CoursePriceResource;
use App\Models\Admin\Finance\CoursePrice\CoursePrice;
use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\School\Course\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления Прайсами курсов в административной панели.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (одиночное и массовое)
 *
 * @version 1.1 (Улучшен с RMB, транзакциями, Form Requests)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see CoursePrice
 * @see CoursePriceRequest
 */
class CoursePriceController extends Controller
{

    /**
     * Общий список прайсов.
     * Пагинация/поиск/сортировка — на фронте (как у курсов).
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $adminCountCoursePrices = (int) config('site_settings.AdminCountCoursePrices', 10);
        $adminSortCoursePrices  = config('site_settings.AdminSortCoursePrices', 'idDesc');

        $prices = collect();
        $pricesCount = 0;

        try {
            $prices = CoursePrice::query()
                ->with([
                    'course:id,title,slug,locale',
                    'currency:id,code,name,symbol',
                ])
                ->orderBy('sort')
                ->orderBy('id')
                ->get();

            $pricesCount = CoursePrice::query()->count();

        } catch (Throwable $e) {
            Log::error('Ошибка загрузки course_prices: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            session()->flash('error', __('admin/controllers.index_error'));
        }

        return Inertia::render('Admin/Finance/CoursePrices/Index', [
            'prices' => CoursePriceResource::collection($prices)->resolve(),
            'pricesCount' => $pricesCount,
            'adminCountCoursePrices' => $adminCountCoursePrices,
            'adminSortCoursePrices' => $adminSortCoursePrices,
        ]);
    }

    /**
     * Форма создания прайса.
     *
     * @return Response
     */
    public function create(): Response
    {
        $courses = Course::query()
            ->select(['id', 'title', 'slug', 'locale'])
            ->orderBy('locale', 'desc')
            ->orderBy('title')
            ->get();

        $currencies = Currency::query()
            ->orderBy('code')
            ->get(['id', 'code', 'name']);

        return Inertia::render('Admin/Finance/CoursePrices/Create', [
            'courses' => $courses,
            'currencies' => $currencies,
        ]);
    }

    /**
     * Сохранение нового прайса.
     *
     * @param CoursePriceRequest $request
     * @return RedirectResponse
     */
    public function store(CoursePriceRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $price = CoursePrice::create($data);

            DB::commit();

            Log::info('CoursePrice created', [
                'id' => $price->id,
                'course_id' => $price->course_id,
                'currency_id' => $price->currency_id,
            ]);

            return redirect()
                ->route('admin.coursePrices.index')
                ->with('success', __('admin/controllers.created_success'));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('CoursePrice store failed: '.$e->getMessage(), [
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
     * @param CoursePrice $coursePrice
     * @return RedirectResponse
     */
    public function show(CoursePrice $coursePrice): RedirectResponse
    {
        return redirect()->route('admin.coursePrices.edit', $coursePrice);
    }

    /**
     * Форма редактирования прайса.
     *
     * @param CoursePrice $coursePrice
     * @return Response
     */
    public function edit(CoursePrice $coursePrice): Response
    {
        $coursePrice->load([
            'course:id,title,slug,locale',
            'currency:id,code,name',
        ]);

        $courses = Course::query()
            ->select(['id', 'title', 'slug', 'locale'])
            ->orderBy('locale', 'desc')
            ->orderBy('title')
            ->get();

        $currencies = Currency::query()
            ->orderBy('code')
            ->get(['id', 'code', 'name']);

        return Inertia::render('Admin/Finance/CoursePrices/Edit', [
            'coursePrice' => (new CoursePriceResource($coursePrice))->resolve(),
            'courses' => $courses,
            'currencies' => $currencies,
        ]);
    }

    /**
     * Обновление прайса.
     *
     * @param CoursePriceRequest $request
     * @param CoursePrice $coursePrice
     * @return RedirectResponse
     */
    public function update(CoursePriceRequest $request, CoursePrice $coursePrice): RedirectResponse
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $coursePrice->update($data);

            DB::commit();

            Log::info('CoursePrice updated', ['id' => $coursePrice->id]);

            return redirect()
                ->route('admin.coursePrices.index')
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('CoursePrice update failed: '.$e->getMessage(), [
                'exception' => $e,
                'id' => $coursePrice->id,
                'data' => $data,
            ]);

            return back()
                ->withInput()
                ->withErrors(['server' => __('admin/controllers.updated_error')]);
        }
    }

    /**
     * Удаление прайса.
     *
     * @param CoursePrice $coursePrice
     * @return RedirectResponse
     */
    public function destroy(CoursePrice $coursePrice): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $coursePrice->delete();

            DB::commit();

            Log::info('CoursePrice deleted', ['id' => $coursePrice->id]);

            return redirect()
                ->route('admin.coursePrices.index')
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('CoursePrice delete failed: '.$e->getMessage(), [
                'exception' => $e,
                'id' => $coursePrice->id,
            ]);

            return back()->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /* ===================== actions как у курсов ===================== */

    /**
     * Обновление статуса активности одного прайса курса.
     *
     * @param UpdateActivityRequest $request
     * @param CoursePrice $coursePrice
     * @return RedirectResponse
     */
    public function updateActivity(UpdateActivityRequest $request, CoursePrice $coursePrice): RedirectResponse
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $coursePrice->activity = (bool) $validated['activity'];
            $coursePrice->save();

            DB::commit();

            return back()->with('success', __('admin/controllers.activity_updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка обновления активности CoursePrice (ID: {$coursePrice->id}): ".$e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', __('admin/controllers.activity_updated_error'));
        }
    }

    /**
     * Массовое обновление активности прайсов курсов.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids'      => ['required', 'array'],
            'ids.*'    => ['integer', 'exists:course_prices,id'],
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
            $updatedCount = CoursePrice::whereIn('id', $ids)->update(['activity' => $activity]);
            $msg = __('admin/controllers.bulk_activity_updated_success');

            return $request->expectsJson()
                ? response()->json(['message' => $msg, 'updatedCount' => $updatedCount])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error('Ошибка bulk активности course_prices: '.$e->getMessage(), [
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
     * Обновление сортировки одного прайса курса.
     *
     * @param UpdateSortEntityRequest $request
     * @param CoursePrice $coursePrice
     * @return RedirectResponse
     */
    public function updateSort(UpdateSortEntityRequest $request, CoursePrice $coursePrice): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $original = $coursePrice->sort;

            $coursePrice->sort = (int) $validated['sort'];
            $coursePrice->save();

            Log::info("Sort CoursePrice {$coursePrice->id}: {$original} -> {$coursePrice->sort}");

            return back()->with('success', __('admin/controllers.sort_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка обновления sort CoursePrice (ID: {$coursePrice->id}): ".$e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', __('admin/controllers.sort_updated_error'));
        }
    }

    /**
     * Массовое обновление сортировки прайсов курсов.
     * Ожидает массив:
     * coursePrices: [{id: 1, sort: 10}, ...]
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'coursePrices' => ['required', 'array'],
            'coursePrices.*.id'   => ['required', 'integer', 'exists:course_prices,id'],
            'coursePrices.*.sort' => ['required', 'integer', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                foreach ($validated['coursePrices'] as $row) {
                    CoursePrice::whereKey($row['id'])->update([
                        'sort' => (int) $row['sort'],
                    ]);
                }
            });

            $msg = __('admin/controllers.bulk_sort_updated_success');

            return $request->expectsJson()
                ? response()->json(['message' => $msg])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error('Ошибка bulk sort course_prices: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            $msg = __('admin/controllers.bulk_sort_updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Массовое удаление прайсов курсов
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function bulkDestroy(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer', 'exists:course_prices,id'],
        ]);

        $ids = $validated['ids'];

        if (empty($ids)) {
            $msg = __('admin/controllers.bulk_deleted_no_selection') ?? 'Не выбраны записи для удаления.';

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 400)
                : back()->with('warning', $msg);
        }

        try {
            CoursePrice::whereIn('id', $ids)->delete();

            $msg = __('admin/controllers.bulk_deleted_success') ?? 'Прайсы удалены.';

            return $request->expectsJson()
                ? response()->json(['message' => $msg])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error('Ошибка bulk delete course_prices: '.$e->getMessage(), [
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
