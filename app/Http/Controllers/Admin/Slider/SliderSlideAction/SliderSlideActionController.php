<?php

namespace App\Http\Controllers\Admin\Slider\SliderSlideAction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Slider\SliderSlideAction\SliderSlideActionRequest;
use App\Http\Resources\Admin\Slider\SliderSlideAction\SliderSlideActionResource;
use App\Http\Resources\Admin\Slider\SliderSlideAction\SliderSlideActionSharedResource;
use App\Models\Admin\Slider\SliderSlideAction\SliderSlideAction;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use App\Traits\Admin\Slider\HasSliderActivityTrait;
use App\Traits\Admin\Slider\HasSliderAdminCoreTrait;
use App\Traits\Admin\Slider\HasSliderModerationTrait;
use App\Traits\Admin\Slider\HasSliderSortingTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * CRUD глобального справочника действий (кнопок) слайдов.
 * Свойства размещения в конкретном слайде управляются отдельно через pivot.
 */
class SliderSlideActionController extends Controller
{
    use HasSliderAdminCoreTrait;
    use HasSliderActivityTrait;
    use HasSliderSortingTrait;
    use HasSliderModerationTrait;

    protected string $modelClass = SliderSlideAction::class;

    /** Действия не имеют владельца user_id: доступ ограничивает middleware / Policy. */
    protected function baseQuery(): Builder
    {
        abort_unless(auth()->check(), 403);

        return SliderSlideAction::query();
    }

    public function index(Request $request): Response
    {
        $locale = $this->resolveLocale($request);
        $settings = app(AdminSettingsService::class);

        $perPage = max(1, $settings->int('adminSliderSlideActionsPerPage', 10));
        $defaultSort = $settings->string('adminSliderSlideActionsDefaultSort', 'idAsc');
        $processingMode = $settings->string('adminSliderSlideActionsProcessingMode', 'frontend');
        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $actionsCount = $this->baseQuery()->count();
        $useServerProcessing = app(ProcessingModeService::class)->shouldUseServer(
            $processingMode,
            $actionsCount,
            300
        );

        $props = [
            'currentLocale' => $locale,
            'availableLocales' => $this->availableLocales(),
            'isAdmin' => $this->isAdmin(),
            'useServerProcessing' => $useServerProcessing,
            'adminSliderSlideActionsPerPage' => $perPage,
            'adminSliderSlideActionsDefaultSort' => $defaultSort,
            'adminSliderSlideActionsProcessingMode' => $processingMode,
            'actionsCount' => $actionsCount,
            'sortParam' => $sortParam,
            'search' => $search,
        ];

        try {
            $query = $this->indexQuery($locale);

            $actions = $useServerProcessing
                ? $query->search($search, $locale)
                    ->sortByParam($sortParam, $locale)
                    ->paginate($perPage)
                    ->withQueryString()
                : $query->ordered()->get();

            return Inertia::render('Admin/Slider/SliderSlideActions/Index', [
                ...$props,
                'actions' => SliderSlideActionSharedResource::collection($actions),
            ]);
        } catch (Throwable $exception) {
            Log::error('Ошибка загрузки списка действий слайдов.', [
                'exception' => $exception,
            ]);

            return Inertia::render('Admin/Slider/SliderSlideActions/Index', [
                ...$props,
                'actions' => [],
                'error' => 'Ошибка загрузки действий слайдов.',
            ]);
        }
    }

