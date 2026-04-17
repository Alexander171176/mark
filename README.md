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

1) creating business logic app Rubric <br>
`docker exec mark-php-app php artisan make:model Admin/Blog/Rubric/Rubric -mf` <br>
`docker exec mark-php-app php artisan make:model Admin/Blog/Rubric/RubricImage -mf` <br>
`docker exec mark-php-app php artisan make:migration create_rubric_has_images_table --create=rubric_has_images` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan make:seeder RubricSeeder` <br>
`docker exec mark-php-app php artisan db:seed --class=RubricSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/Rubric/RubricResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/Rubric/RubricImageResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Blog/Rubric/RubricRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Blog/UpdateActivityRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Blog/UpdateSortEntityRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Blog/Rubric/Rubric/RubricController --resource` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/Blog/RubricController` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/Rubric/RubricSharedResource` <br>

2) creating business logic app Article <br>
`docker exec mark-php-app php artisan make:model Admin/Blog/Article/Article -mf` <br>
`docker exec mark-php-app php artisan make:model Admin/Blog/Article/ArticleImage -mf` <br>
`docker exec mark-php-app php artisan make:migration create_article_has_images_table --create=article_has_images` <br>
`docker exec mark-php-app php artisan make:migration create_article_has_rubric_table --create=article_has_rubric` <br>
`docker exec mark-php-app php artisan make:migration create_article_related_table --create=article_related` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan make:seeder ArticleSeeder` <br>
`docker exec mark-php-app php artisan db:seed --class=ArticleSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/Article/ArticleResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/Article/ArticleImageResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Blog/Article/ArticleRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Blog/UpdateLeftRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Blog/UpdateMainRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Blog/UpdateRightRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Blog/Article/ArticleController --resource` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/Blog/ArticleController` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/Article/ArticleSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/Invokable/RemoveArticleFromSectionController --invokable` <br>

3) creating business logic app Tag <br>
`docker exec mark-php-app php artisan make:model Admin/Blog/Tag/Tag -mf` <br>
`docker exec mark-php-app php artisan make:migration create_article_has_tag_table --create=article_has_tag` <br>
`docker exec mark-php-app php artisan make:migration add_icon_to_tags_table` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan make:seeder TagSeeder` <br>
`docker exec mark-php-app php artisan db:seed --class=TagSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/Tag/TagResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/Tag/TagSharedResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Blog/Tag/TagRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Blog/Tag/TagController --resource` <br>
`docker exec mark-php-app php artisan make:controller Admin/Invokable/RemoveArticleFromTagController --invokable` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/Blog/TagController` <br>
`docker exec mark-php-app php artisan make:controller Admin/Invokable/RemoveTagFromArticleController --invokable` <br>

4) creating business logic ap Banner
`docker exec mark-php-app php artisan make:model Admin/Blog/Banner/Banner -mf` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/Banner/BannerResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Blog/Banner/BannerRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Blog/Banner/BannerController --resource` <br>
`docker exec mark-php-app php artisan make:controller Admin/Blog/Invokable/RemoveBannerFromSectionController --invokable` <br>
`docker exec mark-php-app php artisan make:model Admin/Blog/Banner/BannerImage -mf` <br>
`docker exec mark-php-app php artisan make:migration create_banner_has_images_table --create=banner_has_images` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/Banner/BannerImageResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/Banner/BannerSharedResource` <br>
`docker exec mark-php-app php artisan migrate` <br>

5) creating business logic ap Video
`docker exec mark-php-app php artisan make:model Admin/Blog/Video/Video -mf` <br>
`docker exec mark-php-app php artisan make:migration add_is_private_to_videos_table` <br>
`docker exec mark-php-app php artisan make:model Admin/Blog/Video/VideoImage -mf` <br>
`docker exec mark-php-app php artisan make:migration create_video_has_images_table --create=video_has_images` <br>
`docker exec mark-php-app php artisan make:migration create_article_has_video_table --create=article_has_video` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/Video/VideoResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/Video/VideoSharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Blog/Video/VideoImageResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Blog/Video/VideoRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Blog/Video/VideoController --resource` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/Blog/VideoController` <br>
`docker exec mark-php-app php artisan make:migration create_video_related_table --create=video_related` <br>
`docker exec mark-php-app php artisan make:migration create_video_likes_table --create=video_likes` <br>
`docker exec mark-php-app php artisan make:model User/Like/VideoLike` <br>
`docker exec mark-php-app php artisan migrate` <br>
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

2) creating business logic app Like <br>
`docker exec mark-php-app php artisan make:migration create_article_likes_table --create=article_likes` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan make:model User/Like/ArticleLike` <br>
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
`docker exec mark-php-app php artisan make:controller Api/User/ApiUserController --api` <br>
`docker exec mark-php-app php artisan make:controller Api/Permission/ApiPermissionController --api` <br>
`docker exec mark-php-app php artisan make:controller Api/Role/ApiRoleController --api` <br>
`docker exec mark-php-app php artisan make:controller Api/Rubric/ApiRubricController --api` <br>
`docker exec mark-php-app php artisan make:controller Api/Article/ApiArticleController --api` <br>
`docker exec mark-php-app php artisan make:controller Api/Parameter/ApiParameterController --api` <br>
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

1) Home page - Section Hero
`docker exec mark-php-app php artisan make:model Admin/Constructor/HomePage/Hero/HeroSection -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Constructor/HomePage/Hero/HeroIcon -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Constructor/HomePage/Hero/HeroScreenshot -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=HeroSectionSeeder` <br>
`docker exec mark-php-app php artisan db:seed --class=HeroIconSeeder` <br>
`docker exec mark-php-app php artisan db:seed --class=HeroScreenshotSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/HomePage/Hero/HeroSectionResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/HomePage/Hero/HeroIconResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/HomePage/Hero/HeroScreenshotResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/HomePage/Hero/HeroSectionRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/HomePage/Hero/HeroIconRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/HomePage/Hero/HeroScreenshotRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/HomePage/HomePageController` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/HomePage/Hero/HeroSectionController` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/HomePage/Hero/HeroIconController` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/HomePage/Hero/HeroScreenshotController` <br>

