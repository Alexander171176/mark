<?php

namespace App\Http\Controllers\Admin\School\Hashtag;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\Hashtag\HashtagRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\School\Hashtag\HashtagResource;
use App\Models\Admin\School\Hashtag\Hashtag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления полиморфными хештегами (Hashtags) в административной панели.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (одиночное и массовое)
 *
 * @version 1.1 (Улучшен с RMB, транзакциями, Form Requests)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see Hashtag
 * @see HashtagRequest
 */
class HashtagController extends Controller
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
        // Настройки админки (по аналогии с другими сущностями)
        $adminCountHashtags = (int) config('site_settings.AdminCountHashtags', 20);
        $adminSortHashtags  = config('site_settings.AdminSortHashtags', 'idDesc');

        $currentLocale = $request->query('locale', config('app.fallback_locale', 'ru'));

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        $hashtags      = collect();
        $hashtagsCount = 0;

        try {
            $hashtags = Hashtag::query()
                ->where('locale', $currentLocale)
                ->withCount(['courses', 'lessons'])
                ->orderBy('sort')
                ->orderBy('name')
                ->get();

            $hashtagsCount = Hashtag::query()
                ->where('locale', $currentLocale)
                ->count();

        } catch (Throwable $e) {
            Log::error("Ошибка загрузки Hashtag (locale: {$currentLocale}): ".$e->getMessage(), [
                'exception' => $e,
            ]);
            session()->flash('error', __('admin/controllers.index_error'));
        }

        return Inertia::render('Admin/School/Hashtags/Index', [
            'hashtags'          => HashtagResource::collection($hashtags),
            'hashtagsCount'     => $hashtagsCount,
            'adminCountHashtags'=> $adminCountHashtags,
            'adminSortHashtags' => $adminSortHashtags,
            'currentLocale'         => $currentLocale,
            'availableLocales'      => $this->availableLocales,
        ]);
    }

    /**
     * Форма создания хештега.
     *
     * @return Response
     */
    public function create(): Response
    {
        // TODO: $this->authorize('create-hashtags', Hashtag::class);

        return Inertia::render('Admin/School/Hashtags/Create');
    }

    /**
     * Сохранение нового хештега.
     *
     * - HashtagRequest: валидация + нормализация полей
     *
     * @param HashtagRequest $request
     * @return RedirectResponse
     */
    public function store(HashtagRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $tag = Hashtag::create($data);

            DB::commit();

            Log::info('Hashtag создан', [
                'id'    => $tag->id,
                'name'  => $tag->name,
                'slug'  => $tag->slug,
                'locale'=> $tag->locale,
            ]);

            return redirect()
                ->route('admin.hashtags.index', ['locale' => $tag->locale])
                ->with('success', __('admin/controllers.created_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при создании Hashtag: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * Форма редактирования тега.
     *
     * @param Hashtag $hashtag
     * @return Response
     */
    public function edit(Hashtag $hashtag): Response
    {
        // TODO: $this->authorize('update-hashtags', $hashtag);

        // При необходимости можно подгрузить связи:
        $hashtag->loadCount(['courses', 'lessons']);

        return Inertia::render('Admin/School/Hashtags/Edit', [
            'hashtag'      => new HashtagResource($hashtag),
        ]);
    }

    /**
     * Обновление тега.
     *
     * @param HashtagRequest $request
     * @param Hashtag $hashtag
     * @return RedirectResponse
     */
    public function update(HashtagRequest $request, Hashtag $hashtag): RedirectResponse
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $hashtag->update($data);

            DB::commit();

            Log::info('Hashtag обновлён', [
                'id'    => $hashtag->id,
                'name'  => $hashtag->name,
                'slug'  => $hashtag->slug,
                'locale'=> $hashtag->locale,
            ]);

            return redirect()
                ->route('admin.hashtags.index', ['locale' => $hashtag->locale])
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при обновлении Hashtag ID {$hashtag->id}: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', __('admin/controllers.updated_error'));
        }
    }

    /**
     * Удаление одного тега.
     *
     * @param Hashtag $hashtag
     * @return RedirectResponse
     */
    public function destroy(Hashtag $hashtag): RedirectResponse
    {
        // TODO: $this->authorize('delete-hashtags', $hashtag);

        try {
            DB::beginTransaction();

            $hashtag->delete();

            DB::commit();

            Log::info('Hashtag удалён', [
                'id'    => $hashtag->id,
                'name'  => $hashtag->name,
                'slug'  => $hashtag->slug,
            ]);

            return redirect()
                ->route('admin.hashtags.index')
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при удалении Hashtag ID {$hashtag->id}: ".$e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /**
     * Обновление флага активности одного тега.
     * Маршрут: admin.actions.hashtags.updateActivity
     *
     * @param UpdateActivityRequest $request
     * @param Hashtag $hashtag
     * @return RedirectResponse
     */
    public function updateActivity(UpdateActivityRequest $request, Hashtag $hashtag): RedirectResponse
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $hashtag->activity = $validated['activity'];
            $hashtag->save();

            DB::commit();

            Log::info("Обновлено activity Hashtag ID {$hashtag->id} на {$hashtag->activity}");

            return back()->with('success', __('admin/controllers.activity_updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(
                "Ошибка обновления активности Hashtag (ID: {$hashtag->id}): ".$e->getMessage(),
                ['exception' => $e]
            );

            return back()->with('error', __('admin/controllers.activity_updated_error'));
        }
    }

    /**
     * Массовое обновление активности.
     * Маршрут: admin.actions.hashtags.bulkUpdateActivity
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids'      => 'required|array',
            'ids.*'    => 'integer|exists:hashtags,id',
            'activity' => 'required|boolean',
        ]);

        $ids      = $validated['ids'];
        $activity = $validated['activity'];

        if (empty($ids)) {
            $message = __('admin/controllers.bulk_updated_activity_no_selection');

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 400);
            }

            return back()->with('warning', $message);
        }

        try {
            $updatedCount = Hashtag::whereIn('id', $ids)->update(['activity' => $activity]);

            $message = __('admin/controllers.bulk_activity_updated_success');
            Log::info($message, ['ids' => $ids, 'activity' => $activity]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message'      => $message,
                    'updatedCount' => $updatedCount,
                ]);
            }

            return back()->with('success', $message);

        } catch (Throwable $e) {
            Log::error(
                "Ошибка при массовом обновлении активности Hashtag: ".$e->getMessage(),
                ['exception' => $e, 'ids' => $ids]
            );

            $errorMessage = __('admin/controllers.bulk_activity_updated_error');

            if ($request->expectsJson()) {
                return response()->json(['message' => $errorMessage], 500);
            }

            return back()->with('error', $errorMessage);
        }
    }

    /**
     * Обновление сортировки одного тега.
     * Маршрут: admin.actions.hashtags.updateSort
     *
     * @param UpdateSortEntityRequest $request
     * @param Hashtag $hashtag
     * @return RedirectResponse
     */
    public function updateSort(UpdateSortEntityRequest $request, Hashtag $hashtag): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $originalSort       = $hashtag->sort;
            $hashtag->sort  = (int) $validated['sort'];
            $hashtag->save();

            Log::info("Сортировка Hashtag '{$hashtag->name}' (ID: {$hashtag->id}) изменена с {$originalSort} на {$hashtag->sort}");

            return back()->with('success', __('admin/controllers.sort_updated_success'));

        } catch (Throwable $e) {
            Log::error(
                "Ошибка при обновлении сортировки Hashtag (ID: {$hashtag->id}): ".$e->getMessage(),
                ['exception' => $e]
            );

            return back()->with('error', __('admin/controllers.sort_updated_error'));
        }
    }

    /**
     * Массовое обновление сортировки (Drag&Drop).
     * Принимает массив объектов вида:
     * [{id: 1, sort: 10}, {id: 5, sort: 20}]
     *
     * Маршрут: admin.actions.hashtags.updateSortBulk
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'hashtags'        => ['required', 'array'],
            'hashtags.*.id'   => ['required', 'integer', 'exists:hashtags,id'],
            'hashtags.*.sort' => ['required', 'integer', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                foreach ($validated['hashtags'] as $row) {
                    Hashtag::whereKey($row['id'])->update([
                        'sort' => (int) $row['sort'],
                    ]);
                }
            });

            $msg = __('admin/controllers.bulk_sort_updated_success');

            return $request->expectsJson()
                ? response()->json(['message' => $msg])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error(
                "Ошибка массового обновления сортировки Hashtag: ".$e->getMessage(),
                ['exception' => $e]
            );

            $msg = __('admin/controllers.bulk_sort_updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Массовое удаление тегов.
     * Маршрут: admin.actions.hashtags.bulkDestroy
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        // TODO: $this->authorize('delete-hashtags');

        $validated = $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:hashtags,id',
        ]);

        $hashtagIds = $validated['ids'];
        $count  = count($hashtagIds);

        try {
            DB::beginTransaction();

            Hashtag::whereIn('id', $hashtagIds)->delete();

            DB::commit();

            Log::info('Hashtags удалены массово', ['ids' => $hashtagIds]);

            return redirect()
                ->route('admin.hashtags.index')
                ->with('success', __('admin/controllers.bulk_deleted_success', ['count' => $count]));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error(
                "Ошибка при массовом удалении Hashtags: ".$e->getMessage(),
                ['ids' => $hashtagIds]
            );

            return back()
                ->with('error', __('admin/controllers.bulk_deleted_error'));
        }
    }
}
