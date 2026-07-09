<?php

namespace Database\Seeders;

use App\Models\Admin\Cms\CmsPage\CmsPage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CmsPageSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $userId = 1;

            /*
             |--------------------------------------------------------------------------
             | LEVEL 1
             |--------------------------------------------------------------------------
             */
            $blog = $this->createPage(
                $userId, null, 1, 10,
                '<svg class="shrink-0 h-4 w-4 fill-current text-blue-600" viewBox="0 0 512 512"><path d="M352 96c0-53.02-42.98-96-96-96s-96 42.98-96 96 42.98 96 96 96 96-42.98 96-96zM233.59 241.1c-59.33-36.32-155.43-46.3-203.79-49.05C13.55 191.13 0 203.51 0 219.14v222.8c0 14.33 11.59 26.28 26.49 27.05 43.66 2.29 131.99 10.68 193.04 41.43 9.37 4.72 20.48-1.71 20.48-11.87V252.56c-.01-4.67-2.32-8.95-6.42-11.46zm248.61-49.05c-48.35 2.74-144.46 12.73-203.78 49.05-4.1 2.51-6.41 6.96-6.41 11.63v245.79c0 10.19 11.14 16.63 20.54 11.9 61.04-30.72 149.32-39.11 192.97-41.4 14.9-.78 26.49-12.73 26.49-27.06V219.14c-.01-15.63-13.56-28.01-29.81-27.09z"/></svg>',
                '/blog', 320, true, true, false, true, [
                'ru' => ['Блог', 'Статьи и новости', 'Полезные материалы, новости проекта и экспертные статьи.'],
                'en' => ['Blog', 'Articles and news', 'Useful materials, project news and expert articles.'],
                'kk' => ['Блог', 'Мақалалар және жаңалықтар', 'Пайдалы материалдар, жоба жаңалықтары және мақалалар.'],
            ]);

            $school = $this->createPage(
                $userId, null, 1, 20,
                '<svg class="shrink-0 h-5 w-5 fill-current text-blue-600" viewBox="0 0 640 512"><path d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"></path></svg>',
                '/school', 280, true, true, false, true, [
                'ru' => ['Онлайн школа', 'Обучение и курсы', 'Курсы, уроки, программы обучения и полезные материалы.'],
                'en' => ['Online School', 'Learning and courses', 'Courses, lessons, learning programs and useful materials.'],
                'kk' => ['Онлайн мектеп', 'Оқу және курстар', 'Курстар, сабақтар және оқу бағдарламалары.'],
            ]);

            $market = $this->createPage(
                $userId, null, 1, 30,
                '<svg class="shrink-0 h-4 w-4 fill-current text-blue-600" viewBox="0 0 640 512"><path d="M528 448H112c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h416c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16zm64-320c-26.5 0-48 21.5-48 48 0 7.1 1.6 13.7 4.4 19.8L476 239.2c-15.4 9.2-35.3 4-44.2-11.6L350.3 85C361 76.2 368 63 368 48c0-26.5-21.5-48-48-48s-48 21.5-48 48c0 15 7 28.2 17.7 37l-81.5 142.6c-8.9 15.6-28.9 20.8-44.2 11.6l-72.3-43.4c2.7-6 4.4-12.7 4.4-19.8 0-26.5-21.5-48-48-48S0 149.5 0 176s21.5 48 48 48c2.6 0 5.2-.4 7.7-.8L128 416h384l72.3-192.8c2.5.4 5.1.8 7.7.8 26.5 0 48-21.5 48-48s-21.5-48-48-48z"></path></svg>',
                '/market', 410, true, true, false, true, [
                'ru' => ['Маркетплейс', 'Товары и поставщики', 'Каталог товаров, магазинов, брендов и поставщиков.'],
                'en' => ['Marketplace', 'Products and suppliers', 'Catalog of products, shops, brands and suppliers.'],
                'kk' => ['Маркетплейс', 'Тауарлар және жеткізушілер', 'Тауарлар, дүкендер және брендтер каталогы.'],
            ]);

            $company = $this->createPage(
                $userId, null, 1, 40,
                '<svg class="shrink-0 h-4 w-4 fill-current text-blue-600" viewBox="0 0 640 512"><path d="M624 416H381.54c-.74 19.81-14.71 32-32.74 32H288c-18.69 0-33.02-17.47-32.77-32H16c-8.8 0-16 7.2-16 16v16c0 35.2 28.8 64 64 64h512c35.2 0 64-28.8 64-64v-16c0-8.8-7.2-16-16-16zM576 48c0-26.4-21.6-48-48-48H112C85.6 0 64 21.6 64 48v336h512V48zm-64 272H128V64h384v256z"></path></svg>',
                '/company', 140, true, true, true, true, [
                'ru' => ['О компании', 'Информация о проекте', 'Описание компании, миссия, ценности и преимущества.'],
                'en' => ['Company', 'About the project', 'Company description, mission, values and advantages.'],
                'kk' => ['Компания туралы', 'Жоба туралы ақпарат', 'Компания сипаттамасы, миссиясы және артықшылықтары.'],
            ]);

            $contacts = $this->createPage(
                $userId, null, 1, 50,
                '<svg class="shrink-0 h-4 w-4 fill-current text-blue-600" viewBox="0 0 384 512"><path d="M97.333 506.966c-129.874-129.874-129.681-340.252 0-469.933 5.698-5.698 14.527-6.632 21.263-2.422l64.817 40.513a17.187 17.187 0 0 1 6.849 20.958l-32.408 81.021a17.188 17.188 0 0 1-17.669 10.719l-55.81-5.58c-21.051 58.261-20.612 122.471 0 179.515l55.811-5.581a17.188 17.188 0 0 1 17.669 10.719l32.408 81.022a17.188 17.188 0 0 1-6.849 20.958l-64.817 40.513a17.19 17.19 0 0 1-21.264-2.422zM247.126 95.473c11.832 20.047 11.832 45.008 0 65.055-3.95 6.693-13.108 7.959-18.718 2.581l-5.975-5.726c-3.911-3.748-4.793-9.622-2.261-14.41a32.063 32.063 0 0 0 0-29.945c-2.533-4.788-1.65-10.662 2.261-14.41l5.975-5.726c5.61-5.378 14.768-4.112 18.718 2.581zm91.787-91.187c60.14 71.604 60.092 175.882 0 247.428-4.474 5.327-12.53 5.746-17.552.933l-5.798-5.557c-4.56-4.371-4.977-11.529-.93-16.379 49.687-59.538 49.646-145.933 0-205.422-4.047-4.85-3.631-12.008.93-16.379l5.798-5.557c5.022-4.813 13.078-4.394 17.552.933zm-45.972 44.941c36.05 46.322 36.108 111.149 0 157.546-4.39 5.641-12.697 6.251-17.856 1.304l-5.818-5.579c-4.4-4.219-4.998-11.095-1.285-15.931 26.536-34.564 26.534-82.572 0-117.134-3.713-4.836-3.115-11.711 1.285-15.931l5.818-5.579c5.159-4.947 13.466-4.337 17.856 1.304z"/></svg>',
                '/contacts', 230, true, true, true, true, [
                'ru' => ['Контакты', 'Связаться с нами', 'Контактная информация, адрес, телефон и форма обратной связи.'],
                'en' => ['Contacts', 'Contact us', 'Contact information, address, phone and feedback form.'],
                'kk' => ['Байланыс', 'Бізбен байланысу', 'Байланыс ақпараты, мекенжай, телефон және кері байланыс.'],
            ]);

            $help = $this->createPage(
                $userId, null, 1, 60,
                '<svg class="shrink-0 h-3 w-3 fill-current text-blue-600" viewBox="0 0 24 24"><path d="M12,0C5.383,0,0,5.383,0,12s5.383,12,12,12,12-5.383,12-12S18.617,0,12,0Zm0,19.5c-.69,0-1.25-.56-1.25-1.25s.56-1.25,1.25-1.25,1.25,.56,1.25,1.25-.56,1.25-1.25,1.25Zm2.688-7.198c-1.444,1.224-1.688,1.667-1.688,2.198,0,.553-.447,1-1,1s-1-.447-1-1c0-1.434,.807-2.379,2.395-3.724,.447-.38,1.844-1.72,1.024-3.046-.532-.861-2.517-.984-4.162-.256-.507,.223-1.096-.006-1.319-.511-.223-.505,.006-1.096,.511-1.319,2.33-1.03,5.463-.924,6.672,1.035,1.103,1.784,.554,3.938-1.433,5.622Z"></path></svg>',
                '/help', 180, true, true, true, true, [
                'ru' => ['Помощь', 'Справочный раздел', 'Ответы на вопросы, инструкции и полезная информация.'],
                'en' => ['Help', 'Support section', 'Answers, instructions and useful information.'],
                'kk' => ['Көмек', 'Анықтама бөлімі', 'Жауаптар, нұсқаулықтар және пайдалы ақпарат.'],
            ]);

            /*
             |--------------------------------------------------------------------------
             | LEVEL 2
             |--------------------------------------------------------------------------
             */
            $blogNews = $this->createPage(
                $userId, $blog->id, 2, 10,
                '+',
                '/blog/news', 120, true, false, false, true, [
                'ru' => ['Новости', 'Новости проекта', 'Последние новости проекта, обновления и важные события.'],
                'en' => ['News', 'Project news', 'Latest project news, updates and important events.'],
                'kk' => ['Жаңалықтар', 'Жоба жаңалықтары', 'Соңғы жаңалықтар, жаңартулар және маңызды оқиғалар.'],
            ]);

            $blogArticles = $this->createPage(
                $userId, $blog->id, 2, 20,
                '+',
                '/blog/articles', 150, true, false, false, true, [
                'ru' => ['Статьи', 'Полезные публикации', 'Экспертные статьи, обзоры и полезные материалы.'],
                'en' => ['Articles', 'Useful publications', 'Expert articles, reviews and useful materials.'],
                'kk' => ['Мақалалар', 'Пайдалы жарияланымдар', 'Сараптамалық мақалалар және пайдалы материалдар.'],
            ]);

            $schoolCourses = $this->createPage(
                $userId, $school->id, 2, 10,
                '+',
                '/school/courses', 170, true, false, false, true, [
                'ru' => ['Курсы', 'Каталог курсов', 'Образовательные курсы по направлениям и уровням подготовки.'],
                'en' => ['Courses', 'Course catalog', 'Educational courses by topics and levels.'],
                'kk' => ['Курстар', 'Курстар каталогы', 'Бағыттар мен деңгейлер бойынша оқу курстары.'],
            ]);

            $schoolTeachers = $this->createPage(
                $userId, $school->id, 2, 20,
                '+',
                '/school/teachers', 90, true, false, false, true, [
                'ru' => ['Преподаватели', 'Команда школы', 'Преподаватели, эксперты и авторы образовательных программ.'],
                'en' => ['Teachers', 'School team', 'Teachers, experts and authors of learning programs.'],
                'kk' => ['Оқытушылар', 'Мектеп командасы', 'Оқытушылар, сарапшылар және оқу бағдарламаларының авторлары.'],
            ]);

            $marketShops = $this->createPage(
                $userId, $market->id, 2, 10,
                '+',
                '/market/shops', 210, true, false, false, true, [
                'ru' => ['Магазины', 'Поставщики и магазины', 'Список магазинов и официальных поставщиков маркетплейса.'],
                'en' => ['Shops', 'Suppliers and shops', 'List of marketplace shops and official suppliers.'],
                'kk' => ['Дүкендер', 'Жеткізушілер және дүкендер', 'Маркетплейстегі дүкендер мен жеткізушілер тізімі.'],
            ]);

            $marketCatalog = $this->createPage(
                $userId, $market->id, 2, 20,
                '+',
                '/market/catalog', 260, true, false, false, true, [
                'ru' => ['Каталог товаров', 'Товары маркетплейса', 'Разделы, категории, бренды и товары маркетплейса.'],
                'en' => ['Product Catalog', 'Marketplace products', 'Sections, categories, brands and marketplace products.'],
                'kk' => ['Тауарлар каталогы', 'Маркетплейс тауарлары', 'Бөлімдер, санаттар, брендтер және тауарлар.'],
            ]);

            $companyAbout = $this->createPage(
                $userId, $company->id, 2, 10,
                '+',
                '/company/about', 80, true, true, true, true, [
                'ru' => ['О проекте', 'Подробнее о проекте', 'Подробная информация о проекте, целях и возможностях платформы.'],
                'en' => ['About Project', 'More about the project', 'Detailed information about the project, goals and platform features.'],
                'kk' => ['Жоба туралы', 'Жоба жайлы толығырақ', 'Жоба, мақсаттар және платформа мүмкіндіктері туралы ақпарат.'],
            ]);

            $companyPartners = $this->createPage(
                $userId, $company->id, 2, 20,
                '+',
                '/company/partners', 70, true, true, true, true, [
                'ru' => ['Партнёрам', 'Сотрудничество', 'Информация для партнёров, поставщиков и компаний.'],
                'en' => ['For Partners', 'Cooperation', 'Information for partners, suppliers and companies.'],
                'kk' => ['Серіктестерге', 'Ынтымақтастық', 'Серіктестерге, жеткізушілерге және компанияларға арналған ақпарат.'],
            ]);

            $helpFaq = $this->createPage(
                $userId, $help->id, 2, 10,
                '+',
                '/help/faq', 110, true, true, true, true, [
                'ru' => ['FAQ', 'Частые вопросы', 'Ответы на популярные вопросы пользователей.'],
                'en' => ['FAQ', 'Frequently asked questions', 'Answers to common user questions.'],
                'kk' => ['FAQ', 'Жиі қойылатын сұрақтар', 'Пайдаланушылардың жиі сұрақтарына жауаптар.'],
            ]);

            $helpRules = $this->createPage(
                $userId, $help->id, 2, 20,
                '+',
                '/help/rules', 95, true, true, true, true, [
                'ru' => ['Правила', 'Правила платформы', 'Основные правила использования платформы и сервисов.'],
                'en' => ['Rules', 'Platform rules', 'Main rules for using the platform and services.'],
                'kk' => ['Ережелер', 'Платформа ережелері', 'Платформа мен сервистерді пайдаланудың негізгі ережелері.'],
            ]);

            /*
             |--------------------------------------------------------------------------
             | LEVEL 3
             |--------------------------------------------------------------------------
             */
            $this->createPage(
                $userId, $blogNews->id, 3, 10,
                '+',
                '/blog/news/platform', 60, true, false, false, true, [
                'ru' => ['Новости платформы', 'Обновления системы', 'Новости о развитии платформы и новых возможностях.'],
                'en' => ['Platform News', 'System updates', 'News about platform development and new features.'],
                'kk' => ['Платформа жаңалықтары', 'Жүйе жаңартулары', 'Платформаның дамуы және жаңа мүмкіндіктер туралы жаңалықтар.'],
            ]);

            $this->createPage(
                $userId, $blogArticles->id, 3, 10,
                '+',
                '/blog/articles/guides', 75, true, false, false, true, [
                'ru' => ['Гайды', 'Практические инструкции', 'Пошаговые инструкции, советы и обучающие материалы.'],
                'en' => ['Guides', 'Practical instructions', 'Step-by-step guides, tips and learning materials.'],
                'kk' => ['Нұсқаулықтар', 'Практикалық нұсқаулар', 'Қадамдық нұсқаулықтар, кеңестер және оқу материалдары.'],
            ]);

            $this->createPage(
                $userId, $schoolCourses->id, 3, 10,
                '+',
                '/school/courses/programming', 95, true, false, false, true, [
                'ru' => ['Программирование', 'Курсы разработки', 'Курсы по программированию, веб-разработке и технологиям.'],
                'en' => ['Programming', 'Development courses', 'Courses about programming, web development and technologies.'],
                'kk' => ['Бағдарламалау', 'Әзірлеу курстары', 'Бағдарламалау, веб-әзірлеу және технология курстары.'],
            ]);

            $this->createPage(
                $userId, $schoolCourses->id, 3, 20,
                '+',
                '/school/courses/marketing', 85, true, false, false, true, [
                'ru' => ['Маркетинг', 'Курсы маркетинга', 'Курсы по маркетингу, рекламе, аналитике и продвижению.'],
                'en' => ['Marketing', 'Marketing courses', 'Courses about marketing, ads, analytics and promotion.'],
                'kk' => ['Маркетинг', 'Маркетинг курстары', 'Маркетинг, жарнама, аналитика және жылжыту курстары.'],
            ]);

            $this->createPage(
                $userId, $marketShops->id, 3, 10,
                '+',
                '/market/shops/suppliers', 130, true, false, false, true, [
                'ru' => ['Поставщики', 'Официальные поставщики', 'Страницы компаний, поставщиков и представителей брендов.'],
                'en' => ['Suppliers', 'Official suppliers', 'Pages of companies, suppliers and brand representatives.'],
                'kk' => ['Жеткізушілер', 'Ресми жеткізушілер', 'Компаниялар, жеткізушілер және бренд өкілдері беттері.'],
            ]);

            $this->createPage(
                $userId, $marketCatalog->id, 3, 10,
                '+',
                '/market/catalog/brands', 145, true, false, false, true, [
                'ru' => ['Бренды', 'Каталог брендов', 'Список брендов, производителей и торговых марок.'],
                'en' => ['Brands', 'Brand catalog', 'List of brands, manufacturers and trademarks.'],
                'kk' => ['Брендтер', 'Брендтер каталогы', 'Брендтер, өндірушілер және сауда белгілері тізімі.'],
            ]);

            $this->createPage(
                $userId, $companyPartners->id, 3, 10,
                '+',
                '/company/partners/cooperation', 55, true, true, true, true, [
                'ru' => ['Условия сотрудничества', 'Как стать партнёром', 'Описание условий сотрудничества и подключения к платформе.'],
                'en' => ['Cooperation Terms', 'How to become a partner', 'Terms of cooperation and platform connection process.'],
                'kk' => ['Ынтымақтастық шарттары', 'Серіктес болу жолы', 'Ынтымақтастық шарттары және платформаға қосылу тәртібі.'],
            ]);

            $this->createPage(
                $userId, $helpRules->id, 3, 10,
                '+',
                '/help/rules/privacy', 65, true, true, true, true, [
                'ru' => ['Политика конфиденциальности', 'Защита данных', 'Информация о хранении, обработке и защите пользовательских данных.'],
                'en' => ['Privacy Policy', 'Data protection', 'Information about storing, processing and protecting user data.'],
                'kk' => ['Құпиялылық саясаты', 'Деректерді қорғау', 'Пайдаланушы деректерін сақтау, өңдеу және қорғау туралы ақпарат.'],
            ]);
        });
    }

    protected function createPage(
        int $userId,
        ?int $parentId,
        int $level,
        int $sort,
        string $icon,
        string $url,
        int $views,
        bool $inMenu,
        bool $inFooter,
        bool $showContent,
        bool $showSeo,
        array $translations
    ): Builder|Model {
        $page = CmsPage::query()->updateOrCreate(
            ['url' => $url],
            [
                'user_id' => $userId,
                'parent_id' => $parentId,
                'level' => $level,
                'icon' => $icon,

                'in_menu' => $inMenu,
                'in_footer' => $inFooter,
                'show_content' => $showContent,
                'show_seo' => $showSeo,

                'sort' => $sort,
                'activity' => true,
                'status' => 'published',

                'published_at' => now(),
                'show_from_at' => null,
                'show_to_at' => null,

                'views' => $views,
            ]
        );

        foreach ($translations as $locale => $data) {
            $page->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title' => $data[0],
                    'subtitle' => $data[1],
                    'short' => $data[2],
                    'description' => $this->htmlContent(
                        title: $data[0],
                        subtitle: $data[1],
                        text: $data[2]
                    ),
                    'meta_title' => $data[0],
                    'meta_keywords' => $data[0] . ', cms, page',
                    'meta_desc' => $data[2],
                ]
            );
        }

        return $page;
    }

    protected function htmlContent(
        string $title,
        string $subtitle,
        string $text
    ): string {
        return <<<HTML
<section class="cms-page">
    <div class="cms-page__container">
        <h1>{$title}</h1>
        <h2>{$subtitle}</h2>
        <p>{$text}</p>
    </div>
</section>
HTML;
    }
}
