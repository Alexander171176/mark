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
        $url = '/' . trim((string) $url, '/');

        return $url === '/' ? '/' : $url;
    }
}
