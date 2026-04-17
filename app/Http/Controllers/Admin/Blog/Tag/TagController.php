<?php

namespace App\Http\Controllers\Admin\Blog\Tag;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Blog\Tag\TagRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\Blog\Tag\TagResource;
use App\Models\Admin\Blog\Tag\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления Тегами (Blog) в админке.
 *
 * Паттерн:
 * - локали (табы)
 * - CRUD
 * - owner/ограничение “владелец/админ”
 * - activity (single + bulk)
 * - sort + drag&drop (bulk)
 * - moderation (approve/reject) только для admin
 *
 * @version 1.1 (Улучшен с RMB, транзакциями, Form Requests)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see Tag
 * @see TagRequest
 */
class TagController extends Controller
{
    /**
     * Разрешённые локали.
     *
     * @var array|string[]
     */
    protected array $availableLocales = ['ru', 'en', 'kk'];

    /**
     * @param string|null $locale
     * @return string
     */
    private function normalizeLocale(?string $locale): string
    {
        $locale = $locale ?: config('app.fallback_locale', 'ru');
        return in_array($locale, $this->availableLocales, true)
            ? $locale
            : config('app.fallback_locale', 'ru');
    }

    /**
     * Базовый query с ограничением “владелец/админ”.
     * Автор видит только свои. Admin — все.
     *
     * @return Builder
     */
    private function baseQuery(): Builder
    {
        $q = Tag::query();

        $user = auth()->user();
        if ($user && ! $user->hasRole('admin')) {
            $q->where('user_id', $user->id);
        }

        return $q;
    }

    /**
     * Общие селекты
     * + locale-фильтр.
     *
     * @param string|null $locale
     * @return array
     */
    private function sharedSelects(?string $locale = null): array
    {
        $locale = $this->normalizeLocale($locale);

        return [
            'currentLocale'    => $locale,
            'availableLocales' => $this->availableLocales,
        ];
    }

