1) `php artisan key:generate --ansi` <br><br>

2) Install Webp, Spatie Media Library <br>
`прежде отключить файрвол антивируса, потом снова включить` <br>
`composer require "laravel/framework:^10.48" -W --prefer-dist` <br>
`composer require spatie/laravel-sitemap` <br>
`composer require intervention/image:^2.7` <br>
`composer require spatie/laravel-image-optimizer` <br>
`composer require mcamara/laravel-localization` <br>
`docker exec mark-php-app php artisan vendor:publish --provider="Mcamara\LaravelLocalization\LaravelLocalizationServiceProvider"` <br>
`docker exec -it mark-php-app composer require spatie/laravel-medialibrary:"^11.0" --with-all-dependencies` <br>
`docker exec mark-php-app php artisan vendor:publish --tag="medialibrary-migrations"`  <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-config"`<br>

3) Make directory for docker: <br>
   `mkdir ./storage/docker` <br>

4) Copy .env.example <br>
   `cp .env.example .env` <br>

5) Add host user to .env <br>
   `echo UID=$(id -u) >> .env` <br>
   `echo GID=$(id -g) >> .env` <br>

6) Run services docker <br>
   `docker-compose up -d --build` <br>

7) Install eslint, prettier <br>
   `npm install --save-dev @rushstack/eslint-patch` <br>
   `npm install --save-dev @vue/eslint-config-prettier` <br>
   `npm install --save-dev eslint` <br>
   `npm install --save-dev eslint-plugin-vue` <br>
   `npm install --save-dev prettier` <br>

8) `npm run lint` <br>

9) Install npm dependencies <br>
   `npm install` <br>
   `npm run dev` <br>
   `vite build` <br>
   `vite` <br>

10) composer require unisharp/laravel-filemanager
    `php artisan vendor:publish --tag=lfm_config` <br>
    `php artisan vendor:publish --tag=lfm_public` <br>
    web.php: `Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
    });` <br>
    .env: `FILESYSTEM_DRIVER=public` <br>

11) Create link Storage <br>
   `docker exec mark-php-app php artisan storage:link`<br>

12)  npm install <br>
    `npm install @inertiajs/inertia` <br>
    `npm install @mayasabha/ckeditor4-vue3` <br>
    `npm install tinymce` <br>
    `npm install chart.js chartjs-adapter-moment` <br>
    `npm install xlsx html2pdf.js jszip file-saver docx` <br>
    `npm install codemirror @codemirror/lang-javascript @codemirror/state @codemirror/view @codemirror/theme-one-dark` <br>
    `npm install @fortawesome/vue-fontawesome @fortawesome/fontawesome-svg-core @fortawesome/free-solid-svg-icons` <br>
    `npm install vue-i18n@next` <br>
    `npm install vue-draggable-next` <br>
    `npm install roughjs` <br>
    `npm install @vueuse/head` <br>
    `npm install @vue-flow/core @vue-flow/background @vue-flow/controls @vue-flow/minimap` <br>
    `npm i flowchart` <br>
    `npm i vue-echarts-v3` <br>
    `npm install date-fns` <br>
    `npm install highlight.js` <br>
    `npm install vue-toastification@next` <br>
    `npm install @popperjs/core` <br>
     `npm i ` <br>

-------------------------------------------------------------------------------------

`npm install -D sass-embedded` <br>
`npm install -D sass` <br>
`npm i @fontsource-variable/roboto-flex @fontsource-variable/montserrat vue3-carousel @vueform/slider` <br>
-------------------------------------------------------------------------------------

1) Install Jetstream <br>
    `composer require laravel/jetstream` <br>
    `docker exec mark-php-app php artisan jetstream:install inertia --ssr --teams` <br>
    `npm install` <br>
    `npm run dev` <br>

2) Install Spatie <br>
`composer require spatie/laravel-permission` <br>
`docker exec mark-php-app php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"` <br>
`docker exec mark-php-app php artisan optimize:clear` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback --path=database/migrations/2025_04_03_073100_create_video_likes_table.php`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan db:seed` <br>
`// The User model requires this trait
    use HasRoles;`<br>
-------------------------------------------------------------------------------------

1) Помощь в командах
 Удалите существующие символические ссылки <br>
`docker exec -it mark-php-app rm /var/www/public/storage` <br>
`docker exec -it mark-php-app rm /var/www/storage/api-docs` <br>
 Создайте новые символические ссылки внутри контейнера <br>
`docker exec -it mark-php-app ln -s /var/www/storage/app/public /var/www/public/storage`  <br>
`docker exec -it mark-php-app ln -s /var/www/storage/api-docs /var/www/public/storage/api-docs`  <br>
 Установите правильные права доступа <br>
`docker exec -it --user root mark-php-app sh`  <br>
`chmod -R 775 /var/www/storage/app/public`  <br>
`chmod -R 775 /var/www/storage/app/settings`  <br>
`chmod -R 775 /var/www/storage/api-docs`  <br>
`exit`  <br>
 Скопируйте нужные файлы <br>
`docker exec -it mark-php-app mkdir -p /var/www/public/vendor/swagger-api/swagger-ui/dist`  <br>
`docker exec -it mark-php-app cp -r /var/www/vendor/swagger-api/swagger-ui/dist/. /var/www/public/vendor/swagger-api/swagger-ui/dist/`  <br>
 Очистите кэш и перезапустите контейнер:  <br>
`docker exec -it mark-php-app php artisan cache:clear`  <br>
`docker exec -it mark-php-app php artisan config:clear`  <br>
`docker exec -it mark-php-app php artisan route:clear`  <br>
`docker exec -it mark-php-app php artisan optimize:clear`  <br>
`docker exec -it mark-php-app php artisan view:clear`  <br>
`docker restart mark-php-app`  <br>
`docker exec -it mark-php-app php artisan route:list`  <br>
`mkdir -p app/Services`  <br>
`composer config --global disable-tls true` <br> отключение сертификатов, если нужно
`php --ini` <br> найти php.ini
`composer diagnose` <br> диагностика composer
`composer self-update` <br> обновление текущей версии composer
`docker exec -it mark-php-app sh` <br> открытие командной строки в linux
`docker exec mark-php-app php -m` <br> проверка расширений контейнера сервера
`docker exec -it mark-php-app composer dump-autoload` <br> очистка кеша перед пересборкой
`docker exec -it mark-php-app composer install --no-cache --no-interaction --prefer-dist` <br> пересборка зависимостей composer
`composer config --global disable-tls false` <br> включение сертификатов обратно
`docker exec mark-php-app php -r "echo ini_get('upload_max_filesize').PHP_EOL; echo ini_get('post_max_size').PHP_EOL;"`<br> посмотреть параметры php.ini 
-------------------------------------------------------------------------------------

1) creating business logic app Role <br>
`docker exec mark-php-app php artisan make:controller Admin/System/Role/RoleController --resource` <br>
`docker exec mark-php-app php artisan make:resource Admin/System/Role/RoleResource` <br>
`docker exec mark-php-app php artisan make:request Admin/System/Role/RoleRequest` <br>
`docker exec mark-php-app php artisan make:seeder RoleSeeder` <br>
`docker exec mark-php-app php artisan db:seed --class=RoleSeeder` <br>

2) creating business logic app Permission <br>
`docker exec mark-php-app php artisan make:controller Admin/System/Permission/PermissionController --resource` <br>
`docker exec mark-php-app php artisan make:resource Admin/System/Permission/PermissionResource` <br>
`docker exec mark-php-app php artisan make:request Admin/System/Permission/PermissionRequest` <br>

3) creating business logic app User <br>
`docker exec mark-php-app php artisan make:controller Admin/System/User/UserController --resource` <br>
`docker exec mark-php-app php artisan make:resource Admin/System/User/UserResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/System/User/UserSharedResource` <br>
-------------------------------------------------------------------------------------

1) Create revoke controllers <br>
`docker exec mark-php-app php artisan make:controller Admin/Invokable/RemovePermissionFromRoleController --invokable` <br>
`docker exec mark-php-app php artisan make:controller Admin/Invokable/RemoveRoleFromUserController --invokable` <br>
`docker exec mark-php-app php artisan make:request Admin/System/User/StoreUserRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/System/User/UpdateUserRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Invokable/RemovePermissionFromUserController --invokable` <br>
-------------------------------------------------------------------------------------

