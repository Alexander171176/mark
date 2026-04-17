<?php

namespace App\Services\Public\Blog;

use App\Http\Resources\Admin\Blog\Article\ArticleResource;
use App\Http\Resources\Admin\Blog\Banner\BannerResource;
use App\Http\Resources\Admin\Blog\Tag\TagSharedResource;
use App\Http\Resources\Admin\Blog\Video\VideoResource;
use App\Http\Resources\Admin\School\Course\CourseSharedResource;
use App\Http\Resources\Admin\School\Hashtag\HashtagSharedResource;
use App\Models\Admin\Blog\Article\Article;
use App\Models\Admin\Blog\Banner\Banner;
use App\Models\Admin\Blog\Tag\Tag;
use App\Models\Admin\Blog\Video\Video;
use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\Hashtag\Hashtag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Сервис отображения левой и правой колонки
 *  - время кэширования
 *  - используемый кэш, ключи
 *  - Строитель блоков:
 *  - Баннеры по флагам left, main, right
 *  - Видео по флагам left, main, right и по популярности
 *  - Иерархия Рубрик (Блог)
 *  - Статьи по флагам left, main, right и по популярности (Блог)
 *  - Облако Тегов (Блог)
 *  - Курсы по флагам left, main, right и по популярности (Школа)
 *  - Иерархия Треков (Школа)
 *  - Облако Хештегов (Школа)
 */
class BlogSidebarService
{
    /**
     * период кэша
     * @var int
     */
    protected int $ttl = 600;

    /**
     * Кэширование данных
     *
     * @param string $locale
     * @return array
     */
    public function getSidebarData(string $locale): array
    {
        return Cache::remember(
            $this->getCacheKey($locale),
            $this->ttl,
            fn () => $this->buildSidebarData($locale)
        );
    }

    /**
     * Используемый кэш
     *
     * @param string $locale
     * @return void
     */
    public function forget(string $locale): void
    {
        Cache::forget($this->getCacheKey($locale));
    }

    public function forgetAll(): void
    {
        foreach (config('app.available_locales', []) as $locale) {
            $this->forget($locale);
        }
    }

    /**
     * Строитель блоков данных в колонках
     *
     * @param string $locale
     * @return array
     */
    protected function buildSidebarData(string $locale): array
    {
        return [
            'tags' => TagSharedResource::collection($this->getTags($locale)),

            'leftArticles' => ArticleResource::collection($this->getArticlesByFlag($locale, 'left', 3)),
            'mainArticles' => ArticleResource::collection($this->getArticlesByFlag($locale, 'main', 6)),
            'rightArticles' => ArticleResource::collection($this->getArticlesByFlag($locale, 'right', 3)),
            'popularArticles' => ArticleResource::collection($this->getPopularArticles($locale, 6)),

            'leftBanners' => BannerResource::collection($this->getBannersByFlag($locale, 'left', 3)),
            'mainBanners' => BannerResource::collection($this->getBannersByFlag($locale, 'main', 6)),
            'rightBanners' => BannerResource::collection($this->getBannersByFlag($locale, 'right', 3)),

            'leftVideos' => VideoResource::collection($this->getVideosByFlag($locale, 'left', 3)),
            'mainVideos' => VideoResource::collection($this->getVideosByFlag($locale, 'main', 6)),
            'rightVideos' => VideoResource::collection($this->getVideosByFlag($locale, 'right', 3)),
            'popularVideos' => VideoResource::collection($this->getPopularVideos($locale, 6)),

            'hashtags' => HashtagSharedResource::collection($this->getHashtags($locale)),

            'leftCourses' => CourseSharedResource::collection($this->getCoursesByFlag($locale, 'left', 3)),
            'mainCourses' => CourseSharedResource::collection($this->getCoursesByFlag($locale, 'main', 6)),
            'rightCourses' => CourseSharedResource::collection($this->getCoursesByFlag($locale, 'right', 3)),
            'popularCourses' => CourseSharedResource::collection($this->getPopularCourses($locale, 6)),
        ];
    }

    /**
     * Блок Тегов
     *
     * @param string $locale
     * @return Collection
     */
    protected function getTags(string $locale): Collection
    {
        return Tag::query()
            ->forTagCloud($locale)
            ->get();
    }

    /**
     * Статьи по флагам
     *
     * @param string $locale
     * @param string $flag
     * @param int $limit
     * @return Collection
     */
    protected function getArticlesByFlag(string $locale, string $flag, int $limit): Collection
    {
        return Article::query()
            ->forPublic($locale)
            ->where($flag, true)
            ->with([
                'owner',
                'images' => fn ($q) => $q->orderBy('order'),
            ])
            ->sortByParam('sort_desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Популярные Статьи
     *
     * @param string $locale
     * @param int $limit
     * @return Collection
     */
    protected function getPopularArticles(string $locale, int $limit): Collection
    {
        return Article::query()
            ->forPublic($locale)
            ->with([
                'owner',
                'images' => fn ($q) => $q->orderBy('order'),
            ])
            ->sortByParam('views_desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Баннеры по флагам
     *
     * @param string $locale
     * @param string $flag
     * @param int $limit
     * @return Collection
     */
    protected function getBannersByFlag(string $locale, string $flag, int $limit): Collection
    {
        return Banner::query()
            ->forPublic($locale)
            ->wherePosition($flag)
            ->withImages()
            ->ordered()
            ->limit($limit)
            ->get();
    }

    /**
     * Видео по флагам
     *
     * @param string $locale
     * @param string $flag
     * @param int $limit
     * @return Collection
     */
    protected function getVideosByFlag(string $locale, string $flag, int $limit): Collection
    {
        return Video::query()
            ->forPublic($locale)
            ->where($flag, true)
            ->with([
                'owner',
                'images' => fn ($q) => $q->orderBy('order'),
            ])
            ->sortByParam('sort_desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Популярные видео
     *
     * @param string $locale
     * @param int $limit
     * @return Collection
     */
    protected function getPopularVideos(string $locale, int $limit): Collection
    {
        return Video::query()
            ->forPublic($locale)
            ->with([
                'owner',
                'images' => fn ($q) => $q->orderBy('order'),
            ])
            ->sortByParam('views_desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Блок Хештегов школы
     *
     * @param string $locale
     * @return Collection
     */
    protected function getHashtags(string $locale): Collection
    {
        return Hashtag::query()
            ->forTagCloud($locale)
            ->get();
    }

    /**
     * Курсы по флагам
     *
     * @param string $locale
     * @param string $flag
     * @param int $limit
     * @return Collection
     */
    protected function getCoursesByFlag(string $locale, string $flag, int $limit): Collection
    {
        return Course::query()
            ->forPublic($locale)
            ->where($flag, true)
            ->with([
                'instructorProfile.images' => fn ($q) => $q->orderBy('instructor_profile_has_images.order', 'asc'),
                'images' => fn ($q) => $q->orderBy('course_has_images.order', 'asc'),
            ])
            ->sortByParam('sort_desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Популярные курсы
     *
     * @param string $locale
     * @param int $limit
     * @return Collection
     */
    protected function getPopularCourses(string $locale, int $limit): Collection
    {
        return Course::query()
            ->forPublic($locale)
            ->with([
                'instructorProfile.images' => fn ($q) => $q->orderBy('instructor_profile_has_images.order', 'asc'),
                'images' => fn ($q) => $q->orderBy('course_has_images.order', 'asc'),
            ])
            ->sortByParam('views_desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Ключи для кэша
     *
     * @param string $locale
     * @return string
     */
    protected function getCacheKey(string $locale): string
    {
        return "blog_sidebar_data_{$locale}";
    }
}
