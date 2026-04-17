<?php

namespace App\Http\Controllers\Admin\School\InstructorProfile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\InstructorProfile\InstructorProfileRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\School\InstructorProfile\InstructorProfileResource;
use App\Http\Resources\Admin\System\User\UserResource;
use App\Models\Admin\School\InstructorProfile\InstructorProfile;
use App\Models\Admin\School\InstructorProfile\InstructorProfileImage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
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
 * @version 1.1 (Улучшен с RMB, транзакциями, Form Requests)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see InstructorProfile Модель
 * @see InstructorProfileRequest Запрос для создания/обновления
 */
class InstructorProfileController extends Controller
{
    /**
     * Разрешённые локали.
     *
     * @var array|string[]
     */
    protected array $availableLocales = ['ru', 'en', 'kk'];

    /**
     *  Отображение списка.
     *  Загружает пагинированный список с сортировкой по настройкам.
     *  Передает данные для отображения и настройки пагинации/сортировки.
     *  Пагинация и сортировка выполняются на фронтенде.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        // Настройки админки из конфигурации (с безопасными дефолтами)
        $adminCountInstructors = (int) config('site_settings.AdminCountInstructors', 10);
        $adminSortInstructors  = config('site_settings.AdminSortInstructors', 'idDesc');

        $currentLocale = $request->query('locale', config('app.fallback_locale', 'ru'));

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        $instructorProfiles = collect();
        $instructorProfilesCount = 0;

        try {
            $instructorProfiles = InstructorProfile::query()
                ->byLocale($currentLocale)
                ->with([
                    'user:id,name',
                    'images' => fn($q) => $q->orderBy('order'),
                    'courses' => fn($q) => $q
                        ->select('id', 'instructor_profile_id', 'title', 'slug')
                        ->orderBy('title'),
                ])
                ->withCount('courses')
                ->orderBy('sort')
                ->get();

            $instructorProfilesCount = InstructorProfile::query()
                ->byLocale($currentLocale)
                ->count();

        } catch (Throwable $e) {
            Log::error("Ошибка загрузки инструкторов (locale: {$currentLocale}): ".$e->getMessage(), ['exception' => $e]);
            session()->flash('error', __('admin/controllers.index_error'));
        }

        return Inertia::render('Admin/School/InstructorProfiles/Index', [
            'instructorProfiles'      => InstructorProfileResource::collection($instructorProfiles),
            'instructorProfilesCount' => $instructorProfilesCount,
            'adminCountInstructors'   => $adminCountInstructors,
            'adminSortInstructors'    => $adminSortInstructors,
            'currentLocale'           => $currentLocale,
            'availableLocales'        => $this->availableLocales,
        ]);
    }

    /**
     * Отображение формы создания новой сущности.
     * Передает список инструкторов для выбора.
     *
     * @return Response
     */
    public function create(): Response
    {
        // TODO: Проверка прав $this->authorize('create-instructor-profiles', InstructorProfile::class);

        $users = User::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('Admin/School/InstructorProfiles/Create', [
            'users' => UserResource::collection($users),
        ]);
    }

    /**
     * Сохранение новой сущности в базе данных.
     * Использует Request для валидации и авторизации.
     * Синхронизирует связанные изображения, инструкторы.
     *
     * @param InstructorProfileRequest $request
     * @return RedirectResponse
     */
    public function store(InstructorProfileRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $imagesData = $data['images'] ?? [];
        unset($data['images'], $data['deletedImages']); // на create deletedImages, скорее всего, нет, но не мешает

        try {
            DB::beginTransaction();

            // user_id уже в $data и есть в $fillable
            $instructorProfile = InstructorProfile::create($data);

            // Обработка изображений
            $imageSyncData = [];
            $imageIndex    = 0;

            foreach ($imagesData as $imageData) {
                $fileKey = "images.{$imageIndex}.file";

                if ($request->hasFile($fileKey)) {
                    $image = InstructorProfileImage::create([
                        'order'   => $imageData['order']   ?? 0,
                        'alt'     => $imageData['alt']     ?? '',
                        'caption' => $imageData['caption'] ?? '',
                    ]);

                    try {
                        $file = $request->file($fileKey);

                        if ($file->isValid()) {
                            $image
                                ->addMedia($file)
                                ->toMediaCollection('images');

                            $imageSyncData[$image->id] = ['order' => $image->order];
                        } else {
                            Log::warning("Недопустимый файл изображения с индексом {$imageIndex}
                            для профиля инструктора {$instructorProfile->id}", [
                                'fileKey' => $fileKey,
                                'error'   => $file->getErrorMessage(),
                            ]);
                            $image->delete();
                            continue;
                        }
                    } catch (Throwable $e) {
                        Log::error("Ошибка Spatie media-library Инструктора {$instructorProfile->id},
                        индекс изображения - {$imageIndex}: {$e->getMessage()}", [
                            'trace' => $e->getTraceAsString(),
                        ]);
                        $image->delete();
                        continue;
                    }
                }

                $imageIndex++;
            }

