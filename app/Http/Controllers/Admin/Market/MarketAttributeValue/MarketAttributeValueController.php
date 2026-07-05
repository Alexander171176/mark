<?php

namespace App\Http\Controllers\Admin\Market\MarketAttributeValue;

use App\Http\Controllers\Admin\Market\BaseMarketAdminController;
use App\Http\Requests\Admin\Market\MarketAttributeValue\MarketAttributeValueRequest;
use App\Http\Resources\Admin\Market\MarketAttribute\MarketAttributeSharedResource;
use App\Http\Resources\Admin\Market\MarketAttributeValue\MarketAttributeValueResource;
use App\Models\Admin\Market\MarketAttribute\MarketAttribute;
use App\Models\Admin\Market\MarketAttributeValue\MarketAttributeValue;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class MarketAttributeValueController extends BaseMarketAdminController
{
    /** Основная модель контроллера */
    protected string $modelClass = MarketAttributeValue::class;

    /** Название сущности для сообщений */
    protected string $entityLabel = 'значений характеристик';

    /** Поля переводов */
    protected array $translationFields = [
        'title',
        'subtitle',
        'short',
        'description',
    ];

    /** Список значений характеристик */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int('adminMarketAttributeValuesPerPage', 10);
        $defaultSort = $settings->string('adminMarketAttributeValuesDefaultSort', 'idDesc');

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $processingMode = $settings->string(
            'adminMarketAttributeValuesProcessingMode',
            'frontend'
        );

        $valuesCount = $this->baseQuery()->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer($processingMode, $valuesCount, 300);

        try {
            $values = $this->getIndexValues(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            return Inertia::render('Admin/Market/MarketAttributeValues/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminMarketAttributeValuesPerPage' => $perPage,
                'adminMarketAttributeValuesDefaultSort' => $defaultSort,
                'adminMarketAttributeValuesProcessingMode' => $processingMode,

                'values' => MarketAttributeValueResource::collection($values),
                'valuesCount' => $valuesCount,

                'sortParam' => $sortParam,
                'search' => $search,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка market attribute values: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/Market/MarketAttributeValues/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminMarketAttributeValuesPerPage' => $perPage,
                'adminMarketAttributeValuesDefaultSort' => $defaultSort,
                'adminMarketAttributeValuesProcessingMode' => $processingMode,

                'values' => [],
                'valuesCount' => 0,

                'sortParam' => $sortParam,
                'search' => $search,

                'error' => 'Ошибка загрузки значений характеристик.',
            ]);
        }
    }

    /** Страница создания значения характеристики */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/Market/MarketAttributeValues/Create', [
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'attributes' => MarketAttributeSharedResource::collection(
                $this->attributesForSelect()
            ),
        ]);
    }

    /** Создание значения характеристики */
    public function store(MarketAttributeValueRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];

        unset($data['translations']);

        $user = auth()->user();

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            unset(
                $data['moderation_status'],
                $data['moderated_by'],
                $data['moderated_at'],
                $data['moderation_note']
            );
        }

        try {
            DB::transaction(function () use (&$value, $data, $translations) {
                if (!isset($data['sort']) || is_null($data['sort'])) {
                    $maxSort = MarketAttributeValue::query()
                        ->where('market_attribute_id', $data['market_attribute_id'])
                        ->max('sort');

                    $data['sort'] = is_null($maxSort) ? 0 : $maxSort + 1;
                }

                $value = MarketAttributeValue::create($data);

                $this->syncTranslations($value, $translations);
            });

            return redirect()
                ->route('admin.marketAttributeValues.index')
                ->with('success', 'Значение характеристики успешно создано.');
        } catch (Throwable $e) {
            Log::error('Ошибка при создании market attribute value: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании значения характеристики.');
        }
    }

    /** Перенаправление просмотра на редактирование */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route('admin.marketAttributeValues.edit', $id);
    }

    /** Страница редактирования значения характеристики */
    public function edit(int $marketAttributeValue, Request $request): Response
    {
        $value = $this->baseQuery()
            ->with([
                'attribute.translations',
                'attribute.group.translations',
                'moderator',
                'translations',
            ])
            ->findOrFail($marketAttributeValue);

        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/Market/MarketAttributeValues/Edit', [
            'value' => new MarketAttributeValueResource($value),

            'attributes' => MarketAttributeSharedResource::collection(
                $this->attributesForSelect()
            ),

            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /** Обновление значения характеристики */
    public function update(
        MarketAttributeValueRequest $request,
        int $marketAttributeValue
    ): RedirectResponse {
        $value = $this->baseQuery()->findOrFail($marketAttributeValue);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];

        unset(
            $data['translations'],
            $data['_method']
        );

        $user = auth()->user();

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            unset(
                $data['moderation_status'],
                $data['moderated_by'],
                $data['moderated_at'],
                $data['moderation_note']
            );
        }

        try {
            DB::transaction(function () use ($value, $data, $translations) {
                $value->update($data);

                $this->syncTranslations($value, $translations);
            });

            return redirect()
                ->route('admin.marketAttributeValues.index')
                ->with('success', 'Значение характеристики успешно обновлено.');
        } catch (Throwable $e) {
            Log::error('Ошибка при обновлении market attribute value ID ' . $value->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении значения характеристики.');
        }
    }

    /** Удаление значения характеристики */
    public function destroy(int $marketAttributeValue): RedirectResponse
    {
        $value = $this->baseQuery()
            ->with('translations')
            ->findOrFail($marketAttributeValue);

        try {
            DB::transaction(function () use ($value) {
                $value->translations()->delete();
                $value->delete();
            });

            return redirect()
                ->route('admin.marketAttributeValues.index')
                ->with('success', 'Значение характеристики успешно удалено.');
        } catch (Throwable $e) {
            Log::error('Ошибка при удалении market attribute value ID ' . $value->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении значения характеристики.');
        }
    }

    /** Массовое удаление значений характеристик */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:market_attribute_values,id'],
        ]);

        $ids = $validated['ids'];

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            return back()->with('error', 'Часть значений характеристик недоступна для удаления.');
        }

        try {
            DB::transaction(function () use ($allowedIds) {
                DB::table('market_attribute_value_translations')
                    ->whereIn('market_attribute_value_id', $allowedIds)
                    ->delete();

                MarketAttributeValue::query()
                    ->whereIn('id', $allowedIds)
                    ->delete();
            });

            return back()->with('success', 'Выбранные значения характеристик успешно удалены.');
        } catch (Throwable $e) {
            Log::error('Ошибка bulkDestroy market attribute values: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при массовом удалении значений характеристик.');
        }
    }

    /** Базовый запрос списка значений характеристик */
    private function indexQuery(): Builder
    {
        return $this->baseQuery()
            ->with([
                'attribute.translations',
                'attribute.group.translations',
                'moderator',
                'translations',
            ]);
    }

    /** Получение списка значений характеристик для индекса */
    private function getIndexValues(
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

    /** Характеристики для select */
    private function attributesForSelect()
    {
        return MarketAttribute::query()
            ->with([
                'translations',
                'group.translations',
            ])
            ->withCount('values')
            ->active()
            ->approved()
            ->ordered()
            ->get();
    }
}
