<?php

namespace Database\Seeders;

use App\Models\Admin\Cms\CmsPage\CmsPage;
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
             * Уровень 1. Основная навигация.
             */

            $catalog = $this->page(
                $userId, null, 1, 10,
                '/market/catalog',
                'shopping-cart',
                true, false, false,
                [
                    'ru' => ['Каталог товаров', 'Товары и оборудование', 'Каталог товаров, оборудования и комплектующих от поставщиков.'],
                    'en' => ['Product Catalog', 'Products and equipment', 'Browse equipment, products and components from suppliers.'],
                    'kk' => ['Тауарлар каталогы', 'Тауарлар мен жабдықтар', 'Жеткізушілердің тауарлары, жабдықтары және бөлшектері.'],
                ]
            );

            $shops = $this->page(
                $userId, null, 1, 20,
                '/market/shops',
                'store',
                true, false, false,
                [
                    'ru' => ['Магазины', 'Магазины и поставщики', 'Найдите магазины, поставщиков и компании, предлагающие товары и оборудование.'],
                    'en' => ['Shops', 'Shops and suppliers', 'Find shops, suppliers and companies offering products and equipment.'],
                    'kk' => ['Дүкендер', 'Дүкендер мен жеткізушілер', 'Тауарлар мен жабдықтарды ұсынатын дүкендер мен жеткізушілерді табыңыз.'],
                ]
            );

            $blog = $this->page(
                $userId, null, 1, 30,
                '/blog',
                'newspaper',
                true, false, false,
                [
                    'ru' => ['Блог', 'Новости, обзоры и рекомендации', 'Полезные публикации о товарах, оборудовании, технологиях и выборе поставщиков.'],
                    'en' => ['Blog', 'News, reviews and recommendations', 'Articles about products, equipment, technologies and choosing suppliers.'],
                    'kk' => ['Блог', 'Жаңалықтар мен шолулар', 'Тауарлар, жабдықтар, технологиялар және жеткізушілер туралы мақалалар.'],
                ]
            );

            $company = $this->page(
                $userId, null, 1, 40,
                '/company',
                'building',
                true, true, true,
                [
                    'ru' => ['О компании', 'О проекте Agrovent', 'Agrovent — онлайн-каталог товаров, оборудования, магазинов и поставщиков.'],
                    'en' => ['About Us', 'About Agrovent', 'Agrovent is an online catalog of products, equipment, shops and suppliers.'],
                    'kk' => ['Компания туралы', 'Agrovent туралы', 'Agrovent — тауарлар, жабдықтар, дүкендер мен жеткізушілердің онлайн каталогы.'],
                ]
            );

            $buyers = $this->page(
                $userId, null, 1, 50,
                '/buyers',
                'circle-help',
                false, true, true,
                [
                    'ru' => ['Покупателям', 'Информация для покупателей', 'Узнайте, как выбирать товары, обращаться к поставщикам и уточнять условия покупки.'],
                    'en' => ['For Buyers', 'Buyer information', 'Learn how to choose products, contact suppliers and confirm purchase terms.'],
                    'kk' => ['Сатып алушыларға', 'Сатып алушыларға ақпарат', 'Тауар таңдау, жеткізушімен байланысу және сатып алу шарттарын нақтылау.'],
                ]
            );

            $contacts = $this->page(
                $userId, null, 1, 60,
                '/contacts',
                'phone',
                true, true, true,
                [
                    'ru' => ['Контакты', 'Связаться с Agrovent', 'По вопросам работы каталога, размещения товаров и сотрудничества свяжитесь с администрацией проекта.'],
                    'en' => ['Contacts', 'Contact Agrovent', 'Contact the project administration about the catalog, product listings and cooperation.'],
                    'kk' => ['Байланыс', 'Agrovent-пен байланысу', 'Каталог, тауар орналастыру және ынтымақтастық мәселелері бойынша хабарласыңыз.'],
                ]
            );

            $legal = $this->page(
                $userId, null, 1, 70,
                '/legal',
                'shield-check',
                false, true, true,
                [
                    'ru' => ['Правовая информация', 'Документы и правила', 'Информация об использовании сайта, обработке персональных данных и условиях работы каталога.'],
                    'en' => ['Legal Information', 'Documents and policies', 'Information about website use, personal data and catalog policies.'],
                    'kk' => ['Құқықтық ақпарат', 'Құжаттар мен ережелер', 'Сайтты пайдалану, дербес деректер және каталог ережелері туралы ақпарат.'],
                ]
            );

            /*
             * Уровень 2. Каталог.
             *
             * Информационные CMS-страницы.
             * Не заменяют динамические категории и товары.
             */

            $this->page(
                $userId, $catalog->id, 2, 10,
                '/catalog/categories',
                'layout-grid',
                false, false, true,
                [
                    'ru' => ['Категории товаров', 'Поиск по категориям', 'Выбирайте товары по категориям, назначению и характеристикам.'],
                    'en' => ['Product Categories', 'Browse by category', 'Explore products by category, application and specifications.'],
                    'kk' => ['Тауар санаттары', 'Санаттар бойынша іздеу', 'Тауарларды санаты, мақсаты және сипаттамалары бойынша таңдаңыз.'],
                ]
            );

            $this->page(
                $userId, $catalog->id, 2, 20,
                '/catalog/brands',
                'award',
                false, false, true,
                [
                    'ru' => ['Бренды', 'Производители и торговые марки', 'Изучайте бренды и сравнивайте предложения разных поставщиков.'],
                    'en' => ['Brands', 'Manufacturers and brands', 'Explore brands and compare supplier offers.'],
                    'kk' => ['Брендтер', 'Өндірушілер мен брендтер', 'Брендтерді қарап, жеткізушілердің ұсыныстарын салыстырыңыз.'],
                ]
            );

            $selection = $this->page(
                $userId, $catalog->id, 2, 30,
                '/catalog/selection',
                'sliders-horizontal',
                false, true, true,
                [
                    'ru' => ['Подбор оборудования', 'Как выбрать оборудование', 'Основные рекомендации по подбору оборудования с учётом назначения и технических характеристик.'],
                    'en' => ['Equipment Selection', 'How to choose equipment', 'Recommendations for selecting equipment by purpose and technical specifications.'],
                    'kk' => ['Жабдық таңдау', 'Жабдықты қалай таңдау керек', 'Мақсаты мен техникалық сипаттамаларына қарай жабдық таңдау бойынша кеңестер.'],
                ]
            );

            /*
             * Уровень 2. Магазины и поставщики.
             */

            $this->page(
                $userId, $shops->id, 2, 10,
                '/suppliers',
                'building-2',
                false, false, true,
                [
                    'ru' => ['Поставщики', 'Компании и поставщики', 'Информация о поставщиках, ассортименте и возможностях сотрудничества.'],
                    'en' => ['Suppliers', 'Companies and suppliers', 'Information about suppliers, product ranges and cooperation.'],
                    'kk' => ['Жеткізушілер', 'Компаниялар мен жеткізушілер', 'Жеткізушілер, ассортимент және ынтымақтастық туралы ақпарат.'],
                ]
            );

            $partners = $this->page(
                $userId, $shops->id, 2, 20,
                '/partners',
                'handshake',
                false, true, true,
                [
                    'ru' => ['Поставщикам', 'Сотрудничество с Agrovent', 'Информация для компаний, магазинов и поставщиков, заинтересованных в размещении товаров.'],
                    'en' => ['For Suppliers', 'Cooperate with Agrovent', 'Information for companies, shops and suppliers interested in listing products.'],
                    'kk' => ['Жеткізушілерге', 'Agrovent-пен ынтымақтастық', 'Тауар орналастыруға қызығушылық танытқан компаниялар мен жеткізушілерге ақпарат.'],
                ]
            );

            /*
             * Уровень 2. Блог.
             *
             * CMS-страницы с информационным содержимым.
             * Рубрики BlogRubric остаются отдельными сущностями.
             */

            $this->page(
                $userId, $blog->id, 2, 10,
                '/blog/news',
                'megaphone',
                false, false, true,
                [
                    'ru' => ['Новости', 'Новости рынка и оборудования', 'Новости производителей, поставщиков, технологий и рынка оборудования.'],
                    'en' => ['News', 'Industry and equipment news', 'News about manufacturers, suppliers, technologies and equipment markets.'],
                    'kk' => ['Жаңалықтар', 'Нарық және жабдық жаңалықтары', 'Өндірушілер, жеткізушілер, технологиялар және жабдық нарығы туралы жаңалықтар.'],
                ]
            );

            $this->page(
                $userId, $blog->id, 2, 20,
                '/blog/reviews',
                'book-open',
                false, false, true,
                [
                    'ru' => ['Обзоры оборудования', 'Сравнения и обзоры', 'Материалы о характеристиках, назначении и особенностях различного оборудования.'],
                    'en' => ['Equipment Reviews', 'Reviews and comparisons', 'Materials about equipment features, applications and specifications.'],
                    'kk' => ['Жабдық шолулары', 'Шолулар мен салыстырулар', 'Жабдықтардың сипаттамалары, қолданылуы және ерекшеліктері туралы материалдар.'],
                ]
            );

            /*
             * Уровень 2. О компании.
             */

            $this->page(
                $userId, $company->id, 2, 10,
                '/company/about',
                'info',
                false, true, true,
                [
                    'ru' => ['О проекте', 'Возможности Agrovent', 'Каталог помогает находить товары, изучать предложения и устанавливать контакты с поставщиками.'],
                    'en' => ['About the Project', 'Agrovent features', 'The catalog helps users find products, explore offers and contact suppliers.'],
                    'kk' => ['Жоба туралы', 'Agrovent мүмкіндіктері', 'Каталог тауарларды табуға, ұсыныстарды қарауға және жеткізушілермен байланысуға көмектеседі.'],
                ]
            );

            /*
             * Уровень 2. Покупателям.
             */

            $this->page(
                $userId, $buyers->id, 2, 10,
                '/buyers/how-to-order',
                'clipboard-list',
                false, true, true,
                [
                    'ru' => ['Как заказать', 'Выбор товара и обращение к продавцу', 'Выберите товар в каталоге и уточните у продавца наличие, цену и условия приобретения.'],
                    'en' => ['How to Order', 'Choosing and contacting a seller', 'Choose a product and confirm availability, price and purchase terms with the seller.'],
                    'kk' => ['Қалай тапсырыс беруге болады', 'Тауар таңдау және сатушымен байланысу', 'Тауарды таңдап, бағасын, қолжетімділігін және сатып алу шарттарын сатушыдан нақтылаңыз.'],
                ]
            );

            $this->page(
                $userId, $buyers->id, 2, 20,
                '/buyers/payment-delivery',
                'truck',
                false, true, true,
                [
                    'ru' => ['Оплата и доставка', 'Условия оплаты и доставки', 'Способы оплаты, сроки и стоимость доставки уточняются в условиях конкретного продавца.'],
                    'en' => ['Payment and Delivery', 'Payment and shipping terms', 'Payment methods, delivery times and costs depend on the individual seller.'],
                    'kk' => ['Төлем және жеткізу', 'Төлем мен жеткізу шарттары', 'Төлем тәсілдері, жеткізу мерзімі мен құны нақты сатушының шарттарына байланысты.'],
                ]
            );

            $this->page(
                $userId, $buyers->id, 2, 30,
                '/buyers/returns',
                'rotate-ccw',
                false, true, true,
                [
                    'ru' => ['Возврат товаров', 'Возврат и гарантия', 'Условия возврата и гарантии зависят от продавца, товара и применимого законодательства.'],
                    'en' => ['Returns', 'Returns and warranty', 'Return and warranty terms depend on the seller, product and applicable law.'],
                    'kk' => ['Тауарды қайтару', 'Қайтару және кепілдік', 'Қайтару және кепілдік шарттары сатушыға, тауарға және қолданыстағы заңнамаға байланысты.'],
                ]
            );

            $this->page(
                $userId, $buyers->id, 2, 40,
                '/buyers/faq',
                'help-circle',
                false, true, true,
                [
                    'ru' => ['Частые вопросы', 'Вопросы и ответы', 'Ответы на распространённые вопросы о каталоге, магазинах, товарах и поставщиках.'],
                    'en' => ['FAQ', 'Frequently asked questions', 'Answers about the catalog, shops, products and suppliers.'],
                    'kk' => ['Жиі қойылатын сұрақтар', 'Сұрақтар мен жауаптар', 'Каталог, дүкендер, тауарлар мен жеткізушілер туралы жауаптар.'],
                ]
            );

            /*
             * Уровень 2. Правовые страницы.
             *
             * Тексты здесь предварительные.
             * Перед публикацией требуют юридической проверки.
             */

            $this->page(
                $userId, $legal->id, 2, 10,
                '/legal/privacy',
                'lock',
                false, true, true,
                [
                    'ru' => ['Политика конфиденциальности', 'Обработка персональных данных', 'Информация о сборе и обработке персональных данных пользователей сайта.'],
                    'en' => ['Privacy Policy', 'Personal data processing', 'Information about collection and processing of website users’ personal data.'],
                    'kk' => ['Құпиялылық саясаты', 'Дербес деректерді өңдеу', 'Сайт пайдаланушыларының дербес деректерін жинау және өңдеу туралы ақпарат.'],
                ]
            );

            $this->page(
                $userId, $legal->id, 2, 20,
                '/legal/terms',
                'file-text',
                false, true, true,
                [
                    'ru' => ['Условия использования', 'Правила использования сайта', 'Основные сведения об использовании каталога и взаимодействии с продавцами.'],
                    'en' => ['Terms of Use', 'Website terms', 'General information about using the catalog and interacting with sellers.'],
                    'kk' => ['Пайдалану шарттары', 'Сайтты пайдалану ережелері', 'Каталогты пайдалану және сатушылармен өзара әрекеттесу туралы ақпарат.'],
                ]
            );

            /*
             * Уровень 3. Практические подразделы.
             */

            $this->page(
                $userId, $selection->id, 3, 10,
                '/catalog/selection/specifications',
                'settings',
                false, false, true,
                [
                    'ru' => ['Технические характеристики', 'На что обратить внимание', 'При выборе оборудования сравнивайте производительность, мощность, размеры, условия эксплуатации и документацию.'],
                    'en' => ['Technical Specifications', 'What to compare', 'Compare performance, power, dimensions, operating conditions and documentation.'],
                    'kk' => ['Техникалық сипаттамалар', 'Нені салыстыру керек', 'Өнімділік, қуат, өлшемдер, пайдалану шарттары және құжаттаманы салыстырыңыз.'],
                ]
            );

            $this->page(
                $userId, $partners->id, 3, 10,
                '/partners/cooperation',
                'handshake',
                false, true, true,
                [
                    'ru' => ['Условия сотрудничества', 'Размещение товаров и предложений', 'Для обсуждения размещения ассортимента и сотрудничества обратитесь к администрации Agrovent.'],
                    'en' => ['Cooperation Terms', 'Listing products and offers', 'Contact Agrovent to discuss product listings and cooperation.'],
                    'kk' => ['Ынтымақтастық шарттары', 'Тауарлар мен ұсыныстарды орналастыру', 'Тауар орналастыру және ынтымақтастық үшін Agrovent әкімшілігіне хабарласыңыз.'],
                ]
            );
        });
    }

    /**
     * Создание или обновление CMS-страницы и её переводов.
     */
    protected function page(
        int $userId,
        ?int $parentId,
        int $level,
        int $sort,
        string $url,
        string $icon,
        bool $inMenu,
        bool $inFooter,
        bool $showContent,
        array $translations
    ): Model {
        $page = CmsPage::query()->updateOrCreate(
            ['url' => $url],
            [
                'user_id' => $userId,
                'parent_id' => $parentId,
                'level' => $level,
                'sort' => $sort,
                'icon' => $icon,

                'in_menu' => $inMenu,
                'in_footer' => $inFooter,
                'show_content' => $showContent,
                'show_seo' => true,

                'activity' => true,
                'status' => 'published',

                'published_at' => now(),
                'show_from_at' => null,
                'show_to_at' => null,

                'views' => 0,
            ]
        );

        foreach ($translations as $locale => $data) {
            [$title, $subtitle, $short] = $data;

            $page->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title' => $title,
                    'subtitle' => $subtitle,
                    'short' => $short,

                    'description' => $this->htmlContent(
                        $title,
                        $subtitle,
                        $short
                    ),

                    'meta_title' => $title . ' | Agrovent',
                    'meta_keywords' => implode(', ', [
                        $title,
                        $subtitle,
                        'Agrovent',
                    ]),
                    'meta_desc' => $short,
                ]
            );
        }

        return $page;
    }

    /**
     * Начальный HTML-контент.
     *
     * Это информационная заготовка.
     * Содержимое коммерческих и правовых страниц
     * необходимо дополнить перед публикацией.
     */
    protected function htmlContent(
        string $title,
        string $subtitle,
        string $text
    ): string {
        $title = e($title);
        $subtitle = e($subtitle);
        $text = e($text);

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