            $instructorProfile->images()->sync($imageSyncData);

            DB::commit();

            Log::info('Инструктор успешно создан', [
                'id'    => $instructorProfile->id,
                'title' => $instructorProfile->title,
            ]);

            return redirect()->route('admin.instructorProfiles.index')
                ->with('success', __('admin/controllers.created_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при создании Инструктора: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()
                ->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * Отображение формы редактирования.
     * Использует Route Model Binding для получения модели.
     *
     * @param InstructorProfile $instructorProfile
     * @return Response
     */
    public function edit(InstructorProfile $instructorProfile): Response
    {
        // TODO: Проверка прав $this->authorize('update-instructor-profiles', $instructorProfile);

        // Загружаем все необходимые связи
        $instructorProfile->load(['images' => fn($q) => $q->orderBy('order', 'asc')]);

        /// Список пользователей для мультиселекта/селекта (назначение пользователя профилю)
        $users = User::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // Грузаем связанные данные для конкретного профиля
        $instructorProfile->loadMissing([
            'user:id,name',
            'images' => fn($q) => $q->orderBy('order'),
        ]);

        return Inertia::render('Admin/School/InstructorProfiles/Edit', [
            'instructorProfile' => new InstructorProfileResource($instructorProfile),
            'users'             => $users,
        ]);
    }


    /**
     * Обновление в базе данных.
     * Использует Request и Route Model Binding.
     * Синхронизирует связанные изображения, если они переданы.
     *
     * @param InstructorProfileRequest $request
     * @param InstructorProfile $instructorProfile
     * @return RedirectResponse
     */
    public function update(InstructorProfileRequest $request, InstructorProfile $instructorProfile): RedirectResponse
    {
        $data = $request->validated();

        $imagesData      = $data['images']        ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];

        unset(
            $data['images'],
            $data['deletedImages'],
            $data['_method']
        );

        try {
            DB::beginTransaction();

            // 1) Удаляем выбранные изображения
            if (!empty($deletedImageIds)) {
                $instructorProfile->images()->detach($deletedImageIds);
                $this->deleteImages($deletedImageIds);
            }

            // 2) Обновляем базовые поля профиля (включая user_id)
            $instructorProfile->update($data);

            // 3) Обработка изображений — как у тебя уже написано
            $syncData = [];
            foreach ($imagesData as $index => $imageData) {
                $fileKey = "images.{$index}.file";

                if (!empty($imageData['id'])) {
                    $img = InstructorProfileImage::find($imageData['id']);

                    if ($img && !in_array($img->id, $deletedImageIds, true)) {
                        $img->update([
                            'order'   => $imageData['order']   ?? $img->order,
                            'alt'     => $imageData['alt']     ?? $img->alt,
                            'caption' => $imageData['caption'] ?? $img->caption,
                        ]);

                        if ($request->hasFile($fileKey)) {
                            $img->clearMediaCollection('images');
                            $img->addMedia($request->file($fileKey))
                                ->toMediaCollection('images');
                        }

                        $syncData[$img->id] = ['order' => $img->order];
                    }

                } elseif ($request->hasFile($fileKey)) {
                    $new = InstructorProfileImage::create([
                        'order'   => $imageData['order']   ?? 0,
                        'alt'     => $imageData['alt']     ?? '',
                        'caption' => $imageData['caption'] ?? '',
                    ]);

                    $new->addMedia($request->file($fileKey))
                        ->toMediaCollection('images');

                    $syncData[$new->id] = ['order' => $new->order];
                }
            }

            $instructorProfile->images()->sync($syncData);

            DB::commit();

            Log::info('Инструктор обновлен: ', [
                'id'    => $instructorProfile->id,
                'title' => $instructorProfile->title,
            ]);

            return redirect()->route('admin.instructorProfiles.index')
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при обновлении Инструктора ID {$instructorProfile->id}: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()
                ->with('error', __('admin/controllers.updated_error'));
        }
    }

    /**
     * Удаление указанной сущности вместе с изображениями.
     * Использует Route Model Binding. Связи удаляются каскадно.
     *
     * @param InstructorProfile $instructorProfile
     * @return RedirectResponse
     */
    public function destroy(InstructorProfile $instructorProfile): RedirectResponse
    {
        // TODO: Проверка прав $this->authorize('delete-instructor-profiles', $instructorProfile);

        try {
            DB::beginTransaction();
            // Используем приватный метод deleteImages
            $this->deleteImages($instructorProfile->images()->pluck('id')->toArray());
            $instructorProfile->delete();
            DB::commit();

            Log::info('Инструктор удалена: ID ' . $instructorProfile->id);
            return redirect()->route('admin.instructorProfiles.index')
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при удалении Инструктора ID {$instructorProfile->id}: " . $e->getMessage());
            return back()
                ->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /**
     * Обновление статуса активности.
     * Использует Route Model Binding и UpdateActivityRequest.
     *
     * @param UpdateActivityRequest $request
     * @param InstructorProfile $instructorProfile
     * @return RedirectResponse
     */
    public function updateActivity(UpdateActivityRequest $request, InstructorProfile $instructorProfile): RedirectResponse
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();
            $instructorProfile->activity = $validated['activity'];
            $instructorProfile->save();
            DB::commit();

            Log::info("Обновлено activity инструктора ID {$instructorProfile->id} на {$instructorProfile->activity}");
            return back()->with('success', __('admin/controllers.activity_updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка обновления активности инструктора (ID: {$instructorProfile->id}): ".$e->getMessage(), ['exception' => $e]);
            return back()->with('error', __('admin/controllers.activity_updated_error'));
        }
    }

    /**
     * Обновление статуса активности массово
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids'      => 'required|array',
            'ids.*'    => 'integer|exists:instructor_profiles,id',
            'activity' => 'required|boolean',
        ]);

        $ids = $validated['ids'];
        $activity = $validated['activity'];

        if (empty($ids)) {
            $message = __('admin/controllers.bulk_updated_activity_no_selection');
            if ($request->expectsJson()) return response()->json(['message' => $message], 400);
            return back()->with('warning', $message);
        }

        try {
            $updatedCount = InstructorProfile::whereIn('id', $ids)->update(['activity' => $activity]);
            $message = __('admin/controllers.bulk_activity_updated_success');
            Log::info($message, ['ids' => $ids, 'activity' => $activity]);

            if ($request->expectsJson()) {
                return response()->json(['message' => $message, 'updatedCount' => $updatedCount]);
            }
            return back()->with('success', $message);

        } catch (Throwable $e) {
            Log::error("Ошибка при массовом обновлении активности инструкторов: ".$e->getMessage(), ['exception' => $e, 'ids' => $ids]);
            $errorMessage = __('admin/controllers.bulk_activity_updated_error');

            if ($request->expectsJson()) {
                return response()->json(['message' => $errorMessage], 500);
            }
            return back()->with('error', $errorMessage);
        }
    }

    /**
     * Обновление значения сортировки для одной сущности.
     * Использует Route Model Binding и UpdateSortEntityRequest.
     *
     * @param UpdateSortEntityRequest $request
     * @param InstructorProfile $instructorProfile
     * @return RedirectResponse
     */
    public function updateSort(UpdateSortEntityRequest $request, InstructorProfile $instructorProfile): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $originalSort = $instructorProfile->sort;
            $instructorProfile->sort = (int)$validated['sort'];
            $instructorProfile->save();

            Log::info("Сортировка инструктора '{$instructorProfile->title}' (ID: {$instructorProfile->id}) изменена с {$originalSort} на {$instructorProfile->sort}");
            return back()->with('success', __('admin/controllers.sort_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении сортировки инструктора (ID: {$instructorProfile->id}): ".$e->getMessage(), ['exception' => $e]);
            return back()->with('error', __('admin/controllers.sort_updated_error'));
        }
    }

    /**
     * Массовое обновление сортировки на основе переданного порядка ID.
     * Принимает массив объектов вида `[{id: 1, sort: 10}, {id: 5, sort: 20}]`.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'instructorProfiles'        => ['required','array'],
            'instructorProfiles.*.id'   => ['required','integer','exists:instructor_profiles,id'],
            'instructorProfiles.*.sort' => ['required','integer','min:0'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                foreach ($validated['instructorProfiles'] as $row) {
                    InstructorProfile::whereKey($row['id'])->update([
                        'sort' => (int) $row['sort'],
                    ]);
                }
            });

            $msg = __('admin/controllers.bulk_sort_updated_success');

            return $request->expectsJson()
                ? response()->json(['message' => $msg])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error("Ошибка массового обновления сортировки инструкторов: ".$e->getMessage(), ['ex' => $e]);
            $msg = __('admin/controllers.bulk_sort_updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Приватный метод удаления изображений (для Spatie)
     *
     * @param array $imageIds
     * @return void
     */
    private function deleteImages(array $imageIds): void
    {
        if (empty($imageIds)) return;
        $imagesToDelete = InstructorProfileImage::whereIn('id', $imageIds)->get();
        foreach ($imagesToDelete as $image) {
            $image->clearMediaCollection('images');
            $image->delete();
        }
        Log::info('Удалены записи InstructorProfileImage и их медиа: ', ['image_ids' => $imageIds]);
    }
}
