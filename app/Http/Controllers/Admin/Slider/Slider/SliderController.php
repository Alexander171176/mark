<?php

namespace App\Http\Controllers\Admin\Slider\Slider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Slider\Slider\SliderRequest;
use App\Http\Resources\Admin\Slider\Slider\SliderResource;
use App\Http\Resources\Admin\Slider\Slider\SliderSharedResource;
use App\Models\Admin\Slider\Slider\Slider;
use App\Models\Admin\Slider\SliderSlide\SliderSlide;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use App\Traits\Admin\Slider\HasSliderActivityTrait;
use App\Traits\Admin\Slider\HasSliderAdminCoreTrait;
use App\Traits\Admin\Slider\HasSliderModerationTrait;
use App\Traits\Admin\Slider\HasSliderSlidesTrait;
use App\Traits\Admin\Slider\HasSliderSortingTrait;
use App\Traits\Admin\Slider\HasSliderTranslationsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class SliderController extends Controller
{
    use HasSliderAdminCoreTrait;
    use HasSliderActivityTrait;
    use HasSliderSortingTrait;
    use HasSliderModerationTrait;
    use HasSliderTranslationsTrait;
    use HasSliderSlidesTrait;

    protected string $modelClass = Slider::class;

    private const TRANSLATION_FIELDS = ['title', 'subtitle', 'description', 'meta_title', 'meta_description'];
    private const MODERATION_FIELDS = ['moderation_status', 'moderated_by', 'moderated_at', 'moderation_note'];

    public function index(Request $request): Response
    {
        $locale = $this->resolveLocale($request);
        $settings = app(AdminSettingsService::class);
        $perPage = max(1, $settings->int('adminSlidersPerPage', 4));
        $defaultSort = $settings->string('adminSlidersDefaultSort', 'sortAsc');
        $sort = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));
        $mode = $settings->string('adminSlidersProcessingMode', 'frontend');
        $count = $this->baseQuery()->count();
        $server = app(ProcessingModeService::class)->shouldUseServer($mode, $count, 300);
        $query = $this->indexQuery($locale);
        $sliders = $server
            ? $query->search($search, $locale)->sortByParam($sort, $locale)->paginate($perPage)->withQueryString()
            : $query->ordered()->get();

        return Inertia::render('Admin/Slider/Sliders/Index', [
            'sliders' => SliderSharedResource::collection($sliders),
            'slidersCount' => $count,
            'adminSlidersPerPage' => $perPage,
            'adminSlidersDefaultSort' => $defaultSort,
            'adminSlidersProcessingMode' => $mode,
            'useServerProcessing' => $server,
            'sortParam' => $sort,
            'search' => $search,
            'currentLocale' => $locale,
            'availableLocales' => $this->availableLocales(),
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    private function indexQuery(string $locale): Builder
    {
        return $this->baseQuery()->with([
            'translations' => fn ($query) => $query->whereIn('locale', $this->resourceLocales($locale)),
            'owner:id,name,email,profile_photo_path',
            'slides.images.media',
        ])->withCount('slides');
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Admin/Slider/Sliders/Create', [
            'currentLocale' => $this->resolveLocale($request),
            'availableLocales' => $this->availableLocales(),
            'availableSlides' => $this->availableSlides(),
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    public function store(SliderRequest $request): RedirectResponse
    {
        $data = $request->validated();
        DB::transaction(function () use ($data, $request): void {
            $attributes = $this->sliderAttributes($data);
            $attributes['user_id'] = (int) $request->user()->getAuthIdentifier();
            $this->resetModeration($attributes);
            $this->applyAdminModeration($attributes, $data, $attributes['user_id']);
            $this->normalizePublication($attributes);
            $slider = $this->baseQuery()->create($attributes);
            $this->syncTranslations($slider, $data['translations'] ?? [], self::TRANSLATION_FIELDS);
            if (array_key_exists('slides', $data)) {
                $this->syncSliderSlides($slider, $data['slides']);
            }
        });

        return redirect()->route('admin.sliders.index')->with('success', 'Слайдер успешно создан.');
    }

    public function show(Request $request, int $slider): Response
    {
        $locale = $this->resolveLocale($request);
        return Inertia::render('Admin/Slider/Sliders/Show', [
            'slider' => new SliderResource($this->fullQuery()->findOrFail($slider)),
            'currentLocale' => $locale,
            'availableLocales' => $this->availableLocales(),
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    public function edit(Request $request, int $slider): Response
    {
        $locale = $this->resolveLocale($request);
        return Inertia::render('Admin/Slider/Sliders/Edit', [
            'slider' => new SliderResource($this->fullQuery()->findOrFail($slider)),
            'availableSlides' => $this->availableSlides(),
            'currentLocale' => $locale,
            'availableLocales' => $this->availableLocales(),
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    public function update(SliderRequest $request, int $slider): RedirectResponse
    {
        $data = $request->validated();
        DB::transaction(function () use ($slider, $data, $request): void {
            $model = $this->baseQuery()->lockForUpdate()->findOrFail($slider);
            $attributes = $this->sliderAttributes($data);
            $this->normalizePublication($attributes, $model);
            $model->fill($attributes);
            $changed = $model->isDirty();
            $model->save();

            if (array_key_exists('translations', $data)) {
                $before = $model->translations()->get()->mapWithKeys(fn ($item) => [$item->locale => Arr::only($item->getAttributes(), self::TRANSLATION_FIELDS)])->all();
                $this->syncTranslations($model, $data['translations'], self::TRANSLATION_FIELDS);
                $after = $model->translations()->get()->mapWithKeys(fn ($item) => [$item->locale => Arr::only($item->getAttributes(), self::TRANSLATION_FIELDS)])->all();
                $changed = $changed || $before != $after;
            }
            if (array_key_exists('slides', $data)) {
                $changed = $this->syncSliderSlides($model, $data['slides']) || $changed;
            }

            if ($this->isAdmin()) {
                $moderation = [];
                $this->applyAdminModeration($moderation, $data, (int) $request->user()->getAuthIdentifier());
                if ($moderation !== []) {
                    $model->forceFill($moderation)->save();
                }
            } elseif ($changed) {
                $moderation = [];
                $this->resetModeration($moderation);
                $model->forceFill($moderation)->save();
            }
        });

        return redirect()->route('admin.sliders.index')->with('success', 'Слайдер успешно обновлён.');
    }

    public function destroy(int $slider): RedirectResponse
    {
        DB::transaction(function () use ($slider): void {
            $model = $this->baseQuery()->lockForUpdate()->findOrFail($slider);
            $this->deleteSlider($model);
        });
        return redirect()->route('admin.sliders.index')->with('success', 'Слайдер удалён.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:500'],
            'ids.*' => ['required', 'integer', 'distinct', 'min:1'],
        ]);
        $ids = array_map('intval', $validated['ids']);
        $deleted = 0;
        $failed = 0;
        foreach ($ids as $id) {
            try {
                DB::transaction(function () use ($id): void {
                    $model = $this->baseQuery()->lockForUpdate()->findOrFail($id);
                    $this->deleteSlider($model);
                });
                $deleted++;
            } catch (Throwable $exception) {
                $failed++;
                Log::error('Ошибка удаления слайдера.', ['slider_id' => $id, 'exception' => $exception]);
            }
        }
        return back()->with($failed ? 'error' : 'success', $failed
            ? "Удалено слайдеров: {$deleted}. Не удалось удалить: {$failed}."
            : "Успешно удалено слайдеров: {$deleted}.");
    }

    private function deleteSlider(Slider $slider): void
    {
        // Удаляем только размещения; глобальные слайды и их файлы сохраняются.
        $slider->slides()->detach();
        $slider->translations()->delete();
        $slider->delete();
    }

    private function sliderAttributes(array $data): array
    {
        return Arr::except($data, ['translations', 'slides', 'user_id', ...self::MODERATION_FIELDS]);
    }

    private function resetModeration(array &$attributes): void
    {
        $attributes['moderation_status'] = 0;
        $attributes['moderated_by'] = null;
        $attributes['moderated_at'] = null;
        $attributes['moderation_note'] = null;
    }

    private function applyAdminModeration(array &$attributes, array $data, int $adminId): void
    {
        if (! $this->isAdmin() || ! array_key_exists('moderation_status', $data)) {
            return;
        }
        $status = (int) $data['moderation_status'];
        if ($status === 0) {
            $this->resetModeration($attributes);
            return;
        }
        $attributes['moderation_status'] = $status;
        $attributes['moderated_by'] = $adminId;
        $attributes['moderated_at'] = now();
        $attributes['moderation_note'] = $data['moderation_note'] ?? null;
    }

    private function normalizePublication(array &$attributes, ?Slider $slider = null): void
    {
        if (($attributes['status'] ?? $slider?->status) === 'published'
            && empty($attributes['published_at']) && ! $slider?->published_at) {
            $attributes['published_at'] = now();
        }
    }

    private function availableSlides(): Collection|array
    {
        return SliderSlide::query()->with('translations')->orderBy('id')->get();
    }

    private function fullQuery(): Builder
    {
        return $this->baseQuery()->with([
            'translations',
            'owner:id,name,email,profile_photo_path',
            'moderator:id,name,email,profile_photo_path',
            'slides.translations',
            'slides.images.media',
            'slides.desktopImages.media',
            'slides.mobileImages.media',
            'slides.actions.translations',
            'slides.advantages.translations',
        ])->withCount('slides');
    }
}