2) Home page - Section Wave
`docker exec mark-php-app php artisan make:model Admin/Constructor/HomePage/Wave/WaveSection -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Constructor/HomePage/Wave/WaveTech -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=WaveSectionSeeder` <br>
`docker exec mark-php-app php artisan db:seed --class=WaveTechSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/HomePage/Wave/WaveSectionResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/HomePage/Wave/WaveTechResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/HomePage/Wave/WaveSectionRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/HomePage/Wave/WaveTechRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/HomePage/Wave/WaveSectionController` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/HomePage/Wave/WaveTechController` <br>

3) Home page - Section Feature
`docker exec mark-php-app php artisan make:model Admin/Constructor/HomePage/Feature/FeatureSection -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Constructor/HomePage/Feature/FeatureItem -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=FeatureSectionSeeder` <br>
`docker exec mark-php-app php artisan db:seed --class=FeatureItemSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/HomePage/Feature/FeatureSectionResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/HomePage/Feature/FeatureItemResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/HomePage/Feature/FeatureSectionRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/HomePage/Feature/FeatureItemRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/HomePage/Feature/FeatureSectionController` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/HomePage/Feature/FeatureItemController` <br>

4) Home page - Section Developer
`docker exec mark-php-app php artisan make:model Admin/Constructor/HomePage/Developer/DeveloperSection -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Constructor/HomePage/Developer/DeveloperItem -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=DeveloperSectionSeeder` <br>
`docker exec mark-php-app php artisan db:seed --class=DeveloperItemSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/HomePage/Developer/DeveloperSectionResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/HomePage/Developer/DeveloperItemResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/HomePage/Developer/DeveloperSectionRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/HomePage/Developer/DeveloperItemRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/HomePage/Developer/DeveloperSectionController` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/HomePage/Developer/DeveloperItemController` <br>

5) Home page - Section Quickstart
`docker exec mark-php-app php artisan make:model Admin/Constructor/HomePage/Quickstart/QuickstartSection -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=QuickstartSectionSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/HomePage/Quickstart/QuickstartSectionResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/HomePage/Quickstart/QuickstartSectionRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/HomePage/Quickstart/QuickstartSectionController` <br>

6) Home page - Section Demo
`docker exec mark-php-app php artisan make:model Admin/Constructor/HomePage/Demo/DemoSection -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Constructor/HomePage/Demo/DemoGroup -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Constructor/HomePage/Demo/DemoItem -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=DemoSectionSeeder` <br>
`docker exec mark-php-app php artisan db:seed --class=DemoGroupSeeder` <br>
`docker exec mark-php-app php artisan db:seed --class=DemoItemSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/HomePage/Demo/DemoSectionResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/HomePage/Demo/DemoGroupResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/HomePage/Demo/DemoItemResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/HomePage/Demo/DemoSectionRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/HomePage/Demo/DemoGroupRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/HomePage/Demo/DemoItemRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/HomePage/Demo/DemoSectionController` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/HomePage/Demo/DemoGroupController` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/HomePage/Demo/DemoItemController` <br>

7) Home page - Section Quality
`docker exec mark-php-app php artisan make:model Admin/Constructor/HomePage/Quality/QualitySection -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Constructor/HomePage/Quality/QualityItem -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=QualitySectionSeeder` <br>
`docker exec mark-php-app php artisan db:seed --class=QualityItemSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/HomePage/Quality/QualitySectionResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/HomePage/Quality/QualityItemResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/HomePage/Quality/QualitySectionRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/HomePage/Quality/QualityItemRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/HomePage/Quality/QualitySectionController` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/HomePage/Quality/QualityItemController` <br>

8) Home page - Section Component
`docker exec mark-php-app php artisan make:model Admin/Constructor/HomePage/Component/ComponentSection -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Constructor/HomePage/Component/ComponentFeature -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Constructor/HomePage/Component/ComponentTab -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Constructor/HomePage/Component/ComponentTile -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=ComponentSectionSeeder` <br>
`docker exec mark-php-app php artisan db:seed --class=ComponentFeatureSeeder` <br>
`docker exec mark-php-app php artisan db:seed --class=ComponentTabSeeder` <br>
`docker exec mark-php-app php artisan db:seed --class=ComponentTileSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/HomePage/Component/ComponentSectionResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/HomePage/Component/ComponentFeatureResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/HomePage/Component/ComponentTabResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/HomePage/Component/ComponentTileResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/HomePage/Component/ComponentSectionRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/HomePage/Component/ComponentFeatureRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/HomePage/Component/ComponentTabRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/HomePage/Component/ComponentTileRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/HomePage/Component/ComponentSectionController` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/HomePage/Component/ComponentFeatureController` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/HomePage/Component/ComponentTabController` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/HomePage/Component/ComponentTileController` <br>

9) Home page - Section Reason
`docker exec mark-php-app php artisan make:model Admin/Constructor/HomePage/Reason/ReasonSection -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Constructor/HomePage/Reason/ReasonItem -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=ReasonSectionSeeder` <br>
`docker exec mark-php-app php artisan db:seed --class=ReasonItemSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/HomePage/Reason/ReasonSectionResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/HomePage/Reason/ReasonItemResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/HomePage/Reason/ReasonSectionRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/HomePage/Reason/ReasonItemRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/HomePage/Reason/ReasonSectionController` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/HomePage/Reason/ReasonItemController` <br>
-------------------------------------------------------------------------------------