    /**
     * Список статей + локали.
     * GET /admin/tags?locale=ru
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $adminCountTags = (int) config('site_settings.AdminCountTags', 15);
        $adminSortTags  = (string) config('site_settings.AdminSortTags', 'idAsc');

        $currentLocale = $this->normalizeLocale($request->query('locale'));
        if (!in_array($request->query('locale', $currentLocale), $this->availableLocales, true)) {
            session()->flash('warning', __('admin/controllers.index_locale_error'));
        }

        try {
            $tags = $this->baseQuery()
                ->where('locale', $currentLocale)
                ->withCount(['articles'])
                ->with([
                    'owner',
                    'moderator:id,name',
                ])
                ->orderBy('sort')
                ->orderBy('id')
                ->get();

            return Inertia::render('Admin/Blog/Tags/Index', [
                'tags' => TagResource::collection($tags),
                'tagsCount' => $tags->count(),

                'adminCountTags' => $adminCountTags,
                'adminSortTags'  => $adminSortTags,

                'currentLocale'    => $currentLocale,
                'availableLocales' => $this->availableLocales,
            ]);

        } catch (Throwable $e) {
            Log::error("Ошибка загрузки тегов для Index (locale: {$currentLocale}): ".$e->getMessage(), ['exception' => $e]);

            return Inertia::render('Admin/Blog/Tags/Index', [
                'tags' => [],
                'tagsCount' => 0,

                'adminCountTags' => $adminCountTags,
                'adminSortTags'  => $adminSortTags,

                'currentLocale'    => $currentLocale,
                'availableLocales' => $this->availableLocales,

                'error' => __('admin/controllers.index_error'),
            ]);
        }
    }

    /**
     * Создание Тега
     * GET /admin/tags/create?locale=ru
     *
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Blog/Tags/Create');
    }

    /**
     * Сохранение нового Тега
     * POST /admin/tags
     *
     * @param TagRequest $request
     * @return RedirectResponse
     */
    public function store(TagRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = auth()->user();

        // Владелец: принудительно. Admin может передать user_id, но по умолчанию тоже себе.
        if ($user && ! $user->hasRole('admin')) {
            $data['user_id'] = $user->id;
            // автор не должен управлять модерацией
            unset($data['moderation_status'], $data['moderation_note']);
        } else {
            $data['user_id'] = $data['user_id'] ?? ($user?->id);
        }

        try {
            DB::beginTransaction();
            $tag = Tag::create($data);
            DB::commit();

            Log::info('Тег создан', ['id' => $tag->id, 'name' => $tag->name]);
            return redirect()->route('admin.tags.index')
                ->with('success', __('admin/controllers.created_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при создании тега: ".$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * Редактирование Тега
     * GET /admin/tags/{tag}/edit?locale=ru
     *
     * @param Request $request
     * @param int $tag
     * @return Response
     */
    public function edit(Request $request, int $tag): Response
    {
        $tagModel = $this->baseQuery()
            ->with(['owner:id,name,email', 'moderator:id,name'])
            ->findOrFail($tag);

        $targetLocale = $this->normalizeLocale($request->query('locale', $tagModel->locale));

        return Inertia::render('Admin/Blog/Tags/Edit', array_merge(
            ['tag' => new TagResource($tagModel), 'targetLocale' => $targetLocale],
            $this->sharedSelects($tagModel->locale)
        ));
    }

    /**
     * Обновление Тега
     * PUT/PATCH /admin/tags/{tag}
     *
     * @param TagRequest $request
     * @param int $tag
     * @return RedirectResponse
     */
    public function update(TagRequest $request, int $tag): RedirectResponse
    {
        $tag = $this->baseQuery()->findOrFail($tag);

        $data = $request->validated();
        unset($data['_method']);

        $user = auth()->user();

        if ($user && ! $user->hasRole('admin')) {
            // автор не меняет владельца и модерацию
            $data['user_id'] = $user->id;
            unset($data['moderation_status'], $data['moderation_note']);
        }

        try {
            DB::beginTransaction();
            $tag->update($data);
            DB::commit();

            Log::info('Тег обновлен', ['id' => $tag->id, 'name' => $tag->name]);
            return redirect()->route('admin.tags.index')
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при обновлении тега ID {$tag->id}: ".$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->with('error', __('admin/controllers.updated_error'));
        }
    }

    /**
     * Удаление Тега
     * DELETE /admin/tags/{tag}
     *
     * @param int $tag
     * @return RedirectResponse
     */
    public function destroy(int $tag): RedirectResponse
    {
        $tag = $this->baseQuery()->findOrFail($tag);

        try {
            DB::beginTransaction();
            $tag->delete();
            DB::commit();

            Log::info('Тег удален', ['id' => $tag->id]);
            return redirect()->route('admin.tags.index')
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при удалении тега ID {$tag->id}: ".$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /**
     * Массовое удаление
     * DELETE /admin/actions/tags/bulk-destroy
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:tags,id',
        ]);

        $ids = $validated['ids'];
        $count = count($ids);

        // Ограничение по владельцу
        $allowedIds = $this->baseQuery()->whereIn('id', $ids)->pluck('id')->toArray();
        if (count($allowedIds) !== $count) {
            return back()->with('error', __('admin/controllers.bulk_deleted_error'));
        }

        try {
            DB::beginTransaction();
            Tag::whereIn('id', $allowedIds)->delete();
            DB::commit();

            Log::info('Теги удалены', ['ids' => $allowedIds]);
            return redirect()->route('admin.tags.index')
                ->with('success', __('admin/controllers.bulk_deleted_success', ['count' => $count]));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при массовом удалении тегов: ".$e->getMessage(), ['ids' => $allowedIds]);
            return back()->with('error', __('admin/controllers.bulk_deleted_error'));
        }
    }

    /**
     * Обновление активности
     * PUT /admin/actions/tags/{tag}/activity
     *
     * @param UpdateActivityRequest $request
     * @param int $tag
     * @return RedirectResponse
     */
    public function updateActivity(UpdateActivityRequest $request, int $tag): RedirectResponse
    {
        $tag = $this->baseQuery()->findOrFail($tag);
        $validated = $request->validated();

        try {
            DB::beginTransaction();
            $tag->activity = $validated['activity'];
            $tag->save();
            DB::commit();

            return back()->with('success', __('admin/controllers.activity_updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка обновления активности тега ID {$tag->id}: ".$e->getMessage());
            return back()->with('error', __('admin/controllers.activity_updated_error'));
        }
    }

    /**
     * Массовое обновление активности
     * PUT /admin/actions/tags/bulk-activity
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids'      => 'required|array',
            'ids.*'    => 'required|integer|exists:tags,id',
            'activity' => 'required|boolean',
        ]);

        $allowedIds = $this->baseQuery()->whereIn('id', $validated['ids'])->pluck('id')->toArray();
        if (count($allowedIds) !== count($validated['ids'])) {
            return back()->with('error', __('admin/controllers.bulk_activity_updated_error'));
        }

        try {
            DB::beginTransaction();
            Tag::whereIn('id', $allowedIds)->update(['activity' => $validated['activity']]);
            DB::commit();

            return back()->with('success', __('admin/controllers.bulk_activity_updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка массового обновления активности тегов: ".$e->getMessage());
            return back()->with('error', __('admin/controllers.bulk_activity_updated_error'));
        }
    }

    /**
     * Обновление сортировки
     * PUT /admin/actions/tags/{tag}/sort
     *
     * @param UpdateSortEntityRequest $request
     * @param int $tag
     * @return RedirectResponse
     */
    public function updateSort(UpdateSortEntityRequest $request, int $tag): RedirectResponse
    {
        $tag = $this->baseQuery()->findOrFail($tag);
        $validated = $request->validated();

        try {
            $tag->sort = $validated['sort'];
            $tag->save();

            return back()->with('success', __('admin/controllers.sort_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка обновления сортировки тега ID {$tag->id}: ".$e->getMessage());
            return back()->with('error', __('admin/controllers.sort_updated_error'));
        }
    }

    /**
     * Массовое обновление сортировки
     * PUT /admin/actions/tags/update-sort-bulk
     *
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function updateSortBulk(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['nullable','string', Rule::in($this->availableLocales)],

            'items'             => ['required_without:tags','array'],
            'items.*.id'        => ['required_with:items','integer','exists:tags,id'],
            'items.*.sort'      => ['required_with:items','integer','min:0'],

            'tags'              => ['required_without:items','array'],
            'tags.*.id'         => ['required_with:tags','integer','exists:tags,id'],
            'tags.*.sort'       => ['required_with:tags','integer','min:0'],
        ]);

        $data = $validated['items'] ?? $validated['tags'];

        try {
            DB::transaction(function () use ($data, $validated) {
                $ids = array_column($data, 'id');

                $q = $this->baseQuery()->whereIn('id', $ids);
                if (!empty($validated['locale'])) {
                    $q->where('locale', $validated['locale']);
                }

                $allowedIds = $q->pluck('id')->toArray();
                if (count($allowedIds) !== count($ids)) {
                    abort(403);
                }

                foreach ($data as $row) {
                    Tag::whereKey($row['id'])->update(['sort' => (int) $row['sort']]);
                }
            });

            $msg = __('admin/controllers.bulk_sort_updated_success');
            return $request->expectsJson()
                ? response()->json(['message' => $msg])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error("Bulk sort tags error: ".$e->getMessage(), ['exception' => $e]);

            $msg = __('admin/controllers.bulk_sort_updated_error');
            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Одобрение admin
     * PUT/POST /admin/actions/tags/{tag}/approve
     *
     * @param Request $request
     * @param int $tag
     * @return RedirectResponse|JsonResponse
     */
    public function approve(Request $request, int $tag): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('admin')) {
            abort(403);
        }

        $tagModel = Tag::query()->findOrFail($tag);

        $validated = $request->validate([
            'moderation_status' => ['required', 'integer', Rule::in([0, 1, 2])],
            'moderation_note'   => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $tagModel->update([
                'moderation_status' => (int) $validated['moderation_status'],
                'moderation_note'   => $validated['moderation_note'] ?? null,
                'moderated_by'      => $user->id,
                'moderated_at'      => now(),
            ]);

            $msg = __('admin/controllers.updated_success');

            return $request->expectsJson()
                ? response()->json(['message' => $msg, 'tag' => new TagResource($tagModel->load(['owner','moderator']))])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error("Ошибка approve тега {$tagModel->id}: ".$e->getMessage(), ['exception' => $e]);

            $msg = __('admin/controllers.updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }
}
