<?php

namespace App\Http\Controllers\Admin\School\SchoolEnrollment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\SchoolEnrollment\SchoolEnrollmentRequest;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleSharedResource;
use App\Http\Resources\Admin\School\SchoolEnrollment\SchoolEnrollmentResource;
use App\Http\Resources\Admin\School\SchoolOrder\SchoolOrderSharedResource;
use App\Models\Admin\School\SchoolCourseSchedule\SchoolCourseSchedule;
use App\Models\Admin\School\SchoolEnrollment\SchoolEnrollment;
use App\Models\Admin\School\SchoolOrder\SchoolOrder;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\User;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления Зачислениями в админке.
 *
 * CRUD без сложной бизнес-логики:
 * - список с фильтрами (по пользователю, курсу, статусу)
 * - создание
 * - редактирование
 * - удаление
 * - связи с пользователем, курсами, расписанием.
 *
 * @version 1.1 (паттерн)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see SchoolEnrollment
 * @see SchoolEnrollmentRequest
 */
class SchoolEnrollmentController extends Controller
{
    /** Список зачислений учеников. */
    public function index(Request $request): Response
    {
        $status = $request->query('status');
        $userId = $request->query('user_id');
        $courseId = $request->query('school_course_id');
        $scheduleId = $request->query('school_course_schedule_id');

        $settings = app(AdminSettingsService::class);
        $adminSchoolEnrollmentsPerPage = $settings->int('site_settings.adminSchoolEnrollmentsPerPage', 6);
        $adminSchoolEnrollmentsDefaultSort = $settings->string('site_settings.adminSchoolEnrollmentsDefaultSort', 'idDesc');

        try {
            $query = SchoolEnrollment::query()
                ->with([
                    'user:id,name,email',
                    'course.translation',
                    'course.translations',
                    'schedule.translation',
                    'schedule.translations',
                    'schedule.course.translation',
                    'order',
                    'certificate',
                ])
                ->withCount(['progressRecords']);

            if ($status) {
                $query->where('status', $status);
            }

            if ($userId) {
                $query->where('user_id', (int) $userId);
            }

            if ($courseId) {
                $query->where('school_course_id', (int) $courseId);
            }

            if ($scheduleId) {
                $query->where('school_course_schedule_id', (int) $scheduleId);
            }

            match ($adminSchoolEnrollmentsDefaultSort) {
                'idAsc' => $query->orderBy('school_enrollments.id'),

                'startedAtAsc' => $query->orderBy('started_at')->orderByDesc('school_enrollments.id'),
                'startedAtDesc' => $query->orderByDesc('started_at')->orderByDesc('school_enrollments.id'),

                'expiresAtAsc' => $query->orderBy('expires_at')->orderByDesc('school_enrollments.id'),
                'expiresAtDesc' => $query->orderByDesc('expires_at')->orderByDesc('school_enrollments.id'),

                'completedAtAsc' => $query->orderBy('completed_at')->orderByDesc('school_enrollments.id'),
                'completedAtDesc' => $query->orderByDesc('completed_at')->orderByDesc('school_enrollments.id'),

                'progressAsc' => $query->orderBy('progress_percent')->orderByDesc('school_enrollments.id'),
                'progressDesc' => $query->orderByDesc('progress_percent')->orderByDesc('school_enrollments.id'),

                'statusAsc' => $query->orderBy('status')->orderByDesc('school_enrollments.id'),
                'statusDesc' => $query->orderByDesc('status')->orderByDesc('school_enrollments.id'),

                'userNameAsc' => $query
                    ->join('users', 'school_enrollments.user_id', '=', 'users.id')
                    ->orderBy('users.name')
                    ->orderByDesc('school_enrollments.id')
                    ->select('school_enrollments.*'),

                'userNameDesc' => $query
                    ->join('users', 'school_enrollments.user_id', '=', 'users.id')
                    ->orderByDesc('users.name')
                    ->orderByDesc('school_enrollments.id')
                    ->select('school_enrollments.*'),

                'courseTitleAsc' => $query
                    ->leftJoin('school_course_translations as sct', function ($join) {
                        $join->on('school_enrollments.school_course_id', '=', 'sct.school_course_id')
                            ->where('sct.locale', app()->getLocale());
                    })
                    ->orderBy('sct.title')
                    ->orderByDesc('school_enrollments.id')
                    ->select('school_enrollments.*'),

                'courseTitleDesc' => $query
                    ->leftJoin('school_course_translations as sct', function ($join) {
                        $join->on('school_enrollments.school_course_id', '=', 'sct.school_course_id')
                            ->where('sct.locale', app()->getLocale());
                    })
                    ->orderByDesc('sct.title')
                    ->orderByDesc('school_enrollments.id')
                    ->select('school_enrollments.*'),

                default => $query->orderByDesc('school_enrollments.id'),
            };

            $enrollments = $query->get();

            return Inertia::render('Admin/School/SchoolEnrollments/Index', [
                'enrollments' => SchoolEnrollmentResource::collection($enrollments),
                'enrollmentsCount' => $enrollments->count(),

                'adminSchoolEnrollmentsPerPage' => $adminSchoolEnrollmentsPerPage,
                'adminSchoolEnrollmentsDefaultSort' => $adminSchoolEnrollmentsDefaultSort,

                'filters' => [
                    'status' => $status,
                    'user_id' => $userId,
                    'school_course_id' => $courseId,
                    'school_course_schedule_id' => $scheduleId,
                ],

                'users' => $this->usersForSelect(),
                'courses' => $this->coursesForSelect(),
                'schedules' => $this->schedulesForSelect(),
                'orders' => $this->ordersForSelect(),
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки school enrollments: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/School/SchoolEnrollments/Index', [
                'enrollments' => [],
                'enrollmentsCount' => 0,

                'adminSchoolEnrollmentsPerPage' => $adminSchoolEnrollmentsPerPage,
                'adminSchoolEnrollmentsDefaultSort' => $adminSchoolEnrollmentsDefaultSort,

                'filters' => [
                    'status' => $status,
                    'user_id' => $userId,
                    'school_course_id' => $courseId,
                    'school_course_schedule_id' => $scheduleId,
                ],

                'users' => [],
                'courses' => [],
                'schedules' => [],
                'orders' => [],

                'error' => 'Ошибка загрузки зачислений.',
            ]);
        }
    }

