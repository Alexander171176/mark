<?php

namespace App\Http\Controllers\Public\Default\Market\MarketTag;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Market\MarketTag\MarketTagResource;
use App\Models\Admin\Market\MarketTag\MarketTag;
use App\Traits\Public\Market\BuildsMarketCategoryTreeTrait;
use App\Traits\Public\Market\HasMarketSidebarDataTrait;
use Inertia\Inertia;
use Inertia\Response;

class MarketTagController extends Controller
{
    use BuildsMarketCategoryTreeTrait;
    use HasMarketSidebarDataTrait;

    /** Страница конкретного тега маркетплейса. */
    public function show(string $url): Response
    {
        $locale = app()->getLocale();

        /** Получаем публичный тег */
        $tag = MarketTag::query()
            ->forPublic()
            ->where('url', $url)
            ->with([
                'translations',
                'owner',
            ])
            ->withCount('products')
            ->firstOrFail();

        /** Увеличиваем просмотры тега */
        $tag->increment('views');

        /** Дерево категорий для левого сайдбара */
        $categoryTree = $this->getMarketCategoryTree($locale);

        /** Данные сайдбаров маркетплейса */
        $sidebarData = $this->getMarketSidebarData($locale);

        return Inertia::render('Public/Default/Market/MarketTags/Show', [
            'tag' => new MarketTagResource($tag),

            'categoryTree' => $categoryTree,
            'locale' => $locale,

            ...$sidebarData,
        ]);
    }
}
