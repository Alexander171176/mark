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
use App\Services\Admin\ProcessingModeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use App\Services\SiteSettings\AdminSettingsService;
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
    /** Список зачислений учеников. */
    public function index(Request $request): Response
    {
        $status = $request->query('status');
        $userId = $request->query('user_id');
        $courseId = $request->query('school_course_id');
        $scheduleId = $request->query('school_course_schedule_id');

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int('adminSchoolEnrollmentsPerPage', 6);
        $defaultSort = $settings->string('adminSchoolEnrollmentsDefaultSort', 'idDesc');

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $processingMode = $settings->string(
            'adminSchoolEnrollmentsProcessingMode',
            'frontend'
        );

        $enrollmentsCount = $this->indexQuery(
            status: $status,
            userId: $userId,
            courseId: $courseId,
            scheduleId: $scheduleId,
        )->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $enrollmentsCount,
                300
            );

        try {
            $enrollments = $this->getIndexEnrollments(
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
                status: $status,
                userId: $userId,
                courseId: $courseId,
                scheduleId: $scheduleId,
            );

            return Inertia::render('Admin/School/SchoolEnrollments/Index', [
                'useServerProcessing' => $useServerProcessing,

                'adminSchoolEnrollmentsPerPage' => $perPage,
                'adminSchoolEnrollmentsDefaultSort' => $defaultSort,
                'adminSchoolEnrollmentsProcessingMode' => $processingMode,

                'enrollments' => SchoolEnrollmentResource::collection($enrollments),
                'enrollmentsCount' => $enrollmentsCount,

                'sortParam' => $sortParam,
                'search' => $search,

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
                'useServerProcessing' => $useServerProcessing,

                'adminSchoolEnrollmentsPerPage' => $perPage,
                'adminSchoolEnrollmentsDefaultSort' => $defaultSort,
                'adminSchoolEnrollmentsProcessingMode' => $processingMode,

                'enrollments' => [],
                'enrollmentsCount' => 0,

                'sortParam' => $sortParam,
                'search' => $search,

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

    /** Базовый запрос для списка зачислений. */
    private function indexQuery(
        ?string $status = null,
        mixed $userId = null,
        mixed $courseId = null,
        mixed $scheduleId = null,
    ): Builder {
        return SchoolEnrollment::query()
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
            ->withCount(['progressRecords'])
            ->when($status, fn (Builder $q) => $q->where('status', $status))
            ->when($userId, fn (Builder $q) => $q->where('user_id', (int) $userId))
            ->when($courseId, fn (Builder $q) => $q->where('school_course_id', (int) $courseId))
            ->when($scheduleId, fn (Builder $q) => $q->where('school_course_schedule_id', (int) $scheduleId));
    }

    /** Поиск по зачислениям. */
    private function applySearch(Builder $query, string $search): Builder
    {
        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search) {
            $q->where('school_enrollments.status', 'like', "%{$search}%")
                ->orWhere('school_enrollments.notes', 'like', "%{$search}%")
                ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('course.translations', function (Builder $courseQuery) use ($search) {
                    $courseQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('short', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                })
                ->orWhereHas('schedule.translations', function (Builder $scheduleQuery) use ($search) {
                    $scheduleQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                })
                ->orWhereHas('order', function (Builder $orderQuery) use ($search) {
                    $orderQuery->where('id', 'like', "%{$search}%")
                        ->orWhere('number', 'like', "%{$search}%");
                });
        });
    }

    /** Сортировка зачислений. */
    private function applySort(Builder $query, string $sort): Builder
    {
        return match ($sort) {
            'idAsc' => $query->orderBy('school_enrollments.id', 'asc'),
            'idDesc' => $query->orderBy('school_enrollments.id', 'desc'),

            'startedAtAsc' => $query->orderBy('started_at', 'asc')->orderByDesc('school_enrollments.id'),
            'startedAtDesc' => $query->orderBy('started_at', 'desc')->orderByDesc('school_enrollments.id'),

            'expiresAtAsc' => $query->orderBy('expires_at', 'asc')->orderByDesc('school_enrollments.id'),
            'expiresAtDesc' => $query->orderBy('expires_at', 'desc')->orderByDesc('school_enrollments.id'),

            'completedAtAsc' => $query->orderBy('completed_at', 'asc')->orderByDesc('school_enrollments.id'),
            'completedAtDesc' => $query->orderBy('completed_at', 'desc')->orderByDesc('school_enrollments.id'),

            'progressAsc' => $query->orderBy('progress_percent', 'asc')->orderByDesc('school_enrollments.id'),
            'progressDesc' => $query->orderBy('progress_percent', 'desc')->orderByDesc('school_enrollments.id'),

            'statusAsc' => $query->orderBy('status', 'asc')->orderByDesc('school_enrollments.id'),
            'statusDesc' => $query->orderBy('status', 'desc')->orderByDesc('school_enrollments.id'),

            'userNameAsc' => $query
                ->join('users as users_sort', 'school_enrollments.user_id', '=', 'users_sort.id')
                ->orderBy('users_sort.name', 'asc')
                ->orderByDesc('school_enrollments.id')
                ->select('school_enrollments.*'),

            'userNameDesc' => $query
                ->join('users as users_sort', 'school_enrollments.user_id', '=', 'users_sort.id')
                ->orderBy('users_sort.name', 'desc')
                ->orderByDesc('school_enrollments.id')
                ->select('school_enrollments.*'),

            'userEmailAsc' => $query
                ->join('users as users_sort', 'school_enrollments.user_id', '=', 'users_sort.id')
                ->orderBy('users_sort.email', 'asc')
                ->orderByDesc('school_enrollments.id')
                ->select('school_enrollments.*'),

            'userEmailDesc' => $query
                ->join('users as users_sort', 'school_enrollments.user_id', '=', 'users_sort.id')
                ->orderBy('users_sort.email', 'desc')
                ->orderByDesc('school_enrollments.id')
                ->select('school_enrollments.*'),

            'courseTitleAsc' => $query
                ->leftJoin('school_course_translations as sct_sort', function ($join) {
                    $join->on('school_enrollments.school_course_id', '=', 'sct_sort.school_course_id')
                        ->where('sct_sort.locale', '=', app()->getLocale());
                })
                ->orderBy('sct_sort.title', 'asc')
                ->orderByDesc('school_enrollments.id')
                ->select('school_enrollments.*'),

            'courseTitleDesc' => $query
                ->leftJoin('school_course_translations as sct_sort', function ($join) {
                    $join->on('school_enrollments.school_course_id', '=', 'sct_sort.school_course_id')
                        ->where('sct_sort.locale', '=', app()->getLocale());
                })
                ->orderBy('sct_sort.title', 'desc')
                ->orderByDesc('school_enrollments.id')
                ->select('school_enrollments.*'),

            'scheduleTitleAsc' => $query
                ->leftJoin('school_course_schedule_translations as scst_sort', function ($join) {
                    $join->on('school_enrollments.school_course_schedule_id', '=', 'scst_sort.school_course_schedule_id')
                        ->where('scst_sort.locale', '=', app()->getLocale());
                })
                ->orderBy('scst_sort.title', 'asc')
                ->orderByDesc('school_enrollments.id')
                ->select('school_enrollments.*'),

            'scheduleTitleDesc' => $query
                ->leftJoin('school_course_schedule_translations as scst_sort', function ($join) {
                    $join->on('school_enrollments.school_course_schedule_id', '=', 'scst_sort.school_course_schedule_id')
                        ->where('scst_sort.locale', '=', app()->getLocale());
                })
                ->orderBy('scst_sort.title', 'desc')
                ->orderByDesc('school_enrollments.id')
                ->select('school_enrollments.*'),

            'progressRecordsAsc' => $query->orderBy('progress_records_count', 'asc')->orderByDesc('school_enrollments.id'),
            'progressRecordsDesc' => $query->orderBy('progress_records_count', 'desc')->orderByDesc('school_enrollments.id'),

            'createdAtAsc' => $query->orderBy('school_enrollments.created_at', 'asc')->orderByDesc('school_enrollments.id'),
            'createdAtDesc' => $query->orderBy('school_enrollments.created_at', 'desc')->orderByDesc('school_enrollments.id'),

            'updatedAtAsc' => $query->orderBy('school_enrollments.updated_at', 'asc')->orderByDesc('school_enrollments.id'),
            'updatedAtDesc' => $query->orderBy('school_enrollments.updated_at', 'desc')->orderByDesc('school_enrollments.id'),

            default => $query->orderByDesc('school_enrollments.id'),
        };
    }

    /** Получение списка зачислений по активному режиму обработки. */
    private function getIndexEnrollments(
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = '',
        ?string $status = null,
        mixed $userId = null,
        mixed $courseId = null,
        mixed $scheduleId = null,
    ): Collection|LengthAwarePaginator {
        $query = $this->indexQuery(
            status: $status,
            userId: $userId,
            courseId: $courseId,
            scheduleId: $scheduleId,
        );

        if ($useServerProcessing) {
            return $this->applySort(
                $this->applySearch($query, $search),
                $sort
            )
                ->paginate($perPage)
                ->withQueryString();
        }

        return $this->applySort($query, $sort)->get();
    }
}
