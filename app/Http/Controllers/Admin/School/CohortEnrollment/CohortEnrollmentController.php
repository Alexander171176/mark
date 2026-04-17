<?php

namespace App\Http\Controllers\Admin\School\CohortEnrollment;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\CohortEnrollment\CohortEnrollmentResource;
use App\Models\Admin\School\CohortEnrollment\CohortEnrollment;
use App\Models\Admin\School\CourseSchedule\CourseSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Админ-контроллер для управления записями участников потоков:
 * - просмотр списка
 * - обновление статуса
 * - обновление заметок администратора
 */
class CohortEnrollmentController extends Controller
{
    /**
     * Список записей участников.
     *
     * Фильтры (по желанию):
     * - status: строковый статус (любой)
     * - course_schedule_id: ID потока
     *
     * Поиск по имени/почте/статусу — на фронте.
     */
    public function index(Request $request): Response
    {
        $status           = $request->query('status');
        $courseScheduleId = $request->query('course_schedule_id');

        $adminCountCohortEnrollments = (int) config('site_settings.AdminCountCohortEnrollments', 10);
        $adminSortCohortEnrollments  = config('site_settings.AdminSortCohortEnrollments', 'idDesc');

        $query = CohortEnrollment::query()
            ->with([
                'schedule:id,title,starts_at,ends_at,status',
                'user:id,name,email',
            ]);

        // Фильтр по потоку (если вызовешь страницу для конкретного расписания)
        if ($courseScheduleId) {
            $query->where('course_schedule_id', (int) $courseScheduleId);
        }

        // Фильтр по статусу, но без ограничения по списку значений
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        // Простая сортировка — при желании расширишь по аналогии с другими сущностями
        switch ($adminSortCohortEnrollments) {
            case 'idAsc':
                $query->orderBy('id', 'asc');
                break;
            case 'idDesc':
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        $enrollments      = collect();
        $enrollmentsCount = 0;

        try {
            $enrollments      = $query->get();
            $enrollmentsCount = $enrollments->count();
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки CohortEnrollment: '.$e->getMessage(), [
                'exception' => $e,
            ]);
            session()->flash('error', __('admin/controllers.index_error'));
        }

        // Для селекта потоков (если захочешь фильтр по потоку в интерфейсе).
        // Если не планируешь — этот запрос можно спокойно удалить.
        $schedules = CourseSchedule::query()
            ->select('id', 'title', 'starts_at', 'ends_at', 'status')
            ->orderBy('title')
            ->get();

        return Inertia::render('Admin/School/CohortEnrollments/Index', [
            'adminCountCohortEnrollments' => $adminCountCohortEnrollments,
            'adminSortCohortEnrollments'  => $adminSortCohortEnrollments,

            // На фронт идёт ОТФОРМИРОВАННЫЙ массив (не paginator)
            'enrollments'                 => CohortEnrollmentResource::collection($enrollments)->resolve(),
            'enrollmentsCount'            => $enrollmentsCount,

            'filters'                     => [
                'status'             => $status,
                'course_schedule_id' => $courseScheduleId,
            ],
            'schedules'                   => $schedules,
        ]);
    }

    /**
     * Обновление статуса одной записи (и, при желании, заметок).
     *
     * Ожидает:
     * - status: любая строка (напр. pending/approved/rejected/cancelled и т.п.)
     * - notes (опционально): string|null
     */
    public function updateStatus(Request $request, CohortEnrollment $cohortEnrollment): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string'],
            'notes'  => ['nullable', 'string'],
        ]);

        try {
            $oldStatus = $cohortEnrollment->status;

            $cohortEnrollment->status = $validated['status'];

            // Бизнес-правило: если статус стал "approved" и нет даты зачисления — ставим сейчас
            if ($validated['status'] === 'approved' && !$cohortEnrollment->enrolled_at) {
                $cohortEnrollment->enrolled_at = now();
            }

            // Обновляем заметки, если переданы
            if (array_key_exists('notes', $validated)) {
                $cohortEnrollment->notes = $validated['notes'];
            }

            $cohortEnrollment->save();

            Log::info("Обновлён статус CohortEnrollment ID {$cohortEnrollment->id} с {$oldStatus} на {$cohortEnrollment->status}", [
                'id'      => $cohortEnrollment->id,
                'user_id' => $cohortEnrollment->user_id,
            ]);

            $message = __('admin/controllers.status_updated_success');

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ]);
            }

            return back()->with('success', $message);

        } catch (Throwable $e) {
            Log::error("Ошибка обновления статуса CohortEnrollment ID {$cohortEnrollment->id}: ".$e->getMessage(), [
                'exception' => $e,
            ]);

            $message = __('admin/controllers.status_updated_error');

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 500);
            }

            return back()->with('error', $message);
        }
    }

    /**
     * Отдельное обновление заметок администратора.
     *
     * Если будешь редактировать notes в той же форме, что и статус,
     * можно этот метод не использовать и всё делать через updateStatus.
     */
    public function updateNotes(Request $request, CohortEnrollment $cohortEnrollment): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $cohortEnrollment->notes = $validated['notes'];
            $cohortEnrollment->save();

            Log::info("Обновлены заметки CohortEnrollment ID {$cohortEnrollment->id}");

            $message = __('admin/controllers.notes_updated_success');

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ]);
            }

            return back()->with('success', $message);

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении заметок CohortEnrollment ID {$cohortEnrollment->id}: ".$e->getMessage(), [
                'exception' => $e,
            ]);

            $message = __('admin/controllers.notes_updated_error');

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 500);
            }

            return back()->with('error', $message);
        }
    }
}
