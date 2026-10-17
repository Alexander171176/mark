<?php

namespace App\Http\Controllers\Admin\Slider\SliderSlide;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Slider\SliderSlide\SliderSlideRequest;
use App\Http\Resources\Admin\Slider\SliderSlide\SliderSlideResource;
use App\Http\Resources\Admin\Slider\SliderSlide\SliderSlideSharedResource;
use App\Http\Resources\Admin\Slider\SliderSlideAction\SliderSlideActionSharedResource;
use App\Http\Resources\Admin\Slider\SliderSlideAdvantage\SliderSlideAdvantageSharedResource;
use App\Models\Admin\Slider\SliderSlide\SliderSlide;
use App\Models\Admin\Slider\SliderSlideAction\SliderSlideAction;
use App\Models\Admin\Slider\SliderSlideAdvantage\SliderSlideAdvantage;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use App\Traits\Admin\Slider\HasSliderActivityTrait;
use App\Traits\Admin\Slider\HasSliderAdminCoreTrait;
use App\Traits\Admin\Slider\HasSliderModerationTrait;
use App\Traits\Admin\Slider\HasSliderSortingTrait;
use App\Traits\Admin\Slider\HasSliderTranslationsTrait;
use App\Traits\Admin\Slider\HasSliderImagesTrait;
use App\Traits\Admin\Slider\HasSliderRelationsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/** CRUD независимого каталога слайдов. Связи слайдеров и их pivot управляются отдельно. */
class SliderSlideController extends Controller
{
    use HasSliderAdminCoreTrait;
    use HasSliderActivityTrait;
    use HasSliderSortingTrait;
    use HasSliderModerationTrait;
    use HasSliderTranslationsTrait;
    use HasSliderImagesTrait;
    use HasSliderRelationsTrait;

    protected string $modelClass = SliderSlide::class;

    /** У слайдов нет user_id; разграничение доступа обеспечивает middleware / Policy. */
    protected function baseQuery(): Builder
    {
        abort_unless(auth()->check(), 403);
        return SliderSlide::query();
    }