1) creating business logic app Setting <br>
`docker exec mark-php-app php artisan make:model Admin/System/Setting/Setting -m` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan make:seeder SettingSeeder` <br>
`docker exec mark-php-app php artisan db:seed --class=SettingSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/System/Setting/SettingResource` <br>
`docker exec mark-php-app php artisan make:request Admin/System/Setting/SettingRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/System/Parameter/ParameterRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/System/Setting/UpdateLocaleRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/System/Setting/UpdateCountSettingRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/System/Setting/UpdateSortSettingRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/System/Setting/UpdateWidgetPanelRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/System/Setting/UpdateSidebarSettingsRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/System/Setting/SettingController --resource` <br>
`docker exec mark-php-app php artisan make:request Admin/System/Setting/UpdateSettingValueRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/System/Parameter/UpdateParameterValueRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/System/Parameter/ParameterController --resource` <br>
`docker exec mark-php-app php artisan make:controller Admin/System/Snapshot/SettingsSnapshotController` <br>

2)  Create middleware ShareSettings <br>
`docker exec mark-php-app php artisan make:resource Admin/System/Setting/SettingSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/System/SystemController` <br>
`docker exec mark-php-app php artisan make:controller Api/Parameter/ApiParameterController --api` <br>
`docker exec mark-php-app php artisan make:controller Api/Setting/ApiSettingController --api` <br>

3) creating business logic app System <br>
`docker exec mark-php-app php artisan make:controller Admin/System/Log/LogController` <br>
`docker exec mark-php-app php artisan make:controller Admin/System/PhpInfoController` <br>
`docker exec mark-php-app php artisan make:controller Admin/System/ComposerController` <br>
`docker exec mark-php-app php artisan make:controller Admin/System/PackageController` <br>
`docker exec mark-php-app php artisan make:controller Admin/System/EnvController` <br>
`docker exec mark-php-app php artisan make:controller Admin/System/RobotController` <br>
`docker exec mark-php-app php artisan make:controller Admin/System/SitemapController` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/HomeController` <br>
-------------------------------------------------------------------------------------

1) creating model BaseImage & BaseImageResource
`docker exec mark-php-app php artisan make:model Admin/Image/BaseImage` <br>
`docker exec mark-php-app php artisan make:resource Admin/Image/BaseImageResource` <br>

2) creating business logic Backup
`docker exec mark-php-app php artisan make:controller Admin/System/DatabaseBackupController` <br>
`docker exec mark-php-app php artisan make:controller Admin/System/FileBackupController` <br>
-------------------------------------------------------------------------------------

1) ImagePreset - варианты обработки изображений
`docker exec mark-php-app php artisan make:model Admin/System/ImagePreset/ImagePreset -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=ImagePresetSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/System/ImagePreset/ImagePresetResource` <br>
`docker exec mark-php-app php artisan make:request Admin/System/ImagePreset/ImagePresetRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/System/ImagePreset/ImagePresetController` <br>
-------------------------------------------------------------------------------------

1) Privacy — согласия, cookies, политика конфиденциальности
`docker exec mark-php-app php artisan make:model Admin/Privacy/PrivacyUserConsent/PrivacyUserConsent -m` <br>
`docker exec mark-php-app php artisan make:resource Admin/Privacy/PrivacyUserConsent/PrivacyUserConsentResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Privacy/PrivacyUserConsent/PrivacyUserConsentRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Privacy/PrivacyUserConsent/PrivacyUserConsentStatusRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Privacy/PrivacyUserConsent/PrivacyUserConsentController` <br>
`docker exec mark-php-app php artisan make:controller Public/Privacy/PrivacyController` <br>
`docker exec mark-php-app php artisan make:controller Public/Privacy/PrivacyUserConsentController` <br>

2) Analytics — журнал посещений, отчёты, графики, очистка
`docker exec mark-php-app php artisan make:model Admin/Analytics/AnalyticsVisitorLog/AnalyticsVisitorLog -mf` <br>
`docker exec mark-php-app php artisan make:resource Admin/Analytics/AnalyticsVisitorLog/AnalyticsVisitorLogResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Analytics/AnalyticsVisitorLog/AnalyticsVisitorLogRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Analytics/AnalyticsVisitorLog/AnalyticsVisitorLogController` <br>
`docker exec mark-php-app php artisan make:controller Admin/Analytics/AnalyticsVisitorLog/AdminAnalyticsVisitorLogController` <br>
`docker exec mark-php-app php artisan make:controller Admin/Analytics/AnalyticsImport/AnalyticsImportController` <br>
`docker exec mark-php-app php artisan make:request Admin/Analytics/AnalyticsCleanup/AnalyticsCleanupRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Analytics/AnalyticsCleanup/AnalyticsCleanupController` <br>

3) Services (создать вручную)
Privacy
`app/Services/Admin/Privacy/PrivacyPolicyService.php` <br>
Base
`app/Services/Admin/Base/File/JsonlFileWriterService.php` <br>
Analytics
`app/Services/Admin/Analytics/AnalyticsFileWriterService.php` <br>

4) SQLite Analytics
`docker exec mark-php-app rm database/analytics.sqlite` <br>
`docker exec mark-php-app touch database/analytics.sqlite` <br>
`docker exec mark-php-app chmod 664 database/analytics.sqlite` <br>
`docker exec mark-php-app mkdir -p database/migrations_analytics` <br>
`docker exec mark-php-app php artisan migrate --database=analytics --path=database/migrations_analytics` <br>
`docker exec mark-php-app php artisan schedule:work` <br>

5) Импорт JSONL в SQLite Analytics
`docker exec mark-php-app php artisan make:command ImportAnalyticsVisitorLogsCommand` <br>
`docker exec mark-php-app php artisan analytics:import-visitor-logs` <br>
-------------------------------------------------------------------------------------

1) CMS страницы <br>
`docker exec mark-php-app php artisan make:model Admin/Cms/CmsPage/CmsPage -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Cms/CmsPage/CmsPageTranslation -m` <br>
`docker exec mark-php-app php artisan make:resource Admin/Cms/CmsPage/CmsPageResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Cms/CmsPage/CmsPageSharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Cms/CmsPage/CmsPageTranslationResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Cms/CmsPage/CmsPageRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Cms/CmsPage/CmsPageController --resource` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=CmsPageSeeder` <br>
`docker exec mark-php-app php artisan make:class Services/Public/Cms/CmsNavigationService` <br>
`docker exec mark-php-app php artisan make:controller Public/Cms/CmsPagePublicController` <br>
`docker exec mark-php-app php artisan make:class Services/Public/Cms/CmsPageResolverService` <br>
-------------------------------------------------------------------------------------

1) creating business logic app BlogRubric <br>
`docker exec mark-php-app php artisan make:model Admin/Blog/BlogRubric/BlogRubric -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Blog/BlogRubric/BlogRubricTranslation -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Blog/BlogRubric/BlogRubricImage -mf` <br>
`docker exec mark-php-app php artisan make:migration create_blog_rubric_has_images_table --create=blog_rubric_has_images` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan db:seed --class=BlogRubricSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/Blog/BlogRubric/BlogRubricRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Blog/UpdateActivityRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Blog/UpdateSortEntityRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/BlogRubric/BlogRubricResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/BlogRubric/BlogRubricTranslationResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/BlogRubric/BlogRubricImageResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/BlogRubric/BlogRubricSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/Blog/BlogRubric/BlogRubricController --resource` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/Blog/BlogRubric/BlogRubricController` <br>

2) creating business logic app BlogArticle <br>
`docker exec mark-php-app php artisan make:model Admin/Blog/BlogArticle/BlogArticle -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Blog/BlogArticle/BlogArticleTranslation -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Blog/BlogArticle/BlogArticleImage -mf` <br>
`docker exec mark-php-app php artisan make:migration create_blog_article_has_images_table --create=blog_article_has_images` <br>
`docker exec mark-php-app php artisan make:migration create_blog_article_has_rubric_table --create=blog_article_has_rubric` <br>
`docker exec mark-php-app php artisan make:migration create_blog_article_related_table --create=blog_article_related` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan db:seed --class=BlogArticleSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/Blog/BlogArticle/BlogArticleRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Blog/UpdateLeftRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Blog/UpdateMainRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Blog/UpdateRightRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/BlogArticle/BlogArticleResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/BlogArticle/BlogArticleTranslationResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/BlogArticle/BlogArticleImageResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/BlogArticle/BlogArticleSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/Blog/BlogArticle/BlogArticleController --resource` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/Blog/BlogArticle/BlogArticleController` <br>
`docker exec mark-php-app php artisan make:model User/Like/BlogArticleLike -m` <br>

3) creating business logic app BlogTag <br>
`docker exec mark-php-app php artisan make:model Admin/Blog/BlogTag/BlogTag -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Blog/BlogTag/BlogTagTranslation -mfs` <br>
`docker exec mark-php-app php artisan make:migration create_blog_article_has_tag_table --create=blog_article_has_tag` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan db:seed --class=BlogTagSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/Blog/BlogTag/BlogTagRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/BlogTag/BlogTagResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/BlogTag/BlogTagTranslationResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/BlogTag/BlogTagSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/Blog/BlogTag/BlogTagController --resource` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/Blog/BlogTag/BlogTagController` <br>

4) creating business logic app BlogBanner <br>
`docker exec mark-php-app php artisan make:model Admin/Blog/BlogBanner/BlogBanner -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Blog/BlogBanner/BlogBannerTranslation -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Blog/BlogBanner/BlogBannerImage -mf` <br>
`docker exec mark-php-app php artisan make:migration create_blog_banner_has_images_table --create=blog_banner_has_images` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan db:seed --class=BlogBannerSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/Blog/BlogBanner/BlogBannerRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/BlogBanner/BlogBannerResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/BlogBanner/BlogBannerTranslationResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/BlogBanner/BlogBannerImageResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/BlogBanner/BlogBannerSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/Blog/BlogBanner/BlogBannerController --resource` <br>