1) creating business logic Currency - мультивалютность, курсы валют
`docker exec mark-php-app php artisan make:model Admin/Finance/Currency/Currency -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Finance/Currency/CurrencyRate -mfs` <br>
`docker exec mark-php-app php artisan make:seeder DefaultCurrenciesSeeder` <br>
`docker exec mark-php-app php artisan migrate` <br>
`docker exec mark-php-app php artisan db:seed --class=DefaultCurrenciesSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Finance/Currency/CurrencyResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Finance/Currency/CurrencySelectResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Finance/Currency/CurrencyRateResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Finance/Currency/CurrencyRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Finance/Currency/DefaultCurrencyRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Finance/Currency/CurrencyActivityRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Finance/Currency/CurrencyRateRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Finance/Currency/BulkCurrencyRatesRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Finance/Currency/FetchRatesFromProviderRequest` <br>
`docker exec mark-php-app php artisan make:request Admin/Finance/Currency/CurrencyInlineRateRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Finance/Currency/CurrencyController --resource` <br>
`docker exec mark-php-app php artisan make:controller Admin/Finance/Currency/CurrencyRateController --resource` <br>
-------------------------------------------------------------------------------------

1) creating business logic Hashtag - полиморфные хештеги
`docker exec mark-php-app php artisan make:model Admin/School/Hashtag/Hashtag -mfs` <br>
`docker exec mark-php-app php artisan make:migration create_hashtagables_table`<br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=HashtagSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/Hashtag/HashtagResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/Hashtag/HashtagSharedResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/Hashtag/HashtagRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/Hashtag/HashtagController --resource` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/School/HashtagController` <br>
-------------------------------------------------------------------------------------

1) creating business logic InstructorProfile - преподаватели
`docker exec mark-php-app php artisan make:model Admin/School/InstructorProfile/InstructorProfile -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/InstructorProfile/InstructorProfileImage -mf` <br>
`docker exec mark-php-app php artisan make:migration create_instructor_profile_has_images_table --create=instructor_profile_has_images` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=InstructorProfileSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/InstructorProfile/InstructorProfileResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/InstructorProfile/InstructorProfileImageResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/InstructorProfile/InstructorProfileRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/InstructorProfile/InstructorProfileController --resource` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/School/InstructorController` <br>

2) creating business logic LearningCategory - категории курсов обучения
`docker exec mark-php-app php artisan make:model Admin/School/LearningCategory/LearningCategory -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/LearningCategory/LearningCategoryImage -mf` <br>
`docker exec mark-php-app php artisan make:migration create_learning_category_has_images_table --create=learning_category_has_images` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=LearningCategorySeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/LearningCategory/LearningCategoryResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/LearningCategory/LearningCategorySharedResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/LearningCategory/LearningCategoryImageResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/LearningCategory/LearningCategoryRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/LearningCategory/LearningCategoryController --resource` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/School/TrackController` <br>
`docker exec mark-php-app php artisan make:migration create_track_likes_table --create=track_likes` <br>
`docker exec mark-php-app php artisan make:model User/Like/TrackLike` <br>

3) creating business logic Course - курсы обучения
`docker exec mark-php-app php artisan make:model Admin/School/Course/Course -mfs` <br>
`docker exec mark-php-app php artisan make:migration create_course_related_table --create=course_related` <br>
`docker exec mark-php-app php artisan make:migration create_course_has_learning_category_table` <br>
`docker exec mark-php-app php artisan make:migration create_course_has_learning_tag_table` <br>
`docker exec mark-php-app php artisan make:model Admin/School/Course/CourseImage -mf` <br>
`docker exec mark-php-app php artisan make:migration create_course_has_images_table --create=course_has_images` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=CourseSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/Course/CourseResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/Course/CourseImageResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/Course/CourseRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/Course/CourseController --resource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/Course/CourseSharedResource` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/School/CourseController` <br>
`docker exec mark-php-app php artisan make:migration create_course_likes_table --create=course_likes` <br>
`docker exec mark-php-app php artisan make:model User/Like/CourseLike` <br>

4) creating business logic Module - модули, подразделы курсов
`docker exec mark-php-app php artisan make:model Admin/School/Module/Module -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/Module/ModuleImage -mf` <br>
`docker exec mark-php-app php artisan make:migration create_module_has_images_table --create=module_has_images` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=ModuleSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/Module/ModuleResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/Module/ModuleImageResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/Module/ModuleRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/Module/ModuleController --resource` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/School/ModuleController` <br>
`docker exec mark-php-app php artisan make:migration create_module_likes_table --create=module_likes` <br>
`docker exec mark-php-app php artisan make:model User/Like/ModuleLike` <br>

5) creating business logic Lesson - уроки курсов обучения
`docker exec mark-php-app php artisan make:model Admin/School/Lesson/Lesson -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/Lesson/LessonImage -mf` <br>
`docker exec mark-php-app php artisan make:migration create_lesson_has_images_table --create=lesson_has_images` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=LessonSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/Lesson/LessonResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/Lesson/LessonImageResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/Lesson/LessonRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/Lesson/LessonController --resource` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/School/LessonController` <br>
`docker exec mark-php-app php artisan make:migration create_lesson_likes_table --create=lesson_likes` <br>
`docker exec mark-php-app php artisan make:model User/Like/LessonLike` <br>

6) creating business logic Assignment - домашнее задания/практика
`docker exec mark-php-app php artisan make:model Admin/School/Assignment/Assignment -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/Assignment/AssignmentImage -mf` <br>
`docker exec mark-php-app php artisan make:migration create_assignment_has_images_table --create=assignment_has_images` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=AssignmentSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/Assignment/AssignmentResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/Assignment/AssignmentImageResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/Assignment/AssignmentRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/Assignment/AssignmentController --resource` <br>
`docker exec mark-php-app php artisan make:controller Public/Default/School/AssignmentController` <br>

7) creating business logic CourseSchedule - расписание потоков
`docker exec mark-php-app php artisan make:model Admin/School/CourseSchedule/CourseSchedule -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/CourseSchedule/CourseScheduleImage -mf` <br>
`docker exec mark-php-app php artisan make:migration create_course_schedule_has_images_table --create=course_schedule_has_images` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=CourseScheduleSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/CourseSchedule/CourseScheduleResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/CourseSchedule/CourseScheduleImageResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/CourseSchedule/CourseScheduleRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/CourseSchedule/CourseScheduleController --resource` <br>