    public function index(Request $request): Response
    {
        $locale = $this->resolveLocale($request);
        $settings = app(AdminSettingsService::class);
        $perPage = max(1, $settings->int('adminSliderSlidesPerPage', 10));
        $defaultSort = $settings->string('adminSliderSlidesDefaultSort', 'idAsc');
        $processingMode = $settings->string('adminSliderSlidesProcessingMode', 'frontend');
        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));
        $slidesCount = $this->baseQuery()->count();
        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer($processingMode, $slidesCount, 300);

        $props = [
            'currentLocale' => $locale,
            'availableLocales' => $this->availableLocales(),
            'isAdmin' => $this->isAdmin(),
            'useServerProcessing' => $useServerProcessing,
            'adminSliderSlidesPerPage' => $perPage,
            'adminSliderSlidesDefaultSort' => $defaultSort,
            'adminSliderSlidesProcessingMode' => $processingMode,
            'slidesCount' => $slidesCount,
            'sortParam' => $sortParam,
            'search' => $search,
        ];

        try {
            $query = $this->indexQuery($locale);
            $slides = $useServerProcessing
                ? $query->search($search, $locale)->sortByParam($sortParam, $locale)
                    ->paginate($perPage)->withQueryString()
                : $query->ordered()->get();

            return Inertia::render('Admin/Slider/SliderSlides/Index', [
                ...$props,
                'slides' => SliderSlideSharedResource::collection($slides),
            ]);
        } catch (Throwable $exception) {
            Log::error('Ошибка загрузки слайдов.', ['exception' => $exception]);
            return Inertia::render('Admin/Slider/SliderSlides/Index', [
                ...$props,
                'slides' => [],
                'error' => 'Ошибка загрузки списка слайдов.',
            ]);
        }
    }

    private function indexQuery(string $locale): Builder
    {
        $locales = $this->resourceLocales($locale);
        return $this->baseQuery()
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', $locales),
                'images.media',
            ])
            ->withCount(['sliders', 'images', 'actions', 'advantages']);
    }

    private function fullQuery(): Builder
    {
        return $this->baseQuery()
            ->with([
                'translations', 'moderator:id,name,email',
                'images.media', 'desktopImages.media', 'mobileImages.media',
                'sliders.translations',
                'actions.translations', 'advantages.translations',
            ])
            ->withCount(['sliders', 'images', 'actions', 'advantages']);
    }

    /** Справочники для выбора существующих действий и преимуществ. */
    private function sharedSelects(string $locale): array
    {
        $locales = $this->resourceLocales($locale);
        $actions = SliderSlideAction::query()
            ->with(['translations' => fn ($query) => $query->whereIn('locale', $locales)])
            ->ordered()->get();
        $advantages = SliderSlideAdvantage::query()
            ->with(['translations' => fn ($query) => $query->whereIn('locale', $locales)])
            ->ordered()->get();

        return [
            'availableActions' => SliderSlideActionSharedResource::collection($actions),
            'availableAdvantages' => SliderSlideAdvantageSharedResource::collection($advantages),
        ];
    }

    public function create(Request $request): Response
    {
        $locale = $this->resolveLocale($request);
        return Inertia::render('Admin/Slider/SliderSlides/Create', [
            'currentLocale' => $locale,
            'availableLocales' => $this->availableLocales(),
            'isAdmin' => $this->isAdmin(),
            'imagePreset' => $this->imagePresetPayload(),
            ...$this->sharedSelects($locale),
        ]);
    }

    public function show(Request $request, int $sliderSlide): Response
    {
        $locale = $this->resolveLocale($request);
        return Inertia::render('Admin/Slider/SliderSlides/Show', [
            'slide' => new SliderSlideResource($this->fullQuery()->findOrFail($sliderSlide)),
            'currentLocale' => $locale,
            'availableLocales' => $this->availableLocales(),
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    public function edit(Request $request, int $sliderSlide): Response
    {
        $locale = $this->resolveLocale($request);
        return Inertia::render('Admin/Slider/SliderSlides/Edit', [
            'slide' => new SliderSlideResource($this->fullQuery()->findOrFail($sliderSlide)),
            'currentLocale' => $locale,
            'availableLocales' => $this->availableLocales(),
            'isAdmin' => $this->isAdmin(),
            'imagePreset' => $this->imagePresetPayload(),
            ...$this->sharedSelects($locale),
        ]);
    }

    public function store(SliderSlideRequest $request): RedirectResponse
    {
        $data = $request->validated();
        DB::transaction(function () use ($request, $data): void {
            $slide = $this->baseQuery()->create($this->slideAttributes($data));
            $this->syncTranslations($slide, $data['translations'] ?? [], [
                'label', 'title', 'accent', 'description',
            ]);
            if (array_key_exists('images', $data)) {
                $this->syncSlideImages($slide, $request, $data['images']);
            }
            if (array_key_exists('actions', $data)) {
                $this->syncSlideActions($slide, $data['actions']);
            }
            if (array_key_exists('advantages', $data)) {
                $this->syncSlideAdvantages($slide, $data['advantages']);
            }
        });

        return redirect()->route('admin.sliderSlides.index')
            ->with('success', 'Слайд успешно создан.');
    }

    public function update(SliderSlideRequest $request, int $sliderSlide): RedirectResponse
    {
        $data = $request->validated();
        DB::transaction(function () use ($request, $sliderSlide, $data): void {
            $slide = $this->baseQuery()->lockForUpdate()->findOrFail($sliderSlide);
            $slide->update($this->slideAttributes($data));
            $this->syncTranslations($slide, $data['translations'] ?? [], [
                'label', 'title', 'accent', 'description',
            ]);
            if (array_key_exists('images', $data)) {
                $this->syncSlideImages(
                    $slide,
                    $request,
                    $data['images'] ?? [],
                    $data['deletedImages'] ?? []
                );
            } elseif (! empty($data['deletedImages'])) {
                // Удаляем только связи, принадлежащие этому слайду.
                $deletedIds = array_map('intval', $data['deletedImages']);
                $slide->images()->detach($deletedIds);
            }
            if (array_key_exists('actions', $data)) {
                $this->syncSlideActions($slide, $data['actions']);
            }
            if (array_key_exists('advantages', $data)) {
                $this->syncSlideAdvantages($slide, $data['advantages']);
            }
        });

        return redirect()->route('admin.sliderSlides.index')
            ->with('success', 'Слайд успешно обновлён.');
    }

    /** Обычная форма не вправе менять результат модерации. */
    private function slideAttributes(array $data): array
    {
        return Arr::except($data, [
            'translations', 'images', 'deletedImages', 'actions', 'advantages',
            'moderation_status', 'moderated_by', 'moderated_at', 'moderation_note',
        ]);
    }

    /** Слайд нельзя удалить, пока он используется хотя бы в одном слайдере. */
    public function destroy(int $sliderSlide): RedirectResponse
    {
        DB::transaction(function () use ($sliderSlide): void {
            $slide = $this->baseQuery()->lockForUpdate()->findOrFail($sliderSlide);
            $this->ensureNotAttached($slide);
            $slide->images()->detach();
            $slide->actions()->detach();
            $slide->advantages()->detach();
            $slide->translations()->delete();
            $slide->delete();
        });

        return redirect()->route('admin.sliderSlides.index')
            ->with('success', 'Слайд удалён. Общие изображения и справочники сохранены.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:500'],
            'ids.*' => ['required', 'integer', 'min:1', 'distinct'],
        ]);
        $ids = array_map('intval', $data['ids']);
        DB::transaction(function () use ($ids): void {
            $slides = $this->baseQuery()->whereKey($ids)->lockForUpdate()->get();
            if ($slides->count() !== count($ids)) {
                throw ValidationException::withMessages(['ids' => 'Часть слайдов недоступна.']);
            }
            foreach ($slides as $slide) {
                $this->ensureNotAttached($slide);
            }
            foreach ($slides as $slide) {
                $slide->images()->detach();
                $slide->actions()->detach();
                $slide->advantages()->detach();
                $slide->translations()->delete();
                $slide->delete();
            }
        });
        return back()->with('success', 'Выбранные слайды удалены.');
    }

    /**
     * @throws ValidationException
     */
    private function ensureNotAttached(SliderSlide $slide): void
    {
        if ($slide->sliders()->exists()) {
            throw ValidationException::withMessages([
                'slide' => 'Нельзя удалить слайд, пока он используется в слайдерах.',
            ]);
        }
    }
}