5) creating business logic app BlogVideo <br>
`docker exec mark-php-app php artisan make:model Admin/Blog/BlogVideo/BlogVideo -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Blog/BlogVideo/BlogVideoTranslation -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Blog/BlogVideo/BlogVideoImage -mf` <br>
`docker exec mark-php-app php artisan make:migration create_blog_video_has_images_table --create=blog_video_has_images` <br>
`docker exec mark-php-app php artisan make:migration create_blog_article_has_video_table --create=blog_article_has_video` <br>
`docker exec mark-php-app php artisan make:migration create_blog_video_related_table --create=blog_video_related` <br>
`docker exec mark-php-app php artisan make:model User/Like/BlogVideoLike -m` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan db:seed --class=BlogVideoSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/Blog/BlogVideo/BlogVideoRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/BlogVideo/BlogVideoResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/BlogVideo/BlogVideoTranslationResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/BlogVideo/BlogVideoSharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/BlogVideo/BlogVideoImageResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/Blog/BlogVideo/BlogVideoController --resource` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/Blog/BlogVideo/BlogVideoController` <br>
-------------------------------------------------------------------------------------

1) creating business logic app Comment <br>
`docker exec mark-php-app php artisan make:model Admin/Blog/Comment/Comment -m`
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan make:factory Admin/Blog/Comment/CommentFactory --model=Comment` <br>
`docker exec mark-php-app php artisan make:seeder CommentsSeeder` <br>
`docker exec mark-php-app php artisan db:seed --class=CommentsSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/Comment/CommentResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/Comment/CommentSharedResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Blog/Comment/CommentRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Blog/Comment/ApproveCommentRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Blog/Comment/CommentController --resource` <br>
`docker exec mark-php-app php artisan make:controller Public/CommentController --resource` <br>
-------------------------------------------------------------------------------------

1) creating business logic app Report <br>
`docker exec mark-php-app php artisan make:controller Admin/System/Report/ReportController --resource` <br>

2) creating business logic app Chart <br>
`docker exec mark-php-app php artisan make:controller Admin/System/Chart/ChartController --resource` <br>
-------------------------------------------------------------------------------------

1) creating business logic app Component <br>
`docker exec mark-php-app php artisan make:controller Admin/System/Component/ComponentController --resource` <br>

2) creating business logic app Editor <br>
`docker exec mark-php-app php artisan make:controller Admin/System/Editor/EditorController --resource` <br>

3) creating business logic app Diagram <br>
`docker exec mark-php-app php artisan make:controller Admin/System/Diagram/DiagramController --resource` <br>
-------------------------------------------------------------------------------------

1) creating business logic app API <br>
`composer require "darkaonline/l5-swagger` <br>
`docker exec mark-php-app php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"` <br>
`docker exec mark-php-app php artisan make:controller Api/Blog/BlogRubric/ApiBlogRubricController --api` <br>
`docker exec mark-php-app php artisan make:controller Api/Blog/BlogArticle/ApiBlogArticleController --api` <br>
`docker exec mark-php-app php artisan l5-swagger:generate` <br>
`docker exec -it mark-php-app rm /var/www/public/storage` Удалите текущую символьную ссылку <br>
`docker exec -it mark-php-app ln -s /var/www/storage /var/www/public/storage` Пересоздайте символьную ссылку <br>
`docker exec -it mark-php-app ls -l /var/www/public/storage` Проверьте, правильно ли создана символьная ссылка <br>
`docker exec -it mark-php-app ls -l /var/www/storage/api-docs/` Убедитесь, что права доступа к директории и файлу корректны <br>
`docker exec -it --user root mark-php-app chmod -R 777 /var/www/storage/api-docs` Установите права доступа к папке <br>
`docker exec -it mark-php-app ls -l /var/www/public/storage/api-docs/api-docs.json` Убедитесь, что файл api-docs.json доступен через веб-сервер <br>
`docker exec -it mark-php-app ls /var/www/storage/api-docs/api-docs.json` После генерации проверьте наличие файла <br>
`docker-compose restart` <br>
`docker exec mark-php-app php artisan l5-swagger:generate` <br>
-------------------------------------------------------------------------------------

1) creating business logic SchoolHashtag - полиморфные хештеги онлайн-школы
`docker exec mark-php-app php artisan make:model Admin/School/SchoolHashtag/SchoolHashtag -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolHashtag/SchoolHashtagTranslation -mfs` <br>
`docker exec mark-php-app php artisan make:migration create_school_hashtaggables_table` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolHashtagSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolHashtag/SchoolHashtagRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolHashtag/SchoolHashtagResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolHashtag/SchoolHashtagSharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolHashtag/SchoolHashtagTranslationResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolHashtag/SchoolHashtagController --resource` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/School/SchoolHashtag/SchoolHashtagController` <br>