8) creating business logic CohortEnrollment - запись на курсы
`docker exec mark-php-app php artisan make:model Admin/School/CohortEnrollment/CohortEnrollment -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=CohortEnrollmentSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/CohortEnrollment/CohortEnrollmentResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/CohortEnrollment/CohortEnrollmentRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/CohortEnrollment/CohortEnrollmentController --resource` <br>

9) creating business logic Enrollment - зачисление студентов на потоки
`docker exec mark-php-app php artisan make:model Admin/School/Enrollment/Enrollment -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=EnrollmentSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/Enrollment/EnrollmentResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/Enrollment/EnrollmentRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/Enrollment/EnrollmentController --resource` <br>

10) creating business logic Quiz - вопросники / викторины
`docker exec mark-php-app php artisan make:model Admin/School/Quiz/Quiz -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/School/Quiz/QuizImage -mf` <br>
`docker exec mark-php-app php artisan make:migration create_quiz_has_images_table --create=quiz_has_images` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=QuizSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/Quiz/QuizResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/Quiz/QuizImageResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/Quiz/QuizRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/Quiz/QuizController --resource` <br>

11) creating business logic QuizQuestion - вопросы для викторин
`docker exec mark-php-app php artisan make:model Admin/School/QuizQuestion/QuizQuestion -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=QuizQuestionSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/QuizQuestion/QuizQuestionResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/QuizQuestion/QuizQuestionRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/QuizQuestion/QuizQuestionController --resource` <br>

12) creating business logic QuizAnswer - ответы на вопросы викторин
`docker exec mark-php-app php artisan make:model Admin/School/QuizAnswer/QuizAnswer -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=QuizAnswerSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/QuizAnswer/QuizAnswerResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/QuizAnswer/QuizAnswerRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/QuizAnswer/QuizAnswerController --resource` <br>

13) creating business logic QuizAttempt - попытка прохождения квиза
`docker exec mark-php-app php artisan make:model Admin/School/QuizAttempt/QuizAttempt -mf` <br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan make:resource Admin/School/QuizAttempt/QuizAttemptResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/QuizAttempt/QuizAttemptRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/QuizAttempt/QuizAttemptController --resource` <br>

14) creating business logic QuizAttemptItem - ответ на один конкретный вопрос в рамках этой попытки
`docker exec mark-php-app php artisan make:model Admin/School/QuizAttemptItem/QuizAttemptItem -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=QuizAttemptItemSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/QuizAttemptItem/QuizAttemptItemResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/QuizAttemptItem/QuizAttemptItemRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/QuizAttemptItem/QuizAttemptItemController --resource` <br>

15) creating business logic Bundle - Набор, объединение нескольких курсов под одним предложением
`docker exec mark-php-app php artisan make:model Admin/School/Bundle/Bundle -mfs` <br>
`docker exec mark-php-app php artisan make:migration create_bundle_has_course_table` <br>
`docker exec mark-php-app php artisan make:model Admin/School/Bundle/BundleImage -mf` <br>
`docker exec mark-php-app php artisan make:migration create_bundle_has_images_table --create=bundle_has_images` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=BundleSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/Bundle/BundleResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/Bundle/BundleImageResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/Bundle/BundleRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/Bundle/BundleController --resource` <br>

16) creating business logic CoursePrice - прайсы Курса
`docker exec mark-php-app php artisan make:model Admin/Finance/CoursePrice/CoursePrice -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=CoursePriceSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Finance/CoursePrice/CoursePriceResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Finance/CoursePrice/CoursePriceRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Finance/CoursePrice/CoursePriceController --resource` <br>

17) creating business logic BundlePrice - прайсы набора Курсов
`docker exec mark-php-app php artisan make:model Admin/Finance/BundlePrice/BundlePrice -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=BundlePriceSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Finance/BundlePrice/BundlePriceResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Finance/BundlePrice/BundlePriceRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Finance/BundlePrice/BundlePriceController --resource` <br>

18) creating business logic SubscriptionPlan - тарифные планы
`docker exec mark-php-app php artisan make:model Admin/Finance/SubscriptionPlan/SubscriptionPlan -mfs` <br>
`docker exec mark-php-app php artisan make:model Admin/Finance/SubscriptionPlan/SubscriptionPlanImage -mf` <br>
`docker exec mark-php-app php artisan make:migration create_sp_has_images_table --create=subscription_plan_has_images` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=SubscriptionPlanSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Finance/SubscriptionPlan/SubscriptionPlanResource` <br>
`docker exec mark-php-app php artisan make:resource Admin/Finance/SubscriptionPlan/SubscriptionPlanImageResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Finance/SubscriptionPlan/SubscriptionPlanRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Finance/SubscriptionPlan/SubscriptionPlanController --resource` <br>

19) creating business logic PaymentMethod
`docker exec mark-php-app php artisan make:model Admin/Finance/PaymentMethod/PaymentMethod -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=PaymentMethodSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Finance/PaymentMethod/PaymentMethodResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Finance/PaymentMethod/PaymentMethodRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Finance/PaymentMethod/PaymentMethodController --resource` <br>

20) creating business logic UserPaymentMethod
`docker exec mark-php-app php artisan make:model Admin/Finance/UserPaymentMethod/UserPaymentMethod -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=UserPaymentMethodSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Finance/UserPaymentMethod/UserPaymentMethodResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Finance/UserPaymentMethod/UserPaymentMethodRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Finance/UserPaymentMethod/UserPaymentMethodController --resource` <br>

