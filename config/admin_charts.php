<?php

use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogRubric\BlogRubric;
use App\Models\Admin\Blog\BlogTag\BlogTag;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;

use App\Models\Admin\School\SchoolAssignment\SchoolAssignment;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolHashtag\SchoolHashtag;

use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfile;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use App\Models\Admin\School\SchoolModule\SchoolModule;
use App\Models\Admin\School\SchoolTrack\SchoolTrack;

use App\Models\Admin\Market\MarketCategory\MarketCategory;
use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\Admin\Market\MarketTag\MarketTag;

use App\Models\Admin\Cms\CmsPage\CmsPage;

return [

    /*
    |--------------------------------------------------------------------------
    | Сущность по умолчанию
    |--------------------------------------------------------------------------
    */

    'default_entity' => 'blog_articles',

    /*
    |--------------------------------------------------------------------------
    | Сущности
    |--------------------------------------------------------------------------
    */

    'entities' => [

        /*
        |--------------------------------------------------------------------------
        | Blog
        |--------------------------------------------------------------------------
        */

        'blog_rubrics' => [
            'label_key' => 'rubrics',

            'model' => BlogRubric::class,
            'table' => 'blog_rubrics',

            'title_relation' => 'translations',
            'title_foreign_key' => 'rubric_id',
            'title_table' => 'blog_rubric_translations',
            'title_column' => 'title',

            'has_activity' => true,
            'has_views' => true,
            'has_likes' => false,
        ],

        'blog_articles' => [
            'label_key' => 'articles',

            'model' => BlogArticle::class,
            'table' => 'blog_articles',

            'title_relation' => 'translations',
            'title_foreign_key' => 'article_id',
            'title_table' => 'blog_article_translations',
            'title_column' => 'title',

            'has_activity' => true,
            'has_views' => true,
            'has_likes' => true,

            'likes_relation' => 'likes',
        ],

        'blog_videos' => [
            'label_key' => 'videos',

            'model' => BlogVideo::class,
            'table' => 'blog_videos',

            'title_relation' => 'translations',
            'title_foreign_key' => 'video_id',
            'title_table' => 'blog_video_translations',
            'title_column' => 'title',

            'has_activity' => true,
            'has_views' => true,
            'has_likes' => true,

            'likes_relation' => 'likes',
        ],

        'blog_tags' => [
            'label_key' => 'tags',

            'model' => BlogTag::class,
            'table' => 'blog_tags',

            'title_relation' => 'translations',
            'title_foreign_key' => 'tag_id',
            'title_table' => 'blog_tag_translations',
            'title_column' => 'name',

            'has_activity' => true,
            'has_views' => true,
            'has_likes' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | School
        |--------------------------------------------------------------------------
        */

        'school_instructors' => [
            'label_key' => 'instructors',

            'model' => SchoolInstructorProfile::class,
            'table' => 'school_instructor_profiles',

            'title_relation' => 'translations',
            'title_foreign_key' => 'school_instructor_profile_id',
            'title_table' => 'school_instructor_profile_translations',
            'title_column' => 'title',

            'has_activity' => true,
            'has_views' => true,
            'has_likes' => false,
        ],

        'school_hashtags' => [
            'label_key' => 'hashtags',

            'model' => SchoolHashtag::class,
            'table' => 'school_hashtags',

            'title_relation' => 'translations',
            'title_foreign_key' => 'school_hashtag_id',
            'title_table' => 'school_hashtag_translations',
            'title_column' => 'name',

            'has_activity' => true,
            'has_views' => true,
            'has_likes' => false,
        ],

        'school_tracks' => [
            'label_key' => 'tracks',

            'model' => SchoolTrack::class,
            'table' => 'school_tracks',

            'title_relation' => 'translations',
            'title_foreign_key' => 'school_track_id',
            'title_table' => 'school_track_translations',
            'title_column' => 'name',

            'has_activity' => true,
            'has_views' => true,
            'has_likes' => false,
        ],

        'school_courses' => [
            'label_key' => 'courses',

            'model' => SchoolCourse::class,
            'table' => 'school_courses',

            'title_relation' => 'translations',
            'title_foreign_key' => 'school_course_id',
            'title_table' => 'school_course_translations',
            'title_column' => 'title',

            'has_activity' => true,
            'has_views' => true,
            'has_likes' => true,

            'likes_relation' => 'likes',
        ],

        'school_modules' => [
            'label_key' => 'modules',

            'model' => SchoolModule::class,
            'table' => 'school_modules',

            'title_relation' => 'translations',
            'title_foreign_key' => 'school_module_id',
            'title_table' => 'school_module_translations',
            'title_column' => 'title',

            'has_activity' => true,
            'has_views' => true,
            'has_likes' => true,

            'likes_relation' => 'likes',
        ],

        'school_lessons' => [
            'label_key' => 'lessons',

            'model' => SchoolLesson::class,
            'table' => 'school_lessons',

            'title_relation' => 'translations',
            'title_foreign_key' => 'school_lesson_id',
            'title_table' => 'school_lesson_translations',
            'title_column' => 'title',

            'has_activity' => true,
            'has_views' => true,
            'has_likes' => true,

            'likes_relation' => 'likes',
        ],

        'school_assignments' => [
            'label_key' => 'assignments',

            'model' => SchoolAssignment::class,
            'table' => 'school_assignments',

            'title_relation' => 'translations',
            'title_foreign_key' => 'assignment_id',
            'title_table' => 'school_assignment_translations',
            'title_column' => 'title',

            'has_activity' => true,
            'has_views' => false,
            'has_likes' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | Market
        |--------------------------------------------------------------------------
        */

        'market_categories' => [
            'label_key' => 'categories',

            'model' => MarketCategory::class,
            'table' => 'market_categories',

            'title_relation' => 'translations',
            'title_foreign_key' => 'market_category_id',
            'title_table' => 'market_category_translations',
            'title_column' => 'title',

            'has_activity' => true,
            'has_views' => true,
            'has_likes' => false,
        ],

        'market_tags' => [
            'label_key' => 'tags',

            'model' => MarketTag::class,
            'table' => 'market_tags',

            'title_relation' => 'translations',
            'title_foreign_key' => 'market_tag_id',
            'title_table' => 'market_tag_translations',
            'title_column' => 'title',

            'has_activity' => true,
            'has_views' => true,
            'has_likes' => false,
        ],

        'market_products' => [
            'label_key' => 'products',

            'model' => MarketProduct::class,
            'table' => 'market_products',

            'title_relation' => 'translations',
            'title_foreign_key' => 'market_product_id',
            'title_table' => 'market_product_translations',
            'title_column' => 'title',

            'has_activity' => true,
            'has_views' => true,
            'has_likes' => true,

            'likes_relation' => 'likes',
        ],

        /*
        |--------------------------------------------------------------------------
        | CMS
        |--------------------------------------------------------------------------
        */

        'cms_pages' => [
            'label_key' => 'pages',

            'model' => CmsPage::class,
            'table' => 'cms_pages',

            'title_relation' => 'translations',
            'title_foreign_key' => 'cms_page_id',
            'title_table' => 'cms_page_translations',
            'title_column' => 'title',

            'has_activity' => true,
            'has_views' => true,
            'has_likes' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Метрики
    |--------------------------------------------------------------------------
    */

    'metrics' => [

        'views' => [
            'label_key' => 'views',
            'field' => 'views',
            'type' => 'sum',
        ],

        'likes' => [
            'label_key' => 'likes',
            'field' => 'likes_count',
            'type' => 'count_relation',
        ],

        'activity' => [
            'label_key' => 'activity',
            'field' => 'activity',
            'type' => 'boolean',
        ],

        'created' => [
            'label_key' => 'createdAt',
            'field' => 'created_at',
            'type' => 'date_count',
        ],

        'updated' => [
            'label_key' => 'updatedAt',
            'field' => 'updated_at',
            'type' => 'date_count',
        ],
    ],
];
