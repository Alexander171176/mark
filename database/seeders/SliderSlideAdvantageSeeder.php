<?php

namespace Database\Seeders;

use App\Models\Admin\Slider\SliderSlideAdvantage\SliderSlideAdvantage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Демонстрационный каталог преимуществ интернет-магазина.
 * 7 тематических групп по 4 преимущества, переводы ru/en/kk.
 * Не создаёт связей со слайдами и не удаляет пользовательские данные.
 */
class SliderSlideAdvantageSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Группа 1, преимущество 1.
            [
                'icon' => 'truck',
                'translations' => [
                    'ru' => ['title' => 'Быстрая доставка', 'text' => 'Оперативно доставляем заказы в доступные регионы.'],
                    'en' => ['title' => 'Fast delivery', 'text' => 'We deliver orders promptly to supported locations.'],
                    'kk' => ['title' => 'Жылдам жеткізу', 'text' => 'Тапсырыстарды қызмет көрсетілетін өңірлерге жедел жеткіземіз.'],
                ],
            ],
            // Группа 1, преимущество 2.
            [
                'icon' => 'map-pin',
                'translations' => [
                    'ru' => ['title' => 'Доставка по Казахстану', 'text' => 'Отправляем товары в города и регионы Казахстана.'],
                    'en' => ['title' => 'Delivery across Kazakhstan', 'text' => 'We ship products to cities and regions across Kazakhstan.'],
                    'kk' => ['title' => 'Қазақстан бойынша жеткізу', 'text' => 'Тауарларды Қазақстанның қалалары мен өңірлеріне жібереміз.'],
                ],
            ],
            // Группа 1, преимущество 3.
            [
                'icon' => 'route',
                'translations' => [
                    'ru' => ['title' => 'Отслеживание заказа', 'text' => 'Следите за этапами доставки вашего заказа.'],
                    'en' => ['title' => 'Order tracking', 'text' => 'Follow your order through each delivery stage.'],
                    'kk' => ['title' => 'Тапсырысты қадағалау', 'text' => 'Тапсырысыңыздың жеткізілу кезеңдерін бақылаңыз.'],
                ],
            ],
            // Группа 1, преимущество 4.
            [
                'icon' => 'package-check',
                'translations' => [
                    'ru' => ['title' => 'Удобное получение', 'text' => 'Выбирайте доступный способ получения заказа.'],
                    'en' => ['title' => 'Convenient pickup', 'text' => 'Choose an available way to receive your order.'],
                    'kk' => ['title' => 'Ыңғайлы алу', 'text' => 'Тапсырысты алудың қолжетімді тәсілін таңдаңыз.'],
                ],
            ],
            // Группа 2, преимущество 1.
            [
                'icon' => 'shield-check',
                'translations' => [
                    'ru' => ['title' => 'Безопасная оплата', 'text' => 'Используем защищённые способы проведения платежей.'],
                    'en' => ['title' => 'Secure payments', 'text' => 'We use protected payment methods.'],
                    'kk' => ['title' => 'Қауіпсіз төлем', 'text' => 'Қорғалған төлем тәсілдерін қолданамыз.'],
                ],
            ],
            // Группа 2, преимущество 2.
            [
                'icon' => 'credit-card',
                'translations' => [
                    'ru' => ['title' => 'Несколько способов оплаты', 'text' => 'Оплачивайте заказ удобным доступным способом.'],
                    'en' => ['title' => 'Flexible payment options', 'text' => 'Pay using the available method that suits you.'],
                    'kk' => ['title' => 'Төлем тәсілдерінің таңдауы', 'text' => 'Өзіңізге ыңғайлы қолжетімді тәсілмен төлеңіз.'],
                ],
            ],
            // Группа 2, преимущество 3.
            [
                'icon' => 'badge-percent',
                'translations' => [
                    'ru' => ['title' => 'Прозрачные цены', 'text' => 'Стоимость товаров понятна до оформления заказа.'],
                    'en' => ['title' => 'Transparent pricing', 'text' => 'See clear product prices before checkout.'],
                    'kk' => ['title' => 'Ашық бағалар', 'text' => 'Тапсырысты рәсімдемей тұрып тауар бағасын көресіз.'],
                ],
            ],
            // Группа 2, преимущество 4.
            [
                'icon' => 'receipt-text',
                'translations' => [
                    'ru' => ['title' => 'Электронные чеки', 'text' => 'Получайте подтверждение оплаты в электронном виде.'],
                    'en' => ['title' => 'Digital receipts', 'text' => 'Receive payment confirmation electronically.'],
                    'kk' => ['title' => 'Электрондық түбіртектер', 'text' => 'Төлем растамасын электрондық түрде алыңыз.'],
                ],
            ],
            // Группа 3, преимущество 1.
            [
                'icon' => 'clipboard-check',
                'translations' => [
                    'ru' => ['title' => 'Проверенные товары', 'text' => 'Предлагаем товары с понятными характеристиками.'],
                    'en' => ['title' => 'Carefully selected products', 'text' => 'Explore products with clear specifications.'],
                    'kk' => ['title' => 'Тексерілген тауарлар', 'text' => 'Сипаттамалары түсінікті тауарларды ұсынамыз.'],
                ],
            ],
            // Группа 3, преимущество 2.
            [
                'icon' => 'award',
                'translations' => [
                    'ru' => ['title' => 'Гарантия качества', 'text' => 'Условия гарантии указаны для соответствующих товаров.'],
                    'en' => ['title' => 'Quality assurance', 'text' => 'Warranty terms are provided for eligible products.'],
                    'kk' => ['title' => 'Сапа кепілдігі', 'text' => 'Кепілдік шарттары тиісті тауарлар үшін көрсетіледі.'],
                ],
            ],
            // Группа 3, преимущество 3.
            [
                'icon' => 'badge-check',
                'translations' => [
                    'ru' => ['title' => 'Оригинальная продукция', 'text' => 'Помогаем выбрать продукцию заявленных брендов.'],
                    'en' => ['title' => 'Authentic products', 'text' => 'Find products from the brands shown in our catalog.'],
                    'kk' => ['title' => 'Түпнұсқа өнімдер', 'text' => 'Каталогта көрсетілген брендтердің өнімдерін таңдаңыз.'],
                ],
            ],
            // Группа 3, преимущество 4.
            [
                'icon' => 'scan-search',
                'translations' => [
                    'ru' => ['title' => 'Контроль перед отправкой', 'text' => 'Проверяем комплектацию заказа перед передачей в доставку.'],
                    'en' => ['title' => 'Pre-shipment checks', 'text' => 'We check order contents before dispatch.'],
                    'kk' => ['title' => 'Жіберер алдындағы тексеру', 'text' => 'Жеткізуге берер алдында тапсырыс құрамын тексереміз.'],
                ],
            ],
            // Группа 4, преимущество 1.
            [
                'icon' => 'layout-grid',
                'translations' => [
                    'ru' => ['title' => 'Широкий выбор', 'text' => 'Находите товары для разных задач в одном каталоге.'],
                    'en' => ['title' => 'Wide selection', 'text' => 'Find products for different needs in one catalog.'],
                    'kk' => ['title' => 'Кең таңдау', 'text' => 'Әртүрлі қажеттілікке арналған тауарларды бір каталогтан табыңыз.'],
                ],
            ],
            // Группа 4, преимущество 2.
            [
                'icon' => 'sparkles',
                'translations' => [
                    'ru' => ['title' => 'Новые поступления', 'text' => 'Следите за обновлениями ассортимента магазина.'],
                    'en' => ['title' => 'New arrivals', 'text' => 'Discover the latest additions to our catalog.'],
                    'kk' => ['title' => 'Жаңа тауарлар', 'text' => 'Дүкен ассортиментінің жаңаруын қадағалаңыз.'],
                ],
            ],
            // Группа 4, преимущество 3.
            [
                'icon' => 'tags',
                'translations' => [
                    'ru' => ['title' => 'Известные бренды', 'text' => 'Сравнивайте предложения разных производителей.'],
                    'en' => ['title' => 'Popular brands', 'text' => 'Compare products from different manufacturers.'],
                    'kk' => ['title' => 'Танымал брендтер', 'text' => 'Әртүрлі өндірушілердің ұсыныстарын салыстырыңыз.'],
                ],
            ],
            // Группа 4, преимущество 4.
            [
                'icon' => 'search',
                'translations' => [
                    'ru' => ['title' => 'Удобный поиск', 'text' => 'Быстро находите нужные товары по каталогу.'],
                    'en' => ['title' => 'Easy product search', 'text' => 'Quickly find the products you need.'],
                    'kk' => ['title' => 'Ыңғайлы іздеу', 'text' => 'Қажетті тауарларды каталогтан жылдам табыңыз.'],
                ],
            ],
            // Группа 5, преимущество 1.
            [
                'icon' => 'wallet',
                'translations' => [
                    'ru' => ['title' => 'Выгодные цены', 'text' => 'Сравнивайте варианты и выбирайте подходящий бюджет.'],
                    'en' => ['title' => 'Great value', 'text' => 'Compare options and find a fit for your budget.'],
                    'kk' => ['title' => 'Тиімді бағалар', 'text' => 'Нұсқаларды салыстырып, бюджетіңізге сай тауарды таңдаңыз.'],
                ],
            ],
            // Группа 5, преимущество 2.
            [
                'icon' => 'percent',
                'translations' => [
                    'ru' => ['title' => 'Акции и скидки', 'text' => 'Узнавайте об актуальных скидках на товары.'],
                    'en' => ['title' => 'Deals and discounts', 'text' => 'Explore current discounts on selected products.'],
                    'kk' => ['title' => 'Акциялар мен жеңілдіктер', 'text' => 'Таңдаулы тауарларға арналған ағымдағы жеңілдіктерді көріңіз.'],
                ],
            ],
            // Группа 5, преимущество 3.
            [
                'icon' => 'gift',
                'translations' => [
                    'ru' => ['title' => 'Специальные предложения', 'text' => 'Находите интересные предложения в каталоге.'],
                    'en' => ['title' => 'Special offers', 'text' => 'Discover featured offers in our catalog.'],
                    'kk' => ['title' => 'Арнайы ұсыныстар', 'text' => 'Каталогтағы арнайы ұсыныстарды табыңыз.'],
                ],
            ],
            // Группа 5, преимущество 4.
            [
                'icon' => 'heart-handshake',
                'translations' => [
                    'ru' => ['title' => 'Программа лояльности', 'text' => 'Пользуйтесь преимуществами программы при её наличии.'],
                    'en' => ['title' => 'Loyalty benefits', 'text' => 'Enjoy loyalty benefits where the program is available.'],
                    'kk' => ['title' => 'Адалдық бағдарламасы', 'text' => 'Бағдарлама қолжетімді болса, оның артықшылықтарын пайдаланыңыз.'],
                ],
            ],
            // Группа 6, преимущество 1.
            [
                'icon' => 'headset',
                'translations' => [
                    'ru' => ['title' => 'Поддержка клиентов', 'text' => 'Помогаем разобраться с вопросами по заказам.'],
                    'en' => ['title' => 'Customer support', 'text' => 'Get help with questions about your orders.'],
                    'kk' => ['title' => 'Клиенттерді қолдау', 'text' => 'Тапсырысқа қатысты сұрақтарға жауап алуға көмектесеміз.'],
                ],
            ],
            // Группа 6, преимущество 2.
            [
                'icon' => 'messages-square',
                'translations' => [
                    'ru' => ['title' => 'Помощь с выбором', 'text' => 'Подскажем, на какие характеристики обратить внимание.'],
                    'en' => ['title' => 'Help choosing products', 'text' => 'Learn which specifications matter for your needs.'],
                    'kk' => ['title' => 'Таңдауға көмек', 'text' => 'Қандай сипаттамаларға назар аудару керегін түсіндіреміз.'],
                ],
            ],
            // Группа 6, преимущество 3.
            [
                'icon' => 'user-round-check',
                'translations' => [
                    'ru' => ['title' => 'Консультации специалистов', 'text' => 'Получайте консультации по доступным категориям товаров.'],
                    'en' => ['title' => 'Expert guidance', 'text' => 'Get advice on supported product categories.'],
                    'kk' => ['title' => 'Маман кеңесі', 'text' => 'Қолжетімді тауар санаттары бойынша кеңес алыңыз.'],
                ],
            ],
            // Группа 6, преимущество 4.
            [
                'icon' => 'message-circle',
                'translations' => [
                    'ru' => ['title' => 'Обратная связь', 'text' => 'Делитесь вопросами и предложениями по работе магазина.'],
                    'en' => ['title' => 'Feedback welcome', 'text' => 'Share questions and suggestions about our store.'],
                    'kk' => ['title' => 'Кері байланыс', 'text' => 'Дүкен жұмысына қатысты сұрақтар мен ұсыныстарыңызды жіберіңіз.'],
                ],
            ],
            // Группа 7, преимущество 1.
            [
                'icon' => 'shopping-cart',
                'translations' => [
                    'ru' => ['title' => 'Простой заказ', 'text' => 'Добавляйте товары в корзину и оформляйте покупку.'],
                    'en' => ['title' => 'Simple checkout', 'text' => 'Add products to your cart and place an order.'],
                    'kk' => ['title' => 'Оңай тапсырыс', 'text' => 'Тауарларды себетке қосып, тапсырысты рәсімдеңіз.'],
                ],
            ],
            // Группа 7, преимущество 2.
            [
                'icon' => 'user-round',
                'translations' => [
                    'ru' => ['title' => 'Удобный личный кабинет', 'text' => 'Просматривайте свои заказы в одном месте.'],
                    'en' => ['title' => 'Convenient account', 'text' => 'View your orders in one place.'],
                    'kk' => ['title' => 'Ыңғайлы жеке кабинет', 'text' => 'Тапсырыстарыңызды бір жерден қараңыз.'],
                ],
            ],
            // Группа 7, преимущество 3.
            [
                'icon' => 'rotate-ccw',
                'translations' => [
                    'ru' => ['title' => 'Понятный возврат', 'text' => 'Ознакомьтесь с условиями возврата перед покупкой.'],
                    'en' => ['title' => 'Clear return terms', 'text' => 'Review return conditions before purchasing.'],
                    'kk' => ['title' => 'Қайтарудың түсінікті шарттары', 'text' => 'Сатып алмас бұрын қайтару шарттарымен танысыңыз.'],
                ],
            ],
            // Группа 7, преимущество 4.
            [
                'icon' => 'lock-keyhole',
                'translations' => [
                    'ru' => ['title' => 'Защита персональных данных', 'text' => 'Бережно относимся к данным, необходимым для заказа.'],
                    'en' => ['title' => 'Personal data protection', 'text' => 'We handle the personal data needed for orders with care.'],
                    'kk' => ['title' => 'Жеке деректерді қорғау', 'text' => 'Тапсырысқа қажет жеке деректерді ұқыпты өңдейміз.'],
                ],
            ],
        ];

        DB::transaction(function () use ($items): void {
            foreach ($items as $item) {
                // Русский заголовок служит стабильным ключом повторного запуска
                // в рамках этого демонстрационного набора данных.
                $existing = SliderSlideAdvantage::query()
                    ->whereHas('translations', function ($query) use ($item): void {
                        $query->where('locale', 'ru')
                            ->where('title', $item['translations']['ru']['title']);
                    })
                    ->first();

                // При повторном запуске не перезаписываем оформление существующей записи.
                $advantage = $existing ?? SliderSlideAdvantage::query()->create([
                    'activity' => true,
                    'icon_type' => 'lucide',
                    'icon' => $item['icon'],
                    'icon_color' => null,
                    'style' => 'default',
                    'settings' => null,
                ]);

                foreach ($item['translations'] as $locale => $translation) {
                    // Добавляем недостающие переводы, сохраняя ручные правки CRUD.
                    $advantage->translations()->firstOrCreate(
                        ['locale' => $locale],
                        $translation
                    );
                }
            }
        });
    }
}