2) creating business logic SchoolInstructorProfile - преподаватели онлайн-школы
`docker exec mark-php-app php artisan make:model Admin/School/SchoolInstructorProfile/SchoolInstructorProfile -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolInstructorProfile/SchoolInstructorProfileTranslation -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolInstructorProfile/SchoolInstructorProfileImage -m` <br>
`docker exec mark-php-app php artisan make:migration create_school_instructor_profile_has_images_table --create=school_instructor_profile_has_images` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan make:seeder UserSeeder` <br>
`docker exec mark-php-app php artisan db:seed --class=UserSeeder` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolInstructorProfileSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolInstructorProfile/SchoolInstructorProfileRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolInstructorProfile/SchoolInstructorProfileResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolInstructorProfile/SchoolInstructorProfileSharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolInstructorProfile/SchoolInstructorProfileTranslationResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolInstructorProfile/SchoolInstructorProfileImageResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolInstructorProfile/SchoolInstructorProfileController --resource` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/School/SchoolInstructorProfile/SchoolInstructorController` <br>

3) creating business logic SchoolTrack - категории курсов обучения
`docker exec mark-php-app php artisan make:model Admin/School/SchoolTrack/SchoolTrack -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolTrack/SchoolTrackTranslation -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolTrack/SchoolTrackImage -mf` <br>
`docker exec mark-php-app php artisan make:migration create_school_track_has_images_table --create=school_track_has_images` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolTrackSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolTrack/SchoolTrackRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolTrack/SchoolTrackResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolTrack/SchoolTrackSharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolTrack/SchoolTrackTranslationResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolTrack/SchoolTrackImageResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolTrack/SchoolTrackController --resource` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/School/SchoolTrack/SchoolTrackController` <br>
`docker exec mark-php-app php artisan make:model User/Like/SchoolTrackLike -m` <br>

4) creating business logic SchoolCourse - курсы обучения
`docker exec mark-php-app php artisan make:model Admin/School/SchoolCourse/SchoolCourse -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolCourse/SchoolCourseTranslation -mfs` <br>
`docker exec mark-php-app php artisan make:migration create_school_course_related_table --create=school_course_related` <br>
`docker exec mark-php-app php artisan make:migration create_school_course_has_tracks_table --create=school_course_has_tracks` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolCourse/SchoolCourseImage -m` <br>
`docker exec mark-php-app php artisan make:migration create_school_course_has_images_table --create=school_course_has_images` <br>
`docker exec mark-php-app php artisan make:model User/Like/SchoolCourseLike -m` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolCourseSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolCourse/SchoolCourseRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolCourse/SchoolCourseResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolCourse/SchoolCourseSharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolCourse/SchoolCourseTranslationResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolCourse/SchoolCourseImageResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolCourse/SchoolCourseController --resource` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/School/SchoolCourse/SchoolCourseController` <br>

5) creating business logic SchoolModule - модули, подразделы курсов
`docker exec mark-php-app php artisan make:model Admin/School/SchoolModule/SchoolModule -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolModule/SchoolModuleTranslation -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolModule/SchoolModuleImage -m` <br>
`docker exec mark-php-app php artisan make:migration create_school_module_has_images_table --create=school_module_has_images` <br>
`docker exec mark-php-app php artisan make:model User/Like/SchoolModuleLike -m` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolModuleSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolModule/SchoolModuleRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolModule/SchoolModuleResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolModule/SchoolModuleSharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolModule/SchoolModuleTranslationResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolModule/SchoolModuleImageResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolModule/SchoolModuleController --resource` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/School/SchoolModule/SchoolModuleController` <br>

6) creating business logic SchoolLesson - уроки курсов обучения
`docker exec mark-php-app php artisan make:model Admin/School/SchoolLesson/SchoolLesson -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolLesson/SchoolLessonTranslation -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolLesson/SchoolLessonImage -m` <br>
`docker exec mark-php-app php artisan make:migration create_school_lesson_has_images_table --create=school_lesson_has_images` <br>
`docker exec mark-php-app php artisan make:model User/Like/SchoolLessonLike -m` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolLessonSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolLesson/SchoolLessonRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolLesson/SchoolLessonResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolLesson/SchoolLessonSharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolLesson/SchoolLessonTranslationResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolLesson/SchoolLessonImageResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolLesson/SchoolLessonController --resource` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/School/SchoolLesson/SchoolLessonController` <br>

7) creating business logic SchoolAssignment - домашние задания/практика
`docker exec mark-php-app php artisan make:model Admin/School/SchoolAssignment/SchoolAssignment -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolAssignment/SchoolAssignmentTranslation -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolAssignment/SchoolAssignmentImage -m` <br>
`docker exec mark-php-app php artisan make:migration create_school_assignment_has_images_table --create=school_assignment_has_images` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolAssignmentSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolAssignment/SchoolAssignmentRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolAssignment/SchoolAssignmentResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolAssignment/SchoolAssignmentSharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolAssignment/SchoolAssignmentTranslationResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolAssignment/SchoolAssignmentImageResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolAssignment/SchoolAssignmentController --resource` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/School/SchoolAssignment/SchoolAssignmentController` <br>

8) creating business logic SchoolCourseSchedule - расписание потоков
`docker exec mark-php-app php artisan make:model Admin/School/SchoolCourseSchedule/SchoolCourseSchedule -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolCourseSchedule/SchoolCourseScheduleTranslation -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolCourseSchedule/SchoolCourseScheduleImage -m` <br>
`docker exec mark-php-app php artisan make:migration create_school_course_schedule_has_images_table --create=school_course_schedule_has_images` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolCourseScheduleSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolCourseSchedule/SchoolCourseScheduleRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolCourseSchedule/SchoolCourseScheduleResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolCourseSchedule/SchoolCourseScheduleSharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolCourseSchedule/SchoolCourseScheduleTranslationResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolCourseSchedule/SchoolCourseScheduleImageResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolCourseSchedule/SchoolCourseScheduleController --resource` <br>

9) creating business logic SchoolCohortEnrollment - запись на потоки курсов
`docker exec mark-php-app php artisan make:model Admin/School/SchoolCohortEnrollment/SchoolCohortEnrollment -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolCohortEnrollmentSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolCohortEnrollment/SchoolCohortEnrollmentRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolCohortEnrollment/SchoolCohortEnrollmentResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolCohortEnrollment/SchoolCohortEnrollmentSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolCohortEnrollment/SchoolCohortEnrollmentController --resource` <br>

10) creating business logic SchoolOrder - заказы онлайн-школы
`docker exec mark-php-app php artisan make:model Admin/School/SchoolOrder/SchoolOrder -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolOrderSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolOrder/SchoolOrderRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolOrder/SchoolOrderResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolOrder/SchoolOrderSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolOrder/SchoolOrderController --resource` <br>

11) creating business logic SchoolEnrollment - зачисление студентов на потоки/курсы
`docker exec mark-php-app php artisan make:model Admin/School/SchoolEnrollment/SchoolEnrollment -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolEnrollmentSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolEnrollment/SchoolEnrollmentRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolEnrollment/SchoolEnrollmentResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolEnrollment/SchoolEnrollmentSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolEnrollment/SchoolEnrollmentController --resource` <br>

12) creating business logic SchoolQuiz - вопросники / викторины
`docker exec mark-php-app php artisan make:model Admin/School/SchoolQuiz/SchoolQuiz -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolQuiz/SchoolQuizTranslation -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolQuiz/SchoolQuizImage -m` <br>
`docker exec mark-php-app php artisan make:migration create_school_quiz_has_images_table --create=school_quiz_has_images` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolQuizSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolQuiz/SchoolQuizRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolQuiz/SchoolQuizResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolQuiz/SchoolQuizSharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolQuiz/SchoolQuizTranslationResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolQuiz/SchoolQuizImageResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolQuiz/SchoolQuizController --resource` <br>

13) creating business logic SchoolQuizQuestion - вопросы викторин
`docker exec mark-php-app php artisan make:model Admin/School/SchoolQuizQuestion/SchoolQuizQuestion -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolQuizQuestion/SchoolQuizQuestionTranslation -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolQuizQuestionSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolQuizQuestion/SchoolQuizQuestionRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolQuizQuestion/SchoolQuizQuestionResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolQuizQuestion/SchoolQuizQuestionSharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolQuizQuestion/SchoolQuizQuestionTranslationResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolQuizQuestion/SchoolQuizQuestionController --resource` <br>

14) creating business logic SchoolQuizAnswer - ответы на вопросы викторин
`docker exec mark-php-app php artisan make:model Admin/School/SchoolQuizAnswer/SchoolQuizAnswer -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolQuizAnswer/SchoolQuizAnswerTranslation -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolQuizAnswerSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolQuizAnswer/SchoolQuizAnswerRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolQuizAnswer/SchoolQuizAnswerResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolQuizAnswer/SchoolQuizAnswerSharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolQuizAnswer/SchoolQuizAnswerTranslationResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolQuizAnswer/SchoolQuizAnswerController --resource` <br>

15) creating business logic SchoolQuizAttempt - попытка прохождения викторины
`docker exec mark-php-app php artisan make:model Admin/School/SchoolQuizAttempt/SchoolQuizAttempt -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolQuizAttemptSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolQuizAttempt/SchoolQuizAttemptRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolQuizAttempt/SchoolQuizAttemptResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolQuizAttempt/SchoolQuizAttemptController --resource` <br>

