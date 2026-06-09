<?php

namespace App\Http\Controllers\Admin\School\SchoolCohortEnrollment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\SchoolCohortEnrollment\SchoolCohortEnrollmentRequest;
use App\Http\Resources\Admin\School\SchoolCohortEnrollment\SchoolCohortEnrollmentResource;
use App\Http\Resources\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleSharedResource;
use App\Models\Admin\School\SchoolCohortEnrollment\SchoolCohortEnrollment;
use App\Models\Admin\School\SchoolCourseSchedule\SchoolCourseSchedule;
use App\Models\User;
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

        $adminSchoolCohortEnrollmentsPerPage = (int) config('site_settings.adminSchoolCohortEnrollmentsPerPage', 6);
        $adminSchoolCohortEnrollmentsDefaultSort = (string) config('site_settings.adminSchoolCohortEnrollmentsDefaultSort', 'idDesc');

        try {
            $query = SchoolCohortEnrollment::query()
                ->with([
                    'schedule.translation',
                    'schedule.translations',
                    'schedule.course.translation',
                    'schedule.course.translations',
                    'user:id,name,email',
                ]);

            if ($status !== null && $status !== '') {
                $query->where('status', $status);
            }

            if ($scheduleId) {
                $query->where('school_course_schedule_id', (int) $scheduleId);
            }

            match ($adminSchoolCohortEnrollmentsDefaultSort) {
                'idAsc' => $query->orderBy('id'),

                'enrolledAtAsc' => $query->orderBy('enrolled_at')->orderByDesc('id'),
                'enrolledAtDesc' => $query->orderByDesc('enrolled_at')->orderByDesc('id'),

                'statusAsc' => $query->orderBy('status')->orderByDesc('id'),
                'statusDesc' => $query->orderByDesc('status')->orderByDesc('id'),

                'userNameAsc' => $query
                    ->join('users', 'school_cohort_enrollments.user_id', '=', 'users.id')
                    ->orderBy('users.name')
                    ->orderByDesc('school_cohort_enrollments.id')
                    ->select('school_cohort_enrollments.*'),

                'userNameDesc' => $query
                    ->join('users', 'school_cohort_enrollments.user_id', '=', 'users.id')
                    ->orderByDesc('users.name')
                    ->orderByDesc('school_cohort_enrollments.id')
                    ->select('school_cohort_enrollments.*'),

                'scheduleTitleAsc' => $query
                    ->leftJoin('school_course_schedule_translations as sct', function ($join) {
                        $join->on('school_cohort_enrollments.school_course_schedule_id', '=', 'sct.school_course_schedule_id')
                            ->where('sct.locale', app()->getLocale());
                    })
                    ->orderBy('sct.title')
                    ->orderByDesc('school_cohort_enrollments.id')
                    ->select('school_cohort_enrollments.*'),

                'scheduleTitleDesc' => $query
                    ->leftJoin('school_course_schedule_translations as sct', function ($join) {
                        $join->on('school_cohort_enrollments.school_course_schedule_id', '=', 'sct.school_course_schedule_id')
                            ->where('sct.locale', app()->getLocale());
                    })
                    ->orderByDesc('sct.title')
                    ->orderByDesc('school_cohort_enrollments.id')
                    ->select('school_cohort_enrollments.*'),

                default => $query->orderByDesc('id'),
            };

            $enrollments = $query->get();

            return Inertia::render('Admin/School/CohortEnrollments/Index', [
                'enrollments' => SchoolCohortEnrollmentResource::collection($enrollments),
                'enrollmentsCount' => $enrollments->count(),

                'adminSchoolCohortEnrollmentsPerPage' => $adminSchoolCohortEnrollmentsPerPage,
                'adminSchoolCohortEnrollmentsDefaultSort' => $adminSchoolCohortEnrollmentsDefaultSort,

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

            return Inertia::render('Admin/School/CohortEnrollments/Index', [
                'enrollments' => [],
                'enrollmentsCount' => 0,

                'adminSchoolCohortEnrollmentsPerPage' => $adminSchoolCohortEnrollmentsPerPage,
                'adminSchoolCohortEnrollmentsDefaultSort' => $adminSchoolCohortEnrollmentsDefaultSort,

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
        return Inertia::render('Admin/School/CohortEnrollments/Create', [
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

        return Inertia::render('Admin/School/CohortEnrollments/Edit', [
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
}
