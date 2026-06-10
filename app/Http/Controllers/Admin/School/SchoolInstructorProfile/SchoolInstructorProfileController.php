<?php

namespace App\Http\Controllers\Admin\School\SchoolInstructorProfile;

use App\Http\Controllers\Admin\School\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileRequest;
use App\Http\Resources\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileResource;
use App\Http\Resources\Admin\System\User\UserResource;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfile;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileImage;
use App\Models\User;
use App\Services\SiteSettings\AdminSettingsService;
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

    /** Список инструкторов */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);
        $adminSchoolInstructorsPerPage =
            $settings->int('site_settings.adminSchoolInstructorsPerPage', 6);

        $adminSchoolInstructorsDefaultSort =
            $settings->string(
                'site_settings.adminSchoolInstructorsDefaultSort',
                'idDesc'
            );

        $sort = (string) $request->query(
            'sort',
            $adminSchoolInstructorsDefaultSort
        );

        $items = $this->baseQuery()
            ->with([
                'translation',
                'translations',
                'user:id,name',
                'courses.translation',
                'images',
            ])
            ->withCount('courses')
            ->sortByParam($sort, $currentLocale)
            ->get();

        return Inertia::render(
            'Admin/School/SchoolInstructorProfiles/Index',
            [
                'instructorProfiles' =>
                    SchoolInstructorProfileResource::collection($items),

                'instructorProfilesCount' =>
                    $this->baseQuery()->count(),

                'adminSchoolInstructorsPerPage' =>
                    $adminSchoolInstructorsPerPage,

                'adminSchoolInstructorsDefaultSort' =>
                    $adminSchoolInstructorsDefaultSort,

                'sortParam' => $sort,

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),
            ]
        );
    }

    /** Страница создания */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $users = User::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('Admin/School/SchoolInstructorProfiles/Create', [
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

        return Inertia::render('Admin/School/SchoolInstructorProfiles/Edit', [
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