    /** Страница создания зачисления. */
    public function create(): Response
    {
        return Inertia::render('Admin/School/SchoolEnrollments/Create', [
            'users' => $this->usersForSelect(),
            'courses' => $this->coursesForSelect(),
            'schedules' => $this->schedulesForSelect(),
            'orders' => $this->ordersForSelect(),
        ]);
    }

    /** Сохранение нового зачисления. */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateEnrollment($request);

        try {
            SchoolEnrollment::create($data);

            return redirect()
                ->route('admin.schoolEnrollments.index')
                ->with('success', 'Зачисление успешно создано.');
        } catch (Throwable $e) {
            Log::error('Ошибка создания school enrollment: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании зачисления.');
        }
    }

    /** Редирект на страницу редактирования. */
    public function show(int $schoolEnrollment): RedirectResponse
    {
        return redirect()->route('admin.schoolEnrollments.edit', $schoolEnrollment);
    }

    /** Страница редактирования зачисления. */
    public function edit(int $schoolEnrollment): Response
    {
        $enrollment = SchoolEnrollment::query()
            ->with([
                'user:id,name,email',
                'course.translation',
                'course.translations',
                'schedule.translation',
                'schedule.translations',
                'schedule.course.translation',
                'order',
                'progressRecords',
                'certificate',
            ])
            ->withCount(['progressRecords'])
            ->findOrFail($schoolEnrollment);

        return Inertia::render('Admin/School/SchoolEnrollments/Edit', [
            'enrollment' => new SchoolEnrollmentResource($enrollment),

            'users' => $this->usersForSelect(),
            'courses' => $this->coursesForSelect(),
            'schedules' => $this->schedulesForSelect(),
            'orders' => $this->ordersForSelect(),
        ]);
    }

    /** Обновление зачисления. */
    public function update(Request $request, int $schoolEnrollment): RedirectResponse
    {
        $enrollment = SchoolEnrollment::query()->findOrFail($schoolEnrollment);

        $data = $this->validateEnrollment($request);

        try {
            $enrollment->update($data);

            return redirect()
                ->route('admin.schoolEnrollments.index')
                ->with('success', 'Зачисление успешно обновлено.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления school enrollment ID ' . $enrollment->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении зачисления.');
        }
    }

    /** Удаление зачисления. */
    public function destroy(int $schoolEnrollment): RedirectResponse
    {
        $enrollment = SchoolEnrollment::query()->findOrFail($schoolEnrollment);

        try {
            $enrollment->delete();

            return redirect()
                ->route('admin.schoolEnrollments.index')
                ->with('success', 'Зачисление успешно удалено.');
        } catch (Throwable $e) {
            Log::error('Ошибка удаления school enrollment ID ' . $enrollment->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении зачисления.');
        }
    }

    /** Массовое удаление зачислений. */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:school_enrollments,id'],
        ]);

        try {
            DB::transaction(function () use ($data) {
                SchoolEnrollment::query()
                    ->whereIn('id', $data['ids'])
                    ->delete();
            });

            return back()->with('success', 'Выбранные зачисления успешно удалены.');
        } catch (Throwable $e) {
            Log::error('Ошибка массового удаления school enrollments: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при массовом удалении зачислений.');
        }
    }

    /** Обновление статуса зачисления. */
    public function updateStatus(Request $request, int $schoolEnrollment): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'max:32'],
            'notes' => ['nullable', 'string'],
        ]);

        $enrollment = SchoolEnrollment::query()->findOrFail($schoolEnrollment);

        try {
            $enrollment->status = $data['status'];

            if ($data['status'] === 'active' && !$enrollment->started_at) {
                $enrollment->started_at = now();
            }

            if ($data['status'] === 'completed' && !$enrollment->completed_at) {
                $enrollment->completed_at = now();
                $enrollment->progress_percent = 100;
            }

            if (array_key_exists('notes', $data)) {
                $enrollment->notes = $data['notes'];
            }

            $enrollment->save();

            return back()->with('success', 'Статус зачисления успешно обновлён.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления статуса school enrollment ID ' . $enrollment->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при обновлении статуса зачисления.');
        }
    }

    /** Обновление заметок зачисления. */
    public function updateNotes(Request $request, int $schoolEnrollment): RedirectResponse
    {
        $data = $request->validate([
            'notes' => ['nullable', 'string'],
        ]);

        $enrollment = SchoolEnrollment::query()->findOrFail($schoolEnrollment);

        try {
            $enrollment->update([
                'notes' => $data['notes'] ?? null,
            ]);

            return back()->with('success', 'Заметки зачисления успешно обновлены.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления заметок school enrollment ID ' . $enrollment->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при обновлении заметок зачисления.');
        }
    }

    /** Валидация данных зачисления. */
    private function validateEnrollment(Request $request): array
    {
        return $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'school_course_id' => ['required', 'integer', 'exists:school_courses,id'],
            'school_course_schedule_id' => ['nullable', 'integer', 'exists:school_course_schedules,id'],
            'school_order_id' => ['nullable', 'integer', 'exists:school_orders,id'],

            'status' => ['required', 'string', 'max:32'],

            'started_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],

            'progress_percent' => ['nullable', 'integer', 'min:0', 'max:100'],

            'notes' => ['nullable', 'string'],
            'meta' => ['nullable', 'array'],
        ]);
    }

    /** Пользователи для select. */
    private function usersForSelect(): Collection|array
    {
        return User::query()
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
    }

    /** Курсы для select. */
    private function coursesForSelect(): AnonymousResourceCollection
    {
        $courses = SchoolCourse::query()
            ->with(['translation', 'translations'])
            ->get();

        return SchoolCourseSharedResource::collection($courses);
    }

    /** Расписания потоков для select. */
    private function schedulesForSelect(): AnonymousResourceCollection
    {
        $schedules = SchoolCourseSchedule::query()
            ->with([
                'translation',
                'translations',
                'course.translation',
                'course.translations',
            ])
            ->get();

        return SchoolCourseScheduleSharedResource::collection($schedules);
    }

    /** Заказы для select. */
    private function ordersForSelect(): AnonymousResourceCollection
    {
        $orders = SchoolOrder::query()
            ->with(['user:id,name,email'])
            ->orderByDesc('id')
            ->get();

        return SchoolOrderSharedResource::collection($orders);
    }
}
