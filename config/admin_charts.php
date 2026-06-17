<?php

use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogBanner\BlogBanner;
use App\Models\Admin\Blog\BlogRubric\BlogRubric;
use App\Models\Admin\Blog\BlogTag\BlogTag;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;

return [

    'default_entity' => 'blog_articles',

    'entities' => [

        'blog_rubrics' => [
            'label' => 'Рубрики',
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
            'label' => 'Статьи',
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
            'label' => 'Видео',
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
            'label' => 'Теги',
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

    ],

    'metrics' => [

        'views' => [
            'label' => 'Просмотры',
            'field' => 'views',
            'type' => 'sum',
        ],

        'likes' => [
            'label' => 'Лайки',
            'field' => 'likes_count',
            'type' => 'count_relation',
        ],

        'activity' => [
            'label' => 'Активность',
            'field' => 'activity',
            'type' => 'boolean',
        ],

        'created' => [
            'label' => 'Создано по датам',
            'field' => 'created_at',
            'type' => 'date_count',
        ],

        'updated' => [
            'label' => 'Обновлено по датам',
            'field' => 'updated_at',
            'type' => 'date_count',
        ],

    ],

];
