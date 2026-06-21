<?php

namespace App\Http\Controllers\Admin\System\ImageProcessor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\System\ImageProcessor\ImageProcessorProfileRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\System\ImageProcessor\ImageProcessorProfileResource;
use App\Models\Admin\System\ImageProcessor\ImageProcessorProfile;
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
 * Контроллер для управления Профиля обработки изображений (ImageProcessorProfile) в админке.
 *
 * Паттерн:
 * - Поиск, Пагинация, сортировка (режимы: frontend | auto | server )
 * - CRUD
 * - owner/ограничение “владелец/админ”
 * - activity (single + bulk)
 * - sort + drag&drop (bulk)
 *
 * @version 1.1 (мультиязычеая архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 */
class ImageProcessorProfileController extends Controller
{
    /** Список профилей обработки */
    public function index(Request $request): Response
    {
        $currentLocale = app()->getLocale();

        $settingsService = app(AdminSettingsService::class);

        $perPage = $settingsService->int('adminImageProcessorProfilesPerPage', 6);

        $defaultSort = $settingsService->string(
            'adminImageProcessorProfilesDefaultSort',
            'sortAsc'
        );

        $processingMode = $settingsService->string(
            'adminImageProcessorProfilesProcessingMode',
            'frontend'
        );

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $profilesCount = $this->indexQuery()->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $profilesCount,
                300
            );

        try {
            $profiles = $this->getIndexProfiles(
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            return Inertia::render('Admin/System/ImageProcessor/Profile/Index', [
                'currentLocale' => $currentLocale,

                'useServerProcessing' => $useServerProcessing,

                'adminImageProcessorProfilesPerPage' => $perPage,
                'adminImageProcessorProfilesDefaultSort' => $defaultSort,
                'adminImageProcessorProfilesProcessingMode' => $processingMode,

                'profiles' => ImageProcessorProfileResource::collection($profiles),
                'profilesCount' => $profilesCount,

                'sortParam' => $sortParam,
                'search' => $search,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки профилей Image Processor: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/System/ImageProcessor/Profile/Index', [
                'currentLocale' => $currentLocale,

                'useServerProcessing' => $useServerProcessing,

                'adminImageProcessorProfilesPerPage' => $perPage,
                'adminImageProcessorProfilesDefaultSort' => $defaultSort,
                'adminImageProcessorProfilesProcessingMode' => $processingMode,

                'profiles' => [],
                'profilesCount' => 0,

                'sortParam' => $sortParam,
                'search' => $search,

                'error' => 'Ошибка загрузки профилей обработки изображений.',
            ]);
        }
    }

    /** Форма создания */
    public function create(): Response
    {
        return Inertia::render('Admin/System/ImageProcessor/Profile/Create');
    }

    /** Создание профиля */
    public function store(ImageProcessorProfileRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            ImageProcessorProfile::create($request->validated());
        });

        return redirect()
            ->route('admin.imageProcessorProfiles.index')
            ->with('success', 'Профиль обработки изображений успешно создан.');
    }

    /** Просмотр профиля */
    public function show(ImageProcessorProfile $imageProcessorProfile): Response
    {
        $imageProcessorProfile
            ->load(['variants' => fn ($query) => $query->ordered()])
            ->loadCount('variants');

        return Inertia::render('Admin/System/ImageProcessor/Profile/Show', [
            'profile' => new ImageProcessorProfileResource($imageProcessorProfile),
        ]);
    }

    /** Форма редактирования */
    public function edit(ImageProcessorProfile $imageProcessorProfile): Response
    {
        $imageProcessorProfile
            ->load(['variants' => fn ($query) => $query->ordered()])
            ->loadCount('variants');

        return Inertia::render('Admin/System/ImageProcessor/Profile/Edit', [
            'profile' => new ImageProcessorProfileResource($imageProcessorProfile),
        ]);
    }

    /** Обновление профиля */
    public function update(
        ImageProcessorProfileRequest $request,
        ImageProcessorProfile $imageProcessorProfile
    ): RedirectResponse {
        DB::transaction(function () use ($request, $imageProcessorProfile) {
            $imageProcessorProfile->update($request->validated());
        });

        return redirect()
            ->route('admin.imageProcessorProfiles.index')
            ->with('success', 'Профиль обработки изображений обновлён.');
    }

    /** Удаление профиля */
    public function destroy(ImageProcessorProfile $imageProcessorProfile): RedirectResponse
    {
        DB::transaction(function () use ($imageProcessorProfile) {
            $imageProcessorProfile->delete();
        });

        return redirect()
            ->route('admin.imageProcessorProfiles.index')
            ->with('success', 'Профиль обработки изображений удалён.');
    }

    /** Обновление активности профиля */
    public function updateActivity(
        UpdateActivityRequest $request,
        ImageProcessorProfile $imageProcessorProfile
    ): RedirectResponse {
        $imageProcessorProfile->update([
            'activity' => $request->validated('activity'),
        ]);

        return back()->with('success', 'Активность профиля обновлена.');
    }

    /** Массовое обновление активности профилей */
    public function bulkUpdateActivity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:image_processor_profiles,id'],
            'activity' => ['required', 'boolean'],
        ]);

        ImageProcessorProfile::whereIn('id', $validated['ids'])
            ->update(['activity' => $validated['activity']]);

        return back()->with('success', 'Активность профилей массово обновлена.');
    }

    /** Обновление сортировки профиля */
    public function updateSort(
        UpdateSortEntityRequest $request,
        ImageProcessorProfile $imageProcessorProfile
    ): RedirectResponse {
        $imageProcessorProfile->update([
            'sort' => $request->validated('sort'),
        ]);

        return back()->with('success', 'Сортировка профиля обновлена.');
    }

    /** Массовое обновление сортировки профилей */
    public function updateSortBulk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'profiles' => ['required', 'array'],
            'profiles.*.id' => ['required', 'integer', 'exists:image_processor_profiles,id'],
            'profiles.*.sort' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['profiles'] as $profile) {
                ImageProcessorProfile::where('id', $profile['id'])
                    ->update(['sort' => $profile['sort']]);
            }
        });

        return back()->with('success', 'Сортировка профилей массово обновлена.');
    }

    /** Базовый запрос для списка профилей */
    private function indexQuery(): Builder
    {
        return ImageProcessorProfile::query()
            ->withCount('variants');
    }

    /** Получение профилей по активному режиму обработки */
    private function getIndexProfiles(
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = '',
    ) {
        $query = $this->indexQuery();

        if ($useServerProcessing) {
            return $query
                ->search($search)
                ->sortByParam($sort)
                ->paginate($perPage)
                ->withQueryString();
        }

        return $query
            ->sortByParam($sort)
            ->get();
    }
}
