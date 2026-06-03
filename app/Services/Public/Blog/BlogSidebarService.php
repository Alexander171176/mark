<?php

namespace App\Services\Public\Blog;

use App\Http\Resources\Admin\Blog\BlogArticle\BlogArticleSharedResource;
use App\Http\Resources\Admin\Blog\BlogBanner\BlogBannerSharedResource;
use App\Http\Resources\Admin\Blog\BlogTag\BlogTagSharedResource;
use App\Http\Resources\Admin\Blog\BlogVideo\BlogVideoSharedResource;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolHashtag\SchoolHashtagSharedResource;
use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogBanner\BlogBanner;
use App\Models\Admin\Blog\BlogTag\BlogTag;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolHashtag\SchoolHashtag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class BlogSidebarService
{
    /** Время жизни кэша sidebar-данных. */
    protected int $ttl = 600;

    /** Получить данные для публичных колонок. */
    public function getSidebarData(string $locale): array
    {
        return Cache::remember(
            $this->getCacheKey($locale),
            $this->ttl,
            fn () => $this->buildSidebarData($locale)
        );
    }

    /** Очистить кэш одной локали. */
    public function forget(string $locale): void
    {
        Cache::forget($this->getCacheKey($locale));
    }

    /** Очистить кэш всех локалей. */
    public function forgetAll(): void
    {
        foreach (config('app.available_locales', []) as $locale) {
            $this->forget($locale);
        }
    }

    /** Собрать данные для левой, центральной и правой колонок. */
    protected function buildSidebarData(string $locale): array
    {
        return [
            'tags' => BlogTagSharedResource::collection($this->getTags($locale)),

            'leftArticles' => BlogArticleSharedResource::collection($this->getArticlesByFlag($locale, 'left', 3)),
            'mainArticles' => BlogArticleSharedResource::collection($this->getArticlesByFlag($locale, 'main', 6)),
            'rightArticles' => BlogArticleSharedResource::collection($this->getArticlesByFlag($locale, 'right', 3)),
            'popularArticles' => BlogArticleSharedResource::collection($this->getPopularArticles($locale, 6)),

            'leftBanners' => BlogBannerSharedResource::collection($this->getBannersByFlag($locale, 'left', 3)),
            'mainBanners' => BlogBannerSharedResource::collection($this->getBannersByFlag($locale, 'main', 6)),
            'rightBanners' => BlogBannerSharedResource::collection($this->getBannersByFlag($locale, 'right', 3)),

            'leftVideos' => BlogVideoSharedResource::collection($this->getVideosByFlag($locale, 'left', 3)),
            'mainVideos' => BlogVideoSharedResource::collection($this->getVideosByFlag($locale, 'main', 6)),
            'rightVideos' => BlogVideoSharedResource::collection($this->getVideosByFlag($locale, 'right', 3)),
            'popularVideos' => BlogVideoSharedResource::collection($this->getPopularVideos($locale, 6)),

            'hashtags' => SchoolHashtagSharedResource::collection($this->getHashtags($locale)),

            'leftCourses' => SchoolCourseSharedResource::collection($this->getCoursesByFlag($locale, 'left', 3)),
            'mainCourses' => SchoolCourseSharedResource::collection($this->getCoursesByFlag($locale, 'main', 6)),
            'rightCourses' => SchoolCourseSharedResource::collection($this->getCoursesByFlag($locale, 'right', 3)),
            'popularCourses' => SchoolCourseSharedResource::collection($this->getPopularCourses($locale, 6)),
        ];
    }

    /** Облако тегов блога. */
    protected function getTags(string $locale): Collection
    {
        return BlogTag::query()
            ->with('translations')
            ->forTagCloud(0, $locale)
            ->get();
    }

    /** Статьи блога по флагу left/main/right. */
    protected function getArticlesByFlag(string $locale, string $flag, int $limit): Collection
    {
        return BlogArticle::query()
            ->forPublic()
            ->where($flag, true)
            ->with([
                'translations',
                'owner',
                'images',
            ])
            ->sortByParam('sort_desc', $locale)
            ->limit($limit)
            ->get();
    }

    /** Популярные статьи блога. */
    protected function getPopularArticles(string $locale, int $limit): Collection
    {
        return BlogArticle::query()
            ->forPublic()
            ->with([
                'translations',
                'owner',
                'images',
            ])
            ->sortByParam('views_desc', $locale)
            ->limit($limit)
            ->get();
    }

    /** Баннеры блога по флагу left/main/right. */
    protected function getBannersByFlag(string $locale, string $flag, int $limit): Collection
    {
        return BlogBanner::query()
            ->forPublic()
            ->wherePosition($flag)
            ->with([
                'translations',
                'images',
            ])
            ->sortByParam('sort_asc', $locale)
            ->limit($limit)
            ->get();
    }

    /** Видео блога по флагу left/main/right. */
    protected function getVideosByFlag(string $locale, string $flag, int $limit): Collection
    {
        return BlogVideo::query()
            ->forPublic()
            ->where($flag, true)
            ->with([
                'translations',
                'owner',
                'images',
            ])
            ->sortByParam('sort_desc', $locale)
            ->limit($limit)
            ->get();
    }

    /** Популярные видео блога. */
    protected function getPopularVideos(string $locale, int $limit): Collection
    {
        return BlogVideo::query()
            ->forPublic()
            ->with([
                'translations',
                'owner',
                'images',
            ])
            ->sortByParam('views_desc', $locale)
            ->limit($limit)
            ->get();
    }

    /** Облако хештегов школы. */
    protected function getHashtags(string $locale): Collection
    {
        return SchoolHashtag::query()
            ->forTagCloud($locale)
            ->get();
    }

    /** Курсы школы по флагу left/main/right. */
    protected function getCoursesByFlag(string $locale, string $flag, int $limit): Collection
    {
        return SchoolCourse::query()
            ->forPublic($locale)
            ->where($flag, true)
            ->with([
                'translation',
                'translations',
                'images',
                'instructorProfile',
            ])
            ->sortByParam('sort_desc')
            ->limit($limit)
            ->get();
    }

    /** Популярные курсы школы. */
    protected function getPopularCourses(string $locale, int $limit): Collection
    {
        return SchoolCourse::query()
            ->forPublic($locale)
            ->with([
                'translation',
                'translations',
                'images',
                'instructorProfile',
            ])
            ->sortByParam('views_desc')
            ->limit($limit)
            ->get();
    }

    /** Ключ кэша для sidebar-данных. */
    protected function getCacheKey(string $locale): string
    {
        return "blog_sidebar_data_{$locale}";
    }
}