21) creating business logic Payment
`docker exec mark-php-app php artisan make:model Admin/Finance/Payment/Payment -mf` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan make:resource Admin/Finance/Payment/PaymentResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Finance/Payment/PaymentRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Finance/Payment/PaymentController --resource` <br>

22) creating business logic Subscription
`docker exec mark-php-app php artisan make:model Admin/Finance/Subscription/Subscription -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=SubscriptionSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Finance/Subscription/SubscriptionResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Finance/Subscription/SubscriptionRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Finance/Subscription/SubscriptionController --resource` <br>

23) creating business logic Refund
`docker exec mark-php-app php artisan make:model Admin/Finance/Refund/Refund -mf` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan make:resource Admin/Finance/Refund/RefundResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Finance/Refund/RefundRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Finance/Refund/RefundController --resource` <br>

24) creating business logic Coupon
`docker exec mark-php-app php artisan make:model Admin/School/Coupon/Coupon -mfs` <br>
`docker exec mark-php-app php artisan make:migration create_coupon_has_course_table` <br>
`docker exec mark-php-app php artisan make:migration create_coupon_has_bundle_table` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=CouponSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/Coupon/CouponResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/Coupon/CouponRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/Coupon/CouponController --resource` <br>

25) creating business logic Invoice
`docker exec mark-php-app php artisan make:model Admin/Finance/Invoice/Invoice -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=InvoiceSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Finance/Invoice/InvoiceResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Finance/Invoice/InvoiceRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Finance/Invoice/InvoiceController --resource` <br>

26) creating business logic ProviderAccount
`docker exec mark-php-app php artisan make:model Admin/Finance/ProviderAccount/ProviderAccount -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=ProviderAccountSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Finance/ProviderAccount/ProviderAccountResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Finance/ProviderAccount/ProviderAccountRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Finance/ProviderAccount/ProviderAccountController --resource` <br>

27) creating business logic Payout
`docker exec mark-php-app php artisan make:model Admin/Finance/Payout/Payout -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=PayoutSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Finance/Payout/PayoutResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Finance/Payout/PayoutRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Finance/Payout/PayoutController --resource` <br>

28) creating business logic PayoutItem
`docker exec mark-php-app php artisan make:model Admin/Finance/PayoutItem/PayoutItem -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=PayoutItemSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Finance/PayoutItem/PayoutItemResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Finance/PayoutItem/PayoutItemRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Finance/PayoutItem/PayoutItemController --resource` <br>

29) creating business logic ExchangeRate
`docker exec mark-php-app php artisan make:model Admin/Finance/ExchangeRate/ExchangeRate -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=ExchangeRateSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Finance/ExchangeRate/ExchangeRateResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Finance/ExchangeRate/ExchangeRateRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Finance/ExchangeRate/ExchangeRateController --resource` <br>

30) creating business logic WebhookEvent
`docker exec mark-php-app php artisan make:model Admin/Finance/WebhookEvent/WebhookEvent -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=WebhookEventSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Finance/WebhookEvent/WebhookEventResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Finance/WebhookEvent/WebhookEventRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Finance/WebhookEvent/WebhookEventController --resource` <br>

31) creating business logic AssignmentSubmission
`docker exec mark-php-app php artisan make:model Admin/School/AssignmentSubmission/AssignmentSubmission -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=AssignmentSubmissionSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/AssignmentSubmission/AssignmentSubmissionResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/AssignmentSubmission/AssignmentSubmissionRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/AssignmentSubmission/AssignmentSubmissionController --resource` <br>

32) creating business logic ProgressRecord
`docker exec mark-php-app php artisan make:model Admin/School/ProgressRecord/ProgressRecord -mf` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan make:resource Admin/School/ProgressRecord/ProgressRecordResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/ProgressRecord/ProgressRecordRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/ProgressRecord/ProgressRecordController --resource` <br>

33) creating business logic Review
`docker exec mark-php-app php artisan make:model Admin/School/Review/Review -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=ReviewSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/Review/ReviewResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/Review/ReviewRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/Review/ReviewController --resource` <br>

34) creating business logic QaThread
`docker exec mark-php-app php artisan make:model Admin/School/QaThread/QaThread -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=QaThreadSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/QaThread/QaThreadResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/QaThread/QaThreadRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/QaThread/QaThreadController --resource` <br>

35) creating business logic QaMessage
`docker exec mark-php-app php artisan make:model Admin/School/QaMessage/QaMessage -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=QaMessageSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/QaMessage/QaMessageResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/QaMessage/QaMessageRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/QaMessage/QaMessageController --resource` <br>

36) creating business logic Bookmark
`docker exec mark-php-app php artisan make:model Admin/School/Bookmark/Bookmark -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=BookmarkSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/Bookmark/BookmarkResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/Bookmark/BookmarkRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/Bookmark/BookmarkController --resource` <br>

37) creating business logic Certificate
`docker exec mark-php-app php artisan make:model Admin/School/Certificate/Certificate -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=CertificateSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/Certificate/CertificateResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/Certificate/CertificateRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/Certificate/CertificateController --resource` <br>

38) creating business logic Page (CMS)
`docker exec mark-php-app php artisan make:model Admin/Constructor/Page/Page -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=PageSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/Page/PageResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/Page/PageRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/Page/PageController --resource` <br>

39) creating business logic BlogPost (CMS)
`docker exec mark-php-app php artisan make:model Admin/Constructor/Cms/BlogPost -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=BlogPostSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/Cms/BlogPostResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/Cms/BlogPostRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/Cms/BlogPostController --resource` <br>

40) creating business logic SeoMeta (CMS)
`docker exec mark-php-app php artisan make:model Admin/Constructor/Cms/SeoMeta -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=SeoMetaSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/Cms/SeoMetaResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/Cms/SeoMetaRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/Cms/SeoMetaController --resource` <br>

