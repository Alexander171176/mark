<?php

use App\Context\Location\LocationContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Вход в публичный контент без Location.
 *
 * Например:
 * /ru/blog/articles
 *
 * перенаправляется на:
 * /ru/astana/blog/articles
 */
Route::get('/{section}/{path?}', function (
    Request $request,
    LocationContext $context,
    string $section,
    ?string $path = null
) {
    $location = $context->current();

    if (!$location) {
        abort(404);
    }

    $segments = $request->segments();

    /**
     * После locale вставляем slug текущей Location:
     *
     * ru/blog/articles
     * ↓
     * ru/astana/blog/articles
     */
    array_splice(
        $segments,
        1,
        0,
        [$location->slug]
    );

    $url = url('/' . implode('/', $segments));

    if ($request->getQueryString()) {
        $url .= '?' . $request->getQueryString();
    }

    return redirect()->to($url);
})
    ->where('section', 'blog|school|market')
    ->where('path', '.*')
    ->name('public.content.gateway');


/**
 * Канонические географические маршруты.
 */
Route::prefix('{location}')
    ->where([
        'location' => '[A-Za-z0-9\-]+',
    ])
    ->group(function () {

        // blog
        require __DIR__ . '/blog/rubrics.php'; // Рубрики Блога
        require __DIR__ . '/blog/articles.php'; // Посты Блога
        require __DIR__ . '/blog/tags.php'; // Теги Блога
        require __DIR__ . '/blog/videos.php'; // Видео

        // school
        require __DIR__ . '/school/instructors.php'; // Инструкторы
        require __DIR__ . '/school/hashtags.php'; // Категории обучения
        require __DIR__ . '/school/tracks.php'; // Категории обучения
        require __DIR__ . '/school/courses.php'; // Курсы
        require __DIR__ . '/school/modules.php'; // Модули
        require __DIR__ . '/school/lessons.php'; // Уроки
        require __DIR__ . '/school/assignments.php'; // Задания

        // market
        require __DIR__ . '/market/categories.php'; // Категории магазина
        require __DIR__ . '/market/tags.php'; // Теги магазина
        require __DIR__ . '/market/products.php'; // Товары магазина
    });
