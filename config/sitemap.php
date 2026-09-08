<?php

use App\Models\Admin\Cms\CmsPage\CmsPage;
use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogRubric\BlogRubric;
use App\Models\Admin\Blog\BlogTag\BlogTag;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use App\Models\Admin\Market\MarketCategory\MarketCategory;
use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\Admin\Market\MarketTag\MarketTag;
use App\Models\Admin\School\SchoolAssignment\SchoolAssignment;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolHashtag\SchoolHashtag;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfile;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use App\Models\Admin\School\SchoolModule\SchoolModule;
use App\Models\Admin\School\SchoolTrack\SchoolTrack;
use Spatie\Sitemap\Tags\Url;

return [

    /** Главный sitemap index. */
    'file' => 'sitemap.xml',

    /** Шаблон sitemap-файла для отдельной локали. */
    'localeFile' => 'sitemap-{locale}.xml',

    /** Все доступные локали приложения. */
    'locales' => config(
        'app.available_locales',
        [
            config('app.locale', 'ru'),
        ]
    ),

    /** Публичные ресурсы sitemap. */
    'resources' => [

        /** Блог */
        'blog' => [

            'rubrics' => [
                'model' => BlogRubric::class,

                'indexRoute' => 'public.blogRubrics.index',
                'showRoute' => 'public.blogRubrics.show',

                'urlField' => 'url',
                'routeParameter' => 'url',

                'indexPriority' => 0.8,
                'priority' => 0.8,

                'indexChangeFrequency' => Url::CHANGE_FREQUENCY_WEEKLY,
                'changeFrequency' => Url::CHANGE_FREQUENCY_WEEKLY,
            ],

            'articles' => [
                'model' => BlogArticle::class,

                'indexRoute' => 'public.blogArticles.index',
                'showRoute' => 'public.blogArticles.show',

                'urlField' => 'url',
                'routeParameter' => 'url',

                'indexPriority' => 0.9,
                'priority' => 0.9,

                'indexChangeFrequency' => Url::CHANGE_FREQUENCY_DAILY,
                'changeFrequency' => Url::CHANGE_FREQUENCY_WEEKLY,
            ],

            'tags' => [
                'model' => BlogTag::class,

                'indexRoute' => null,
                'showRoute' => 'public.blogTags.show',

                'urlField' => 'slug',
                'routeParameter' => 'slug',

                'priority' => 0.7,

                'changeFrequency' => Url::CHANGE_FREQUENCY_MONTHLY,
            ],

            'videos' => [
                'model' => BlogVideo::class,

                'indexRoute' => 'public.blogVideos.index',
                'showRoute' => 'public.blogVideos.show',

                'urlField' => 'url',
                'routeParameter' => 'url',

                'indexPriority' => 0.8,
                'priority' => 0.8,

                'indexChangeFrequency' => Url::CHANGE_FREQUENCY_WEEKLY,
                'changeFrequency' => Url::CHANGE_FREQUENCY_WEEKLY,
            ],
        ],

        /** CMS */
        'cms' => [

            'pages' => [
                'model' => CmsPage::class,

                'indexRoute' => null,
                'showRoute' => 'public.cmsPages.show',

                'urlField' => 'url',
                'routeParameter' => 'slug',

                'scope' => 'withOwnContent',

                'trimRouteParameterSlashes' => true,

                'excludeUrlValues' => [
                    '/',
                ],

                'priority' => 0.7,

                'changeFrequency' => Url::CHANGE_FREQUENCY_MONTHLY,
            ],
        ],

        /** Онлайн-школа */
        'school' => [

            /** Направления обучения */
            'tracks' => [
                'model' => SchoolTrack::class,

                'indexRoute' => 'public.schoolTracks.index',
                'showRoute' => 'public.schoolTracks.show',

                'urlField' => 'slug',
                'routeParameter' => 'slug',

                'indexPriority' => 0.8,
                'priority' => 0.8,

                'indexChangeFrequency' => Url::CHANGE_FREQUENCY_WEEKLY,
                'changeFrequency' => Url::CHANGE_FREQUENCY_WEEKLY,
            ],

            /** Курсы */
            'courses' => [
                'model' => SchoolCourse::class,

                'indexRoute' => 'public.schoolCourses.index',
                'showRoute' => 'public.schoolCourses.show',

                'urlField' => 'slug',
                'routeParameter' => 'slug',

                'indexPriority' => 0.9,
                'priority' => 0.9,

                'indexChangeFrequency' => Url::CHANGE_FREQUENCY_DAILY,
                'changeFrequency' => Url::CHANGE_FREQUENCY_WEEKLY,
            ],

            /** Модули курсов */
            'modules' => [
                'model' => SchoolModule::class,

                'indexRoute' => 'public.schoolModules.index',
                'showRoute' => 'public.schoolModules.show',

                'urlField' => 'slug',
                'routeParameter' => 'slug',

                'indexPriority' => 0.7,
                'priority' => 0.7,

                'indexChangeFrequency' => Url::CHANGE_FREQUENCY_WEEKLY,
                'changeFrequency' => Url::CHANGE_FREQUENCY_WEEKLY,
            ],

            /** Уроки */
            'lessons' => [
                'model' => SchoolLesson::class,

                'indexRoute' => 'public.schoolLessons.index',
                'showRoute' => 'public.schoolLessons.show',

                'urlField' => 'slug',
                'routeParameter' => 'slug',

                'indexPriority' => 0.7,
                'priority' => 0.7,

                'indexChangeFrequency' => Url::CHANGE_FREQUENCY_WEEKLY,
                'changeFrequency' => Url::CHANGE_FREQUENCY_WEEKLY,
            ],

            /** Задания */
            'assignments' => [
                'model' => SchoolAssignment::class,

                'indexRoute' => 'public.schoolAssignments.index',
                'showRoute' => 'public.schoolAssignments.show',

                'urlField' => 'slug',
                'routeParameter' => 'slug',

                'indexPriority' => 0.6,
                'priority' => 0.6,

                'indexChangeFrequency' => Url::CHANGE_FREQUENCY_WEEKLY,
                'changeFrequency' => Url::CHANGE_FREQUENCY_WEEKLY,
            ],

            /** Преподаватели */
            'instructors' => [
                'model' => SchoolInstructorProfile::class,

                'indexRoute' => 'public.schoolInstructors.index',
                'showRoute' => 'public.schoolInstructors.show',

                'urlField' => 'slug',
                'routeParameter' => 'slug',

                'indexPriority' => 0.7,
                'priority' => 0.7,

                'indexChangeFrequency' => Url::CHANGE_FREQUENCY_WEEKLY,
                'changeFrequency' => Url::CHANGE_FREQUENCY_MONTHLY,
            ],

            /** Хэштеги */
            'hashtags' => [
                'model' => SchoolHashtag::class,

                'indexRoute' => null,
                'showRoute' => 'public.schoolHashtags.show',

                'urlField' => 'slug',
                'routeParameter' => 'slug',

                'priority' => 0.6,

                'changeFrequency' => Url::CHANGE_FREQUENCY_MONTHLY,
            ],
        ],

        /** Маркетплейс */
        'market' => [

            /** Категории */
            'categories' => [
                'model' => MarketCategory::class,

                'indexRoute' => 'public.marketCategories.index',
                'showRoute' => 'public.marketCategories.show',

                'urlField' => 'url',
                'routeParameter' => 'url',

                'indexPriority' => 0.8,
                'priority' => 0.8,

                'indexChangeFrequency' => Url::CHANGE_FREQUENCY_DAILY,
                'changeFrequency' => Url::CHANGE_FREQUENCY_WEEKLY,
            ],

            /** Товары */
            'products' => [
                'model' => MarketProduct::class,

                'indexRoute' => 'public.marketProducts.index',
                'showRoute' => 'public.marketProducts.show',

                'urlField' => 'url',
                'routeParameter' => 'url',

                'indexPriority' => 0.9,
                'priority' => 0.9,

                'indexChangeFrequency' => Url::CHANGE_FREQUENCY_DAILY,
                'changeFrequency' => Url::CHANGE_FREQUENCY_DAILY,
            ],

            /** Теги */
            'tags' => [
                'model' => MarketTag::class,

                'indexRoute' => null,
                'showRoute' => 'public.marketTags.show',

                'urlField' => 'url',
                'routeParameter' => 'url',

                'priority' => 0.7,

                'changeFrequency' => Url::CHANGE_FREQUENCY_MONTHLY,
            ],
        ],
    ],
];
