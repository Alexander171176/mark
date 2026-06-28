<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'type' => 'static',
                'option' => 'infoModVersion',
                'value' => '1.0.0',
                'constant' => 'INFO_MOD_VERSION',
                'category' => 'system',
                'description' => 'Версия движка сайта',
                'activity' => true,
                'sort' => 0,
            ], // Версия движка сайта
            [
                'type' => 'string',
                'option' => 'siteLayout',
                'value' => 'Default',
                'constant' => 'SITE_LAYOUT',
                'category' => 'public',
                'description' => 'Шаблон публичной части сайта',
                'activity' => true,
                'sort' => 0,
            ], // LAYOUT SITE PUBLIC
            [
                'type' => 'checkbox',
                'option' => 'downtimeSite',
                'value' => 'false',
                'constant' => 'DOWNTIME_SITE',
                'category' => 'public',
                'description' => 'Включение/Выключение публичной части сайта на технические работы',
                'activity' => true,
                'sort' => 0,
            ], // SITE ON/OFF
            [
                'type' => 'string',
                'option' => 'widgetHexColor',
                'value' => '155e75',
                'constant' => 'WIDGET_HEX_COLOR',
                'category' => 'admin',
                'description' => 'Задаёт цвет панелей сайдбара и виджетов',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN Задаёт цвет панелей сайдбара и виджетов
            [
                'type' => 'float',
                'option' => 'widgetOpacity',
                'value' => '0.99',
                'constant' => 'WIDGET_OPACITY',
                'category' => 'admin',
                'description' => 'Задаёт прозрачность цвета панелей сайдбара и виджетов',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN Задаёт прозрачность цвета панелей сайдбара и виджетов
            [
                'type' => 'string',
                'option' => 'adminSidebarLightColor',
                'value' => 'bg-cyan-800',
                'constant' => 'ADMIN_SIDEBAR_LIGHT_COLOR',
                'category' => 'admin',
                'description' => 'Класс Tailwind CSS цвета светлого режима для левого Сайдбара',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN LIGHT BACKGROUND SIDEBAR
            [
                'type' => 'string',
                'option' => 'adminSidebarDarkColor',
                'value' => 'bg-gray-700',
                'constant' => 'ADMIN_SIDEBAR_DARK_COLOR',
                'category' => 'admin',
                'description' => 'Класс Tailwind CSS цвета тёмного режима для левого Сайдбара',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN DARK BACKGROUND SIDEBAR
            [
                'type' => 'string',
                'option' => 'adminSidebarLightText',
                'value' => 'text-slate-200',
                'constant' => 'ADMIN_SIDEBAR_LIGHT_TEXT',
                'category' => 'admin',
                'description' => 'Класс Tailwind CSS цвета текста светлого режима для левого Сайдбара',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN LIGHT TEXT SIDEBAR
            [
                'type' => 'string',
                'option' => 'adminSidebarDarkText',
                'value' => 'text-slate-200',
                'constant' => 'ADMIN_SIDEBAR_DARK_TEXT',
                'category' => 'admin',
                'description' => 'Класс Tailwind CSS цвета текста тёмного режима для левого Сайдбара',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN DARK TEXT SIDEBAR
            [
                'type' => 'string',
                'option' => 'adminSidebarLightHoverText',
                'value' => 'text-red-300',
                'constant' => 'ADMIN_SIDEBAR_LIGHT_HOVER_TEXT',
                'category' => 'admin',
                'description' => 'Класс Tailwind CSS цвета при наведении на текст в светлом режиме левого Сайдбара',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN LIGHT TEXT HOVER SIDEBAR
            [
                'type' => 'string',
                'option' => 'adminSidebarDarkHoverText',
                'value' => 'text-red-300',
                'constant' => 'ADMIN_SIDEBAR_DARK_HOVER_TEXT',
                'category' => 'admin',
                'description' => 'Класс Tailwind CSS цвета при наведении на текст в тёмном режиме левого Сайдбара',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN DARK TEXT HOVER SIDEBAR
            [
                'type' => 'string',
                'option' => 'adminSidebarLightActiveText',
                'value' => 'text-yellow-200',
                'constant' => 'ADMIN_SIDEBAR_LIGHT_ACTIVE_TEXT',
                'category' => 'admin',
                'description' => 'Класс Tailwind CSS цвета активной ссылки в светлом режиме левого Сайдбара',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN LIGHT TEXT ACTIVE SIDEBAR
            [
                'type' => 'string',
                'option' => 'adminSidebarDarkActiveText',
                'value' => 'text-yellow-200',
                'constant' => 'ADMIN_SIDEBAR_DARK_ACTIVE_TEXT',
                'category' => 'admin',
                'description' => 'Класс Tailwind CSS цвета активной ссылки в тёмном режиме левого Сайдбара',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN DARK TEXT ACTIVE SIDEBAR
            [
                'type' => 'string',
                'option' => 'publicLightBackgroundColor',
                'value' => 'bg-slate-50',
                'constant' => 'PUBLIC_LIGHT_BACKGROUND_COLOR',
                'category' => 'public',
                'description' => 'Класс Tailwind CSS фонового цвета светлого режима',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC LIGHT BACKGROUND
            [
                'type' => 'string',
                'option' => 'publicDarkBackgroundColor',
                'value' => 'bg-blue-800',
                'constant' => 'PUBLIC_DARK_BACKGROUND_COLOR',
                'category' => 'public',
                'description' => 'Класс Tailwind CSS фонового цвета тёмного режима',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC DARK BACKGROUND
            [
                'type' => 'string',
                'option' => 'publicHeaderLightBackgroundColor',
                'value' => 'bg-blue-800',
                'constant' => 'PUBLIC_HEADER_LIGHT_BACKGROUND_COLOR',
                'category' => 'public',
                'description' => 'Класс Tailwind CSS цвета светлого режима для HEADER',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC LIGHT BACKGROUND HEADER
            [
                'type' => 'string',
                'option' => 'publicHeaderDarkBackgroundColor',
                'value' => 'bg-slate-800',
                'constant' => 'PUBLIC_HEADER_DARK_BACKGROUND_COLOR',
                'category' => 'public',
                'description' => 'Класс Tailwind CSS цвета тёмного режима для HEADER',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC DARK BACKGROUND HEADER
            [
                'type' => 'checkbox',
                'option' => 'publicLeftColumnEnabled',
                'value' => 'true',
                'constant' => 'PUBLIC_LEFT_COLUMN_ENABLED',
                'category' => 'public',
                'description' => 'Включение/Выключение левой колонки в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SHOW LEFT COLUMN
            [
                'type' => 'checkbox',
                'option' => 'publicRightColumnEnabled',
                'value' => 'true',
                'constant' => 'PUBLIC_RIGHT_COLUMN_ENABLED',
                'category' => 'public',
                'description' => 'Включение/Выключение правой колонки в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SHOW RIGHT COLUMN
            [
                'type' => 'string',
                'option' => 'adminSystemSettingsProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SYSTEM_SETTINGS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных настроек в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SYSTEM SETTINGS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSystemSettingsPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SYSTEM_SETTINGS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество параметров в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SYSTEM SETTINGS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSystemSettingsDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SYSTEM_SETTINGS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка параметров по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SYSTEM SETTINGS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSystemSettingsDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_SYSTEM_SETTINGS_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка параметров в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SYSTEM SETTINGS DEFAULT VIEW
            [
                'type' => 'checkbox',
                'option' => 'imageProcessorEnabled',
                'value' => '1',
                'constant' => 'IMAGE_PROCESSOR_ENABLED',
                'category' => 'system',
                'description' =>
                    'Включает или выключает автоматическую обработку изображений через пресеты.',
                'activity' => true,
                'sort' => 100,
            ], // IMAGE PROCESSOR ENABLED
            [
                'type' => 'number',
                'option' => 'adminImagePresetsPerPage',
                'value' => '6',
                'constant' => 'ADMIN_IMAGE_PRESETS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество вариантов обработки изображений в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN IMAGE PRESETS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminImagePresetsDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_IMAGE_PRESETS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка вариантов обработки изображений по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN IMAGE PRESETS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminBlogRubricsProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_BLOG_RUBRICS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных рубрик в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN BLOG RUBRICS PROCESSING MODE
            [
                'type' => 'string',
                'option' => 'publicBlogRubricsProcessingMode',
                'value' => 'auto',
                'constant' => 'PUBLIC_BLOG_RUBRICS_PROCESSING_MODE',
                'category' => 'public',
                'description' => 'Режим обработки данных рубрик в публичной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC BLOG RUBRICS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminBlogRubricsPerPage',
                'value' => '6',
                'constant' => 'ADMIN_BLOG_RUBRICS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество рубрик в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN BLOG RUBRICS PER PAGE
            [
                'type' => 'number',
                'option' => 'publicBlogRubricsPerPage',
                'value' => '6',
                'constant' => 'PUBLIC_BLOG_RUBRICS_PER_PAGE',
                'category' => 'public',
                'description' => 'Показывать количество рубрик в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC BLOG RUBRICS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminBlogRubricsDefaultSort',
                'value' => 'sortAsc',
                'constant' => 'ADMIN_BLOG_RUBRICS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка по полю у Рубрик по умолчанию в таблице Панели Администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN BLOG RUBRICS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'publicBlogRubricsDefaultSort',
                'value' => 'sortAsc',
                'constant' => 'PUBLIC_BLOG_RUBRICS_DEFAULT_SORT',
                'category' => 'public',
                'description' => 'Сортировка рубрик по умолчанию в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC BLOG RUBRICS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminBlogRubricsDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_BLOG_RUBRICS_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка рубрик в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN BLOG RUBRICS DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'publicBlogRubricsDefaultView',
                'value' => 'grid',
                'constant' => 'PUBLIC_BLOG_RUBRICS_DEFAULT_VIEW',
                'category' => 'public',
                'description' => 'Вид списка рубрик в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC BLOG RUBRICS DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'adminBlogArticlesProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_BLOG_ARTICLES_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных статей в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN BLOG ARTICLES PROCESSING MODE
            [
                'type' => 'string',
                'option' => 'publicBlogArticlesProcessingMode',
                'value' => 'auto',
                'constant' => 'PUBLIC_BLOG_ARTICLES_PROCESSING_MODE',
                'category' => 'public',
                'description' => 'Режим обработки данных статей в публичной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC BLOG ARTICLES PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminBlogArticlesPerPage',
                'value' => '6',
                'constant' => 'ADMIN_BLOG_ARTICLES_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество статей в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN BLOG ARTICLES PER PAGE
            [
                'type' => 'number',
                'option' => 'publicBlogArticlesPerPage',
                'value' => '3',
                'constant' => 'PUBLIC_BLOG_ARTICLES_PER_PAGE',
                'category' => 'public',
                'description' => 'Показывать количество статей в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC BLOG ARTICLES PER PAGE
            [
                'type' => 'string',
                'option' => 'adminBlogArticlesDefaultSort',
                'value' => 'sortAsc',
                'constant' => 'ADMIN_BLOG_ARTICLES_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка статей по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN BLOG ARTICLES DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'publicBlogArticlesDefaultSort',
                'value' => 'sortAsc',
                'constant' => 'PUBLIC_BLOG_ARTICLES_DEFAULT_SORT',
                'category' => 'public',
                'description' => 'Сортировка статей по умолчанию в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC BLOG ARTICLES DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminBlogArticlesDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_BLOG_ARTICLES_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка статей в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN BLOG ARTICLES DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'publicBlogArticlesDefaultView',
                'value' => 'grid',
                'constant' => 'PUBLIC_BLOG_ARTICLES_DEFAULT_VIEW',
                'category' => 'public',
                'description' => 'Вид списка статей в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC BLOG ARTICLES DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'adminBlogTagsProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_BLOG_TAGS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных тегов в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN BLOG TAGS PROCESSING MODE
            [
                'type' => 'string',
                'option' => 'publicBlogTagsProcessingMode',
                'value' => 'auto',
                'constant' => 'PUBLIC_BLOG_TAGS_PROCESSING_MODE',
                'category' => 'public',
                'description' => 'Режим обработки данных тегов в публичной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC BLOG TAGS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminBlogTagsPerPage',
                'value' => '6',
                'constant' => 'ADMIN_BLOG_TAGS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество тегов в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN BLOG TAGS PER PAGE
            [
                'type' => 'number',
                'option' => 'publicBlogTagsPerPage',
                'value' => '6',
                'constant' => 'PUBLIC_BLOG_TAGS_PER_PAGE',
                'category' => 'public',
                'description' => 'Показывать количество тегов в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC BLOG TAGS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminBlogTagsDefaultSort',
                'value' => 'nameAsc',
                'constant' => 'ADMIN_BLOG_TAGS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка тегов по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN BLOG TAGS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'publicBlogTagsDefaultSort',
                'value' => 'nameAsc',
                'constant' => 'PUBLIC_BLOG_TAGS_DEFAULT_SORT',
                'category' => 'public',
                'description' => 'Сортировка тегов по умолчанию в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC BLOG TAGS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminBlogTagsDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_BLOG_TAGS_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка тегов в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN BLOG TAGS DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'publicBlogTagsDefaultView',
                'value' => 'grid',
                'constant' => 'PUBLIC_BLOG_TAGS_DEFAULT_VIEW',
                'category' => 'public',
                'description' => 'Вид списка тегов в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC BLOG TAGS DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'adminBlogBannersProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_BLOG_BANNERS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных баннеров в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN BLOG BANNERS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminBlogBannersPerPage',
                'value' => '6',
                'constant' => 'ADMIN_BLOG_BANNERS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество баннеров в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN BLOG BANNERS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminBlogBannersDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_BLOG_BANNERS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка баннеров по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN BLOG BANNERS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminBlogVideosProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_BLOG_VIDEOS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных видео в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN BLOG VIDEOS PROCESSING MODE
            [
                'type' => 'string',
                'option' => 'publicBlogVideosProcessingMode',
                'value' => 'auto',
                'constant' => 'PUBLIC_BLOG_VIDEOS_PROCESSING_MODE',
                'category' => 'public',
                'description' => 'Режим обработки данных видео в публичной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC BLOG VIDEOS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminBlogVideosPerPage',
                'value' => '6',
                'constant' => 'ADMIN_BLOG_VIDEOS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество видео в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN BLOG VIDEOS PER PAGE
            [
                'type' => 'number',
                'option' => 'publicBlogVideosPerPage',
                'value' => '6',
                'constant' => 'PUBLIC_BLOG_VIDEOS_PER_PAGE',
                'category' => 'public',
                'description' => 'Показывать количество видео в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC BLOG VIDEOS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminBlogVideosDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_BLOG_VIDEOS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка видео по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN BLOG VIDEOS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'publicBlogVideosDefaultSort',
                'value' => 'idDesc',
                'constant' => 'PUBLIC_BLOG_VIDEOS_DEFAULT_SORT',
                'category' => 'public',
                'description' => 'Сортировка видео по умолчанию в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC BLOG VIDEOS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminCommentsProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_COMMENTS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных комментариев в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN COMMENTS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminCommentsPerPage',
                'value' => '6',
                'constant' => 'ADMIN_COMMENTS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество комментариев в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN COMMENTS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminCommentsDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_COMMENTS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка комментариев по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN COMMENTS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminFinanceCurrenciesProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_FINANCE_CURRENCIES_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных валют в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN FINANCE CURRENCIES PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminFinanceCurrenciesPerPage',
                'value' => '6',
                'constant' => 'ADMIN_FINANCE_CURRENCIES_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество Валют в таблице Панели Администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN FINANCE CURRENCIES PER PAGE
            [
                'type' => 'string',
                'option' => 'adminFinanceCurrenciesDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_FINANCE_CURRENCIES_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка по полю у Валют по умолчанию в таблице Панели Администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN FINANCE CURRENCIES DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSchoolHashtagsProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SCHOOL_HASHTAGS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных хештегов в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL HASHTAGS PROCESSING MODE
            [
                'type' => 'string',
                'option' => 'publicSchoolHashtagsProcessingMode',
                'value' => 'auto',
                'constant' => 'PUBLIC_SCHOOL_HASHTAGS_PROCESSING_MODE',
                'category' => 'public',
                'description' => 'Режим обработки данных хештегов в публичной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL HASHTAGS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSchoolHashtagsPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SCHOOL_HASHTAGS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество хештегов в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL HASHTAGS PER PAGE
            [
                'type' => 'number',
                'option' => 'publicSchoolHashtagsPerPage',
                'value' => '6',
                'constant' => 'PUBLIC_SCHOOL_HASHTAGS_PER_PAGE',
                'category' => 'public',
                'description' => 'Показывать количество хештегов в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL HASHTAGS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSchoolHashtagsDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SCHOOL_HASHTAGS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка хештегов по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL HASHTAGS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'publicSchoolHashtagsDefaultSort',
                'value' => 'idDesc',
                'constant' => 'PUBLIC_SCHOOL_HASHTAGS_DEFAULT_SORT',
                'category' => 'public',
                'description' => 'Сортировка хештегов по умолчанию в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL HASHTAGS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSchoolHashtagsDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_SCHOOL_HASHTAGS_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка хештегов в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL HASHTAGS DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'publicSchoolHashtagsDefaultView',
                'value' => 'grid',
                'constant' => 'PUBLIC_SCHOOL_HASHTAGS_DEFAULT_VIEW',
                'category' => 'public',
                'description' => 'Вид списка хештегов в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL HASHTAGS DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'adminSchoolInstructorsProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SCHOOL_INSTRUCTORS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных инструкторов в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL INSTRUCTORS PROCESSING MODE
            [
                'type' => 'string',
                'option' => 'publicSchoolInstructorsProcessingMode',
                'value' => 'auto',
                'constant' => 'PUBLIC_SCHOOL_INSTRUCTORS_PROCESSING_MODE',
                'category' => 'public',
                'description' => 'Режим обработки данных инструкторов в публичной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL INSTRUCTORS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSchoolInstructorsPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SCHOOL_INSTRUCTORS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество преподавателей в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL INSTRUCTORS PER PAGE
            [
                'type' => 'number',
                'option' => 'publicSchoolInstructorsPerPage',
                'value' => '6',
                'constant' => 'PUBLIC_SCHOOL_INSTRUCTORS_PER_PAGE',
                'category' => 'public',
                'description' => 'Показывать количество преподавателей в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL INSTRUCTORS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSchoolInstructorsDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SCHOOL_INSTRUCTORS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка преподавателей по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL INSTRUCTORS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'publicSchoolInstructorsDefaultSort',
                'value' => 'idDesc',
                'constant' => 'PUBLIC_SCHOOL_INSTRUCTORS_DEFAULT_SORT',
                'category' => 'public',
                'description' => 'Сортировка преподавателей по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL INSTRUCTORS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSchoolInstructorsDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_SCHOOL_INSTRUCTORS_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка инструкторов в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL INSTRUCTORS DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'publicSchoolInstructorsDefaultView',
                'value' => 'grid',
                'constant' => 'PUBLIC_SCHOOL_INSTRUCTORS_DEFAULT_VIEW',
                'category' => 'public',
                'description' => 'Вид списка инструкторов в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL INSTRUCTORS DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'adminSchoolTracksProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SCHOOL_TRACKS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных треков в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL TRACKS PROCESSING MODE
            [
                'type' => 'string',
                'option' => 'publicSchoolTracksProcessingMode',
                'value' => 'auto',
                'constant' => 'PUBLIC_SCHOOL_TRACKS_PROCESSING_MODE',
                'category' => 'public',
                'description' => 'Режим обработки данных треков в публичной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL TRACKS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSchoolTracksPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SCHOOL_TRACKS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество треков в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL TRACKS PER PAGE
            [
                'type' => 'number',
                'option' => 'publicSchoolTracksPerPage',
                'value' => '6',
                'constant' => 'PUBLIC_SCHOOL_TRACKS_PER_PAGE',
                'category' => 'public',
                'description' => 'Показывать количество треков в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL TRACKS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSchoolTracksDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SCHOOL_TRACKS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка треков по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL TRACKS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'publicSchoolTracksDefaultSort',
                'value' => 'idDesc',
                'constant' => 'PUBLIC_SCHOOL_TRACKS_DEFAULT_SORT',
                'category' => 'public',
                'description' => 'Сортировка треков по умолчанию в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL TRACKS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSchoolTracksDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_SCHOOL_TRACKS_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка треков в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL TRACKS DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'publicSchoolTracksDefaultView',
                'value' => 'grid',
                'constant' => 'PUBLIC_SCHOOL_TRACKS_DEFAULT_VIEW',
                'category' => 'public',
                'description' => 'Вид списка треков в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL TRACKS DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'adminSchoolCoursesProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SCHOOL_COURSES_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных курсов в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL COURSES PROCESSING MODE
            [
                'type' => 'string',
                'option' => 'publicSchoolCoursesProcessingMode',
                'value' => 'auto',
                'constant' => 'PUBLIC_SCHOOL_COURSES_PROCESSING_MODE',
                'category' => 'public',
                'description' => 'Режим обработки данных курсов в публичной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL COURSES PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSchoolCoursesPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SCHOOL_COURSES_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество курсов в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL COURSES PER PAGE
            [
                'type' => 'number',
                'option' => 'publicSchoolCoursesPerPage',
                'value' => '6',
                'constant' => 'PUBLIC_SCHOOL_COURSES_PER_PAGE',
                'category' => 'public',
                'description' => 'Показывать количество курсов в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL COURSES PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSchoolCoursesDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SCHOOL_COURSES_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка курсов по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL COURSES DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'publicSchoolCoursesDefaultSort',
                'value' => 'idDesc',
                'constant' => 'PUBLIC_SCHOOL_COURSES_DEFAULT_SORT',
                'category' => 'public',
                'description' => 'Сортировка курсов по умолчанию в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL COURSES DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSchoolCoursesDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_SCHOOL_COURSES_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка курсов в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL COURSES DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'publicSchoolCoursesDefaultView',
                'value' => 'grid',
                'constant' => 'PUBLIC_SCHOOL_COURSES_DEFAULT_VIEW',
                'category' => 'public',
                'description' => 'Вид списка курсов в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL COURSES DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'adminSchoolModulesProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SCHOOL_MODULES_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных модулей в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL MODULES PROCESSING MODE
            [
                'type' => 'string',
                'option' => 'publicSchoolModulesProcessingMode',
                'value' => 'auto',
                'constant' => 'PUBLIC_SCHOOL_MODULES_PROCESSING_MODE',
                'category' => 'public',
                'description' => 'Режим обработки данных модулей в публичной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL MODULES PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSchoolModulesPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SCHOOL_MODULES_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество модулей обучения в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL MODULES PER PAGE
            [
                'type' => 'number',
                'option' => 'publicSchoolModulesPerPage',
                'value' => '6',
                'constant' => 'PUBLIC_SCHOOL_MODULES_PER_PAGE',
                'category' => 'public',
                'description' => 'Показывать количество модулей обучения в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL MODULES PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSchoolModulesDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SCHOOL_MODULES_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка модулей обучения по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL MODULES DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'publicSchoolModulesDefaultSort',
                'value' => 'idDesc',
                'constant' => 'PUBLIC_SCHOOL_MODULES_DEFAULT_SORT',
                'category' => 'public',
                'description' => 'Сортировка модулей обучения по умолчанию в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL MODULES DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSchoolModulesDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_SCHOOL_MODULES_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка модулей обучения в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL MODULES DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'publicSchoolModulesDefaultView',
                'value' => 'grid',
                'constant' => 'PUBLIC_SCHOOL_MODULES_DEFAULT_VIEW',
                'category' => 'public',
                'description' => 'Вид списка модулей обучения в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL MODULES DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'adminSchoolLessonsProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SCHOOL_LESSONS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных уроков в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL LESSONS PROCESSING MODE
            [
                'type' => 'string',
                'option' => 'publicSchoolLessonsProcessingMode',
                'value' => 'auto',
                'constant' => 'PUBLIC_SCHOOL_LESSONS_PROCESSING_MODE',
                'category' => 'public',
                'description' => 'Режим обработки данных уроков в публичной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL LESSONS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSchoolLessonsPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SCHOOL_LESSONS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество уроков в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL LESSONS PER PAGE
            [
                'type' => 'number',
                'option' => 'publicSchoolLessonsPerPage',
                'value' => '6',
                'constant' => 'PUBLIC_SCHOOL_LESSONS_PER_PAGE',
                'category' => 'public',
                'description' => 'Показывать количество уроков в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL LESSONS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSchoolLessonsDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SCHOOL_LESSONS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка уроков по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL LESSONS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'publicSchoolLessonsDefaultSort',
                'value' => 'idDesc',
                'constant' => 'PUBLIC_SCHOOL_LESSONS_DEFAULT_SORT',
                'category' => 'public',
                'description' => 'Сортировка уроков по умолчанию в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL LESSONS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSchoolLessonsDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_SCHOOL_LESSONS_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка курсов в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL LESSONS DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'publicSchoolLessonsDefaultView',
                'value' => 'grid',
                'constant' => 'PUBLIC_SCHOOL_LESSONS_DEFAULT_VIEW',
                'category' => 'public',
                'description' => 'Вид списка курсов в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL LESSONS DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'adminSchoolAssignmentsProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SCHOOL_ASSIGNMENTS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных заданий в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL ASSIGNMENTS PROCESSING MODE
            [
                'type' => 'string',
                'option' => 'publicSchoolAssignmentsProcessingMode',
                'value' => 'auto',
                'constant' => 'PUBLIC_SCHOOL_ASSIGNMENTS_PROCESSING_MODE',
                'category' => 'public',
                'description' => 'Режим обработки данных заданий в публичной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL ASSIGNMENTS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSchoolAssignmentsPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SCHOOL_ASSIGNMENTS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество заданий в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL ASSIGNMENTS PER PAGE
            [
                'type' => 'number',
                'option' => 'publicSchoolAssignmentsPerPage',
                'value' => '6',
                'constant' => 'PUBLIC_SCHOOL_ASSIGNMENTS_PER_PAGE',
                'category' => 'public',
                'description' => 'Показывать количество заданий в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL ASSIGNMENTS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSchoolAssignmentsDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SCHOOL_ASSIGNMENTS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка заданий по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL ASSIGNMENTS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'publicSchoolAssignmentsDefaultSort',
                'value' => 'idDesc',
                'constant' => 'PUBLIC_SCHOOL_ASSIGNMENTS_DEFAULT_SORT',
                'category' => 'public',
                'description' => 'Сортировка заданий по умолчанию в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL ASSIGNMENTS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSchoolAssignmentsDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_SCHOOL_ASSIGNMENTS_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка заданий в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL ASSIGNMENTS DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'adminSchoolCourseSchedulesProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SCHOOL_COURSE_SCHEDULES_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных расписаний в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL COURSE SCHEDULES PROCESSING MODE
            [
                'type' => 'string',
                'option' => 'publicSchoolCourseSchedulesProcessingMode',
                'value' => 'auto',
                'constant' => 'PUBLIC_SCHOOL_COURSE_SCHEDULES_PROCESSING_MODE',
                'category' => 'public',
                'description' => 'Режим обработки данных расписаний в публичной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL COURSE SCHEDULES PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSchoolCourseSchedulesPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SCHOOL_COURSE_SCHEDULES_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество расписаний в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL COURSE SCHEDULES PER PAGE
            [
                'type' => 'number',
                'option' => 'publicSchoolCourseSchedulesPerPage',
                'value' => '6',
                'constant' => 'PUBLIC_SCHOOL_COURSE_SCHEDULES_PER_PAGE',
                'category' => 'public',
                'description' => 'Показывать количество расписаний в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL COURSE SCHEDULES PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSchoolCourseSchedulesDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SCHOOL_COURSE_SCHEDULES_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка расписаний по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL COURSE SCHEDULES DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'publicSchoolCourseSchedulesDefaultSort',
                'value' => 'idDesc',
                'constant' => 'PUBLIC_SCHOOL_COURSE_SCHEDULES_DEFAULT_SORT',
                'category' => 'public',
                'description' => 'Сортировка расписаний по умолчанию в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL COURSE SCHEDULES DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSchoolCourseSchedulesDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_SCHOOL_COURSE_SCHEDULES_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка расписаний в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL COURSE SCHEDULES DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'adminSchoolCohortEnrollmentsProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SCHOOL_COHORT_ENROLLMENTS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных записей на потоки в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL COHORT ENROLLMENTS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSchoolCohortEnrollmentsPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SCHOOL_COHORT_ENROLLMENTS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество записей на потоки в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL COHORT ENROLLMENTS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSchoolCohortEnrollmentsDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SCHOOL_COHORT_ENROLLMENTS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка записей на потоки по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL COHORT ENROLLMENTS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSchoolCohortEnrollmentsDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_SCHOOL_COHORT_ENROLLMENTS_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка записей на потоки в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL COHORT ENROLLMENTS DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'adminSchoolEnrollmentsProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SCHOOL_ENROLLMENTS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных зачислений на потоки в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL ENROLLMENTS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSchoolEnrollmentsPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SCHOOL_ENROLLMENTS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество зачислений на потоки в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL ENROLLMENTS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSchoolEnrollmentsDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SCHOOL_ENROLLMENTS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка зачислений на потоки по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL ENROLLMENTS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSchoolEnrollmentsDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_SCHOOL_ENROLLMENTS_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка зачислений на потоки в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL ENROLLMENTS DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'adminSchoolQuizzesProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SCHOOL_QUIZZES_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных викторин в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL QUIZZES PROCESSING MODE
            [
                'type' => 'string',
                'option' => 'publicSchoolQuizzesProcessingMode',
                'value' => 'auto',
                'constant' => 'PUBLIC_SCHOOL_QUIZZES_PROCESSING_MODE',
                'category' => 'public',
                'description' => 'Режим обработки данных викторин в публичной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL QUIZZES PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSchoolQuizzesPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SCHOOL_QUIZZES_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество викторин в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL QUIZZES PER PAGE
            [
                'type' => 'number',
                'option' => 'publicSchoolQuizzesPerPage',
                'value' => '6',
                'constant' => 'PUBLIC_SCHOOL_QUIZZES_PER_PAGE',
                'category' => 'public',
                'description' => 'Показывать количество викторин в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL QUIZZES PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSchoolQuizzesDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SCHOOL_QUIZZES_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка викторин по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL QUIZZES DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'publicSchoolQuizzesDefaultSort',
                'value' => 'idDesc',
                'constant' => 'PUBLIC_SCHOOL_QUIZZES_DEFAULT_SORT',
                'category' => 'public',
                'description' => 'Сортировка викторин по умолчанию в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL QUIZZES DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSchoolQuizzesDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_SCHOOL_QUIZZES_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка викторин в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL QUIZZES DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'publicSchoolQuizzesDefaultView',
                'value' => 'grid',
                'constant' => 'PUBLIC_SCHOOL_QUIZZES_DEFAULT_VIEW',
                'category' => 'public',
                'description' => 'Вид списка викторин в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL QUIZZES DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'adminSchoolQuizQuestionsProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SCHOOL_QUIZ_QUESTIONS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных вопросов викторин в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL QUIZ QUESTIONS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSchoolQuizQuestionsPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SCHOOL_QUIZ_QUESTIONS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество вопросов викторин в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL QUIZ QUESTIONS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSchoolQuizQuestionsDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SCHOOL_QUIZ_QUESTIONS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка вопросов викторин по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL QUIZ QUESTIONS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSchoolQuizQuestionsDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_SCHOOL_QUIZ_QUESTIONS_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка вопросов викторин в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL QUIZ QUESTIONS VIEW
            [
                'type' => 'string',
                'option' => 'adminSchoolQuizAnswersProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SCHOOL_QUIZ_ANSWERS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных ответов викторин в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL QUIZ ANSWERS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSchoolQuizAnswersPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SCHOOL_QUIZ_ANSWERS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество ответов викторин в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL QUIZ ANSWERS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSchoolQuizAnswersDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SCHOOL_QUIZ_ANSWERS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка ответов викторин по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL QUIZ ANSWERS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSchoolQuizAnswersDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_SCHOOL_QUIZ_ANSWERS_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка ответов викторин в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL QUIZ ANSWERS VIEW
            [
                'type' => 'string',
                'option' => 'adminSchoolQuizAttemptsProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SCHOOL_QUIZ_ATTEMPTS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных прохождения викторин в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL QUIZ ATTEMPTS PROCESSING MODE
            [
                'type' => 'string',
                'option' => 'publicSchoolQuizAttemptsProcessingMode',
                'value' => 'auto',
                'constant' => 'PUBLIC_SCHOOL_QUIZ_ATTEMPTS_PROCESSING_MODE',
                'category' => 'public',
                'description' => 'Режим обработки данных прохождения викторин в публичной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL QUIZ ATTEMPTS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSchoolQuizAttemptsPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SCHOOL_QUIZ_ATTEMPTS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество прохождений викторин в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL QUIZ ATTEMPTS PER PAGE
            [
                'type' => 'number',
                'option' => 'publicSchoolQuizAttemptsPerPage',
                'value' => '6',
                'constant' => 'PUBLIC_SCHOOL_QUIZ_ATTEMPTS_PER_PAGE',
                'category' => 'public',
                'description' => 'Показывать количество прохождений викторин в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL QUIZ ATTEMPTS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSchoolQuizAttemptsDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SCHOOL_QUIZ_ATTEMPTS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка прохождений викторин по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL QUIZ ATTEMPTS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'publicSchoolQuizAttemptsDefaultSort',
                'value' => 'idDesc',
                'constant' => 'PUBLIC_SCHOOL_QUIZ_ATTEMPTS_DEFAULT_SORT',
                'category' => 'public',
                'description' => 'Сортировка прохождений викторин по умолчанию в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL QUIZ ATTEMPTS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSchoolQuizAttemptsDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_SCHOOL_QUIZ_ATTEMPTS_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка прохождения викторин в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL QUIZ ATTEMPTS VIEW
            [
                'type' => 'string',
                'option' => 'publicSchoolQuizAttemptsDefaultView',
                'value' => 'grid',
                'constant' => 'PUBLIC_SCHOOL_QUIZ_ATTEMPTS_DEFAULT_VIEW',
                'category' => 'public',
                'description' => 'Вид списка прохождения викторин в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL QUIZ ATTEMPTS VIEW
            [
                'type' => 'string',
                'option' => 'adminSchoolQuizAttemptItemsProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SCHOOL_QUIZ_ATTEMPT_ITEMS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных попыток ответов в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL QUIZ ATTEMPT ITEMS PROCESSING MODE
            [
                'type' => 'string',
                'option' => 'publicSchoolQuizAttemptItemsProcessingMode',
                'value' => 'auto',
                'constant' => 'PUBLIC_SCHOOL_QUIZ_ATTEMPT_ITEMS_PROCESSING_MODE',
                'category' => 'public',
                'description' => 'Режим обработки данных попыток ответов в публичной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL QUIZ ATTEMPT ITEMS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSchoolQuizAttemptItemsPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SCHOOL_QUIZ_ATTEMPT_ITEMS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество попыток ответов в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL QUIZ ATTEMPT ITEMS PER PAGE
            [
                'type' => 'number',
                'option' => 'publicSchoolQuizAttemptItemsPerPage',
                'value' => '6',
                'constant' => 'PUBLIC_SCHOOL_QUIZ_ATTEMPT_ITEMS_PER_PAGE',
                'category' => 'public',
                'description' => 'Показывать количество попыток ответов в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL QUIZ ATTEMPT ITEMS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSchoolQuizAttemptItemsDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SCHOOL_QUIZ_ATTEMPT_ITEMS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка попыток ответов по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL QUIZ ATTEMPT ITEMS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'publicSchoolQuizAttemptItemsDefaultSort',
                'value' => 'idDesc',
                'constant' => 'PUBLIC_SCHOOL_QUIZ_ATTEMPT_ITEMS_DEFAULT_SORT',
                'category' => 'public',
                'description' => 'Сортировка попыток ответов по умолчанию в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL QUIZ ATTEMPT ITEMS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSchoolQuizAttemptItemsDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_SCHOOL_QUIZ_ATTEMPT_ITEMS_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка попыток ответов викторин в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL QUIZ ATTEMPTS VIEW
            [
                'type' => 'string',
                'option' => 'publicSchoolQuizAttemptItemsDefaultView',
                'value' => 'grid',
                'constant' => 'PUBLIC_SCHOOL_QUIZ_ATTEMPT_ITEMS_DEFAULT_VIEW',
                'category' => 'public',
                'description' => 'Вид списка попыток ответов викторин в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL QUIZ ATTEMPTS VIEW
            [
                'type' => 'string',
                'option' => 'adminSchoolBundlesProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SCHOOL_BUNDLES_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных бандлов курсов в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL BUNDLES PROCESSING MODE
            [
                'type' => 'string',
                'option' => 'publicSchoolBundlesProcessingMode',
                'value' => 'auto',
                'constant' => 'PUBLIC_SCHOOL_BUNDLES_PROCESSING_MODE',
                'category' => 'public',
                'description' => 'Режим обработки данных бандлов курсов в публичной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL BUNDLES PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSchoolBundlesPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SCHOOL_BUNDLES_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество бандлов в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL BUNDLES PER PAGE
            [
                'type' => 'number',
                'option' => 'publicSchoolBundlesPerPage',
                'value' => '6',
                'constant' => 'PUBLIC_SCHOOL_BUNDLES_PER_PAGE',
                'category' => 'public',
                'description' => 'Показывать количество бандлов в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL BUNDLES PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSchoolBundlesDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SCHOOL_BUNDLES_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка бандлов по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL BUNDLES DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'publicSchoolBundlesDefaultSort',
                'value' => 'idDesc',
                'constant' => 'PUBLIC_SCHOOL_BUNDLES_DEFAULT_SORT',
                'category' => 'public',
                'description' => 'Сортировка бандлов по умолчанию в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL BUNDLES DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSchoolBundlesDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_SCHOOL_BUNDLES_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка бандлов курсов в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL BUNDLES VIEW
            [
                'type' => 'string',
                'option' => 'publicSchoolBundlesDefaultView',
                'value' => 'grid',
                'constant' => 'PUBLIC_SCHOOL_BUNDLES_DEFAULT_VIEW',
                'category' => 'public',
                'description' => 'Вид списка бандлов курсов в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SCHOOL BUNDLES VIEW
            [
                'type' => 'string',
                'option' => 'adminSchoolOrdersProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SCHOOL_ORDERS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных заказов школы в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL ORDERS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSchoolOrdersPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SCHOOL_ORDERS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество заказов школы в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL ORDERS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSchoolOrdersDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SCHOOL_ORDERS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка заказов школы по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL ORDERS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSchoolOrdersDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_SCHOOL_ORDERS_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка заказов школы в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL ORDERS VIEW
            [
                'type' => 'string',
                'option' => 'adminSchoolCoursePricesProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SCHOOL_COURSE_PRICES_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных прайсов курсов в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL COURSE PRICES PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSchoolCoursePricesPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SCHOOL_COURSE_PRICES_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество прайсов курсов в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL COURSE PRICES PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSchoolCoursePricesDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SCHOOL_COURSE_PRICES_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка прайсов курсов по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL COURSE PRICES DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSchoolCoursePricesDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_SCHOOL_COURSE_PRICES_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка прайсов курсов в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL COURSE PRICES VIEW
            [
                'type' => 'string',
                'option' => 'adminSchoolBundlePricesProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SCHOOL_BUNDLE_PRICES_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных прайсов наборов курсов в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL BUNDLE PRICES PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSchoolBundlePricesPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SCHOOL_BUNDLE_PRICES_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество прайсов наборов курсов в таблице панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL BUNDLE PRICES PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSchoolBundlePricesDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SCHOOL_BUNDLE_PRICES_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка прайсов наборов курсов по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL BUNDLE PRICES DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSchoolBundlePricesDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_SCHOOL_BUNDLE_PRICES_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка прайсов наборов курсов в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL BUNDLE PRICES VIEW
            [
                'type' => 'string',
                'option' => 'adminSchoolSubscriptionPlansProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SCHOOL_SUBSCRIPTION_PLANS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных тарифных планов в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL SUBSCRIPTION PLANS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSchoolSubscriptionPlansPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SCHOOL_SUBSCRIPTION_PLANS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество тарифных планов в панели Администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL SUBSCRIPTION PLANS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSchoolSubscriptionPlansDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SCHOOL_SUBSCRIPTION_PLANS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка тарифных планов по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL SUBSCRIPTION PLANS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSchoolSubscriptionPlansDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_SCHOOL_SUBSCRIPTION_PLANS_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка тарифных планов в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SCHOOL SUBSCRIPTION PLANS VIEW
            [
                'type' => 'string',
                'option' => 'adminSystemUsersProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SYSTEM_USERS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных пользователей в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SYSTEM USERS PROCESSING MODE
            [
                'type' => 'string',
                'option' => 'publicSystemUsersProcessingMode',
                'value' => 'auto',
                'constant' => 'PUBLIC_SYSTEM_USERS_PROCESSING_MODE',
                'category' => 'public',
                'description' => 'Режим обработки данных пользователей в публичной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC SYSTEM USERS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSystemUsersPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SYSTEM_USERS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество пользователей в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SYSTEM USERS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSystemUsersDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SYSTEM_USERS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка пользователей по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SYSTEM USERS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSystemRolesProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SYSTEM_ROLES_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных ролей в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SYSTEM ROLES PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSystemRolesPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SYSTEM_ROLES_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество ролей в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SYSTEM ROLES PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSystemRolesDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SYSTEM_ROLES_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка ролей по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SYSTEM ROLES DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminSystemPermissionsProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_SYSTEM_PERMISSIONS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных разрешений в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SYSTEM PERMISSIONS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminSystemPermissionsPerPage',
                'value' => '6',
                'constant' => 'ADMIN_SYSTEM_PERMISSIONS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество разрешений в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SYSTEM PERMISSIONS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminSystemPermissionsDefaultSort',
                'value' => 'idDesc',
                'constant' => 'ADMIN_SYSTEM_PERMISSIONS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка разрешений по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN SYSTEM PERMISSIONS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminMarketCompaniesProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_MARKET_COMPANIES_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных фирм в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN MARKET COMPANIES PROCESSING MODE
            [
                'type' => 'string',
                'option' => 'publicMarketCompaniesProcessingMode',
                'value' => 'auto',
                'constant' => 'PUBLIC_MARKET_COMPANIES_PROCESSING_MODE',
                'category' => 'public',
                'description' => 'Режим обработки данных фирм в публичной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC MARKET COMPANIES PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminMarketCompaniesPerPage',
                'value' => '6',
                'constant' => 'ADMIN_MARKET_COMPANIES_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество фирм в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN MARKET COMPANIES PER PAGE
            [
                'type' => 'number',
                'option' => 'publicMarketCompaniesPerPage',
                'value' => '3',
                'constant' => 'PUBLIC_MARKET_COMPANIES_PER_PAGE',
                'category' => 'public',
                'description' => 'Показывать количество фирм в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC MARKET COMPANIES PER PAGE
            [
                'type' => 'string',
                'option' => 'adminMarketCompaniesDefaultSort',
                'value' => 'sortAsc',
                'constant' => 'ADMIN_MARKET_COMPANIES_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка фирм по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN MARKET COMPANIES DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'publicMarketCompaniesDefaultSort',
                'value' => 'sortAsc',
                'constant' => 'PUBLIC_MARKET_COMPANIES_DEFAULT_SORT',
                'category' => 'public',
                'description' => 'Сортировка фирм по умолчанию в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC MARKET COMPANIES DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminMarketCompaniesDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_MARKET_COMPANIES_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка фирм в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN MARKET COMPANIES DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'publicMarketCompaniesDefaultView',
                'value' => 'grid',
                'constant' => 'PUBLIC_MARKET_COMPANIES_DEFAULT_VIEW',
                'category' => 'public',
                'description' => 'Вид списка фирм в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC MARKET COMPANIES DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'adminMarketShopsProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_MARKET_SHOPS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных магазинов в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN MARKET SHOPS PROCESSING MODE
            [
                'type' => 'string',
                'option' => 'publicMarketShopsProcessingMode',
                'value' => 'auto',
                'constant' => 'PUBLIC_MARKET_SHOPS_PROCESSING_MODE',
                'category' => 'public',
                'description' => 'Режим обработки данных магазинов в публичной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC MARKET SHOPS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminMarketShopsPerPage',
                'value' => '6',
                'constant' => 'ADMIN_MARKET_SHOPS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество магазинов в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN MARKET SHOPS PER PAGE
            [
                'type' => 'number',
                'option' => 'publicMarketShopsPerPage',
                'value' => '3',
                'constant' => 'PUBLIC_MARKET_SHOPS_PER_PAGE',
                'category' => 'public',
                'description' => 'Показывать количество магазинов в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC MARKET SHOPS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminMarketShopsDefaultSort',
                'value' => 'sortAsc',
                'constant' => 'ADMIN_MARKET_SHOPS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка магазинов по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN MARKET SHOPS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'publicMarketShopsDefaultSort',
                'value' => 'sortAsc',
                'constant' => 'PUBLIC_MARKET_SHOPS_DEFAULT_SORT',
                'category' => 'public',
                'description' => 'Сортировка магазинов по умолчанию в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC MARKET SHOPS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminMarketShopsDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_MARKET_SHOPS_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка магазинов в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN MARKET SHOPS DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'publicMarketShopsDefaultView',
                'value' => 'grid',
                'constant' => 'PUBLIC_MARKET_SHOPS_DEFAULT_VIEW',
                'category' => 'public',
                'description' => 'Вид списка магазинов в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC MARKET SHOPS DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'adminMarketCategoriesProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_MARKET_CATEGORIES_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных категорий товаров в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN MARKET CATEGORIES PROCESSING MODE
            [
                'type' => 'string',
                'option' => 'publicMarketCategoriesProcessingMode',
                'value' => 'auto',
                'constant' => 'PUBLIC_MARKET_CATEGORIES_PROCESSING_MODE',
                'category' => 'public',
                'description' => 'Режим обработки данных категорий товаров в публичной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC MARKET CATEGORIES PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminMarketCategoriesPerPage',
                'value' => '6',
                'constant' => 'ADMIN_MARKET_CATEGORIES_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество категорий товаров в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN MARKET CATEGORIES PER PAGE
            [
                'type' => 'number',
                'option' => 'publicMarketCategoriesPerPage',
                'value' => '3',
                'constant' => 'PUBLIC_MARKET_CATEGORIES_PER_PAGE',
                'category' => 'public',
                'description' => 'Показывать количество категорий товаров в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC MARKET CATEGORIES PER PAGE
            [
                'type' => 'string',
                'option' => 'adminMarketCategoriesDefaultSort',
                'value' => 'sortAsc',
                'constant' => 'ADMIN_MARKET_CATEGORIES_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка категорий товаров по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN MARKET CATEGORIES DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'publicMarketCategoriesDefaultSort',
                'value' => 'sortAsc',
                'constant' => 'PUBLIC_MARKET_CATEGORIES_DEFAULT_SORT',
                'category' => 'public',
                'description' => 'Сортировка категорий товаров по умолчанию в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC MARKET CATEGORIES DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminMarketCategoriesDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_MARKET_CATEGORIES_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка категорий товаров в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN MARKET CATEGORIES DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'publicMarketCategoriesDefaultView',
                'value' => 'grid',
                'constant' => 'PUBLIC_MARKET_CATEGORIES_DEFAULT_VIEW',
                'category' => 'public',
                'description' => 'Вид списка категорий товаров в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC MARKET CATEGORIES DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'adminMarketBrandsProcessingMode',
                'value' => 'auto',
                'constant' => 'ADMIN_MARKET_BRANDS_PROCESSING_MODE',
                'category' => 'admin',
                'description' => 'Режим обработки данных брендов в административной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN MARKET BRANDS PROCESSING MODE
            [
                'type' => 'string',
                'option' => 'publicMarketBrandsProcessingMode',
                'value' => 'auto',
                'constant' => 'PUBLIC_MARKET_BRANDS_PROCESSING_MODE',
                'category' => 'public',
                'description' => 'Режим обработки данных брендов в публичной части: frontend / server / auto',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC MARKET BRANDS PROCESSING MODE
            [
                'type' => 'number',
                'option' => 'adminMarketBrandsPerPage',
                'value' => '6',
                'constant' => 'ADMIN_MARKET_BRANDS_PER_PAGE',
                'category' => 'admin',
                'description' => 'Показывать количество брендов в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN MARKET BRANDS PER PAGE
            [
                'type' => 'number',
                'option' => 'publicMarketBrandsPerPage',
                'value' => '3',
                'constant' => 'PUBLIC_MARKET_BRANDS_PER_PAGE',
                'category' => 'public',
                'description' => 'Показывать количество брендов в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC MARKET BRANDS PER PAGE
            [
                'type' => 'string',
                'option' => 'adminMarketBrandsDefaultSort',
                'value' => 'sortAsc',
                'constant' => 'ADMIN_MARKET_BRANDS_DEFAULT_SORT',
                'category' => 'admin',
                'description' => 'Сортировка брендов по умолчанию в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN MARKET BRANDS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'publicMarketBrandsDefaultSort',
                'value' => 'sortAsc',
                'constant' => 'PUBLIC_MARKET_BRANDS_DEFAULT_SORT',
                'category' => 'public',
                'description' => 'Сортировка брендов по умолчанию в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC MARKET BRANDS DEFAULT SORT
            [
                'type' => 'string',
                'option' => 'adminMarketBrandsDefaultView',
                'value' => 'grid',
                'constant' => 'ADMIN_MARKET_BRANDS_DEFAULT_VIEW',
                'category' => 'admin',
                'description' => 'Вид списка брендов в панели администратора',
                'activity' => true,
                'sort' => 0,
            ], // ADMIN MARKET BRANDS DEFAULT VIEW
            [
                'type' => 'string',
                'option' => 'publicMarketBrandsDefaultView',
                'value' => 'grid',
                'constant' => 'PUBLIC_MARKET_BRANDS_DEFAULT_VIEW',
                'category' => 'public',
                'description' => 'Вид списка брендов в публичной части',
                'activity' => true,
                'sort' => 0,
            ], // PUBLIC MARKET BRANDS DEFAULT VIEW

            // Добавьте остальные параметры
        ];

        // Если по колонке `option` уже есть уникальный индекс — этого достаточно:
        //DB::table('settings')->insertOrIgnore($settings);

        // Если вдруг уникального индекса по `option` НЕТ (и добавлять миграцией пока не хочешь),
        // замени строку выше на этот блок-фильтр:

        $existing = DB::table('settings')->pluck('option')->all();
        $map = array_flip($existing);
        $toInsert = array_values(array_filter($settings, fn($row) => !isset($map[$row['option']])));

        if ($toInsert) {
            DB::table('settings')->insert($toInsert);
        }
    }
}
