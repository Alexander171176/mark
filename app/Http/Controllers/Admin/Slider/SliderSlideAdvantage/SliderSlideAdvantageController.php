<?php

namespace App\Http\Controllers\Admin\Slider\SliderSlideAdvantage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Slider\SliderSlideAdvantage\SliderSlideAdvantageRequest;
use App\Http\Resources\Admin\Slider\SliderSlideAdvantage\SliderSlideAdvantageResource;
use App\Http\Resources\Admin\Slider\SliderSlideAdvantage\SliderSlideAdvantageSharedResource;
use App\Models\Admin\Slider\SliderSlideAdvantage\SliderSlideAdvantage;
use App\Services\Admin\ProcessingModeService;
use App\Services\Admin\Slider\SliderSlideAdvantageService;
use App\Services\SiteSettings\AdminSettingsService;
use App\Traits\Admin\Slider\HasSliderActivityTrait;
use App\Traits\Admin\Slider\HasSliderAdminCoreTrait;
use App\Traits\Admin\Slider\HasSliderModerationTrait;
use App\Traits\Admin\Slider\HasSliderSortingTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Административный CRUD общего справочника преимуществ слайдов.
 *
 * Поддерживает режимы frontend / server / auto, локали, поиск,
 * сортировку, пагинацию, активность, модерацию и массовые операции.
 * Преимущества не имеют user_id: доступ контролируется middleware/Policy.
 */
class SliderSlideAdvantageController extends Controller
{
    use HasSliderAdminCoreTrait;
    use HasSliderActivityTrait;
    use HasSliderSortingTrait;
    use HasSliderModerationTrait;

    protected string $modelClass = SliderSlideAdvantage::class;

    public function __construct(
        private readonly SliderSlideAdvantageService $service
    ) {
    }

    /** Общий справочник без ограничения по владельцу. */
    protected function baseQuery(): Builder
    {
        abort_unless(auth()->check(), 403);

        return SliderSlideAdvantage::query();
    }

    /**
     * Список преимуществ. В режиме frontend данные загружаются целиком;
     * в server — поиск, сортировка и пагинация выполняются в БД.
     */
    public function index(Request $request): Response
    {
        $locale = $this->resolveLocale($request);
        $settings = app(AdminSettingsService::class);

        $perPage = max(1, $settings->int('adminSliderSlideAdvantagesPerPage', 10));
        $defaultSort = $settings->string('adminSliderSlideAdvantagesDefaultSort', 'idAsc');
        $processingMode = $settings->string('adminSliderSlideAdvantagesProcessingMode', 'frontend');
        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $advantagesCount = $this->baseQuery()->count();
        $useServerProcessing = app(ProcessingModeService::class)->shouldUseServer(
            $processingMode,
            $advantagesCount,
            300
        );

        $props = [
            'currentLocale' => $locale,
            'availableLocales' => $this->availableLocales(),
            'isAdmin' => $this->isAdmin(),
            'useServerProcessing' => $useServerProcessing,
            'adminSliderSlideAdvantagesPerPage' => $perPage,
            'adminSliderSlideAdvantagesDefaultSort' => $defaultSort,
            'adminSliderSlideAdvantagesProcessingMode' => $processingMode,
            'advantagesCount' => $advantagesCount,
            'sortParam' => $sortParam,
            'search' => $search,
        ];

        try {
            $query = $this->indexQuery($locale);

            $advantages = $useServerProcessing
                ? $query->search($search, $locale)
                    ->sortByParam($sortParam, $locale)
                    ->paginate($perPage)
                    ->withQueryString()
                : $query->ordered()->get();

            return Inertia::render('Admin/Slider/SliderSlideAdvantages/Index', [
                ...$props,
                'advantages' => SliderSlideAdvantageSharedResource::collection($advantages),
            ]);
        } catch (Throwable $exception) {
            Log::error('Ошибка загрузки списка преимуществ слайдов.', [
                'exception' => $exception,
            ]);

            return Inertia::render('Admin/Slider/SliderSlideAdvantages/Index', [
                ...$props,
                'advantages' => [],
                'error' => 'Ошибка загрузки преимуществ.',
            ]);
        }
    }

    /** Минимальные связи для таблицы: текущий перевод и fallback, без N+1. */
    private function indexQuery(string $locale): Builder
    {
        $locales = $this->resourceLocales($locale);

        return $this->baseQuery()
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', $locales),
            ])
            ->withCount('slides');
    }

    /** Все переводы для редактирования и просмотра. */
    private function fullQuery(): Builder
    {
        return $this->baseQuery()
            ->with(['translations', 'moderator:id,name,email'])
            ->withCount('slides');
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Admin/Slider/SliderSlideAdvantages/Create', [
            'currentLocale' => $this->resolveLocale($request),
            'availableLocales' => $this->availableLocales(),
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    public function store(SliderSlideAdvantageRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('admin.sliderSlideAdvantages.index')
            ->with('success', 'Преимущество успешно создано.');
    }

    public function show(Request $request, int $sliderSlideAdvantage): Response
    {
        $locale = $this->resolveLocale($request);
        $advantage = $this->fullQuery()->findOrFail($sliderSlideAdvantage);

        return Inertia::render('Admin/Slider/SliderSlideAdvantages/Show', [
            'advantage' => new SliderSlideAdvantageResource($advantage),
            'currentLocale' => $locale,
            'availableLocales' => $this->availableLocales(),
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    public function edit(Request $request, int $sliderSlideAdvantage): Response
    {
        $locale = $this->resolveLocale($request);
        $advantage = $this->fullQuery()->findOrFail($sliderSlideAdvantage);

        return Inertia::render('Admin/Slider/SliderSlideAdvantages/Edit', [
            'advantage' => new SliderSlideAdvantageResource($advantage),
            'currentLocale' => $locale,
            'availableLocales' => $this->availableLocales(),
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    public function update(
        SliderSlideAdvantageRequest $request,
        int $sliderSlideAdvantage
    ): RedirectResponse {
        $advantage = $this->baseQuery()->findOrFail($sliderSlideAdvantage);
        $this->service->update($advantage, $request->validated());

        return redirect()->route('admin.sliderSlideAdvantages.index')
            ->with('success', 'Преимущество успешно обновлено.');
    }

    /** Удаление и проверка привязок к слайдам остаются в сервисе. */
    public function destroy(int $sliderSlideAdvantage): RedirectResponse
    {
        $advantage = $this->baseQuery()->findOrFail($sliderSlideAdvantage);
        $this->service->delete($advantage);

        return redirect()->route('admin.sliderSlideAdvantages.index')
            ->with('success', 'Преимущество удалено.');
    }

    /** Массовое удаление с предварительной проверкой всех идентификаторов. */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:500'],
            'ids.*' => ['required', 'integer', 'min:1', 'distinct'],
        ]);

        $ids = array_map('intval', $validated['ids']);
        $allowedCount = $this->baseQuery()->whereKey($ids)->count();

        if ($allowedCount !== count($ids)) {
            throw ValidationException::withMessages([
                'ids' => 'Часть преимуществ недоступна для удаления.',
            ]);
        }

        $deleted = $this->service->deleteMany($ids);

        return back()->with('success', "Удалено преимуществ: {$deleted}.");
    }
}