16) creating business logic SchoolQuizAttemptItem - ответ на один конкретный вопрос в рамках попытки
`docker exec mark-php-app php artisan make:model Admin/School/SchoolQuizAttemptItem/SchoolQuizAttemptItem -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolQuizAttemptItemSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolQuizAttemptItem/SchoolQuizAttemptItemRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolQuizAttemptItem/SchoolQuizAttemptItemResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolQuizAttemptItem/SchoolQuizAttemptItemController --resource` <br>

17) creating business logic SchoolBundle - набор курсов
`docker exec mark-php-app php artisan make:model Admin/School/SchoolBundle/SchoolBundle -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolBundle/SchoolBundleTranslation -mfs` <br>
`docker exec mark-php-app php artisan make:migration create_school_bundle_has_courses_table --create=school_bundle_has_courses` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolBundle/SchoolBundleImage -m` <br>
`docker exec mark-php-app php artisan make:migration create_school_bundle_has_images_table --create=school_bundle_has_images` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolBundleSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolBundle/SchoolBundleRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolBundle/SchoolBundleResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolBundle/SchoolBundleSharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolBundle/SchoolBundleTranslationResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolBundle/SchoolBundleImageResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolBundle/SchoolBundleController --resource` <br>

18) creating business logic SchoolCoursePrice - прайсы курса
`docker exec mark-php-app php artisan make:model Admin/School/SchoolCoursePrice/SchoolCoursePrice -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolCoursePriceSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolCoursePrice/SchoolCoursePriceRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolCoursePrice/SchoolCoursePriceResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolCoursePrice/SchoolCoursePriceSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolCoursePrice/SchoolCoursePriceController --resource` <br>

19) creating business logic SchoolBundlePrice - прайсы набора курсов
`docker exec mark-php-app php artisan make:model Admin/School/SchoolBundlePrice/SchoolBundlePrice -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolBundlePriceSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolBundlePrice/SchoolBundlePriceRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolBundlePrice/SchoolBundlePriceResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolBundlePrice/SchoolBundlePriceSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolBundlePrice/SchoolBundlePriceController --resource` <br>

20) creating business logic SchoolSubscriptionPlan - тарифные планы
`docker exec mark-php-app php artisan make:model Admin/School/SchoolSubscriptionPlan/SchoolSubscriptionPlan -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolSubscriptionPlan/SchoolSubscriptionPlanTranslation -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolSubscriptionPlan/SchoolSubscriptionPlanImage -m` <br>
`docker exec mark-php-app php artisan make:migration create_school_subscription_plan_has_images_table --create=school_subscription_plan_has_images` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolSubscriptionPlanSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolSubscriptionPlan/SchoolSubscriptionPlanRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolSubscriptionPlan/SchoolSubscriptionPlanResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolSubscriptionPlan/SchoolSubscriptionPlanSharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolSubscriptionPlan/SchoolSubscriptionPlanTranslationResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolSubscriptionPlan/SchoolSubscriptionPlanImageResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolSubscriptionPlan/SchoolSubscriptionPlanController --resource` <br>

21) creating business logic SchoolOrderItem - позиции заказа онлайн-школы
`docker exec mark-php-app php artisan make:model Admin/School/SchoolOrderItem/SchoolOrderItem -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolOrderItemSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolOrderItem/SchoolOrderItemRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolOrderItem/SchoolOrderItemResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolOrderItem/SchoolOrderItemController --resource` <br>

22) creating business logic SchoolPaymentMethod
`docker exec mark-php-app php artisan make:model Admin/School/SchoolPaymentMethod/SchoolPaymentMethod -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolPaymentMethodSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolPaymentMethod/SchoolPaymentMethodRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolPaymentMethod/SchoolPaymentMethodResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolPaymentMethod/SchoolPaymentMethodController --resource` <br>

23) creating business logic SchoolUserPaymentMethod
`docker exec mark-php-app php artisan make:model Admin/School/SchoolUserPaymentMethod/SchoolUserPaymentMethod -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolUserPaymentMethodSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolUserPaymentMethod/SchoolUserPaymentMethodRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolUserPaymentMethod/SchoolUserPaymentMethodResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolUserPaymentMethod/SchoolUserPaymentMethodController --resource` <br>

24) creating business logic SchoolPayment
`docker exec mark-php-app php artisan make:model Admin/School/SchoolPayment/SchoolPayment -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolPayment/SchoolPaymentRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolPayment/SchoolPaymentResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolPayment/SchoolPaymentController --resource` <br>

25) creating business logic SchoolSubscription - подписки пользователей
`docker exec mark-php-app php artisan make:model Admin/School/SchoolSubscription/SchoolSubscription -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolSubscriptionSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolSubscription/SchoolSubscriptionRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolSubscription/SchoolSubscriptionResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolSubscription/SchoolSubscriptionSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolSubscription/SchoolSubscriptionController --resource` <br>

26) creating business logic SchoolRefund - возвраты платежей онлайн-школы
`docker exec mark-php-app php artisan make:model Admin/School/SchoolRefund/SchoolRefund -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolRefund/SchoolRefundRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolRefund/SchoolRefundResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolRefund/SchoolRefundSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolRefund/SchoolRefundController --resource` <br>

27) creating business logic SchoolCoupon - купоны/промокоды онлайн-школы
`docker exec mark-php-app php artisan make:model Admin/School/SchoolCoupon/SchoolCoupon -mfs` <br>
`docker exec mark-php-app php artisan make:migration create_school_coupon_has_courses_table --create=school_coupon_has_courses` <br>
`docker exec mark-php-app php artisan make:migration create_school_coupon_has_bundles_table --create=school_coupon_has_bundles` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolCouponSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolCoupon/SchoolCouponRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolCoupon/SchoolCouponResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolCoupon/SchoolCouponSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolCoupon/SchoolCouponController --resource` <br>

28) creating business logic SchoolInvoice - инвойсы онлайн-школы
`docker exec mark-php-app php artisan make:model Admin/School/SchoolInvoice/SchoolInvoice -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolInvoiceSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolInvoice/SchoolInvoiceRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolInvoice/SchoolInvoiceResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolInvoice/SchoolInvoiceSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolInvoice/SchoolInvoiceController --resource` <br>

29) creating business logic SchoolProviderAccount - аккаунты платёжных провайдеров
`docker exec mark-php-app php artisan make:model Admin/School/SchoolProviderAccount/SchoolProviderAccount -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolProviderAccountSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolProviderAccount/SchoolProviderAccountRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolProviderAccount/SchoolProviderAccountResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolProviderAccount/SchoolProviderAccountSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolProviderAccount/SchoolProviderAccountController --resource` <br>

30) creating business logic SchoolPayout - выплаты преподавателям
`docker exec mark-php-app php artisan make:model Admin/School/SchoolPayout/SchoolPayout -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolPayoutSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolPayout/SchoolPayoutRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolPayout/SchoolPayoutResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolPayout/SchoolPayoutSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolPayout/SchoolPayoutController --resource` <br>

31) creating business logic SchoolPayoutItem - позиции выплат
`docker exec mark-php-app php artisan make:model Admin/School/SchoolPayoutItem/SchoolPayoutItem -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolPayoutItemSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolPayoutItem/SchoolPayoutItemRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolPayoutItem/SchoolPayoutItemResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolPayoutItem/SchoolPayoutItemSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolPayoutItem/SchoolPayoutItemController --resource` <br>

32) creating business logic SchoolWebhookEvent - события вебхуков
`docker exec mark-php-app php artisan make:model Admin/School/SchoolWebhookEvent/SchoolWebhookEvent -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolWebhookEventSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolWebhookEvent/SchoolWebhookEventRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolWebhookEvent/SchoolWebhookEventResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolWebhookEvent/SchoolWebhookEventSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolWebhookEvent/SchoolWebhookEventController --resource` <br>

