<?php

namespace App\Http\Controllers\Admin\Market\MarketShop;

use App\Http\Controllers\Admin\Market\BaseMarketAdminController;
use App\Http\Requests\Admin\Market\MarketShop\MarketShopRequest;
use App\Http\Resources\Admin\Market\MarketCompany\MarketCompanyResource;
use App\Http\Resources\Admin\Market\MarketShop\MarketShopResource;
use App\Models\Admin\Market\MarketCompany\MarketCompany;
use App\Models\Admin\Market\MarketShop\MarketShop;
use App\Models\Admin\Market\MarketShop\MarketShopImage;
use App\Services\Admin\AdminFeatureService;
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
 * Контроллер для управления Магазинами (MarketShop) в админке.
 *
 * Паттерн:
 * - Поиск, Пагинация, сортировка (режимы: frontend | auto | server )
 * - CRUD
 * - owner/ограничение “владелец/админ”
 * - activity/left/main/right (single + bulk)
 * - activity (single + bulk)
 * - sort + drag&drop (bulk)
 * - moderation (approve/reject) только для admin
 * - images + сервис обработки изображений.
 *
 * @version 1.1 (мультиязычеая архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 */
class MarketShopController extends BaseMarketAdminController
{
    /** Основная модель контроллера */
    protected string $modelClass = MarketShop::class;

    /** Модель изображений */
    protected string $imageModelClass = MarketShopImage::class;

    /** Коллекция изображений */
    protected string $imageMediaCollection = 'images';

    /** Название сущности для сообщений */
    protected string $entityLabel = 'магазинов';

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

    /** Пресет для галереи магазина */
    protected string $imagePresetKey = 'rectangle_large';

    /** Директория для обработанных изображений галереи */
    protected string $imagePresetDirectory = 'market/market_shop_images/preset';

    /** Список магазинов */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int('adminMarketShopsPerPage', 6);
        $defaultSort = $settings->string('adminMarketShopsDefaultSort', 'idDesc');

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $processingMode = $settings->string(
            'adminMarketShopsProcessingMode',
            'frontend'
        );

        $shopsCount = $this->baseQuery()->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer($processingMode, $shopsCount, 300);

        try {
            $shops = $this->getIndexShops(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            return Inertia::render('Admin/Market/MarketShops/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminMarketShopsPerPage' => $perPage,
                'adminMarketShopsDefaultSort' => $defaultSort,
                'adminMarketShopsProcessingMode' => $processingMode,

                'shops' => MarketShopResource::collection($shops),
                'shopsCount' => $shopsCount,

                'sortParam' => $sortParam,
                'search' => $search,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка market shops: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/Market/MarketShops/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminMarketShopsPerPage' => $perPage,
                'adminMarketShopsDefaultSort' => $defaultSort,
                'adminMarketShopsProcessingMode' => $processingMode,

                'shops' => [],
                'shopsCount' => 0,

                'sortParam' => $sortParam,
                'search' => $search,

                'error' => 'Ошибка загрузки магазинов.',
            ]);
        }
    }

    /** Страница создания магазина */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/Market/MarketShops/Create', [
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'companies' => MarketCompanyResource::collection(
                $this->availableCompaniesForCreate()
            ),

            'imageProcessorEnabled' => $this->imageProcessorEnabled(),
        ]);
    }

    /** Создание магазина */
    public function store(MarketShopRequest $request): RedirectResponse
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

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            $data['user_id'] = $user->id;

            $company = MarketCompany::query()
                ->where('user_id', $user->id)
                ->firstOrFail();

            $data['market_company_id'] = $company->id;

            unset(
                $data['moderation_status'],
                $data['moderated_by'],
                $data['moderated_at'],
                $data['moderation_note']
            );
        } else {
            $data['user_id'] = $data['user_id'] ?? $user?->id;
        }

        try {
            DB::transaction(function () use (
                &$shop,
                $request,
                $data,
                $translations,
                $imagesData
            ) {
                if (!isset($data['sort']) || is_null($data['sort'])) {
                    $maxSort = MarketShop::query()->max('sort');
                    $data['sort'] = is_null($maxSort) ? 0 : $maxSort + 1;
                }

                $shop = MarketShop::create($data);

                $this->syncTranslations($shop, $translations);
                $this->syncImages($shop, $request, $imagesData);
            });

            if ($request->hasFile('logo')) {
                $shop->update([
                    'logo' => $this->storeShopLogo($request),
                ]);
            }

            return redirect()
                ->route('admin.marketShops.index')
                ->with('success', 'Магазин успешно создан.');
        } catch (Throwable $e) {
            Log::error('Ошибка при создании market shop: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании магазина.');
        }
    }

    /** Перенаправление просмотра на редактирование */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route('admin.marketShops.edit', $id);
    }

    /** Страница редактирования магазина */
    public function edit(int $marketShop, Request $request): Response
    {
        $shop = $this->baseQuery()
            ->with([
                'company.translations',
                'owner',
                'moderator',
                'translations',
                'images',
            ])
            ->withCount([
                'images',
            ])
            ->findOrFail($marketShop);

        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/Market/MarketShops/Edit', [
            'shop' => new MarketShopResource($shop),
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'companies' => MarketCompanyResource::collection(
                $this->availableCompaniesForEdit($shop)
            ),

            'imageProcessorEnabled' => $this->imageProcessorEnabled(),
        ]);
    }

    /** Обновление магазина */
    public function update(
        MarketShopRequest $request,
        int $marketShop
    ): RedirectResponse {
        $shop = $this->baseQuery()->findOrFail($marketShop);

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

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            $data['user_id'] = $user->id;
            $data['market_company_id'] = $shop->market_company_id;

            unset(
                $data['moderation_status'],
                $data['moderated_by'],
                $data['moderated_at'],
                $data['moderation_note']
            );
        }

        try {
            DB::transaction(function () use (
                $shop,
                $request,
                $data,
                $translations,
                $imagesData,
                $deletedImageIds
            ) {
                $shop->update($data);

                $this->syncTranslations($shop, $translations);
                $this->syncImages($shop, $request, $imagesData, $deletedImageIds);
            });

            if ($request->hasFile('logo')) {
                if ($shop->logo) {
                    Storage::disk('public')->delete($shop->logo);
                }

                $shop->update([
                    'logo' => $this->storeShopLogo($request),
                ]);
            }

            return redirect()
                ->route('admin.marketShops.index')
                ->with('success', 'Магазин успешно обновлён.');
        } catch (Throwable $e) {
            Log::error('Ошибка при обновлении market shop ID ' . $shop->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении магазина.');
        }
    }

    /** Удаление магазина */
    public function destroy(int $marketShop): RedirectResponse
    {
        $shop = $this->baseQuery()
            ->with(['images', 'translations'])
            ->findOrFail($marketShop);

        try {
            DB::transaction(function () use ($shop) {
                if ($shop->logo) {
                    Storage::disk('public')->delete($shop->logo);
                }

                $this->deleteImages(
                    $shop->images()->pluck('market_shop_images.id')->toArray()
                );

                $shop->images()->detach();
                $shop->translations()->delete();
                $shop->delete();
            });

            return redirect()
                ->route('admin.marketShops.index')
                ->with('success', 'Магазин успешно удалён.');
        } catch (Throwable $e) {
            Log::error('Ошибка при удалении market shop ID ' . $shop->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении магазина.');
        }
    }

    /** Массовое удаление магазинов */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:market_shops,id'],
        ]);

        $ids = $validated['ids'];

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            return back()->with('error', 'Часть магазинов недоступна для удаления.');
        }

        try {
            DB::transaction(function () use ($allowedIds) {
                $shops = MarketShop::query()
                    ->whereIn('id', $allowedIds)
                    ->with('images')
                    ->get();

                foreach ($shops as $shop) {
                    if ($shop->logo) {
                        Storage::disk('public')->delete($shop->logo);
                    }

                    $this->deleteImages(
                        $shop->images()->pluck('market_shop_images.id')->toArray()
                    );

                    $shop->images()->detach();
                    $shop->translations()->delete();
                    $shop->delete();
                }
            });

            return back()->with('success', 'Выбранные магазины успешно удалены.');
        } catch (Throwable $e) {
            Log::error('Ошибка bulkDestroy market shops: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при массовом удалении магазинов.');
        }
    }

    /** Базовый запрос списка магазинов */
    private function indexQuery(): Builder
    {
        return $this->baseQuery()
            ->with([
                'company.translations',
                'owner',
                'moderator',
                'translations',
                'images',
            ])
            ->withCount([
                'images',
            ]);
    }

    /** Получение списка магазинов для индекса */
    private function getIndexShops(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query = $this->indexQuery();

        if ($useServerProcessing) {
            return $query
                ->search($search, $locale)
                ->sortByParam($sort, $locale)
                ->paginate($perPage)
                ->withQueryString();
        }

        return $query
            ->ordered()
            ->get();
    }

    /** Компании для создания магазина */
    private function availableCompaniesForCreate()
    {
        $user = auth()->user();

        $query = MarketCompany::query()
            ->with(['translations'])
            ->orderBy('id');

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            return $query
                ->where('user_id', $user->id)
                ->whereDoesntHave('shop')
                ->get();
        }

        return $query
            ->whereDoesntHave('shop')
            ->get();
    }

    /** Компании для редактирования магазина */
    private function availableCompaniesForEdit(MarketShop $shop)
    {
        $user = auth()->user();

        $query = MarketCompany::query()
            ->with(['translations'])
            ->orderBy('id');

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            return $query
                ->where('user_id', $user->id)
                ->where('id', $shop->market_company_id)
                ->get();
        }

        return $query
            ->where(function (Builder $query) use ($shop) {
                $query
                    ->whereDoesntHave('shop')
                    ->orWhere('id', $shop->market_company_id);
            })
            ->get();
    }

    /** Метод для сохранения логтипа */
    private function storeShopLogo(Request $request): string
    {
        if (!$this->imageProcessorEnabled()) {
            return $request->file('logo')->store(
                'market/market_shops/logos',
                'public'
            );
        }

        return app(ImagePresetService::class)->storeUploadedImage(
            file: $request->file('logo'),
            presetKey: 'square_medium',
            directory: 'market/market_shops/logos',
            storeOriginal: false
        );
    }

    /** Сервис определения настройки процессора изображений */
    protected function imageProcessorEnabled(): bool
    {
        return app(AdminFeatureService::class)->imageProcessorEnabled();
    }
}
