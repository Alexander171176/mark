<?php

namespace App\Services\Public\Cms;

use App\Models\Admin\Cms\CmsPage\CmsPage;

class CmsPageResolverService
{
    /**
     * Найти публичную CMS-страницу по URL.
     */
    public function resolve(string $url): ?CmsPage
    {
        $url = $this->normalizeUrl($url);

        return CmsPage::query()
            ->with([
                'translations',
            ])
            ->forPublic()
            ->where('url', $url)
            ->first();
    }

    /**
     * Нормализация URL для поиска в cms_pages.url.
     */
    public function normalizeUrl(?string $url): string
    {
        $url = trim((string) $url, '/');

        $locales = config('app.available_locales', ['ru', 'kk', 'en']);

        $segments = explode('/', $url);

        if (! empty($segments[0]) && in_array($segments[0], $locales, true)) {
            array_shift($segments);
        }

        $url = '/' . trim(implode('/', $segments), '/');

        return $url === '/' ? '/' : $url;
    }

    /**
     * Найти CMS-страницу для SEO по URL.
     */
    public function resolveSeo(string $url): ?CmsPage
    {
        $url = $this->normalizeUrl($url);

        return CmsPage::query()
            ->with([
                'translations',
            ])
            ->forPublic()
            ->where('url', $url)
            ->where('show_seo', true)
            ->first();
    }
}