33) creating business logic SchoolAssignmentSubmission - сдачи заданий студентами
`docker exec mark-php-app php artisan make:model Admin/School/SchoolAssignmentSubmission/SchoolAssignmentSubmission -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolAssignmentSubmissionSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolAssignmentSubmission/SchoolAssignmentSubmissionRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolAssignmentSubmission/SchoolAssignmentSubmissionResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolAssignmentSubmission/SchoolAssignmentSubmissionSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolAssignmentSubmission/SchoolAssignmentSubmissionController --resource` <br>

34) creating business logic SchoolProgressRecord - прогресс обучения
`docker exec mark-php-app php artisan make:model Admin/School/SchoolProgressRecord/SchoolProgressRecord -mf` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolProgressRecord/SchoolProgressRecordRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolProgressRecord/SchoolProgressRecordResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolProgressRecord/SchoolProgressRecordSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolProgressRecord/SchoolProgressRecordController --resource` <br>

35) creating business logic SchoolReview - отзывы/рейтинги онлайн-школы
`docker exec mark-php-app php artisan make:model Admin/School/SchoolReview/SchoolReview -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolReviewSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolReview/SchoolReviewRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolReview/SchoolReviewResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolReview/SchoolReviewSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolReview/SchoolReviewController --resource` <br>

36) creating business logic QaThread - темы вопросов/обсуждений
`docker exec mark-php-app php artisan make:model Admin/School/SchoolQaThread/SchoolQaThread -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolQaThreadSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolQaThread/SchoolQaThreadRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolQaThread/SchoolQaThreadResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolQaThread/SchoolQaThreadSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolQaThread/SchoolQaThreadController --resource` <br>

37) creating business logic QaMessage - сообщения в темах
`docker exec mark-php-app php artisan make:model Admin/School/SchoolQaMessage/SchoolQaMessage -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolQaMessageSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolQaMessage/SchoolQaMessageRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolQaMessage/SchoolQaMessageResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolQaMessage/SchoolQaMessageController --resource` <br>

38) creating business logic SchoolBookmark - закладки пользователя
`docker exec mark-php-app php artisan make:model Admin/School/SchoolBookmark/SchoolBookmark -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolBookmarkSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolBookmark/SchoolBookmarkRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolBookmark/SchoolBookmarkResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolBookmark/SchoolBookmarkSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolBookmark/SchoolBookmarkController --resource` <br>

39) creating business logic SchoolCertificate - сертификаты об окончании курса
`docker exec mark-php-app php artisan make:model Admin/School/SchoolCertificate/SchoolCertificate -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolCertificateSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolCertificate/SchoolCertificateRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolCertificate/SchoolCertificateResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolCertificate/SchoolCertificateSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolCertificate/SchoolCertificateController --resource` <br>

40) creating business logic SchoolFaqCategory - категории FAQ
`docker exec mark-php-app php artisan make:model Admin/School/SchoolFaqCategory/SchoolFaqCategory -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolFaqCategory/SchoolFaqCategoryTranslation -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolFaqCategorySeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolFaqCategory/SchoolFaqCategoryResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolFaqCategory/SchoolFaqCategoryTranslationResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolFaqCategory/SchoolFaqCategoryRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolFaqCategory/SchoolFaqCategoryController --resource` <br>

41) creating business logic SchoolFaq - FAQ (вопросы и ответы)
`docker exec mark-php-app php artisan make:model Admin/School/SchoolFaq/SchoolFaq -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/SchoolFaq/SchoolFaqTranslation -mfs` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=SchoolFaqSeeder` <br>
`docker exec mark-php-app php artisan make:request Admin/School/SchoolFaq/SchoolFaqRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolFaq/SchoolFaqResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/SchoolFaq/SchoolFaqTranslationResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/SchoolFaq/SchoolFaqController --resource` <br>
-------------------------------------------------------------------------------------

