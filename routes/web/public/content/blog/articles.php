<?php
// Посты Блога из шаблона

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicArticleController = "App\\Http\\Controllers\\Public\\{$siteLayout}\\Blog\\ArticleController";

Route::get('/blog/articles', [$publicArticleController, 'index'])
    ->name('public.articles.index');

Route::get('/blog/articles/{url}', [$publicArticleController, 'show'])
    ->name('public.articles.show');
