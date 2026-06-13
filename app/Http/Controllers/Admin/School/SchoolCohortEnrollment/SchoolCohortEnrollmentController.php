<?php

namespace App\Http\Controllers\Admin\School\SchoolCohortEnrollment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\SchoolCohortEnrollment\SchoolCohortEnrollmentRequest;
use App\Http\Resources\Admin\School\SchoolCohortEnrollment\SchoolCohortEnrollmentResource;
use App\Http\Resources\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleSharedResource;
use App\Models\Admin\School\SchoolCohortEnrollment\SchoolCohortEnrollment;
use App\Models\Admin\School\SchoolCourseSchedule\SchoolCourseSchedule;
use App\Models\User;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
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
 * Админ-контроллер для управления записями участников потоков (SchoolCohortEnrollment)
 *  CRUD +
 * - просмотр списка
 * - обновление статуса
 * - обновление заметок администратора
 * - связи с преподователями и расписанием.
 *
 * @version 1.1 (паттерн)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see SchoolCohortEnrollment
 * @see SchoolCohortEnrollmentRequest
 */
class SchoolCohortEnrollmentController extends Controller
{
    /** Список записей на потоки */
    public function index(Request $request): Response
    {
        $status = $request->query('status');
        $scheduleId = $request->query('school_course_schedule_id');

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int('adminSchoolCohortEnrollmentsPerPage', 6);
        $defaultSort = $settings->string('adminSchoolCohortEnrollmentsDefaultSort', 'idDesc');

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $processingMode = $settings->string(
            'adminSchoolCohortEnrollmentsProcessingMode',
            'frontend'
        );

        $enrollmentsCount = SchoolCohortEnrollment::query()
            ->when($status !== null && $status !== '', fn (Builder $q) => $q->where('status', $status))
            ->when($scheduleId, fn (Builder $q) => $q->where('school_course_schedule_id', (int) $scheduleId))
            ->count();

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
                scheduleId: $scheduleId,
            );

