<?php

namespace App\Http\Controllers\Admin\Market\MarketBrand;

use App\Http\Controllers\Admin\Market\BaseMarketAdminController;
use App\Http\Requests\Admin\Market\MarketBrand\MarketBrandRequest;
use App\Http\Resources\Admin\Market\MarketBrand\MarketBrandResource;
use App\Http\Resources\Admin\Market\MarketBrand\MarketBrandSharedResource;
use App\Models\Admin\Market\MarketBrand\MarketBrand;
use App\Models\Admin\Market\MarketBrand\MarketBrandImage;
use App\Services\Admin\ImagePresetService;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления Брендами (MarketBrand) в админке.
 *
 * Паттерн:
 * - Поиск, Пагинация, сортировка (режимы: frontend | auto | server)
 * - CRUD
 * - owner/ограничение “владелец/админ”
 * - activity/left/main/right (single + bulk)
 * - sort + drag&drop (bulk)
 * - moderation (approve/reject) только для admin
 * - images + сервис обработки изображений.
 *
 * @version 1.1 (мультиязычеая архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 */
class MarketBrandController extends BaseMarketAdminController
{
    /** Основная модель контроллера */
    protected string $modelClass = MarketBrand::class;

    /** Модель изображений */
    protected string $imageModelClass = MarketBrandImage::class;

    /** Коллекция изображений */
    protected string $imageMediaCollection = 'images';

    /** Название сущности для сообщений */
    protected string $entityLabel = 'брендов';

    /** Поля переводов */
    protected array $translationFields = [
        'title',
        'subtitle',
        'short',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    /** Пресет для галереи бренда */
    protected string $imagePresetKey = 'rectangle_large';

    /** Директория для обработанных изображений галереи */
    protected string $imagePresetDirectory = 'market/market_brand_images/preset';

    /**
     * =========================================================
     * INDEX
     * =========================================================
     */

    /** Список брендов */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int(
            'adminMarketBrandsPerPage',
            6
        );

        $defaultSort = $settings->string(
            'adminMarketBrandsDefaultSort',
            'idDesc'
        );

        $sortParam = (string) $request->query(
            'sort',
            $defaultSort
        );

        $search = trim(
            (string) $request->query(
                'search',
                ''
            )
        );

        $processingMode = $settings->string(
            'adminMarketBrandsProcessingMode',
            'frontend'
        );

        $brandsCount = $this->baseQuery()->count();

        $useServerProcessing = app(
            ProcessingModeService::class
        )->shouldUseServer(
            $processingMode,
            $brandsCount,
            300
        );

        try {
            $brands = $this->getIndexBrands(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search
            );

            return Inertia::render(
                'Admin/Market/MarketBrands/Index',
                [
                    'currentLocale' => $currentLocale,
                    'availableLocales' => $this->availableLocales(),

                    'useServerProcessing' => $useServerProcessing,

                    'adminMarketBrandsPerPage' => $perPage,
                    'adminMarketBrandsDefaultSort' => $defaultSort,
                    'adminMarketBrandsProcessingMode' => $processingMode,

                    'brands' => MarketBrandSharedResource::collection(
                        $brands
                    ),

                    'brandsCount' => $brandsCount,

                    'sortParam' => $sortParam,
                    'search' => $search,
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки списка market brands: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/Market/MarketBrands/Index',
                [
                    'currentLocale' => $currentLocale,
                    'availableLocales' => $this->availableLocales(),

                    'useServerProcessing' => $useServerProcessing,

                    'adminMarketBrandsPerPage' => $perPage,
                    'adminMarketBrandsDefaultSort' => $defaultSort,
                    'adminMarketBrandsProcessingMode' => $processingMode,

                    'brands' => [],
                    'brandsCount' => 0,

                    'sortParam' => $sortParam,
                    'search' => $search,

                    'error' => 'Ошибка загрузки брендов.',
                ]
            );
        }
    }

    /**
     * =========================================================
     * CREATE / STORE
     * =========================================================
     */

    /** Страница создания бренда */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        return Inertia::render(
            'Admin/Market/MarketBrands/Create',
            [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'imageProcessorEnabled' => $this->imageProcessorEnabled(),
                'imagePreset' => $this->imagePresetPayload(),
            ]
        );
    }

    /** Создание бренда */
    public function store(MarketBrandRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages'],
            $data['logo']
        );

        $user = auth()->user();

        if (
            $user
            && method_exists($user, 'hasRole')
            && !$user->hasRole('admin')
        ) {
            $data['user_id'] = $user->id;

            unset(
                $data['moderation_status'],
                $data['moderated_by'],
                $data['moderated_at'],
                $data['moderation_note']
            );
        } else {
            $data['user_id'] = $data['user_id']
                ?? $user?->id;
        }

        try {
            DB::transaction(
                function () use (
                    &$brand,
                    $request,
                    $data,
                    $translations,
                    $imagesData
                ) {
                    if (
                        !isset($data['sort'])
                        || is_null($data['sort'])
                    ) {
                        $maxSort = MarketBrand::query()
                            ->max('market_brands.sort');

                        $data['sort'] = is_null($maxSort)
                            ? 0
                            : $maxSort + 1;
                    }

                    $brand = MarketBrand::create(
                        $data
                    );

                    $this->syncTranslations(
                        $brand,
                        $translations
                    );

                    $this->syncImages(
                        $brand,
                        $request,
                        $imagesData
                    );
                }
            );

            if ($request->hasFile('logo')) {
                $brand->update([
                    'logo' => $this->storeBrandLogo(
                        $request
                    ),
                ]);
            }

            return redirect()
                ->route('admin.marketBrands.index')
                ->with(
                    'success',
                    'Бренд успешно создан.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при создании market brand: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ошибка при создании бренда.'
                );
        }
    }

    /**
     * =========================================================
     * SHOW / EDIT / UPDATE
     * =========================================================
     */

    /** Перенаправление просмотра на редактирование */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route(
            'admin.marketBrands.edit',
            $id
        );
    }

    /** Страница редактирования бренда */
    public function edit(
        int $marketBrand,
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $brand = $this->baseQuery()
            ->with([
                /**
                 * Для TranslationTabs нужны
                 * все переводы бренда.
                 */
                'translations',

                /**
                 * MarketBrandImageResource использует
                 * Spatie MediaLibrary.
                 */
                'images.media',
            ])
            ->findOrFail(
                $marketBrand
            );

        return Inertia::render(
            'Admin/Market/MarketBrands/Edit',
            [
                'brand' => new MarketBrandResource(
                    $brand
                ),

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'imageProcessorEnabled' => $this->imageProcessorEnabled(),
                'imagePreset' => $this->imagePresetPayload(),
            ]
        );
    }

    /** Обновление бренда */
    public function update(
        MarketBrandRequest $request,
        int $marketBrand
    ): RedirectResponse {
        $brand = $this->baseQuery()
            ->findOrFail(
                $marketBrand
            );

        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages'],
            $data['_method'],
            $data['logo']
        );

        $user = auth()->user();

        if (
            $user
            && method_exists($user, 'hasRole')
            && !$user->hasRole('admin')
        ) {
            $data['user_id'] = $user->id;

            unset(
                $data['moderation_status'],
                $data['moderated_by'],
                $data['moderated_at'],
                $data['moderation_note']
            );
        }

        try {
            DB::transaction(
                function () use (
                    $brand,
                    $request,
                    $data,
                    $translations,
                    $imagesData,
                    $deletedImageIds
                ) {
                    $brand->update(
                        $data
                    );

                    $this->syncTranslations(
                        $brand,
                        $translations
                    );

                    $this->syncImages(
                        $brand,
                        $request,
                        $imagesData,
                        $deletedImageIds
                    );
                }
            );

            if ($request->hasFile('logo')) {
                if ($brand->logo) {
                    Storage::disk('public')
                        ->delete(
                            $brand->logo
                        );
                }

                $brand->update([
                    'logo' => $this->storeBrandLogo(
                        $request
                    ),
                ]);
            }

            return redirect()
                ->route('admin.marketBrands.index')
                ->with(
                    'success',
                    'Бренд успешно обновлён.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при обновлении market brand ID '
                . $brand->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ошибка при обновлении бренда.'
                );
        }
    }

    /**
     * =========================================================
     * DESTROY
     * =========================================================
     */

    /** Удаление бренда */
    public function destroy(
        int $marketBrand
    ): RedirectResponse {
        $brand = $this->baseQuery()
            ->findOrFail(
                $marketBrand
            );

        try {
            DB::transaction(
                function () use ($brand) {
                    if ($brand->logo) {
                        Storage::disk('public')
                            ->delete(
                                $brand->logo
                            );
                    }

                    $imageIds = $brand->images()
                        ->pluck(
                            'market_brand_images.id'
                        )
                        ->toArray();

                    $brand->images()
                        ->detach();

                    if (!empty($imageIds)) {
                        $this->deleteImages(
                            $imageIds
                        );
                    }

                    $brand->translations()
                        ->delete();

                    $brand->delete();
                }
            );

            return redirect()
                ->route('admin.marketBrands.index')
                ->with(
                    'success',
                    'Бренд успешно удалён.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при удалении market brand ID '
                . $brand->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при удалении бренда.'
            );
        }
    }

    /** Массовое удаление брендов */
    public function bulkDestroy(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'ids' => [
                'required',
                'array',
            ],

            'ids.*' => [
                'required',
                'integer',
                'exists:market_brands,id',
            ],
        ]);

        $ids = $validated['ids'];

        $allowedIds = $this->baseQuery()
            ->whereIn(
                'market_brands.id',
                $ids
            )
            ->pluck(
                'market_brands.id'
            )
            ->toArray();

        if (
            count($allowedIds)
            !== count($ids)
        ) {
            return back()->with(
                'error',
                'Часть брендов недоступна для удаления.'
            );
        }

        try {
            DB::transaction(
                function () use ($allowedIds) {
                    $brands = MarketBrand::query()
                        ->whereIn(
                            'market_brands.id',
                            $allowedIds
                        )
                        ->get();

                    foreach ($brands as $brand) {
                        if ($brand->logo) {
                            Storage::disk('public')
                                ->delete(
                                    $brand->logo
                                );
                        }

                        $imageIds = $brand->images()
                            ->pluck(
                                'market_brand_images.id'
                            )
                            ->toArray();

                        $brand->images()
                            ->detach();

                        if (!empty($imageIds)) {
                            $this->deleteImages(
                                $imageIds
                            );
                        }

                        $brand->translations()
                            ->delete();

                        $brand->delete();
                    }
                }
            );

            return back()->with(
                'success',
                'Выбранные бренды успешно удалены.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка bulkDestroy market brands: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при массовом удалении брендов.'
            );
        }
    }

    /**
     * =========================================================
     * INDEX QUERIES
     * =========================================================
     */

    /**
     * Основной запрос для Admin Index.
     *
     * Загружаем:
     * - только перевод текущей локали;
     * - владельца;
     * - изображения вместе с Spatie Media;
     * - количество изображений.
     */
    private function indexQuery(
        string $locale
    ): Builder {
        return $this->baseQuery()
            ->with([
                'translations' => fn ($query) => $query
                    ->where(
                        'locale',
                        $locale
                    ),

                'owner:id,name,email,profile_photo_path',

                'images.media',
            ])
            ->withCount([
                'images',
            ]);
    }

    /** Получение списка брендов для Index */
    private function getIndexBrands(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query = $this->indexQuery(
            $locale
        );

        /**
         * SERVER:
         * поиск + сортировка + SQL pagination.
         */
        if ($useServerProcessing) {
            return $query
                ->search(
                    $search,
                    $locale
                )
                ->sortByParam(
                    $sort,
                    $locale
                )
                ->paginate(
                    $perPage
                )
                ->withQueryString();
        }

        /**
         * FRONTEND:
         * передаём полный dataset.
         *
         * Поиск и выбранная сортировка
         * выполняются локально в Index.vue.
         */
        return $query
            ->ordered()
            ->get();
    }

    /**
     * =========================================================
     * LOGO
     * =========================================================
     */

    /** Метод для сохранения логотипа бренда */
    private function storeBrandLogo(
        Request $request
    ): string {
        if (
            !$this->imageProcessorEnabled()
        ) {
            return $request
                ->file('logo')
                ->store(
                    'market/market_brands/logos',
                    'public'
                );
        }

        return app(
            ImagePresetService::class
        )->storeUploadedImage(
            file: $request->file('logo'),
            presetKey: 'square_medium',
            directory: 'market/market_brands/logos',
            storeOriginal: false
        );
    }
}