    /** Текущая локаль и fallback; без N+1 при формировании списка. */
    private function indexQuery(string $locale): Builder
    {
        $locales = $this->resourceLocales($locale);

        return $this->baseQuery()
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', $locales),
            ])
            ->withCount('slides');
    }

    /** Все переводы и данные модерации для подробного просмотра и редактирования. */
    private function fullQuery(): Builder
    {
        return $this->baseQuery()
            ->with(['translations', 'moderator:id,name,email'])
            ->withCount('slides');
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Admin/Slider/SliderSlideActions/Create', [
            'currentLocale' => $this->resolveLocale($request),
            'availableLocales' => $this->availableLocales(),
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    public function store(SliderSlideActionRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $data = $request->validated();
            $translations = $data['translations'];
            unset($data['translations']);

            // Модерация назначается административным действием, а не обычной формой.
            $data = $this->withoutModerationInput($data);
            $action = $this->baseQuery()->create($data);
            $this->syncTranslations($action, $translations);
        });

        return redirect()->route('admin.sliderSlideActions.index')
            ->with('success', 'Действие слайда успешно создано.');
    }

    public function show(Request $request, int $sliderSlideAction): Response
    {
        $action = $this->fullQuery()->findOrFail($sliderSlideAction);

        return Inertia::render('Admin/Slider/SliderSlideActions/Show', [
            'action' => new SliderSlideActionResource($action),
            'currentLocale' => $this->resolveLocale($request),
            'availableLocales' => $this->availableLocales(),
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    public function edit(Request $request, int $sliderSlideAction): Response
    {
        $action = $this->fullQuery()->findOrFail($sliderSlideAction);

        return Inertia::render('Admin/Slider/SliderSlideActions/Edit', [
            'action' => new SliderSlideActionResource($action),
            'currentLocale' => $this->resolveLocale($request),
            'availableLocales' => $this->availableLocales(),
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    public function update(
        SliderSlideActionRequest $request,
        int $sliderSlideAction
    ): RedirectResponse {
        DB::transaction(function () use ($request, $sliderSlideAction): void {
            $action = $this->baseQuery()->lockForUpdate()->findOrFail($sliderSlideAction);
            $data = $request->validated();
            $translations = $data['translations'];
            unset($data['translations']);

            $action->update($this->withoutModerationInput($data));
            $this->syncTranslations($action, $translations);
        });

        return redirect()->route('admin.sliderSlideActions.index')
            ->with('success', 'Действие слайда успешно обновлено.');
    }

    /** Не удаляем общую кнопку, пока она используется хотя бы одним слайдом. */
    public function destroy(int $sliderSlideAction): RedirectResponse
    {
        DB::transaction(function () use ($sliderSlideAction): void {
            $action = $this->baseQuery()->lockForUpdate()->findOrFail($sliderSlideAction);
            $this->ensureNotAttached($action);
            $action->translations()->delete();
            $action->delete();
        });

        return redirect()->route('admin.sliderSlideActions.index')
            ->with('success', 'Действие слайда удалено.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:500'],
            'ids.*' => ['required', 'integer', 'min:1', 'distinct'],
        ]);

        $ids = array_map('intval', $validated['ids']);

        $deleted = DB::transaction(function () use ($ids): int {
            $actions = $this->baseQuery()
                ->whereKey($ids)
                ->lockForUpdate()
                ->get();

            if ($actions->count() !== count($ids)) {
                throw ValidationException::withMessages([
                    'ids' => 'Часть действий недоступна для удаления.',
                ]);
            }

            if ($this->baseQuery()->whereKey($ids)->whereHas('slides')->exists()) {
                throw ValidationException::withMessages([
                    'ids' => 'Нельзя удалить действия, которые используются в слайдах.',
                ]);
            }

            foreach ($actions as $action) {
                $action->translations()->delete();
                $action->delete();
            }

            return $actions->count();
        });

        return back()->with('success', "Удалено действий: {$deleted}.");
    }

    /** Не доверяем полям модерации из обычного CRUD-запроса. */
    private function withoutModerationInput(array $data): array
    {
        unset(
            $data['moderation_status'],
            $data['moderated_by'],
            $data['moderated_at'],
            $data['moderation_note']
        );

        return $data;
    }

    /**
     * Обновляем только переданные языки. Пустой перевод удаляется;
     * языки, отсутствующие в запросе, сохраняются без изменений.
     */
    private function syncTranslations(SliderSlideAction $action, array $translations): void
    {
        foreach ($translations as $locale => $translation) {
            if (! is_array($translation)) {
                continue;
            }

            $fields = [
                'label' => $translation['label'] ?? null,
                'title' => $translation['title'] ?? null,
                'aria_label' => $translation['aria_label'] ?? null,
            ];

            if (blank($fields['label']) && blank($fields['title']) && blank($fields['aria_label'])) {
                $action->translations()->where('locale', $locale)->delete();
                continue;
            }

            $action->translations()->updateOrCreate(
                ['locale' => $locale],
                $fields
            );
        }
    }

    /**
     * @throws ValidationException
     */
    private function ensureNotAttached(SliderSlideAction $action): void
    {
        if ($action->slides()->exists()) {
            throw ValidationException::withMessages([
                'action' => 'Нельзя удалить действие, которое используется в слайдах.',
            ]);
        }
    }
}