            return Inertia::render('Admin/School/SchoolCohortEnrollments/Index', [
                'useServerProcessing' => $useServerProcessing,

                'adminSchoolCohortEnrollmentsPerPage' => $perPage,
                'adminSchoolCohortEnrollmentsDefaultSort' => $defaultSort,
                'adminSchoolCohortEnrollmentsProcessingMode' => $processingMode,

                'enrollments' => SchoolCohortEnrollmentResource::collection($enrollments),
                'enrollmentsCount' => $enrollmentsCount,

                'sortParam' => $sortParam,
                'search' => $search,

                'filters' => [
                    'status' => $status,
                    'school_course_schedule_id' => $scheduleId,
                ],

                'schedules' => $this->schedulesForSelect(),
                'users' => $this->usersForSelect(),
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки school cohort enrollments: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/School/SchoolCohortEnrollments/Index', [
                'useServerProcessing' => $useServerProcessing,

                'adminSchoolCohortEnrollmentsPerPage' => $perPage,
                'adminSchoolCohortEnrollmentsDefaultSort' => $defaultSort,
                'adminSchoolCohortEnrollmentsProcessingMode' => $processingMode,

                'enrollments' => [],
                'enrollmentsCount' => 0,

                'sortParam' => $sortParam,
                'search' => $search,

                'filters' => [
                    'status' => $status,
                    'school_course_schedule_id' => $scheduleId,
                ],

                'schedules' => [],
                'users' => [],

                'error' => 'Ошибка загрузки записей на потоки.',
            ]);
        }
    }

    /** Страница создания записи */
    public function create(): Response
    {
        return Inertia::render('Admin/School/SchoolCohortEnrollments/Create', [
            'schedules' => $this->schedulesForSelect(),
            'users' => $this->usersForSelect(),
        ]);
    }

    /** Создание записи */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'school_course_schedule_id' => [
                'required',
                'integer',
                'exists:school_course_schedules,id',
            ],
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'status' => [
                'required',
                'string',
                'max:32',
            ],
            'enrolled_at' => [
                'nullable',
                'date',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        try {
            SchoolCohortEnrollment::create($data);

            return redirect()
                ->route('admin.schoolCohortEnrollments.index')
                ->with('success', 'Запись на поток успешно создана.');
        } catch (Throwable $e) {
            Log::error('Ошибка создания school cohort enrollment: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании записи на поток.');
        }
    }

    /** Редирект на редактирование */
    public function show(int $schoolCohortEnrollment): RedirectResponse
    {
        return redirect()->route(
            'admin.schoolCohortEnrollments.edit',
            $schoolCohortEnrollment
        );
    }

    /** Страница редактирования записи */
    public function edit(int $schoolCohortEnrollment): Response
    {
        $enrollment = SchoolCohortEnrollment::query()
            ->with([
                'schedule.translation',
                'schedule.translations',
                'schedule.course.translation',
                'schedule.course.translations',
                'user:id,name,email',
            ])
            ->findOrFail($schoolCohortEnrollment);

        return Inertia::render('Admin/School/SchoolCohortEnrollments/Edit', [
            'enrollment' => new SchoolCohortEnrollmentResource($enrollment),
            'schedules' => $this->schedulesForSelect(),
            'users' => $this->usersForSelect(),
        ]);
    }

    /** Обновление записи */
    public function update(
        Request $request,
        int $schoolCohortEnrollment
    ): RedirectResponse {
        $enrollment = SchoolCohortEnrollment::query()
            ->findOrFail($schoolCohortEnrollment);

        $data = $request->validate([
            'school_course_schedule_id' => [
                'required',
                'integer',
                'exists:school_course_schedules,id',
            ],
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'status' => [
                'required',
                'string',
                'max:32',
            ],
            'enrolled_at' => [
                'nullable',
                'date',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        try {
            $enrollment->update($data);

            return redirect()
                ->route('admin.schoolCohortEnrollments.index')
                ->with('success', 'Запись на поток успешно обновлена.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления school cohort enrollment ID ' . $enrollment->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении записи на поток.');
        }
    }

    /** Удаление записи */
    public function destroy(int $schoolCohortEnrollment): RedirectResponse
    {
        $enrollment = SchoolCohortEnrollment::query()
            ->findOrFail($schoolCohortEnrollment);

        try {
            $enrollment->delete();

            return redirect()
                ->route('admin.schoolCohortEnrollments.index')
                ->with('success', 'Запись на поток успешно удалена.');
        } catch (Throwable $e) {
            Log::error('Ошибка удаления school cohort enrollment ID ' . $enrollment->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении записи на поток.');
        }
    }

    /** Массовое удаление записей */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => [
                'required',
                'integer',
                'exists:school_cohort_enrollments,id',
            ],
        ]);

        try {
            DB::transaction(function () use ($data) {
                SchoolCohortEnrollment::query()
                    ->whereIn('id', $data['ids'])
                    ->delete();
            });

            return back()->with(
                'success',
                'Выбранные записи на потоки успешно удалены.'
            );
        } catch (Throwable $e) {
            Log::error('Ошибка массового удаления school cohort enrollments: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with(
                'error',
                'Ошибка при массовом удалении записей на потоки.'
            );
        }
    }

    /** Обновление статуса записи */
    public function updateStatus(
        Request $request,
        int $schoolCohortEnrollment
    ): RedirectResponse|JsonResponse {
        $data = $request->validate([
            'status' => ['required', 'string', 'max:32'],
            'notes' => ['nullable', 'string'],
        ]);

        $enrollment = SchoolCohortEnrollment::query()
            ->findOrFail($schoolCohortEnrollment);

        try {
            $enrollment->status = $data['status'];

            if ($data['status'] === 'approved' && !$enrollment->enrolled_at) {
                $enrollment->enrolled_at = now();
            }

            if (array_key_exists('notes', $data)) {
                $enrollment->notes = $data['notes'];
            }

            $enrollment->save();

            $message = 'Статус записи успешно обновлён.';

            return $request->expectsJson()
                ? response()->json(['message' => $message])
                : back()->with('success', $message);
        } catch (Throwable $e) {
            Log::error('Ошибка обновления статуса school cohort enrollment ID ' . $enrollment->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            $message = 'Ошибка при обновлении статуса записи.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }

    /** Обновление заметок записи */
    public function updateNotes(
        Request $request,
        int $schoolCohortEnrollment
    ): RedirectResponse|JsonResponse {
        $data = $request->validate([
            'notes' => ['nullable', 'string'],
        ]);

        $enrollment = SchoolCohortEnrollment::query()
            ->findOrFail($schoolCohortEnrollment);

        try {
            $enrollment->update([
                'notes' => $data['notes'] ?? null,
            ]);

            $message = 'Заметки записи успешно обновлены.';

            return $request->expectsJson()
                ? response()->json(['message' => $message])
                : back()->with('success', $message);
        } catch (Throwable $e) {
            Log::error('Ошибка обновления заметок school cohort enrollment ID ' . $enrollment->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            $message = 'Ошибка при обновлении заметок записи.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }

    /** Расписания для select */
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

    /** Пользователи для select */
    private function usersForSelect(): Collection|array
    {
        return User::query()
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
    }

    /** Базовый запрос для списка записей на потоки. */
    private function indexQuery(
        ?string $status = null,
        mixed $scheduleId = null
    ): Builder {
        return SchoolCohortEnrollment::query()
            ->with([
                'schedule.translation',
                'schedule.translations',
                'schedule.course.translation',
                'schedule.course.translations',
                'user:id,name,email',
            ])
            ->when($status !== null && $status !== '', function (Builder $query) use ($status) {
                $query->where('status', $status);
            })
            ->when($scheduleId, function (Builder $query) use ($scheduleId) {
                $query->where('school_course_schedule_id', (int) $scheduleId);
            });
    }

    /** Поиск по записям на потоки. */
    private function applySearch(Builder $query, string $search): Builder
    {
        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search) {
            $q->where('school_cohort_enrollments.status', 'like', "%{$search}%")
                ->orWhere('school_cohort_enrollments.notes', 'like', "%{$search}%")
                ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('schedule.translations', function (Builder $scheduleQuery) use ($search) {
                    $scheduleQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                })
                ->orWhereHas('schedule.course.translations', function (Builder $courseQuery) use ($search) {
                    $courseQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('short', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
        });
    }

    /** Сортировка записей на потоки. */
    private function applySort(Builder $query, string $sort): Builder
    {
        return match ($sort) {
            'idAsc' => $query->orderBy('school_cohort_enrollments.id', 'asc'),
            'idDesc' => $query->orderBy('school_cohort_enrollments.id', 'desc'),

            'enrolledAtAsc' => $query
                ->orderBy('school_cohort_enrollments.enrolled_at', 'asc')
                ->orderByDesc('school_cohort_enrollments.id'),

            'enrolledAtDesc' => $query
                ->orderBy('school_cohort_enrollments.enrolled_at', 'desc')
                ->orderByDesc('school_cohort_enrollments.id'),

            'statusAsc' => $query
                ->orderBy('school_cohort_enrollments.status', 'asc')
                ->orderByDesc('school_cohort_enrollments.id'),

            'statusDesc' => $query
                ->orderBy('school_cohort_enrollments.status', 'desc')
                ->orderByDesc('school_cohort_enrollments.id'),

            'userNameAsc' => $query
                ->join('users as users_sort', 'school_cohort_enrollments.user_id', '=', 'users_sort.id')
                ->orderBy('users_sort.name', 'asc')
                ->orderByDesc('school_cohort_enrollments.id')
                ->select('school_cohort_enrollments.*'),

            'userNameDesc' => $query
                ->join('users as users_sort', 'school_cohort_enrollments.user_id', '=', 'users_sort.id')
                ->orderBy('users_sort.name', 'desc')
                ->orderByDesc('school_cohort_enrollments.id')
                ->select('school_cohort_enrollments.*'),

            'userEmailAsc' => $query
                ->join('users as users_sort', 'school_cohort_enrollments.user_id', '=', 'users_sort.id')
                ->orderBy('users_sort.email', 'asc')
                ->orderByDesc('school_cohort_enrollments.id')
                ->select('school_cohort_enrollments.*'),

            'userEmailDesc' => $query
                ->join('users as users_sort', 'school_cohort_enrollments.user_id', '=', 'users_sort.id')
                ->orderBy('users_sort.email', 'desc')
                ->orderByDesc('school_cohort_enrollments.id')
                ->select('school_cohort_enrollments.*'),

            'scheduleTitleAsc' => $query
                ->leftJoin('school_course_schedule_translations as sct_sort', function ($join) {
                    $join->on(
                        'school_cohort_enrollments.school_course_schedule_id',
                        '=',
                        'sct_sort.school_course_schedule_id'
                    )->where('sct_sort.locale', '=', app()->getLocale());
                })
                ->orderBy('sct_sort.title', 'asc')
                ->orderByDesc('school_cohort_enrollments.id')
                ->select('school_cohort_enrollments.*'),

            'scheduleTitleDesc' => $query
                ->leftJoin('school_course_schedule_translations as sct_sort', function ($join) {
                    $join->on(
                        'school_cohort_enrollments.school_course_schedule_id',
                        '=',
                        'sct_sort.school_course_schedule_id'
                    )->where('sct_sort.locale', '=', app()->getLocale());
                })
                ->orderBy('sct_sort.title', 'desc')
                ->orderByDesc('school_cohort_enrollments.id')
                ->select('school_cohort_enrollments.*'),

            'createdAtAsc' => $query
                ->orderBy('school_cohort_enrollments.created_at', 'asc')
                ->orderByDesc('school_cohort_enrollments.id'),

            'createdAtDesc' => $query
                ->orderBy('school_cohort_enrollments.created_at', 'desc')
                ->orderByDesc('school_cohort_enrollments.id'),

            'updatedAtAsc' => $query
                ->orderBy('school_cohort_enrollments.updated_at', 'asc')
                ->orderByDesc('school_cohort_enrollments.id'),

            'updatedAtDesc' => $query
                ->orderBy('school_cohort_enrollments.updated_at', 'desc')
                ->orderByDesc('school_cohort_enrollments.id'),

            default => $query->orderByDesc('school_cohort_enrollments.id'),
        };
    }

    /** Получение списка записей на потоки по активному режиму обработки. */
    private function getIndexEnrollments(
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = '',
        ?string $status = null,
        mixed $scheduleId = null,
    ): Collection|LengthAwarePaginator
    {
        $query = $this->indexQuery(
            status: $status,
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