41) creating business logic Lead (CMS)
`docker exec mark-php-app php artisan make:model Admin/Crm/Lead/Lead -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=LeadSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Crm/Lead/LeadResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Crm/Lead/LeadRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Crm/Lead/LeadController --resource` <br>

42) creating business logic EmailSubscription (CMS)
`docker exec mark-php-app php artisan make:model Admin/Crm/EmailSubscription/EmailSubscription -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=EmailSubscriptionSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Crm/EmailSubscription/EmailSubscriptionResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Crm/EmailSubscription/EmailSubscriptionRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Crm/EmailSubscription/EmailSubscriptionController --resource` <br>

43) creating business logic NavigationMenu (CMS)
`docker exec mark-php-app php artisan make:model Admin/Constructor/NavigationMenu/NavigationMenu -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=NavigationMenuSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/NavigationMenu/NavigationMenuResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/NavigationMenu/NavigationMenuRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/NavigationMenu/NavigationMenuController --resource` <br>

44) creating business logic NavigationItem (CMS)
`docker exec mark-php-app php artisan make:model Admin/Constructor/NavigationItem/NavigationItem -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=NavigationItemSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/NavigationItem/NavigationItemResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/NavigationItem/NavigationItemRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/NavigationItem/NavigationItemController --resource` <br>

45) creating business logic CmsBanner (CMS)
`docker exec mark-php-app php artisan make:model Admin/Constructor/Cms/CmsBanner -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=CmsBannerSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/Cms/CmsBannerResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/Cms/CmsBannerRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/Cms/CmsBannerController --resource` <br>

46) creating business logic Redirect (CMS)
`docker exec mark-php-app php artisan make:model Admin/System/Redirect/Redirect -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=RedirectSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/System/Redirect/RedirectResource` <br>
`docker exec mark-php-app php artisan make:request Admin/System/Redirect/RedirectRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/System/Redirect/RedirectController --resource` <br>

47) creating business logic FormSubmission (CMS)
`docker exec mark-php-app php artisan make:model Admin/Crm/FormSubmission/FormSubmission -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=FormSubmissionSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Crm/FormSubmission/FormSubmissionResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Crm/FormSubmission/FormSubmissionRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Crm/FormSubmission/FormSubmissionController --resource` <br>

48) creating business logic Faq (CMS)
`docker exec mark-php-app php artisan make:model Admin/Constructor/Faq/Faq -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=FaqSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/Constructor/Faq/FaqResource` <br>
`docker exec mark-php-app php artisan make:request Admin/Constructor/Faq/FaqRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/Constructor/Faq/FaqController --resource` <br>

49) creating business logic Testimonial (CMS)
`docker exec mark-php-app php artisan make:model Admin/School/Testimonial/Testimonial -mfs` <br>
`docker exec mark-php-app php artisan migrate`<br>
`docker exec mark-php-app php artisan migrate:rollback`<br>
`docker exec mark-php-app php artisan db:seed --class=TestimonialSeeder` <br>
`docker exec mark-php-app php artisan make:resource Admin/School/Testimonial/TestimonialResource` <br>
`docker exec mark-php-app php artisan make:request Admin/School/Testimonial/TestimonialRequest` <br>
`docker exec mark-php-app php artisan make:controller Admin/School/Testimonial/TestimonialController --resource` <br>
-------------------------------------------------------------------------------------

1) Компания / Витрина / Настройки
`docker exec mark-php-app php artisan make:migration create_market_companies_table --create=market_companies` <br>
`docker exec mark-php-app php artisan make:model Admin/Market/MarketCompany/MarketCompany -fs` <br>
`docker exec mark-php-app php artisan make:request Admin/Market/MarketCompany/MarketCompanyRequest` <br>
`docker exec mark-php-app php artisan make:resource Admin/Market/MarketCompany/MarketCompanyResource` <br>
`docker exec mark-php-app php artisan make:controller Admin/Market/MarketCompany/MarketCompanyController --resource` <br>
`docker exec mark-php-app php artisan make:migration create_market_storefronts_table --create=market_storefronts` <br>
`docker exec mark-php-app php artisan make:migration create_market_storefront_settings_table --create=market_storefront_settings` <br>
`docker exec mark-php-app php artisan make:migration create_market_storefront_locale_settings_table --create=market_storefront_locale_settings` <br>

2) Валюта, локаль витрины
`docker exec mark-php-app php artisan make:migration create_market_storefront_has_currencies_table --create=market_storefront_has_currencies` <br>
`docker exec mark-php-app php artisan make:migration create_market_storefront_locale_currency_settings_table --create=market_storefront_locale_currency_settings` <br>

3) Категории (дерево)
`docker exec mark-php-app php artisan make:migration create_market_categories_table --create=market_categories` <br>

4) Товары / Бренды
`docker exec mark-php-app php artisan make:migration create_market_products_table --create=market_products` <br>
`docker exec mark-php-app php artisan make:migration create_market_category_has_products_table --create=market_category_has_products` <br>
`docker exec mark-php-app php artisan make:migration create_market_brands_table --create=market_brands` <br>
`docker exec mark-php-app php artisan make:migration create_market_brand_has_products_table --create=market_brand_has_products` <br>

5) Варианты товаров / Рекомендованные товары / Комплекты товаров 
`docker exec mark-php-app php artisan make:migration create_market_product_variants_table --create=market_product_variants` <br>
`docker exec mark-php-app php artisan make:migration create_market_product_recommendations_table --create=market_product_recommendations` <br>
`docker exec mark-php-app php artisan make:migration create_market_kits_table --create=market_kits` <br>
`docker exec mark-php-app php artisan make:migration create_market_kit_items_table --create=market_kit_items` <br>

