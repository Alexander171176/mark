<?php

namespace App\Services\Public\School;

use App\Http\Resources\Public\Blog\BlogBanner\BlogBannerSharedResource;
use App\Http\Resources\Public\Blog\BlogVideo\BlogVideoSharedResource;
use App\Http\Resources\Public\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Public\School\SchoolHashtag\SchoolHashtagSharedResource;
use App\Models\Admin\Blog\BlogBanner\BlogBanner;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolHashtag\SchoolHashtag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SchoolSidebarService
{
    /**
     * Время жизни кэша sidebar-данных.
     */
    protected int $ttl = 600;

    /**
     * Получить данные для публичных
     * колонок школы.
     */
    public function getSidebarData(
        string $locale
    ): array {
        return Cache::remember(
            $this->getCacheKey($locale),
            $this->ttl,
            fn () =>
            $this->buildSidebarData($locale)
        );
    }

    /**
     * Очистить кэш одной локали.
     */
    public function forget(
        string $locale
    ): void {
        Cache::forget(
            $this->getCacheKey($locale)
        );
    }

    /**
     * Очистить кэш всех локалей.
     */
    public function forgetAll(): void
    {
        foreach (
            config('app.available_locales', [])
            as $locale
        ) {
            $this->forget($locale);
        }
    }

    /**
     * Собрать sidebar-данные школы.
     *
     * School:
     * - hashtags;
     * - courses.
     *
     * Общие публичные сущности:
     * - banners;
     * - videos.
     */
    protected function buildSidebarData(
        string $locale
    ): array {
        return [
            /**
             * School hashtags.
             */
            'hashtags' =>
                SchoolHashtagSharedResource::collection(
                    $this->getHashtags($locale)
                ),

            /**
             * School courses.
             */
            'leftCourses' =>
                SchoolCourseSharedResource::collection(
                    $this->getCoursesByFlag(
                        $locale,
                        'left',
                        2
                    )
                ),

            'mainCourses' =>
                SchoolCourseSharedResource::collection(
                    $this->getCoursesByFlag(
                        $locale,
                        'main',
                        2
                    )
                ),

            'rightCourses' =>
                SchoolCourseSharedResource::collection(
                    $this->getCoursesByFlag(
                        $locale,
                        'right',
                        2
                    )
                ),

            'popularCourses' =>
                SchoolCourseSharedResource::collection(
                    $this->getPopularCourses(
                        $locale,
                        12
                    )
                ),

            /**
             * Общие публичные баннеры.
             */
            'leftBanners' =>
                BlogBannerSharedResource::collection(
                    $this->getBannersByFlag(
                        $locale,
                        'left',
                        2
                    )
                ),

            'mainBanners' =>
                BlogBannerSharedResource::collection(
                    $this->getBannersByFlag(
                        $locale,
                        'main',
                        2
                    )
                ),

            'rightBanners' =>
                BlogBannerSharedResource::collection(
                    $this->getBannersByFlag(
                        $locale,
                        'right',
                        2
                    )
                ),

            /**
             * Общие публичные видео.
             */
            'leftVideos' =>
                BlogVideoSharedResource::collection(
                    $this->getVideosByFlag(
                        $locale,
                        'left',
                        2
                    )
                ),

            'mainVideos' =>
                BlogVideoSharedResource::collection(
                    $this->getVideosByFlag(
                        $locale,
                        'main',
                        2
                    )
                ),

            'rightVideos' =>
                BlogVideoSharedResource::collection(
                    $this->getVideosByFlag(
                        $locale,
                        'right',
                        2
                    )
                ),
        ];
    }

    /**
     * Облако хештегов школы.
     */
    protected function getHashtags(
        string $locale
    ): Collection {
        $locales =
            $this->publicLocales($locale);

        return SchoolHashtag::query()
            ->forTagCloud($locale)
            ->with([
                'translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),
            ])
            ->get();
    }

    /**
     * Курсы школы
     * по флагу left/main/right.
     */
    protected function getCoursesByFlag(
        string $locale,
        string $flag,
        int $limit
    ): Collection {
        $locales =
            $this->publicLocales($locale);

        return SchoolCourse::query()
            ->forPublic($locale)
            ->where(
                $flag,
                true
            )
            ->with([
                /**
                 * Current locale
                 * + fallback locale.
                 */
                'translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),

                /**
                 * Для sidebar нужны
                 * только изображения курса.
                 */
                'images.media',
            ])
            ->sortByParam(
                'sortDesc',
                $locale
            )
            ->limit($limit)
            ->get();
    }

    /**
     * Популярные курсы школы.
     */
    protected function getPopularCourses(
        string $locale,
        int $limit
    ): Collection {
        $locales =
            $this->publicLocales($locale);

        return SchoolCourse::query()
            ->forPublic($locale)
            ->with([
                /**
                 * Current locale
                 * + fallback locale.
                 */
                'translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),

                /**
                 * Для sidebar нужны
                 * только изображения курса.
                 */
                'images.media',
            ])
            ->sortByParam(
                'viewsDesc',
                $locale
            )
            ->limit($limit)
            ->get();
    }

    /**
     * Общие публичные баннеры
     * по позиции left/main/right.
     */
    protected function getBannersByFlag(
        string $locale,
        string $flag,
        int $limit
    ): Collection {
        $locales =
            $this->publicLocales($locale);

        return BlogBanner::query()
            ->forPublic()
            ->wherePosition($flag)
            ->with([
                /**
                 * Current locale
                 * + fallback locale.
                 */
                'translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),

                /**
                 * Изображения + Spatie Media
                 * загружаются пакетно.
                 */
                'images.media',
            ])
            ->sortByParam(
                'sortAsc',
                $locale
            )
            ->limit($limit)
            ->get();
    }

    /**
     * Общие публичные видео
     * по позиции left/main/right.
     */
    protected function getVideosByFlag(
        string $locale,
        string $flag,
        int $limit
    ): Collection {
        $locales =
            $this->publicLocales($locale);

        return BlogVideo::query()
            ->forPublic()
            ->where(
                $flag,
                true
            )
            ->with([
                /**
                 * Current locale
                 * + fallback locale.
                 */
                'translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),

                'owner',

                /**
                 * Изображения + их Media.
                 */
                'images.media',

                /**
                 * Собственный Media relation
                 * нужен local video.
                 */
                'media',
            ])
            ->sortByParam(
                'sortAsc',
                $locale
            )
            ->limit($limit)
            ->get();
    }

    /**
     * Локали публичного запроса:
     *
     * current
     * + fallback.
     */
    protected function publicLocales(
        string $locale
    ): array {
        return array_values(
            array_unique([
                $locale,

                config(
                    'app.fallback_locale',
                    'ru'
                ),
            ])
        );
    }

    /**
     * Ключ кэша sidebar-данных школы.
     */
    protected function getCacheKey(
        string $locale
    ): string {
        return "school_sidebar_data_{$locale}";
    }
}
