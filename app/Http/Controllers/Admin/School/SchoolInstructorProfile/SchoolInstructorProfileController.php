<?php

namespace App\Http\Controllers\Admin\School\SchoolInstructorProfile;

use App\Http\Controllers\Admin\School\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileRequest;
use App\Http\Resources\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileResource;
use App\Http\Resources\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileSharedResource;
use App\Http\Resources\Admin\System\User\UserResource;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfile;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileImage;
use App\Models\User;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
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

        $perPage = $settings->int('adminSchoolInstructorsPerPage', 6);
        $defaultSort = $settings->string('adminSchoolInstructorsDefaultSort', 'idDesc');

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $processingMode = $settings->string(
            'adminSchoolInstructorsProcessingMode',
            'frontend'
        );

        $instructorProfilesCount = $this->baseQuery()->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $instructorProfilesCount,
                300
            );

        try {
            $instructorProfiles = $this->getIndexInstructorProfiles(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            return Inertia::render('Admin/School/SchoolInstructorProfiles/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminSchoolInstructorsPerPage' => $perPage,
                'adminSchoolInstructorsDefaultSort' => $defaultSort,
                'adminSchoolInstructorsProcessingMode' => $processingMode,

                'instructorProfiles' =>
                    SchoolInstructorProfileSharedResource::collection(
                        $instructorProfiles
                    ),
                'instructorProfilesCount' => $instructorProfilesCount,

                'sortParam' => $sortParam,
                'search' => $search,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка school instructor profiles: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/School/SchoolInstructorProfiles/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminSchoolInstructorsPerPage' => $perPage,
                'adminSchoolInstructorsDefaultSort' => $defaultSort,
                'adminSchoolInstructorsProcessingMode' => $processingMode,

                'instructorProfiles' => [],
                'instructorProfilesCount' => 0,

                'sortParam' => $sortParam,
                'search' => $search,

                'error' => 'Ошибка загрузки инструкторов.',
            ]);
        }
    }

    /** Страница создания */
    public function create(
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        /**
         * Один и тот же список пользователей
         * используется Create / Edit.
         */
        $users = User::query()
            ->select([
                'id',
                'name',
            ])
            ->orderBy(
                'name',
                'asc'
            )
            ->get();

        return Inertia::render(
            'Admin/School/SchoolInstructorProfiles/Create',
            [
                'users' =>
                    UserResource::collection(
                        $users
                    ),

                'targetLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),
            ]
        );
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
    public function edit(
        int $id,
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        /**
         * Для Edit нужны:
         *
         * - все переводы для TranslationTabs;
         * - изображения вместе со Spatie Media;
         * - связанный пользователь.
         *
         * Отдельная relation translation
         * здесь не нужна.
         */
        $model = $this->baseQuery()
            ->with([
                'translations',

                'images.media',

                'user:id,name,email',
            ])
            ->findOrFail(
                $id
            );

        /**
         * Один и тот же список пользователей
         * используется Create / Edit.
         */
        $users = User::query()
            ->select([
                'id',
                'name',
            ])
            ->orderBy(
                'name',
                'asc'
            )
            ->get();

        return Inertia::render(
            'Admin/School/SchoolInstructorProfiles/Edit',
            [
                'instructorProfile' =>
                    new SchoolInstructorProfileResource(
                        $model
                    ),

                'users' =>
                    UserResource::collection(
                        $users
                    ),

                'targetLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),
            ]
        );
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

    /** Базовый запрос для Admin Index инструкторов. */
    private function indexQuery(
        string $locale
    ): Builder {
        return $this->baseQuery()
            ->with([
                /**
                 * Для Admin Index нужен
                 * только перевод выбранной локали.
                 */
                'translations' => fn ($query) =>
                $query->where(
                    'locale',
                    $locale
                ),

                /**
                 * Пользователь нужен таблице,
                 * карточкам и frontend-поиску.
                 */
                'user:id,name,email',

                /**
                 * Курсы нужны frontend-поиску.
                 *
                 * Не загружаем их полный граф.
                 */
                'courses' => fn ($query) =>
                $query->select([
                    'id',
                    'school_instructor_profile_id',
                    'slug',
                ])
                    ->with([
                        'translations' => fn ($translationQuery) =>
                        $translationQuery->where(
                            'locale',
                            $locale
                        ),
                    ]),

                /**
                 * Изображения + Spatie Media
                 * одним пакетным eager loading.
                 */
                'images.media',
            ])
            ->withCount([
                'courses',
                'payouts',
                'images',
            ]);
    }

    /** Получение списка инструкторов по активному режиму обработки. */
    private function getIndexInstructorProfiles(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query = $this->indexQuery($locale);

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