6) Группы характеристик / Характеристики / Значения характеристик
`docker exec mark-php-app php artisan make:migration create_market_property_groups_table --create=market_property_groups` <br>
`docker exec mark-php-app php artisan make:migration create_market_properties_table --create=market_properties` <br>
`docker exec mark-php-app php artisan make:migration create_market_property_values_table --create=market_property_values` <br>
`docker exec mark-php-app php artisan make:migration create_market_category_has_properties_table --create=market_category_has_properties` <br>
`docker exec mark-php-app php artisan make:migration create_market_product_variant_has_property_values_table --create=market_product_variant_has_property_values` <br>

7) Склады / остатки 
`docker exec mark-php-app php artisan make:migration create_market_warehouses_table --create=market_warehouses` <br>
`docker exec mark-php-app php artisan make:migration create_market_warehouse_stocks_table --create=market_warehouse_stocks` <br>

8) Модуль доставки / Логистика / ПВЗ / Зоны
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

9) Корзина / События / Избранное
`docker exec mark-php-app php artisan make:model Admin/Market/Cart/Cart -fs` <br>
`docker exec mark-php-app php artisan make:migration create_market_carts_table --create=market_carts` <br>
`docker exec mark-php-app php artisan make:migration create_market_cart_sessions_table --create=market_cart_sessions` <br>
`docker exec mark-php-app php artisan make:model Admin/Market/CartItem/CartItem -fs` <br>
`docker exec mark-php-app php artisan make:migration create_market_cart_items_table --create=market_cart_items` <br>
`docker exec mark-php-app php artisan make:model Admin/Market/CartEvent/CartEvent -fs` <br>
`docker exec mark-php-app php artisan make:migration create_market_cart_events_table --create=market_cart_events` <br>
`docker exec mark-php-app php artisan make:migration create_market_cart_event_items_table --create=market_cart_event_items` <br>
`docker exec mark-php-app php artisan make:migration create_market_favorites_table --create=market_favorites` <br>

10) Бонусы / Программа бонусов
`docker exec mark-php-app php artisan make:migration create_market_bonus_programs_table --create=market_bonus_programs` <br>
`docker exec mark-php-app php artisan make:migration create_market_bonus_accounts_table --create=market_bonus_accounts` <br>
`docker exec mark-php-app php artisan make:migration create_market_bonus_operations_table --create=market_bonus_operations` <br>
`docker exec mark-php-app php artisan make:migration create_market_bonus_expirations_table --create=market_bonus_expirations` <br>
`docker exec mark-php-app php artisan make:migration create_market_bonus_spend_allocations_table --create=market_bonus_spend_allocations` <br>
`docker exec mark-php-app php artisan make:migration create_market_bonus_rules_table --create=market_bonus_rules` <br>
`docker exec mark-php-app php artisan make:migration create_market_bonus_rule_conditions_table --create=market_bonus_rule_conditions` <br>
`docker exec mark-php-app php artisan make:migration create_market_bonus_rule_rewards_table --create=market_bonus_rule_rewards` <br>

11) Заказы / Статусы заказов / История заказов
`docker exec mark-php-app php artisan make:migration create_market_order_statuses_table --create=market_order_statuses` <br>
`docker exec mark-php-app php artisan make:migration create_market_orders_table --create=market_orders` <br>
`docker exec mark-php-app php artisan make:migration create_market_order_items_table --create=market_order_items` <br>
`docker exec mark-php-app php artisan make:migration create_market_order_histories_table --create=market_order_histories` <br>

12) Модуль оплаты / Провайдеры оплаты / Транзакции / История оплаты 
`docker exec mark-php-app php artisan make:migration create_market_payment_providers_table --create=market_payment_providers` <br>
`docker exec mark-php-app php artisan make:migration create_market_storefront_payment_provider_settings_table --create=market_storefront_payment_provider_settings` <br>
`docker exec mark-php-app php artisan make:migration create_market_payments_table --create=market_payments` <br>
`docker exec mark-php-app php artisan make:migration create_market_payment_transactions_table --create=market_payment_transactions` <br>
`docker exec mark-php-app php artisan make:migration create_market_payment_histories_table --create=market_payment_histories` <br>

13) Инвойсы / Фискальные чеки / Документы
`docker exec mark-php-app php artisan make:migration create_market_invoices_table --create=market_invoices` <br>
`docker exec mark-php-app php artisan make:migration create_market_invoice_items_table --create=market_invoice_items` <br>
`docker exec mark-php-app php artisan make:migration create_market_fiscal_receipts_table --create=market_fiscal_receipts` <br>
`docker exec mark-php-app php artisan make:migration create_market_fiscal_receipt_items_table --create=market_fiscal_receipt_items` <br>
`docker exec mark-php-app php artisan make:migration create_market_order_documents_table --create=market_order_documents` <br>

14) Возвраты / История возвратов / Документы
`docker exec mark-php-app php artisan make:migration create_market_refunds_table --create=market_refunds` <br>
`docker exec mark-php-app php artisan make:migration create_market_refund_items_table --create=market_refund_items` <br>
`docker exec mark-php-app php artisan make:migration create_market_refund_documents_table --create=market_refund_documents` <br>
`docker exec mark-php-app php artisan make:migration create_market_return_statuses_table --create=market_return_statuses` <br>
`docker exec mark-php-app php artisan make:migration create_market_returns_table --create=market_returns` <br>
`docker exec mark-php-app php artisan make:migration create_market_return_items_table --create=market_return_items` <br>
`docker exec mark-php-app php artisan make:migration create_market_return_histories_table --create=market_return_histories` <br>
`docker exec mark-php-app php artisan make:migration create_market_return_documents_table --create=market_return_documents` <br>

15) Промокоды
`docker exec mark-php-app php artisan make:migration create_market_promo_campaigns_table --create=market_promo_campaigns` <br>
`docker exec mark-php-app php artisan make:migration create_market_promo_code_batches_table --create=market_promo_code_batches` <br>
`docker exec mark-php-app php artisan make:migration create_market_promo_codes_table --create=market_promo_codes` <br>
`docker exec mark-php-app php artisan make:migration create_market_promo_redemptions_table --create=market_promo_redemptions` <br>

