<?php

namespace App\Http\Controllers\Admin\School\Enrollment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\Enrollment\EnrollmentRequest;
use App\Http\Resources\Admin\School\Enrollment\EnrollmentResource;
use App\Models\Admin\Finance\Order\Order;
use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\CourseSchedule\CourseSchedule;
use App\Models\Admin\School\Enrollment\Enrollment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
 * - мягкое удаление (SoftDeletes)
 *
 * @see Enrollment
 * @see EnrollmentRequest
 * @see EnrollmentResource
 */
class EnrollmentController extends Controller
{
    /**
     * Список зачислений.
     * Пагинация и сортировка пока на фронте — отдаем выборку с базовыми фильтрами.
     */
    public function index(Request $request): Response
    {
        $adminCountEnrollments = (int) config('site_settings.AdminCountEnrollments', 10);
        $adminSortEnrollments  = config('site_settings.AdminSortEnrollments', 'idDesc');

        $status   = $request->query('status');
        $userId   = $request->query('user_id');
        $courseId = $request->query('course_id');

        $enrollments     = collect();
        $enrollmentsCount = 0;

        try {
            $query = Enrollment::query()
                ->with([
                    'user:id,name,email',
                    'course:id,title,slug',
                    'schedule:id,title,starts_at,ends_at',
                    'certificate:id,enrollment_id,number,issued_at',
                ])
                ->orderByDesc('id');

            if (!empty($status)) {
                $query->where('status', $status);
            }

            if (!empty($userId)) {
                $query->where('user_id', (int) $userId);
            }

            if (!empty($courseId)) {
                $query->where('course_id', (int) $courseId);
            }

            $enrollments = $query->get();
            $enrollmentsCount = Enrollment::count();

        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка зачислений: '.$e->getMessage(), [
                'exception' => $e,
            ]);
            session()->flash('error', __('admin/controllers.index_error'));
        }

        return Inertia::render('Admin/School/Enrollments/Index', [
            'adminCountEnrollments'=> $adminCountEnrollments,
            'adminSortEnrollments' => $adminSortEnrollments,
            'enrollments'      => EnrollmentResource::collection($enrollments)->resolve(),
            'enrollmentsCount' => $enrollmentsCount,
            'filters'          => [
                'status'    => $status,
                'user_id'   => $userId,
                'course_id' => $courseId,
            ],
        ]);
    }

    /**
     * Форма создания зачисления.
     * Передаём списки пользователей, курсов, потоков и заказов для селектов.
     */
    public function create(Request $request): Response
    {
        // TODO: $this->authorize('create-enrollments', Enrollment::class);

        // Пользователи — минимальный набор полей
        $users = User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        // Курсы
        $courses = Course::query()
            ->orderBy('locale', 'desc')
            ->orderBy('title')
            ->get(['id', 'title', 'slug', 'locale']);

        // Потоки / расписание
        $courseSchedules = CourseSchedule::query()
            ->with([
                'course:id,title,slug,locale',
                'instructor:id,title', // или name, если у профиля поле name
            ])
            ->orderBy('locale', 'desc')
            ->get([
                'id',
                'course_id',
                'instructor_profile_id',
                'title',
                'starts_at',
                'enroll_starts_at',
                'enroll_ends_at',
                'locale',
            ]);

        // Заказы
        $orders = Order::query()
            ->with('user:id,name,email')
            ->orderByDesc('id')
            ->get([
                'id',
                'user_id',
                'number',
                'total',
                'currency',
                'created_at',
            ]);

        return Inertia::render('Admin/School/Enrollments/Create', [
            'users'           => $users,
            'courses'         => $courses,
            'courseSchedules' => $courseSchedules,
            'orders'          => $orders,
        ]);
    }

    /**
     * Сохранение нового зачисления.
     */
    public function store(EnrollmentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            /** @var Enrollment $enrollment */
            $enrollment = Enrollment::create($data);

            DB::commit();

            Log::info('Зачисление успешно создано', [
                'id'       => $enrollment->id,
                'user_id'  => $enrollment->user_id,
                'course_id'=> $enrollment->course_id,
            ]);

            return redirect()
                ->route('admin.enrollments.index')
                ->with('success', __('admin/controllers.created_success'));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Ошибка при создании зачисления: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * В админке show обычно редиректим на edit.
     */
    public function show(Enrollment $enrollment): RedirectResponse
    {
        return redirect()->route('admin.enrollments.edit', $enrollment);
    }

    /**
     * Форма редактирования зачисления.
     */
    public function edit(Enrollment $enrollment): Response
    {
        // TODO: $this->authorize('update-enrollments', $enrollment);

        $enrollment->load([
            'user:id,name,email',
            'course:id,title,slug',
            'schedule:id,title,starts_at,ends_at',
            'certificate:id,enrollment_id,number,issued_at',
        ]);

        // Списки для селектов — те же, что и в create()
        $users = User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        // Курсы
        $courses = Course::query()
            ->orderBy('locale', 'desc')
            ->orderBy('title')
            ->get(['id', 'title', 'slug', 'locale']);

        // Потоки / расписание
        $courseSchedules = CourseSchedule::query()
            ->with([
                'course:id,title,slug,locale',
                'instructor:id,title', // или name, если у профиля поле name
            ])
            ->orderBy('locale', 'desc')
            ->get([
                'id',
                'course_id',
                'instructor_profile_id',
                'title',
                'starts_at',
                'enroll_starts_at',
                'enroll_ends_at',
                'locale', // 👈 добавили локаль самого расписания
            ]);

        // Заказы
        $orders = Order::query()
            ->with('user:id,name,email')
            ->orderByDesc('id')
            ->get([
                'id',
                'user_id',
                'number',
                'total',
                'currency',
                'created_at',
            ]);

        return Inertia::render('Admin/School/Enrollments/Edit', [
            'enrollment'      => new EnrollmentResource($enrollment),
            'users'           => $users,
            'courses'         => $courses,
            'courseSchedules' => $courseSchedules,
            'orders'          => $orders,
        ]);
    }

    /**
     * Обновление зачисления.
     */
    public function update(EnrollmentRequest $request, Enrollment $enrollment): RedirectResponse
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $enrollment->update($data);

            DB::commit();

            Log::info('Зачисление обновлено', [
                'id'       => $enrollment->id,
                'user_id'  => $enrollment->user_id,
                'course_id'=> $enrollment->course_id,
            ]);

            return redirect()
                ->route('admin.enrollments.index')
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка при обновлении зачисления ID {$enrollment->id}: ".$e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', __('admin/controllers.updated_error'));
        }
    }

    /**
     * Мягкое удаление зачисления (SoftDeletes).
     */
    public function destroy(Enrollment $enrollment): RedirectResponse
    {
        // TODO: $this->authorize('delete-enrollments', $enrollment);

        try {
            $id       = $enrollment->id;
            $userId   = $enrollment->user_id;
            $courseId = $enrollment->course_id;

            $enrollment->delete(); // Soft delete

            Log::info('Зачисление удалено (soft delete)', [
                'id'        => $id,
                'user_id'   => $userId,
                'course_id' => $courseId,
            ]);

            return redirect()
                ->route('admin.enrollments.index')
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при удалении зачисления ID {$enrollment->id}: ".$e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->with('error', __('admin/controllers.deleted_error'));
        }
    }
}
