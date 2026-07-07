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
            $blog = $this->createPage($userId, null, 1, 10, 'newspaper', '/blog', 320, true, true, false, true, [
                'ru' => ['Блог', 'Статьи и новости', 'Полезные материалы, новости проекта и экспертные статьи.'],
                'en' => ['Blog', 'Articles and news', 'Useful materials, project news and expert articles.'],
                'kk' => ['Блог', 'Мақалалар және жаңалықтар', 'Пайдалы материалдар, жоба жаңалықтары және мақалалар.'],
            ]);

            $school = $this->createPage($userId, null, 1, 20, 'graduation-cap', '/school', 280, true, true, false, true, [
                'ru' => ['Онлайн школа', 'Обучение и курсы', 'Курсы, уроки, программы обучения и полезные материалы.'],
                'en' => ['Online School', 'Learning and courses', 'Courses, lessons, learning programs and useful materials.'],
                'kk' => ['Онлайн мектеп', 'Оқу және курстар', 'Курстар, сабақтар және оқу бағдарламалары.'],
            ]);

            $market = $this->createPage($userId, null, 1, 30, 'store', '/market', 410, true, true, false, true, [
                'ru' => ['Маркетплейс', 'Товары и поставщики', 'Каталог товаров, магазинов, брендов и поставщиков.'],
                'en' => ['Marketplace', 'Products and suppliers', 'Catalog of products, shops, brands and suppliers.'],
                'kk' => ['Маркетплейс', 'Тауарлар және жеткізушілер', 'Тауарлар, дүкендер және брендтер каталогы.'],
            ]);

            $company = $this->createPage($userId, null, 1, 40, 'building', '/company', 140, true, true, true, true, [
                'ru' => ['О компании', 'Информация о проекте', 'Описание компании, миссия, ценности и преимущества.'],
                'en' => ['Company', 'About the project', 'Company description, mission, values and advantages.'],
                'kk' => ['Компания туралы', 'Жоба туралы ақпарат', 'Компания сипаттамасы, миссиясы және артықшылықтары.'],
            ]);

            $contacts = $this->createPage($userId, null, 1, 50, 'phone', '/contacts', 230, true, true, true, true, [
                'ru' => ['Контакты', 'Связаться с нами', 'Контактная информация, адрес, телефон и форма обратной связи.'],
                'en' => ['Contacts', 'Contact us', 'Contact information, address, phone and feedback form.'],
                'kk' => ['Байланыс', 'Бізбен байланысу', 'Байланыс ақпараты, мекенжай, телефон және кері байланыс.'],
            ]);

            $help = $this->createPage($userId, null, 1, 60, 'circle-question', '/help', 180, true, true, true, true, [
                'ru' => ['Помощь', 'Справочный раздел', 'Ответы на вопросы, инструкции и полезная информация.'],
                'en' => ['Help', 'Support section', 'Answers, instructions and useful information.'],
                'kk' => ['Көмек', 'Анықтама бөлімі', 'Жауаптар, нұсқаулықтар және пайдалы ақпарат.'],
            ]);

            /*
             |--------------------------------------------------------------------------
             | LEVEL 2
             |--------------------------------------------------------------------------
             */
            $blogNews = $this->createPage($userId, $blog->id, 2, 10, 'rss', '/blog/news', 120, true, false, false, true, [
                'ru' => ['Новости', 'Новости проекта', 'Последние новости проекта, обновления и важные события.'],
                'en' => ['News', 'Project news', 'Latest project news, updates and important events.'],
                'kk' => ['Жаңалықтар', 'Жоба жаңалықтары', 'Соңғы жаңалықтар, жаңартулар және маңызды оқиғалар.'],
            ]);

            $blogArticles = $this->createPage($userId, $blog->id, 2, 20, 'file-lines', '/blog/articles', 150, true, false, false, true, [
                'ru' => ['Статьи', 'Полезные публикации', 'Экспертные статьи, обзоры и полезные материалы.'],
                'en' => ['Articles', 'Useful publications', 'Expert articles, reviews and useful materials.'],
                'kk' => ['Мақалалар', 'Пайдалы жарияланымдар', 'Сараптамалық мақалалар және пайдалы материалдар.'],
            ]);

            $schoolCourses = $this->createPage($userId, $school->id, 2, 10, 'book-open', '/school/courses', 170, true, false, false, true, [
                'ru' => ['Курсы', 'Каталог курсов', 'Образовательные курсы по направлениям и уровням подготовки.'],
                'en' => ['Courses', 'Course catalog', 'Educational courses by topics and levels.'],
                'kk' => ['Курстар', 'Курстар каталогы', 'Бағыттар мен деңгейлер бойынша оқу курстары.'],
            ]);

            $schoolTeachers = $this->createPage($userId, $school->id, 2, 20, 'users', '/school/teachers', 90, true, false, false, true, [
                'ru' => ['Преподаватели', 'Команда школы', 'Преподаватели, эксперты и авторы образовательных программ.'],
                'en' => ['Teachers', 'School team', 'Teachers, experts and authors of learning programs.'],
                'kk' => ['Оқытушылар', 'Мектеп командасы', 'Оқытушылар, сарапшылар және оқу бағдарламаларының авторлары.'],
            ]);

            $marketShops = $this->createPage($userId, $market->id, 2, 10, 'shop', '/market/shops', 210, true, false, false, true, [
                'ru' => ['Магазины', 'Поставщики и магазины', 'Список магазинов и официальных поставщиков маркетплейса.'],
                'en' => ['Shops', 'Suppliers and shops', 'List of marketplace shops and official suppliers.'],
                'kk' => ['Дүкендер', 'Жеткізушілер және дүкендер', 'Маркетплейстегі дүкендер мен жеткізушілер тізімі.'],
            ]);

            $marketCatalog = $this->createPage($userId, $market->id, 2, 20, 'boxes-stacked', '/market/catalog', 260, true, false, false, true, [
                'ru' => ['Каталог товаров', 'Товары маркетплейса', 'Разделы, категории, бренды и товары маркетплейса.'],
                'en' => ['Product Catalog', 'Marketplace products', 'Sections, categories, brands and marketplace products.'],
                'kk' => ['Тауарлар каталогы', 'Маркетплейс тауарлары', 'Бөлімдер, санаттар, брендтер және тауарлар.'],
            ]);

            $companyAbout = $this->createPage($userId, $company->id, 2, 10, 'info', '/company/about', 80, true, true, true, true, [
                'ru' => ['О проекте', 'Подробнее о проекте', 'Подробная информация о проекте, целях и возможностях платформы.'],
                'en' => ['About Project', 'More about the project', 'Detailed information about the project, goals and platform features.'],
                'kk' => ['Жоба туралы', 'Жоба жайлы толығырақ', 'Жоба, мақсаттар және платформа мүмкіндіктері туралы ақпарат.'],
            ]);

            $companyPartners = $this->createPage($userId, $company->id, 2, 20, 'handshake', '/company/partners', 70, true, true, true, true, [
                'ru' => ['Партнёрам', 'Сотрудничество', 'Информация для партнёров, поставщиков и компаний.'],
                'en' => ['For Partners', 'Cooperation', 'Information for partners, suppliers and companies.'],
                'kk' => ['Серіктестерге', 'Ынтымақтастық', 'Серіктестерге, жеткізушілерге және компанияларға арналған ақпарат.'],
            ]);

            $helpFaq = $this->createPage($userId, $help->id, 2, 10, 'comments', '/help/faq', 110, true, true, true, true, [
                'ru' => ['FAQ', 'Частые вопросы', 'Ответы на популярные вопросы пользователей.'],
                'en' => ['FAQ', 'Frequently asked questions', 'Answers to common user questions.'],
                'kk' => ['FAQ', 'Жиі қойылатын сұрақтар', 'Пайдаланушылардың жиі сұрақтарына жауаптар.'],
            ]);

            $helpRules = $this->createPage($userId, $help->id, 2, 20, 'scale-balanced', '/help/rules', 95, true, true, true, true, [
                'ru' => ['Правила', 'Правила платформы', 'Основные правила использования платформы и сервисов.'],
                'en' => ['Rules', 'Platform rules', 'Main rules for using the platform and services.'],
                'kk' => ['Ережелер', 'Платформа ережелері', 'Платформа мен сервистерді пайдаланудың негізгі ережелері.'],
            ]);

            /*
             |--------------------------------------------------------------------------
             | LEVEL 3
             |--------------------------------------------------------------------------
             */
            $this->createPage($userId, $blogNews->id, 3, 10, 'bullhorn', '/blog/news/platform', 60, true, false, false, true, [
                'ru' => ['Новости платформы', 'Обновления системы', 'Новости о развитии платформы и новых возможностях.'],
                'en' => ['Platform News', 'System updates', 'News about platform development and new features.'],
                'kk' => ['Платформа жаңалықтары', 'Жүйе жаңартулары', 'Платформаның дамуы және жаңа мүмкіндіктер туралы жаңалықтар.'],
            ]);

            $this->createPage($userId, $blogArticles->id, 3, 10, 'pen-nib', '/blog/articles/guides', 75, true, false, false, true, [
                'ru' => ['Гайды', 'Практические инструкции', 'Пошаговые инструкции, советы и обучающие материалы.'],
                'en' => ['Guides', 'Practical instructions', 'Step-by-step guides, tips and learning materials.'],
                'kk' => ['Нұсқаулықтар', 'Практикалық нұсқаулар', 'Қадамдық нұсқаулықтар, кеңестер және оқу материалдары.'],
            ]);

            $this->createPage($userId, $schoolCourses->id, 3, 10, 'code', '/school/courses/programming', 95, true, false, false, true, [
                'ru' => ['Программирование', 'Курсы разработки', 'Курсы по программированию, веб-разработке и технологиям.'],
                'en' => ['Programming', 'Development courses', 'Courses about programming, web development and technologies.'],
                'kk' => ['Бағдарламалау', 'Әзірлеу курстары', 'Бағдарламалау, веб-әзірлеу және технология курстары.'],
            ]);

            $this->createPage($userId, $schoolCourses->id, 3, 20, 'chart-line', '/school/courses/marketing', 85, true, false, false, true, [
                'ru' => ['Маркетинг', 'Курсы маркетинга', 'Курсы по маркетингу, рекламе, аналитике и продвижению.'],
                'en' => ['Marketing', 'Marketing courses', 'Courses about marketing, ads, analytics and promotion.'],
                'kk' => ['Маркетинг', 'Маркетинг курстары', 'Маркетинг, жарнама, аналитика және жылжыту курстары.'],
            ]);

            $this->createPage($userId, $marketShops->id, 3, 10, 'certificate', '/market/shops/suppliers', 130, true, false, false, true, [
                'ru' => ['Поставщики', 'Официальные поставщики', 'Страницы компаний, поставщиков и представителей брендов.'],
                'en' => ['Suppliers', 'Official suppliers', 'Pages of companies, suppliers and brand representatives.'],
                'kk' => ['Жеткізушілер', 'Ресми жеткізушілер', 'Компаниялар, жеткізушілер және бренд өкілдері беттері.'],
            ]);

            $this->createPage($userId, $marketCatalog->id, 3, 10, 'tags', '/market/catalog/brands', 145, true, false, false, true, [
                'ru' => ['Бренды', 'Каталог брендов', 'Список брендов, производителей и торговых марок.'],
                'en' => ['Brands', 'Brand catalog', 'List of brands, manufacturers and trademarks.'],
                'kk' => ['Брендтер', 'Брендтер каталогы', 'Брендтер, өндірушілер және сауда белгілері тізімі.'],
            ]);

            $this->createPage($userId, $companyPartners->id, 3, 10, 'file-signature', '/company/partners/cooperation', 55, true, true, true, true, [
                'ru' => ['Условия сотрудничества', 'Как стать партнёром', 'Описание условий сотрудничества и подключения к платформе.'],
                'en' => ['Cooperation Terms', 'How to become a partner', 'Terms of cooperation and platform connection process.'],
                'kk' => ['Ынтымақтастық шарттары', 'Серіктес болу жолы', 'Ынтымақтастық шарттары және платформаға қосылу тәртібі.'],
            ]);

            $this->createPage($userId, $helpRules->id, 3, 10, 'shield-halved', '/help/rules/privacy', 65, true, true, true, true, [
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
