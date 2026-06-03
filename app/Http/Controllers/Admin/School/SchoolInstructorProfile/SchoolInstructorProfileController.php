<?php

namespace App\Http\Controllers\Admin\School\SchoolInstructorProfile;

use App\Http\Controllers\Admin\School\Base\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileRequest;
use App\Http\Resources\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileResource;
use App\Http\Resources\Admin\System\User\UserResource;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfile;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileImage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления Инструкторами в административной панели.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (одиночное)
 *
 * @version 1.1 (мультиязычная архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see SchoolInstructorProfile Модель
 * @see SchoolInstructorProfileRequest Запрос для создания/обновления
 */
class SchoolInstructorProfileController extends BaseSchoolAdminController
{
    /** Основная модель */
    protected string $modelClass = SchoolInstructorProfile::class;

    /** Модель изображений */
    protected string $imageModelClass = SchoolInstructorProfileImage::class;

    /** Коллекция изображений */
    protected string $imageMediaCollection = 'images';

    /** Название сущности для уведомлений */
    protected string $entityLabel = 'инструктора';

    /** Поля переводов */
    protected array $translationFields = [
        'title',
        'short',
        'bio',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    /** Расширение сортировки для инструкторов. */
    protected function extendedSortMap(): array
    {
        return [
            'slugAsc' => 'slug_asc',
            'slugDesc' => 'slug_desc',

            'ratingCountAsc' => 'rating_count_asc',
            'ratingCountDesc' => 'rating_count_desc',

            'viewsAsc' => 'views_asc',
            'viewsDesc' => 'views_desc',

            'hourlyRateAsc' => 'hourly_rate_asc',
            'hourlyRateDesc' => 'hourly_rate_desc',

            'experienceAsc' => 'experience_asc',
            'experienceDesc' => 'experience_desc',

            'createdAtAsc' => 'created_at_asc',
            'createdAtDesc' => 'created_at_desc',

            'updatedAtAsc' => 'updated_at_asc',
            'updatedAtDesc' => 'updated_at_desc',

            'activity' => 'activity',
            'inactive' => 'inactive',
        ];
    }

    /** Список инструкторов */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $adminSchoolInstructorsPerPage = (int) config('site_settings.adminSchoolInstructorsPerPage', 10);
        $adminSchoolInstructorsDefaultSort = (string) config('site_settings.adminSchoolInstructorsDefaultSort', 'idDesc');
        $sort = $this->normalizeSortParam($adminSchoolInstructorsDefaultSort);

        $items = $this->baseQuery()
            ->with([
                'translation',
                'translations',
                'user:id,name',
                'courses.translation',
                'images',
            ])
            ->withCount('courses')
            ->when($sort === 'activity', fn ($query) => $query->where('activity', true))
            ->when($sort === 'inactive', fn ($query) => $query->where('activity', false))
            ->when($sort === 'slug_asc', fn ($query) => $query->orderBy('slug')->orderByDesc('id'))
            ->when($sort === 'slug_desc', fn ($query) => $query->orderByDesc('slug')->orderByDesc('id'))
            ->when($sort === 'rating_count_asc', fn ($query) => $query->orderBy('rating_count')->orderByDesc('id'))
            ->when($sort === 'rating_count_desc', fn ($query) => $query->orderByDesc('rating_count')->orderByDesc('id'))
            ->when($sort === 'hourly_rate_asc', fn ($query) => $query->orderBy('hourly_rate')->orderByDesc('id'))
            ->when($sort === 'hourly_rate_desc', fn ($query) => $query->orderByDesc('hourly_rate')->orderByDesc('id'))
            ->when($sort === 'experience_asc', fn ($query) => $query->orderBy('experience_years')->orderByDesc('id'))
            ->when($sort === 'experience_desc', fn ($query) => $query->orderByDesc('experience_years')->orderByDesc('id'))
            ->when($sort === 'views_asc', fn ($query) => $query->orderBy('views')->orderByDesc('id'))
            ->when($sort === 'views_desc', fn ($query) => $query->orderByDesc('views')->orderByDesc('id'))
            ->when($sort === 'created_at_asc', fn ($query) => $query->orderBy('created_at')->orderByDesc('id'))
            ->when($sort === 'created_at_desc', fn ($query) => $query->orderByDesc('created_at')->orderByDesc('id'))
            ->when($sort === 'updated_at_asc', fn ($query) => $query->orderBy('updated_at')->orderByDesc('id'))
            ->when($sort === 'updated_at_desc', fn ($query) => $query->orderByDesc('updated_at')->orderByDesc('id'))
            ->when($sort === 'sort_asc', fn ($query) => $query->orderBy('sort')->orderByDesc('id'))
            ->when($sort === 'sort_desc', fn ($query) => $query->orderByDesc('sort')->orderByDesc('id'))
            ->when($sort === 'date_asc', fn ($query) => $query->orderBy('id')->orderByDesc('id'))
            ->when($sort === 'date_desc', fn ($query) => $query->orderByDesc('id'))
            ->get();

        return Inertia::render('Admin/School/InstructorProfiles/Index', [
            'instructorProfiles' => SchoolInstructorProfileResource::collection($items),
            'instructorProfilesCount' => $this->baseQuery()->count(),

            'adminSchoolInstructorsPerPage' => $adminSchoolInstructorsPerPage,
            'adminSchoolInstructorsDefaultSort' => $adminSchoolInstructorsDefaultSort,

            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /** Страница создания */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $users = User::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('Admin/School/InstructorProfiles/Create', [
            'users' => UserResource::collection($users),
            'targetLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /** Создание */
    public function store(SchoolInstructorProfileRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $images = $data['images'] ?? [];
        $deleted = $data['deletedImages'] ?? [];

        unset($data['translations'], $data['images'], $data['deletedImages']);

        try {
            DB::transaction(function () use ($data, $translations, $images, $deleted, $request) {

                if (!isset($data['sort'])) {
                    $data['sort'] = $this->modelClass::max('sort') + 1;
                }

                $model = $this->modelClass::create($data);

                $this->syncTranslations($model, $translations);
                $this->syncImages($model, $request, $images, $deleted);
            });

            return redirect()
                ->route('admin.schoolInstructorProfiles.index')
                ->with('success', 'Инструктор успешно создан.');

        } catch (Throwable $e) {
            Log::error('Ошибка создания инструктора: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Ошибка создания.');
        }
    }

    /** Редирект на edit */
    public function show(int $id): RedirectResponse
    {
        return redirect()->route('admin.schoolInstructorProfiles.edit', $id);
    }

    /** Страница редактирования */
    public function edit(int $id, Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $model = $this->baseQuery()
            ->with(['translation', 'translations', 'images', 'user'])
            ->findOrFail($id);

        $users = User::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('Admin/School/InstructorProfiles/Edit', [
            'instructorProfile' => new SchoolInstructorProfileResource($model),
            'users' => UserResource::collection($users),
            'targetLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /** Обновление */
    public function update(SchoolInstructorProfileRequest $request, int $id): RedirectResponse
    {
        $model = $this->baseQuery()->findOrFail($id);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $images = $data['images'] ?? [];
        $deleted = $data['deletedImages'] ?? [];

        unset($data['translations'], $data['images'], $data['deletedImages'], $data['_method']);

        try {
            DB::transaction(function () use ($model, $data, $translations, $images, $deleted, $request) {

                $model->update($data);

                $this->syncTranslations($model, $translations);
                $this->syncImages($model, $request, $images, $deleted);
            });

            return redirect()
                ->route('admin.schoolInstructorProfiles.index')
                ->with('success', 'Инструктор обновлён.');

        } catch (Throwable $e) {
            Log::error('Ошибка обновления инструктора: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Ошибка обновления.');
        }
    }

    /** Удаление */
    public function destroy(int $id): RedirectResponse
    {
        $model = $this->baseQuery()->findOrFail($id);

        try {
            DB::transaction(function () use ($model) {
                $this->deleteImages($model->images()->pluck('id')->toArray());
                $model->translations()->delete();
                $model->delete();
            });

            return back()->with('success', 'Инструктор удалён.');

        } catch (Throwable $e) {
            Log::error('Ошибка удаления инструктора: ' . $e->getMessage());
            return back()->with('error', 'Ошибка удаления.');
        }
    }
}
