<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Запускаем сидеры после регистрации первого суперпользователя
        $this->call([
//            RoleSeeder::class,

            // Настройки
            SettingSeeder::class,

            // Обработка изображений
            ImagePresetSeeder::class,

            // Блог
            BlogRubricSeeder::class,
            BlogTagSeeder::class,
            BlogArticleSeeder::class,

            // Мультивалютность
            CurrencySeeder::class,
            CurrencyRateSeeder::class,

            // Хештеги
            SchoolHashtagSeeder::class,
            SchoolHashtagTranslationSeeder::class,

            // Пользователи
            UserSeeder::class,

            // Инструкторы
            SchoolInstructorProfileSeeder::class,
            SchoolInstructorProfileTranslationSeeder::class,

            // Треки
            SchoolTrackSeeder::class,
            SchoolTrackTranslationSeeder::class,

            // Курсы
            SchoolCourseSeeder::class,
            SchoolCourseTranslationSeeder::class,

            // Модули
            SchoolModuleSeeder::class,
            SchoolModuleTranslationSeeder::class,

            // Уроки
            SchoolLessonSeeder::class,
            SchoolLessonTranslationSeeder::class,

            // Задания
            SchoolAssignmentSeeder::class,
            SchoolAssignmentTranslationSeeder::class,

            // Потоки
            SchoolCourseScheduleSeeder::class,
            SchoolCourseScheduleTranslationSeeder::class,

            // Записи
            SchoolCohortEnrollmentSeeder::class,

            // Заказы
            SchoolOrderSeeder::class,

            // Зачисления
            SchoolEnrollmentSeeder::class,

            // Квизы
            SchoolQuizSeeder::class,
            SchoolQuizTranslationSeeder::class,

            // Вопросы
            SchoolQuizQuestionSeeder::class,
            SchoolQuizQuestionTranslationSeeder::class,

            // Ответы
            SchoolQuizAnswerSeeder::class,
            SchoolQuizAnswerTranslationSeeder::class,

            // Попытки
            SchoolQuizAttemptSeeder::class,
            SchoolQuizAttemptItemSeeder::class,

            // Бандлы
            SchoolBundleSeeder::class,
            SchoolBundleTranslationSeeder::class,

            // Цены
            SchoolCoursePriceSeeder::class,
            SchoolBundlePriceSeeder::class,

            // Подписки
            SchoolSubscriptionPlanSeeder::class,
            SchoolSubscriptionPlanTranslationSeeder::class,

            // Заказы
            SchoolOrderItemSeeder::class,

            // Комментарии
            CommentsSeeder::class,

            // Конструктор страниц
            CmsPageSeeder::class,

            //  Маркет
            MarketCompanySeeder::class, // Фирмы
            MarketShopSeeder::class,    // Магазины
            MarketBrandSeeder::class,   // Бренды
            MarketTagSeeder::class,     // Теги
            MarketProductSeeder::class, // Товары

            ReviewSeeder::class, // Отзывы

            // Характеристики магазина
            MarketAttributeGroupSeeder::class, // Группы характеристик
            MarketAttributeSeeder::class,      // Характеристики
            MarketAttributeValueSeeder::class, // Значения характеристик

            MarketProductVariantSeeder::class,
            MarketProductBundleSeeder::class,

// Добавьте сюда другие сидеры по мере необходимости
        ]);
    }
}