1) Группы характеристик
`docker exec mark-php-app php artisan make:model Admin/Market/MarketAttributeGroup/MarketAttributeGroup -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Market/MarketAttributeGroup/MarketAttributeGroupTranslation -m` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketAttributeGroup/MarketAttributeGroupResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketAttributeGroup/MarketAttributeGroupSharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketAttributeGroup/MarketAttributeGroupTranslationResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Market/MarketAttributeGroup/MarketAttributeGroupRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Market/MarketAttributeGroup/MarketAttributeGroupController --resource` <br>

2) Характеристики
`docker exec mark-php-app php artisan make:model Admin/Market/MarketAttribute/MarketAttribute -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Market/MarketAttribute/MarketAttributeTranslation -m` <br>
`docker exec mark-php-app php artisan make:model Admin/Market/MarketAttributeValue/MarketAttributeValue -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Market/MarketAttributeValue/MarketAttributeValueTranslation -m` <br>
`docker exec mark-php-app php artisan make:migration create_market_attributables_table` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketAttribute/MarketAttributeResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketAttribute/MarketAttributeSharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketAttribute/MarketAttributeTranslationResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketAttributeValue/MarketAttributeValueResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketAttributeValue/MarketAttributeValueSharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketAttributeValue/MarketAttributeValueTranslationResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Market/MarketAttribute/MarketAttributeRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Market/MarketAttributeValue/MarketAttributeValueRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Market/MarketAttribute/MarketAttributeController --resource` <br>
`docker exec mark-php-app php artisan make:controller Admin/Market/MarketAttributeValue/MarketAttributeValueController --resource` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=MarketAttributeGroupSeeder` <br>
`docker exec mark-php-app php artisan db:seed --class=MarketAttributeSeeder` <br>
`docker exec mark-php-app php artisan db:seed --class=MarketAttributeValueSeeder` <br>

3) Компания <br> 
`docker exec mark-php-app php artisan make:model Admin/Market/MarketCompany/MarketCompany -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Market/MarketCompany/MarketCompanyTranslation -m` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketCompany/MarketCompanyResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketCompany/MarketCompanyTranslationResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Market/MarketCompany/MarketCompanyRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Market/MarketCompany/MarketCompanyController --resource` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=MarketCompanySeeder` <br>

4) Магазин <br>
`docker exec mark-php-app php artisan make:model Admin/Market/MarketShop/MarketShop -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Market/MarketShop/MarketShopTranslation -m` <br>
`docker exec mark-php-app php artisan make:model Admin/Market/MarketShop/MarketShopImage -m` <br>
`docker exec mark-php-app php artisan make:migration create_market_shop_has_images_table --create=market_shop_has_images` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketShop/MarketShopResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketShop/MarketShopTranslationResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketShop/MarketShopImageResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Market/MarketShop/MarketShopRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Market/MarketShop/MarketShopController --resource` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=MarketShopSeeder` <br>

5) Market Categories - категории товаров / дерево категорий <br>
`docker exec mark-php-app php artisan make:model Admin/Market/MarketCategory/MarketCategory -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Market/MarketCategory/MarketCategoryTranslation -m` <br>
`docker exec mark-php-app php artisan make:model Admin/Market/MarketCategory/MarketCategoryImage -m` <br>
`docker exec mark-php-app php artisan make:migration create_market_category_has_images_table --create=market_category_has_images` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketCategory/MarketCategoryResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketCategory/MarketCategorySharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketCategory/MarketCategoryTranslationResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketCategory/MarketCategoryImageResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Market/MarketCategory/MarketCategoryRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Market/MarketCategory/MarketCategoryController --resource` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=MarketCategorySeeder` <br>
`docker exec mark-php-app php artisan make:class Services/Public/Market/MarketCatalogNavigationService` <br>

6) Бренды <br>
`docker exec mark-php-app php artisan make:model Admin/Market/MarketBrand/MarketBrand -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Market/MarketBrand/MarketBrandTranslation -m` <br>
`docker exec mark-php-app php artisan make:model Admin/Market/MarketBrand/MarketBrandImage -m` <br>
`docker exec mark-php-app php artisan make:migration create_market_brand_has_images_table --create=market_brand_has_images` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketBrand/MarketBrandResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketBrand/MarketBrandSharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketBrand/MarketBrandTranslationResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketBrand/MarketBrandImageResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Market/MarketBrand/MarketBrandRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Market/MarketBrand/MarketBrandController --resource` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=MarketBrandSeeder` <br>

7) Теги <br>
`docker exec mark-php-app php artisan make:model Admin/Market/MarketTag/MarketTag -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Market/MarketTag/MarketTagTranslation -m` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketTag/MarketTagResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketTag/MarketTagSharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketTag/MarketTagTranslationResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Market/MarketTag/MarketTagRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Market/MarketTag/MarketTagController --resource` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=MarketTagSeeder` <br>

8) Товары <br>
`docker exec mark-php-app php artisan make:model Admin/Market/MarketProduct/MarketProduct -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Market/MarketProduct/MarketProductTranslation -m` <br>
`docker exec mark-php-app php artisan make:model Admin/Market/MarketProduct/MarketProductImage -m` <br>
`docker exec mark-php-app php artisan make:migration create_market_product_has_images_table --create=market_product_has_images` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketProduct/MarketProductResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketProduct/MarketProductSharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketProduct/MarketProductTranslationResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketProduct/MarketProductImageResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Market/MarketProduct/MarketProductRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Market/MarketProduct/MarketProductController --resource` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan migrate:rollback` <br>
`docker exec mark-php-app php artisan db:seed --class=MarketProductSeeder` <br>


5) Товары / Бренды
`docker exec mark-php-app php artisan make:migration create_market_products_table --create=market_products` <br>
`docker exec mark-php-app php artisan make:migration create_market_category_has_products_table --create=market_category_has_products` <br>
`docker exec mark-php-app php artisan make:migration create_market_brands_table --create=market_brands` <br>
`docker exec mark-php-app php artisan make:migration create_market_brand_has_products_table --create=market_brand_has_products` <br>

6) Варианты товаров / Рекомендованные товары / Комплекты товаров 
`docker exec mark-php-app php artisan make:migration create_market_product_variants_table --create=market_product_variants` <br>
`docker exec mark-php-app php artisan make:migration create_market_product_recommendations_table --create=market_product_recommendations` <br>
`docker exec mark-php-app php artisan make:migration create_market_kits_table --create=market_kits` <br>
`docker exec mark-php-app php artisan make:migration create_market_kit_items_table --create=market_kit_items` <br>

7) Группы характеристик / Характеристики / Значения характеристик
`docker exec mark-php-app php artisan make:migration create_market_property_groups_table --create=market_property_groups` <br>
`docker exec mark-php-app php artisan make:migration create_market_properties_table --create=market_properties` <br>
`docker exec mark-php-app php artisan make:migration create_market_property_values_table --create=market_property_values` <br>
`docker exec mark-php-app php artisan make:migration create_market_category_has_properties_table --create=market_category_has_properties` <br>
`docker exec mark-php-app php artisan make:migration create_market_product_variant_has_property_values_table --create=market_product_variant_has_property_values` <br>

8) Склады / остатки 
`docker exec mark-php-app php artisan make:migration create_market_warehouses_table --create=market_warehouses` <br>
`docker exec mark-php-app php artisan make:migration create_market_warehouse_stocks_table --create=market_warehouse_stocks` <br>

9) Модуль доставки / Логистика / ПВЗ / Зоны
`docker exec mark-php-app php artisan make:migration create_market_address_dictionary_table --create=market_address_dictionary` <br>
`docker exec mark-php-app php artisan make:migration create_market_delivery_providers_table --create=market_delivery_providers` <br>
`docker exec mark-php-app php artisan make:migration create_market_delivery_provider_services_table --create=market_delivery_provider_services` <br>
`docker exec mark-php-app php artisan make:migration create_market_delivery_statuses_table --create=market_delivery_statuses` <br>
`docker exec mark-php-app php artisan make:migration create_market_delivery_zones_table --create=market_delivery_zones` <br>
`docker exec mark-php-app php artisan make:migration create_market_delivery_zone_geometries_table --create=market_delivery_zone_geometries` <br>
`docker exec mark-php-app php artisan make:migration create_market_delivery_zone_coverage_rules_table --create=market_delivery_zone_coverage_rules` <br>
`docker exec mark-php-app php artisan make:model Admin/Market/DeliveryMethod/DeliveryMethod -fs` <br>
`docker exec mark-php-app php artisan make:migration create_market_delivery_methods_table --create=market_delivery_methods` <br>
`docker exec mark-php-app php artisan make:migration create_market_pickup_points_table --create=market_pickup_points` <br>
`docker exec mark-php-app php artisan make:migration create_market_delivery_method_has_pickup_points_table --create=market_delivery_method_has_pickup_points` <br>
`docker exec mark-php-app php artisan make:migration create_market_delivery_zone_rates_table --create=market_delivery_zone_rates` <br>
`docker exec mark-php-app php artisan make:migration create_market_delivery_zone_has_addresses_table --create=market_delivery_zone_has_addresses` <br>
`docker exec mark-php-app php artisan make:migration create_market_storefront_delivery_provider_settings_table --create=market_storefront_delivery_provider_settings` <br>
`docker exec mark-php-app php artisan make:migration create_market_delivery_method_has_providers_table --create=market_delivery_method_has_providers` <br>
`docker exec mark-php-app php artisan make:migration create_market_delivery_quotes_cache_table --create=market_delivery_quotes_cache` <br>

10) Корзина / События / Избранное
`docker exec mark-php-app php artisan make:model Admin/Market/Cart/Cart -fs` <br>
`docker exec mark-php-app php artisan make:migration create_market_carts_table --create=market_carts` <br>
`docker exec mark-php-app php artisan make:migration create_market_cart_sessions_table --create=market_cart_sessions` <br>
`docker exec mark-php-app php artisan make:model Admin/Market/CartItem/CartItem -fs` <br>
`docker exec mark-php-app php artisan make:migration create_market_cart_items_table --create=market_cart_items` <br>
`docker exec mark-php-app php artisan make:model Admin/Market/CartEvent/CartEvent -fs` <br>
`docker exec mark-php-app php artisan make:migration create_market_cart_events_table --create=market_cart_events` <br>
`docker exec mark-php-app php artisan make:migration create_market_cart_event_items_table --create=market_cart_event_items` <br>
`docker exec mark-php-app php artisan make:migration create_market_favorites_table --create=market_favorites` <br>

11) Бонусы / Программа бонусов
`docker exec mark-php-app php artisan make:migration create_market_bonus_programs_table --create=market_bonus_programs` <br>
`docker exec mark-php-app php artisan make:migration create_market_bonus_accounts_table --create=market_bonus_accounts` <br>
`docker exec mark-php-app php artisan make:migration create_market_bonus_operations_table --create=market_bonus_operations` <br>
`docker exec mark-php-app php artisan make:migration create_market_bonus_expirations_table --create=market_bonus_expirations` <br>
`docker exec mark-php-app php artisan make:migration create_market_bonus_spend_allocations_table --create=market_bonus_spend_allocations` <br>
`docker exec mark-php-app php artisan make:migration create_market_bonus_rules_table --create=market_bonus_rules` <br>
`docker exec mark-php-app php artisan make:migration create_market_bonus_rule_conditions_table --create=market_bonus_rule_conditions` <br>
`docker exec mark-php-app php artisan make:migration create_market_bonus_rule_rewards_table --create=market_bonus_rule_rewards` <br>

12) Заказы / Статусы заказов / История заказов
`docker exec mark-php-app php artisan make:migration create_market_order_statuses_table --create=market_order_statuses` <br>
`docker exec mark-php-app php artisan make:migration create_market_orders_table --create=market_orders` <br>
`docker exec mark-php-app php artisan make:migration create_market_order_items_table --create=market_order_items` <br>
`docker exec mark-php-app php artisan make:migration create_market_order_histories_table --create=market_order_histories` <br>

13) Модуль оплаты / Провайдеры оплаты / Транзакции / История оплаты 
`docker exec mark-php-app php artisan make:migration create_market_payment_providers_table --create=market_payment_providers` <br>
`docker exec mark-php-app php artisan make:migration create_market_storefront_payment_provider_settings_table --create=market_storefront_payment_provider_settings` <br>
`docker exec mark-php-app php artisan make:migration create_market_payments_table --create=market_payments` <br>
`docker exec mark-php-app php artisan make:migration create_market_payment_transactions_table --create=market_payment_transactions` <br>
`docker exec mark-php-app php artisan make:migration create_market_payment_histories_table --create=market_payment_histories` <br>

14) Инвойсы / Фискальные чеки / Документы
`docker exec mark-php-app php artisan make:migration create_market_invoices_table --create=market_invoices` <br>
`docker exec mark-php-app php artisan make:migration create_market_invoice_items_table --create=market_invoice_items` <br>
`docker exec mark-php-app php artisan make:migration create_market_fiscal_receipts_table --create=market_fiscal_receipts` <br>
`docker exec mark-php-app php artisan make:migration create_market_fiscal_receipt_items_table --create=market_fiscal_receipt_items` <br>
`docker exec mark-php-app php artisan make:migration create_market_order_documents_table --create=market_order_documents` <br>

15) Возвраты / История возвратов / Документы
`docker exec mark-php-app php artisan make:migration create_market_refunds_table --create=market_refunds` <br>
`docker exec mark-php-app php artisan make:migration create_market_refund_items_table --create=market_refund_items` <br>
`docker exec mark-php-app php artisan make:migration create_market_refund_documents_table --create=market_refund_documents` <br>
`docker exec mark-php-app php artisan make:migration create_market_return_statuses_table --create=market_return_statuses` <br>
`docker exec mark-php-app php artisan make:migration create_market_returns_table --create=market_returns` <br>
`docker exec mark-php-app php artisan make:migration create_market_return_items_table --create=market_return_items` <br>
`docker exec mark-php-app php artisan make:migration create_market_return_histories_table --create=market_return_histories` <br>
`docker exec mark-php-app php artisan make:migration create_market_return_documents_table --create=market_return_documents` <br>

16) Промокоды
`docker exec mark-php-app php artisan make:migration create_market_promo_campaigns_table --create=market_promo_campaigns` <br>
`docker exec mark-php-app php artisan make:migration create_market_promo_code_batches_table --create=market_promo_code_batches` <br>
`docker exec mark-php-app php artisan make:migration create_market_promo_codes_table --create=market_promo_codes` <br>
`docker exec mark-php-app php artisan make:migration create_market_promo_redemptions_table --create=market_promo_redemptions` <br>

17) Реферальная система 
`docker exec mark-php-app php artisan make:migration create_market_referral_programs_table --create=market_referral_programs` <br>
`docker exec mark-php-app php artisan make:migration create_market_referral_codes_table --create=market_referral_codes` <br>
`docker exec mark-php-app php artisan make:migration create_market_referral_events_table --create=market_referral_events` <br>
`docker exec mark-php-app php artisan make:migration create_market_referral_rewards_table --create=market_referral_rewards` <br>

18) Отзывы / Бонусы
`docker exec mark-php-app php artisan make:migration create_market_review_bonus_rules_table --create=market_review_bonus_rules` <br>
`docker exec mark-php-app php artisan make:migration create_market_product_reviews_table --create=market_product_reviews` <br>
`docker exec mark-php-app php artisan make:migration create_market_review_bonus_awards_table --create=market_review_bonus_awards` <br>
`docker exec mark-php-app php artisan make:migration create_market_company_reviews_table --create=market_company_reviews` <br>

19) Промо-коды
`docker exec mark-php-app php artisan make:migration add_unique_to_market_promo_redemptions_table --create=market_promo_redemptions` <br>
`docker exec mark-php-app php artisan make:migration create_market_promo_campaign_has_products_table --create=market_promo_campaign_has_products` <br>
`docker exec mark-php-app php artisan make:migration create_market_promo_campaign_has_categories_table --create=market_promo_campaign_has_categories` <br>
`docker exec mark-php-app php artisan make:migration create_market_promo_campaign_has_brands_table --create=market_promo_campaign_has_brands` <br>
`docker exec mark-php-app php artisan make:migration create_market_promo_campaign_has_delivery_methods_table --create=market_promo_campaign_has_delivery_methods` <br>

20) Страницы (дерево) / FAQ, категории
`docker exec mark-php-app php artisan make:migration create_market_storefront_pages_table --create=market_storefront_pages` <br>
`docker exec mark-php-app php artisan make:migration create_market_faq_categories_table --create=market_faq_categories` <br>
`docker exec mark-php-app php artisan make:migration create_market_faq_items_table --create=market_faq_items` <br>

21) Тикетная система (вопросы/ответы) / История / Теги
`docker exec mark-php-app php artisan make:migration create_market_ticket_statuses_table --create=market_ticket_statuses` <br>
`docker exec mark-php-app php artisan make:migration create_market_tickets_table --create=market_tickets` <br>
`docker exec mark-php-app php artisan make:migration create_market_ticket_messages_table --create=market_ticket_messages` <br>
`docker exec mark-php-app php artisan make:migration create_market_ticket_attachments_table --create=market_ticket_attachments` <br>
`docker exec mark-php-app php artisan make:migration create_market_ticket_histories_table --create=market_ticket_histories` <br>
`docker exec mark-php-app php artisan make:migration create_market_ticket_tags_table --create=market_ticket_tags` <br>
`docker exec mark-php-app php artisan make:migration create_market_ticket_has_tags_table --create=market_ticket_has_tags` <br>

22) Конструктор форм обратной связи / Антиспам
`docker exec mark-php-app php artisan make:migration create_market_feedback_forms_table --create=market_feedback_forms` <br>
`docker exec mark-php-app php artisan make:migration create_market_feedback_form_fields_table --create=market_feedback_form_fields` <br>
`docker exec mark-php-app php artisan make:migration create_market_feedback_form_rules_table --create=market_feedback_form_rules` <br>
`docker exec mark-php-app php artisan make:migration create_market_feedback_submissions_table --create=market_feedback_submissions` <br>
`docker exec mark-php-app php artisan make:migration create_market_feedback_submission_fields_table --create=market_feedback_submission_fields` <br>
`docker exec mark-php-app php artisan make:migration create_market_feedback_spam_blocks_table --create=market_feedback_spam_blocks` <br>
`docker exec mark-php-app php artisan make:migration create_market_feedback_submission_events_table --create=market_feedback_submission_events` <br>

23) Конструктор опросов / Опции / Результаты
`docker exec mark-php-app php artisan make:migration create_market_poll_surveys_table --create=market_poll_surveys` <br>
`docker exec mark-php-app php artisan make:migration create_market_poll_questions_table --create=market_poll_questions` <br>
`docker exec mark-php-app php artisan make:migration create_market_poll_options_table --create=market_poll_options` <br>
`docker exec mark-php-app php artisan make:migration create_market_poll_votes_table --create=market_poll_votes` <br>
`docker exec mark-php-app php artisan make:migration create_market_poll_vote_items_table --create=market_poll_vote_items` <br>
`docker exec mark-php-app php artisan make:migration create_market_poll_results_cache_table --create=market_poll_results_cache` <br>

24) Категории вакансий / Вакансии / Отклики
`docker exec mark-php-app php artisan make:migration create_market_job_categories_table --create=market_job_categories` <br>
`docker exec mark-php-app php artisan make:migration create_market_jobs_table --create=market_jobs` <br>
`docker exec mark-php-app php artisan make:migration create_market_job_applications_table --create=market_job_applications` <br>
`docker exec mark-php-app php artisan make:migration create_market_job_application_attachments_table --create=market_job_application_attachments` <br>
`docker exec mark-php-app php artisan make:migration create_market_job_application_messages_table --create=market_job_application_messages` <br>
`docker exec mark-php-app php artisan make:migration create_market_job_application_message_media_table --create=market_job_application_message_media` <br>

25) Каталог групп / Группы
`docker exec mark-php-app php artisan make:migration create_market_catalog_groups_table --create=market_catalog_groups` <br>
`docker exec mark-php-app php artisan make:model Admin/Market/CatalogGroupItem -fs` <br>
`docker exec mark-php-app php artisan make:migration create_market_catalog_group_items_table --create=market_catalog_group_items` <br>
-------------------------------------------------------------------------------------
