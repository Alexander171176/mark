<?php

namespace App\Http\Controllers\Admin\Statistics\Chart;

use App\Http\Controllers\Controller;
use App\Models\Admin\Blog\Article\Article;
use App\Models\Admin\Blog\Rubric\Rubric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ChartController extends Controller
{
    /**
     * Страница с графиками.
     */
    public function index(Request $request): Response
    {
        /**
         * 1) График по рубрикам
         * Формат для RubricBarChart01: [{ name: string, value: number }]
         *
         * Считаем сумму просмотров статей, привязанных к рубрике через pivot article_has_rubric
         * Важно: leftJoin, чтобы рубрики без статей тоже попадали с value = 0
         */
        $rubrics = DB::table('rubrics')
            ->leftJoin('article_has_rubric as ahr', 'rubrics.id', '=', 'ahr.rubric_id')
            ->leftJoin('articles', 'ahr.article_id', '=', 'articles.id')
            ->select([
                'rubrics.id',
                DB::raw('rubrics.title as name'),
                DB::raw('COALESCE(SUM(articles.views), 0) as value'),
            ])
            ->groupBy('rubrics.id', 'rubrics.title')
            ->orderByDesc('value')
            ->limit(15)
            ->get();

        /**
         * 2) График по статьям
         * Для ArticleLineChart01 нужны поля: id, views, likes_count
         * (title можно оставить — пригодится в тултипах/позже)
         */
        $articles = Article::query()
            ->select(['id', 'title', 'views'])
            ->withCount('likes')            // даст likes_count
            ->orderByDesc('views')
            ->limit(50)
            ->get();

        return Inertia::render('Admin/Statistics/Charts/Index', [
            'rubrics'  => $rubrics,
            'articles' => $articles,
        ]);
    }
}
