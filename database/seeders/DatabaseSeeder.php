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
            SettingSeeder::class,
            RubricSeeder::class,
            ArticleSeeder::class,
            TagSeeder::class,
            HeroSectionSeeder::class,
            HeroIconSeeder::class,
            HeroScreenshotSeeder::class,
            WaveSectionSeeder::class,
            WaveTechSeeder::class,
            FeatureSectionSeeder::class,
            FeatureItemSeeder::class,
            DeveloperSectionSeeder::class,
            DeveloperItemSeeder::class,
            QuickstartSectionSeeder::class,
            DemoSectionSeeder::class,
            DemoGroupSeeder::class,
            DemoItemSeeder::class,
            QualitySectionSeeder::class,
            QualityItemSeeder::class,
            ComponentSectionSeeder::class,
            ComponentFeatureSeeder::class,
            ComponentTabSeeder::class,
            ComponentTileSeeder::class,
            ReasonSectionSeeder::class,
            ReasonItemSeeder::class,
            HashtagSeeder::class,
            InstructorProfileSeeder::class,
            CommentsSeeder::class,
            LearningCategorySeeder::class,
            CourseSeeder::class,
            ModuleSeeder::class,
            LessonSeeder::class,
            AssignmentSeeder::class,
            CourseScheduleSeeder::class,
            CohortEnrollmentSeeder::class,
            OrderSeeder::class,
            EnrollmentSeeder::class,
            QuizSeeder::class,
            QuizQuestionSeeder::class,
            QuizAnswerSeeder::class,
            QuizAttemptItemSeeder::class,
            BundleSeeder::class,
            DefaultCurrenciesSeeder::class,
            CoursePriceSeeder::class,
            BundlePriceSeeder::class,
            SubscriptionPlanSeeder::class,
            OrderItemSeeder::class,
            PaymentMethodSeeder::class,
            UserPaymentMethodSeeder::class,
            SubscriptionSeeder::class,
            CouponSeeder::class,
            InvoiceSeeder::class,
            ProviderAccountSeeder::class,
            PayoutSeeder::class,
            PayoutItemSeeder::class,
            ExchangeRateSeeder::class,
            WebhookEventSeeder::class,
            AssignmentSubmissionSeeder::class,
            ReviewSeeder::class,
            QaThreadSeeder::class,
            QaMessageSeeder::class,
            BookmarkSeeder::class,
            CertificateSeeder::class,
            PageSeeder::class,
            BlogPostSeeder::class,
            SeoMetaSeeder::class,
            LeadSeeder::class,
            EmailSubscriptionSeeder::class,
            NavigationMenuSeeder::class,
            NavigationItemSeeder::class,
            CmsBannerSeeder::class,
            RedirectSeeder::class,
            FormSubmissionSeeder::class,
            FaqSeeder::class,
            TestimonialSeeder::class,

            // Добавьте сюда другие сидеры по мере необходимости
        ]);
    }
}
