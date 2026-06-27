<?php

namespace Database\Seeders;

use App\Models\Admin\Market\MarketCategory\MarketCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketCategorySeeder extends Seeder
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
            $electronics = $this->createCategory(
                userId: $userId,
                parentId: null,
                level: 1,
                sort: 10,
                icon: 'computer',
                url: 'electronics',
                views: 240,
                translations: [
                    'ru' => ['Электроника', 'Техника и гаджеты', 'Смартфоны, ноутбуки, аксессуары и другая электроника.'],
                    'en' => ['Electronics', 'Tech and gadgets', 'Smartphones, laptops, accessories and electronics.'],
                    'kk' => ['Электроника', 'Техника және гаджеттер', 'Смартфондар, ноутбуктер, аксессуарлар және электроника.'],
                ]
            );

            $home = $this->createCategory(
                userId: $userId,
                parentId: null,
                level: 1,
                sort: 20,
                icon: 'home',
                url: 'home-and-living',
                views: 190,
                translations: [
                    'ru' => ['Дом и быт', 'Товары для дома', 'Мебель, посуда, текстиль и товары для уюта.'],
                    'en' => ['Home & Living', 'Goods for home', 'Furniture, dishes, textiles and home goods.'],
                    'kk' => ['Үй және тұрмыс', 'Үйге арналған тауарлар', 'Жиһаз, ыдыс, тоқыма және тұрмыстық тауарлар.'],
                ]
            );

            $fashion = $this->createCategory(
                userId: $userId,
                parentId: null,
                level: 1,
                sort: 30,
                icon: 'shirt',
                url: 'fashion',
                views: 210,
                translations: [
                    'ru' => ['Одежда и обувь', 'Мода и стиль', 'Одежда, обувь и аксессуары для всей семьи.'],
                    'en' => ['Fashion', 'Style and clothing', 'Clothes, shoes and accessories for everyone.'],
                    'kk' => ['Киім және аяқ киім', 'Сән және стиль', 'Киім, аяқ киім және аксессуарлар.'],
                ]
            );

            $beauty = $this->createCategory(
                userId: $userId,
                parentId: null,
                level: 1,
                sort: 40,
                icon: 'sparkles',
                url: 'beauty-and-health',
                views: 160,
                translations: [
                    'ru' => ['Красота и здоровье', 'Уход и здоровье', 'Косметика, уход, товары для здоровья и гигиены.'],
                    'en' => ['Beauty & Health', 'Care and wellness', 'Cosmetics, care, health and hygiene products.'],
                    'kk' => ['Сұлулық және денсаулық', 'Күтім және денсаулық', 'Косметика, күтім және денсаулық тауарлары.'],
                ]
            );

            $auto = $this->createCategory(
                userId: $userId,
                parentId: null,
                level: 1,
                sort: 50,
                icon: 'car',
                url: 'auto-products',
                views: 120,
                translations: [
                    'ru' => ['Автотовары', 'Товары для авто', 'Аксессуары, расходники и товары для автомобиля.'],
                    'en' => ['Auto Products', 'Car goods', 'Accessories, supplies and products for cars.'],
                    'kk' => ['Автотауарлар', 'Көлікке арналған тауарлар', 'Аксессуарлар және көлікке арналған өнімдер.'],
                ]
            );

            /*
             |--------------------------------------------------------------------------
             | LEVEL 2
             |--------------------------------------------------------------------------
             */
            $smartphones = $this->createCategory($userId, $electronics->id, 2, 10, 'mobile', 'smartphones', 180, [
                'ru' => ['Смартфоны', 'Мобильные устройства', 'Смартфоны, телефоны и мобильные устройства.'],
                'en' => ['Smartphones', 'Mobile devices', 'Smartphones, phones and mobile devices.'],
                'kk' => ['Смартфондар', 'Мобильді құрылғылар', 'Смартфондар және мобильді құрылғылар.'],
            ]);

            $laptops = $this->createCategory($userId, $electronics->id, 2, 20, 'laptop', 'laptops', 170, [
                'ru' => ['Ноутбуки', 'Компьютеры для работы', 'Ноутбуки, ультрабуки и портативные компьютеры.'],
                'en' => ['Laptops', 'Computers for work', 'Laptops, ultrabooks and portable computers.'],
                'kk' => ['Ноутбуктер', 'Жұмысқа арналған компьютерлер', 'Ноутбуктер және портативті компьютерлер.'],
            ]);

            $furniture = $this->createCategory($userId, $home->id, 2, 10, 'couch', 'furniture', 130, [
                'ru' => ['Мебель', 'Мебель для дома', 'Шкафы, столы, диваны, кресла и другая мебель.'],
                'en' => ['Furniture', 'Home furniture', 'Cabinets, tables, sofas, chairs and furniture.'],
                'kk' => ['Жиһаз', 'Үй жиһазы', 'Шкафтар, үстелдер, дивандар және креслолар.'],
            ]);

            $kitchen = $this->createCategory($userId, $home->id, 2, 20, 'utensils', 'kitchen-products', 115, [
                'ru' => ['Кухня', 'Товары для кухни', 'Посуда, кухонные аксессуары и товары для готовки.'],
                'en' => ['Kitchen', 'Kitchen products', 'Dishes, kitchen accessories and cooking goods.'],
                'kk' => ['Ас үй', 'Ас үй тауарлары', 'Ыдыс және ас үй аксессуарлары.'],
            ]);

            $mensClothing = $this->createCategory($userId, $fashion->id, 2, 10, 'user-tie', 'mens-clothing', 150, [
                'ru' => ['Мужская одежда', 'Одежда для мужчин', 'Футболки, брюки, куртки и деловая одежда.'],
                'en' => ['Men’s Clothing', 'Clothes for men', 'T-shirts, pants, jackets and business clothing.'],
                'kk' => ['Ерлер киімі', 'Ерлерге арналған киім', 'Футболкалар, шалбарлар және күртешелер.'],
            ]);

            $womensClothing = $this->createCategory($userId, $fashion->id, 2, 20, 'person-dress', 'womens-clothing', 155, [
                'ru' => ['Женская одежда', 'Одежда для женщин', 'Платья, блузки, брюки, верхняя одежда и аксессуары.'],
                'en' => ['Women’s Clothing', 'Clothes for women', 'Dresses, blouses, pants, outerwear and accessories.'],
                'kk' => ['Әйелдер киімі', 'Әйелдерге арналған киім', 'Көйлектер, блузкалар және сырт киім.'],
            ]);

            $cosmetics = $this->createCategory($userId, $beauty->id, 2, 10, 'wand-magic-sparkles', 'cosmetics', 100, [
                'ru' => ['Косметика', 'Декоративная косметика', 'Косметика для лица, глаз, губ и макияжа.'],
                'en' => ['Cosmetics', 'Makeup products', 'Cosmetics for face, eyes, lips and makeup.'],
                'kk' => ['Косметика', 'Сәндік косметика', 'Бетке, көзге және ерінге арналған косметика.'],
            ]);

            $carAccessories = $this->createCategory($userId, $auto->id, 2, 10, 'gears', 'car-accessories', 90, [
                'ru' => ['Аксессуары для авто', 'Автоаксессуары', 'Органайзеры, держатели, коврики и аксессуары.'],
                'en' => ['Car Accessories', 'Auto accessories', 'Organizers, holders, mats and accessories.'],
                'kk' => ['Көлік аксессуарлары', 'Автоаксессуарлар', 'Ұйымдастырғыштар, ұстағыштар және кілемшелер.'],
            ]);

            /*
             |--------------------------------------------------------------------------
             | LEVEL 3
             |--------------------------------------------------------------------------
             */
            $this->createCategory($userId, $smartphones->id, 3, 10, 'apple-whole', 'apple-iphone', 95, [
                'ru' => ['Apple iPhone', 'Смартфоны Apple', 'iPhone разных поколений и комплектаций.'],
                'en' => ['Apple iPhone', 'Apple smartphones', 'iPhone models of different generations.'],
                'kk' => ['Apple iPhone', 'Apple смартфондары', 'Әртүрлі буындағы iPhone модельдері.'],
            ]);

            $this->createCategory($userId, $smartphones->id, 3, 20, 'robot', 'android-smartphones', 105, [
                'ru' => ['Android смартфоны', 'Смартфоны на Android', 'Смартфоны Samsung, Xiaomi, Honor и других брендов.'],
                'en' => ['Android Smartphones', 'Android phones', 'Samsung, Xiaomi, Honor and other Android phones.'],
                'kk' => ['Android смартфондары', 'Android телефондары', 'Samsung, Xiaomi, Honor және басқа смартфондар.'],
            ]);

            $this->createCategory($userId, $laptops->id, 3, 10, 'briefcase', 'business-laptops', 88, [
                'ru' => ['Ноутбуки для работы', 'Бизнес-ноутбуки', 'Ноутбуки для офиса, учёбы и удалённой работы.'],
                'en' => ['Business Laptops', 'Work laptops', 'Laptops for office, study and remote work.'],
                'kk' => ['Жұмысқа арналған ноутбуктер', 'Бизнес ноутбуктер', 'Офиске, оқуға және қашықтан жұмысқа арналған.'],
            ]);

            $this->createCategory($userId, $furniture->id, 3, 10, 'bed', 'bedroom-furniture', 75, [
                'ru' => ['Мебель для спальни', 'Спальная мебель', 'Кровати, матрасы, комоды и прикроватные тумбы.'],
                'en' => ['Bedroom Furniture', 'Furniture for bedroom', 'Beds, mattresses, dressers and bedside tables.'],
                'kk' => ['Жатын бөлме жиһазы', 'Жатын бөлмеге арналған жиһаз', 'Кереуеттер, матрастар және тумбалар.'],
            ]);
        });
    }

    protected function createCategory(
        int $userId,
        ?int $parentId,
        int $level,
        int $sort,
        string $icon,
        string $url,
        int $views,
        array $translations
    ): Builder|Model
    {
        $category = MarketCategory::query()->updateOrCreate(
            ['url' => $url],
            [
                'user_id' => $userId,
                'parent_id' => $parentId,
                'level' => $level,
                'icon' => $icon,
                'in_menu' => true,
                'sort' => $sort,
                'activity' => true,
                'status' => 'published',
                'moderation_status' => 1,
                'moderated_by' => $userId,
                'moderated_at' => now(),
                'moderation_note' => null,
                'published_at' => now(),
                'show_from_at' => null,
                'show_to_at' => null,
                'views' => $views,
            ]
        );

        foreach ($translations as $locale => $data) {
            $category->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title' => $data[0],
                    'subtitle' => $data[1],
                    'short' => $data[2],
                    'description' => $data[2],
                    'meta_title' => $data[0],
                    'meta_keywords' => $data[0] . ', marketplace, market',
                    'meta_desc' => $data[2],
                ]
            );
        }

        return $category;
    }
}
