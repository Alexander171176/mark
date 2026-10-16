<?php

namespace App\Http\Controllers\Admin\Slider\Slider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Slider\Slider\SliderBuilderRequest;
use App\Http\Resources\Admin\Slider\Slider\SliderResource;
use App\Http\Resources\Admin\Slider\Slider\SliderSharedResource;
use App\Models\Admin\Slider\Slider\Slider;
use App\Services\Admin\Slider\SliderService;
use App\Services\Admin\Slider\SliderBuilderService;
use App\Services\SiteSettings\AdminSettingsService;
use App\Traits\Admin\Slider\HasSliderActivityTrait;
use App\Traits\Admin\Slider\HasSliderAdminCoreTrait;
use App\Traits\Admin\Slider\HasSliderDeletionTrait;
use App\Traits\Admin\Slider\HasSliderModerationTrait;
use App\Traits\Admin\Slider\HasSliderSortingTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Административный контроллер слайдеров.
 *
 * Управляет списком, созданием, редактированием и удалением.
 * Активность, сортировка и модерация вынесены в трейты.
 */
class SliderController extends Controller
{
    use HasSliderAdminCoreTrait;
    use HasSliderActivityTrait;
    use HasSliderSortingTrait;
    use HasSliderModerationTrait;
    use HasSliderDeletionTrait;

    /** Основная модель слайдера */
    protected string $modelClass = Slider::class;

    /** Сервис создания, обновления и удаления слайдеров */
    public function __construct(
        private readonly SliderService $service,
        private readonly SliderBuilderService $builder
    ) {
    }

    /** Список слайдеров: поиск, сортировка и пагинация выполняются во Vue */
    public function index(Request $request): Response
    {
        $locale = $this->resolveLocale($request);
        $locales = $this->resourceLocales($locale);

        // Настройки административного списка из таблицы settings.
        $settings = app(AdminSettingsService::class);

        $perPage = max(1, $settings->int('adminSlidersPerPage', 4));
        $defaultSort = $settings->string('adminSlidersDefaultSort', 'sortAsc');

        // Загружаем переводы, владельца и количество слайдов без N+1.
        // Связь moderator здесь не нужна: компактный ресурс отдаёт поля модерации.
        $sliders = $this->baseQuery()
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', $locales),
                'owner:id,name,email,profile_photo_path',
                'slides' => fn ($query) => $query->orderBy('sort')->orderBy('id'),
                'slides.images.media',
            ])
            ->withCount('slides')
            ->ordered()
            ->get();

        return Inertia::render('Admin/Slider/Sliders/Index', [
            'sliders' => SliderSharedResource::collection($sliders),
            'adminSlidersPerPage' => $perPage,
            'adminSlidersDefaultSort' => $defaultSort,
            'currentLocale' => $locale,
            'availableLocales' => $this->availableLocales(),
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    /** Страница создания слайдера */
    public function create(Request $request): Response
    {
        return Inertia::render('Admin/Slider/Sliders/Create', [
            'currentLocale' => $this->resolveLocale($request),
            'availableLocales' => $this->availableLocales(),
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    /** Создание слайдера через сервис */
    public function store(SliderBuilderRequest $request): RedirectResponse
    {
        $this->builder->create(
            $request->validated(),
            (int) $request->user()->getAuthIdentifier(),
            $this->isAdmin()
        );

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Слайдер успешно создан.');
    }

    /** Страница просмотра слайдера */
    public function show(Request $request, int $slider): Response
    {
        $locale = $this->resolveLocale($request);
        $model = $this->fullQuery()->findOrFail($slider);

        return Inertia::render('Admin/Slider/Sliders/Show', [
            'slider' => new SliderResource($model),
            'currentLocale' => $locale,
            'availableLocales' => $this->availableLocales(),
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    /** Страница редактирования слайдера */
    public function edit(Request $request, int $slider): Response
    {
        $locale = $this->resolveLocale($request);
        $model = $this->fullQuery()->findOrFail($slider);

        return Inertia::render('Admin/Slider/Sliders/Edit', [
            'slider' => new SliderResource($model),
            'currentLocale' => $locale,
            'availableLocales' => $this->availableLocales(),
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    /** Обновление слайдера через сервис */
    public function update(SliderBuilderRequest $request, int $slider): RedirectResponse
    {
        $model = $this->baseQuery()->findOrFail($slider);

        $this->builder->update($model, $request->validated(), $this->isAdmin());

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Слайдер успешно обновлён.');
    }

    /** Удаление слайдера через сервис */
    public function destroy(int $slider): RedirectResponse
    {
        $model = $this->baseQuery()->findOrFail($slider);
        $this->service->delete($model);

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Слайдер удалён.');
    }

    /** Полные связи для Show/Edit, включая модератора, без N+1 */
    private function fullQuery(): Builder
    {
        return $this->baseQuery()
            ->with([
                'translations',
                'owner:id,name,email,profile_photo_path',
                'moderator:id,name,email,profile_photo_path',
                'slides.translations',
                'slides.images.media',
                'slides.desktopImages.media',
                'slides.mobileImages.media',
                'slides.actions.translations',
                'slides.advantages.translations',
            ])
            ->withCount('slides');
    }
}