16) Реферальная система 
`docker exec mark-php-app php artisan make:migration create_market_referral_programs_table --create=market_referral_programs` <br>
`docker exec mark-php-app php artisan make:migration create_market_referral_codes_table --create=market_referral_codes` <br>
`docker exec mark-php-app php artisan make:migration create_market_referral_events_table --create=market_referral_events` <br>
`docker exec mark-php-app php artisan make:migration create_market_referral_rewards_table --create=market_referral_rewards` <br>

17) Отзывы / Бонусы
`docker exec mark-php-app php artisan make:migration create_market_review_bonus_rules_table --create=market_review_bonus_rules` <br>
`docker exec mark-php-app php artisan make:migration create_market_product_reviews_table --create=market_product_reviews` <br>
`docker exec mark-php-app php artisan make:migration create_market_review_bonus_awards_table --create=market_review_bonus_awards` <br>
`docker exec mark-php-app php artisan make:migration create_market_company_reviews_table --create=market_company_reviews` <br>

18) Промо-коды
`docker exec mark-php-app php artisan make:migration add_unique_to_market_promo_redemptions_table --create=market_promo_redemptions` <br>
`docker exec mark-php-app php artisan make:migration create_market_promo_campaign_has_products_table --create=market_promo_campaign_has_products` <br>
`docker exec mark-php-app php artisan make:migration create_market_promo_campaign_has_categories_table --create=market_promo_campaign_has_categories` <br>
`docker exec mark-php-app php artisan make:migration create_market_promo_campaign_has_brands_table --create=market_promo_campaign_has_brands` <br>
`docker exec mark-php-app php artisan make:migration create_market_promo_campaign_has_delivery_methods_table --create=market_promo_campaign_has_delivery_methods` <br>

19) Страницы (дерево) / FAQ, категории
`docker exec mark-php-app php artisan make:migration create_market_storefront_pages_table --create=market_storefront_pages` <br>
`docker exec mark-php-app php artisan make:migration create_market_faq_categories_table --create=market_faq_categories` <br>
`docker exec mark-php-app php artisan make:migration create_market_faq_items_table --create=market_faq_items` <br>

20) Тикетная система (вопросы/ответы) / История / Теги
`docker exec mark-php-app php artisan make:migration create_market_ticket_statuses_table --create=market_ticket_statuses` <br>
`docker exec mark-php-app php artisan make:migration create_market_tickets_table --create=market_tickets` <br>
`docker exec mark-php-app php artisan make:migration create_market_ticket_messages_table --create=market_ticket_messages` <br>
`docker exec mark-php-app php artisan make:migration create_market_ticket_attachments_table --create=market_ticket_attachments` <br>
`docker exec mark-php-app php artisan make:migration create_market_ticket_histories_table --create=market_ticket_histories` <br>
`docker exec mark-php-app php artisan make:migration create_market_ticket_tags_table --create=market_ticket_tags` <br>
`docker exec mark-php-app php artisan make:migration create_market_ticket_has_tags_table --create=market_ticket_has_tags` <br>

21) Конструктор форм обратной связи / Антиспам
`docker exec mark-php-app php artisan make:migration create_market_feedback_forms_table --create=market_feedback_forms` <br>
`docker exec mark-php-app php artisan make:migration create_market_feedback_form_fields_table --create=market_feedback_form_fields` <br>
`docker exec mark-php-app php artisan make:migration create_market_feedback_form_rules_table --create=market_feedback_form_rules` <br>
`docker exec mark-php-app php artisan make:migration create_market_feedback_submissions_table --create=market_feedback_submissions` <br>
`docker exec mark-php-app php artisan make:migration create_market_feedback_submission_fields_table --create=market_feedback_submission_fields` <br>
`docker exec mark-php-app php artisan make:migration create_market_feedback_spam_blocks_table --create=market_feedback_spam_blocks` <br>
`docker exec mark-php-app php artisan make:migration create_market_feedback_submission_events_table --create=market_feedback_submission_events` <br>

22) Конструктор опросов / Опции / Результаты
`docker exec mark-php-app php artisan make:migration create_market_poll_surveys_table --create=market_poll_surveys` <br>
`docker exec mark-php-app php artisan make:migration create_market_poll_questions_table --create=market_poll_questions` <br>
`docker exec mark-php-app php artisan make:migration create_market_poll_options_table --create=market_poll_options` <br>
`docker exec mark-php-app php artisan make:migration create_market_poll_votes_table --create=market_poll_votes` <br>
`docker exec mark-php-app php artisan make:migration create_market_poll_vote_items_table --create=market_poll_vote_items` <br>
`docker exec mark-php-app php artisan make:migration create_market_poll_results_cache_table --create=market_poll_results_cache` <br>

23) Категории вакансий / Вакансии / Отклики
`docker exec mark-php-app php artisan make:migration create_market_job_categories_table --create=market_job_categories` <br>
`docker exec mark-php-app php artisan make:migration create_market_jobs_table --create=market_jobs` <br>
`docker exec mark-php-app php artisan make:migration create_market_job_applications_table --create=market_job_applications` <br>
`docker exec mark-php-app php artisan make:migration create_market_job_application_attachments_table --create=market_job_application_attachments` <br>
`docker exec mark-php-app php artisan make:migration create_market_job_application_messages_table --create=market_job_application_messages` <br>
`docker exec mark-php-app php artisan make:migration create_market_job_application_message_media_table --create=market_job_application_message_media` <br>

24) Каталог групп / Группы
`docker exec mark-php-app php artisan make:migration create_market_catalog_groups_table --create=market_catalog_groups` <br>
`docker exec mark-php-app php artisan make:model Admin/Market/CatalogGroupItem -fs` <br>
`docker exec mark-php-app php artisan make:migration create_market_catalog_group_items_table --create=market_catalog_group_items` <br>
-------------------------------------------------------------------------------------
