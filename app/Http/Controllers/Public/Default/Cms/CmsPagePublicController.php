<?php

namespace App\Http\Controllers\Public\Default\Cms;

use App\Http\Controllers\Controller;
use App\Services\Public\Cms\CmsPageResolverService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CmsPagePublicController extends Controller
{
    public function __construct(
        protected CmsPageResolverService $resolver
    ) {}

    /**
     * Показ публичной CMS-страницы по URL.
     */
    public function show(Request $request, ?string $slug = null): Response
    {
        $page = $this->resolver->resolve($slug ?? '/');

        abort_if(! $page, 404);
        abort_if(! $page->show_content, 404);

        $translation = $page->translationOrFallback();

        abort_if(! $translation, 404);

        return Inertia::render('Public/Default/Cms/Show', [
            'cmsPage' => [
                'id' => $page->id,
                'url' => $page->url,

                'title' => $translation->title,
                'subtitle' => $translation->subtitle,
                'short' => $translation->short,
                'description' => $translation->description,

                'show_content' => (bool) $page->show_content,
                'show_seo' => (bool) $page->show_seo,

                'seo' => $page->show_seo
                    ? [
                        'title' => $translation->meta_title ?: $translation->title,
                        'keywords' => $translation->meta_keywords,
                        'description' => $translation->meta_desc ?: $translation->short,
                    ]
                    : null,
            ],
        ]);
    }
}
