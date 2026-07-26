<?php

namespace App\PathGenerators;

use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogArticle\BlogArticleImage;
use App\Models\Admin\Blog\BlogBanner\BlogBanner;
use App\Models\Admin\Blog\BlogBanner\BlogBannerImage;
use App\Models\Admin\Blog\BlogRubric\BlogRubric;
use App\Models\Admin\Blog\BlogRubric\BlogRubricImage;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use App\Models\Admin\Blog\BlogVideo\BlogVideoImage;
use App\Models\Admin\Market\MarketBrand\MarketBrand;
use App\Models\Admin\Market\MarketBrand\MarketBrandImage;
use App\Models\Admin\Market\MarketCategory\MarketCategory;
use App\Models\Admin\Market\MarketCategory\MarketCategoryImage;
use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\Admin\Market\MarketProduct\MarketProductImage;
use App\Models\Admin\Market\MarketProductBundle\MarketProductBundle;
use App\Models\Admin\Market\MarketProductBundle\MarketProductBundleImage;
use App\Models\Admin\Market\MarketProductVariant\MarketProductVariant;
use App\Models\Admin\Market\MarketProductVariant\MarketProductVariantImage;
use App\Models\Admin\Market\MarketShop\MarketShop;
use App\Models\Admin\Market\MarketShop\MarketShopImage;
use App\Models\Admin\School\SchoolAssignment\SchoolAssignment;
use App\Models\Admin\School\SchoolAssignment\SchoolAssignmentImage;
use App\Models\Admin\School\SchoolBundle\SchoolBundle;
use App\Models\Admin\School\SchoolBundle\SchoolBundleImage;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolCourse\SchoolCourseImage;
use App\Models\Admin\School\SchoolCourseSchedule\SchoolCourseSchedule;
use App\Models\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleImage;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfile;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileImage;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use App\Models\Admin\School\SchoolLesson\SchoolLessonImage;
use App\Models\Admin\School\SchoolModule\SchoolModule;
use App\Models\Admin\School\SchoolModule\SchoolModuleImage;
use App\Models\Admin\School\SchoolQuiz\SchoolQuiz;
use App\Models\Admin\School\SchoolQuiz\SchoolQuizImage;
use App\Models\Admin\School\SchoolSubscriptionPlan\SchoolSubscriptionPlan;
use App\Models\Admin\School\SchoolSubscriptionPlan\SchoolSubscriptionPlanImage;
use App\Models\Admin\School\SchoolTrack\SchoolTrack;
use App\Models\Admin\School\SchoolTrack\SchoolTrackImage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        // === блог (новая мультиязычная архитектура) ===
        if ($media->model_type === BlogArticle::class)      return 'blog/blog_articles/' . $media->model_id . '/';
        if ($media->model_type === BlogArticleImage::class) return 'blog/blog_article_images/' . $media->model_id . '/';

        if ($media->model_type === BlogRubric::class)       return 'blog/blog_rubrics/' . $media->model_id . '/';
        if ($media->model_type === BlogRubricImage::class)  return 'blog/blog_rubric_images/' . $media->model_id . '/';

        if ($media->model_type === BlogBanner::class)       return 'blog/blog_banners/' . $media->model_id . '/';
        if ($media->model_type === BlogBannerImage::class)  return 'blog/blog_banner_images/' . $media->model_id . '/';

        if ($media->model_type === BlogVideo::class)        return 'blog/blog_videos/' . $media->model_id . '/';
        if ($media->model_type === BlogVideoImage::class)   return 'blog/blog_video_images/' . $media->model_id . '/';

        // === школа ===
        if ($media->model_type === SchoolInstructorProfile::class)      return 'school/school_instructor_profiles/' . $media->model_id . '/';
        if ($media->model_type === SchoolInstructorProfileImage::class) return 'school/school_instructor_profile_images/' . $media->model_id . '/';

        if ($media->model_type === SchoolTrack::class)       return 'school/school_tracks/' . $media->model_id . '/';
        if ($media->model_type === SchoolTrackImage::class)  return 'school/school_track_images/' . $media->model_id . '/';

        if ($media->model_type === SchoolCourse::class)       return 'school/school_courses/' . $media->model_id . '/';
        if ($media->model_type === SchoolCourseImage::class)  return 'school/school_course_images/' . $media->model_id . '/';

        if ($media->model_type === SchoolModule::class)       return 'school/school_modules/' . $media->model_id . '/';
        if ($media->model_type === SchoolModuleImage::class)  return 'school/school_module_images/' . $media->model_id . '/';

        if ($media->model_type === SchoolLesson::class)       return 'school/school_lessons/' . $media->model_id . '/';
        if ($media->model_type === SchoolLessonImage::class)  return 'school/school_lesson_images/' . $media->model_id . '/';

        if ($media->model_type === SchoolAssignment::class)       return 'school/school_assignments/' . $media->model_id . '/';
        if ($media->model_type === SchoolAssignmentImage::class)  return 'school/school_assignment_images/' . $media->model_id . '/';

        if ($media->model_type === SchoolCourseSchedule::class)       return 'school/school_course_schedules/' . $media->model_id . '/';
        if ($media->model_type === SchoolCourseScheduleImage::class)  return 'school/school_course_schedule_images/' . $media->model_id . '/';

        if ($media->model_type === SchoolQuiz::class)       return 'school/school_quizzes/' . $media->model_id . '/';
        if ($media->model_type === SchoolQuizImage::class)  return 'school/school_quiz_images/' . $media->model_id . '/';

        if ($media->model_type === SchoolBundle::class)       return 'school/school_bundles/' . $media->model_id . '/';
        if ($media->model_type === SchoolBundleImage::class)  return 'school/school_bundle_images/' . $media->model_id . '/';

        if ($media->model_type === SchoolSubscriptionPlan::class)      return 'school/school_subscription_plans/' . $media->model_id . '/';
        if ($media->model_type === SchoolSubscriptionPlanImage::class) return 'school/school_subscription_plan_images/' . $media->model_id . '/';

        // === маркет ===
        if ($media->model_type === MarketShop::class)      return 'market/market_shops/' . $media->model_id . '/';
        if ($media->model_type === MarketShopImage::class) return 'market/market_shop_images/' . $media->model_id . '/';

        if ($media->model_type === MarketCategory::class)      return 'market/market_categories/' . $media->model_id . '/';
        if ($media->model_type === MarketCategoryImage::class) return 'market/market_category_images/' . $media->model_id . '/';

        if ($media->model_type === MarketProduct::class)      return 'market/market_products/' . $media->model_id . '/';
        if ($media->model_type === MarketProductImage::class) return 'market/market_product_images/' . $media->model_id . '/';

        if ($media->model_type === MarketProductVariant::class)      return 'market/market_product_variants/' . $media->model_id . '/';
        if ($media->model_type === MarketProductVariantImage::class) return 'market/market_product_variant_images/' . $media->model_id . '/';

        if ($media->model_type === MarketProductBundle::class)      return 'market/market_product_bundles/' . $media->model_id . '/';
        if ($media->model_type === MarketProductBundleImage::class) return 'market/market_product_bundle_images/' . $media->model_id . '/';

        if ($media->model_type === MarketBrand::class)      return 'market/market_brands/' . $media->model_id . '/';
        if ($media->model_type === MarketBrandImage::class) return 'market/market_brand_images/' . $media->model_id . '/';


        // Дефолт
        return 'media/' . $media->model_id . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media) . 'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media) . 'responsive/';
    }
}
