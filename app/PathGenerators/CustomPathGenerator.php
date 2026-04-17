<?php

namespace App\PathGenerators;

use App\Models\Admin\Blog\Article\Article;
use App\Models\Admin\Blog\Article\ArticleImage;
use App\Models\Admin\Blog\Banner\Banner;
use App\Models\Admin\Blog\Banner\BannerImage;
use App\Models\Admin\Blog\Rubric\Rubric;
use App\Models\Admin\Blog\Rubric\RubricImage;
use App\Models\Admin\Blog\Video\Video;
use App\Models\Admin\Blog\Video\VideoImage;
use App\Models\Admin\Constructor\HomePage\Component\ComponentTile;
use App\Models\Admin\Constructor\HomePage\Demo\DemoItem;
use App\Models\Admin\Constructor\HomePage\Hero\HeroScreenshot;
use App\Models\Admin\Constructor\HomePage\Quality\QualitySection;
use App\Models\Admin\Constructor\HomePage\Quickstart\QuickstartSection;
use App\Models\Admin\Constructor\HomePage\Wave\WaveTech;
use App\Models\Admin\Finance\SubscriptionPlan\SubscriptionPlan;
use App\Models\Admin\Finance\SubscriptionPlan\SubscriptionPlanImage;
use App\Models\Admin\School\Assignment\Assignment;
use App\Models\Admin\School\Assignment\AssignmentImage;
use App\Models\Admin\School\Bundle\Bundle;
use App\Models\Admin\School\Bundle\BundleImage;
use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\Course\CourseImage;
use App\Models\Admin\School\CourseSchedule\CourseSchedule;
use App\Models\Admin\School\CourseSchedule\CourseScheduleImage;
use App\Models\Admin\School\InstructorProfile\InstructorProfile;
use App\Models\Admin\School\InstructorProfile\InstructorProfileImage;
use App\Models\Admin\School\LearningCategory\LearningCategory;
use App\Models\Admin\School\LearningCategory\LearningCategoryImage;
use App\Models\Admin\School\Lesson\Lesson;
use App\Models\Admin\School\Lesson\LessonImage;
use App\Models\Admin\School\Module\Module;
use App\Models\Admin\School\Module\ModuleImage;
use App\Models\Admin\School\Quiz\Quiz;
use App\Models\Admin\School\Quiz\QuizImage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        // === уже существующие ===
        if ($media->model_type === Article::class)      return 'articles/'.$media->model_id.'/';
        if ($media->model_type === ArticleImage::class) return 'article_images/'.$media->model_id.'/';
        if ($media->model_type === Rubric::class)      return 'rubrics/'.$media->model_id.'/';
        if ($media->model_type === RubricImage::class) return 'rubric_images/'.$media->model_id.'/';
        if ($media->model_type === Banner::class)       return 'banners/'.$media->model_id.'/';
        if ($media->model_type === BannerImage::class)  return 'banner_images/'.$media->model_id.'/';
        if ($media->model_type === Video::class)        return 'videos/'.$media->model_id.'/';
        if ($media->model_type === VideoImage::class)   return 'video_images/'.$media->model_id.'/';
        if ($media->model_type === InstructorProfile::class)       return 'instructor_profiles/'.$media->model_id.'/';
        if ($media->model_type === InstructorProfileImage::class)  return 'instructor_profile_images/'.$media->model_id.'/';
        if ($media->model_type === LearningCategory::class)        return 'learning_categories/'.$media->model_id.'/';
        if ($media->model_type === LearningCategoryImage::class)   return 'learning_category_images/'.$media->model_id.'/';
        if ($media->model_type === Course::class)        return 'courses/'.$media->model_id.'/';
        if ($media->model_type === CourseImage::class)   return 'course_images/'.$media->model_id.'/';
        if ($media->model_type === Module::class)        return 'modules/'.$media->model_id.'/';
        if ($media->model_type === ModuleImage::class)   return 'module_images/'.$media->model_id.'/';
        if ($media->model_type === Lesson::class)        return 'lessons/'.$media->model_id.'/';
        if ($media->model_type === LessonImage::class)   return 'lesson_images/'.$media->model_id.'/';
        if ($media->model_type === Assignment::class)        return 'assignments/'.$media->model_id.'/';
        if ($media->model_type === AssignmentImage::class)   return 'assignment_images/'.$media->model_id.'/';
        if ($media->model_type === CourseSchedule::class)        return 'course_schedules/'.$media->model_id.'/';
        if ($media->model_type === CourseScheduleImage::class)   return 'course_schedule_images/'.$media->model_id.'/';
        if ($media->model_type === Quiz::class)        return 'quizzes/'.$media->model_id.'/';
        if ($media->model_type === QuizImage::class)   return 'quiz_images/'.$media->model_id.'/';
        if ($media->model_type === Bundle::class)        return 'bundles/'.$media->model_id.'/';
        if ($media->model_type === BundleImage::class)   return 'bundle_images/'.$media->model_id.'/';
        if ($media->model_type === SubscriptionPlan::class)        return 'subscription_plans/'.$media->model_id.'/';
        if ($media->model_type === SubscriptionPlanImage::class)   return 'subscription_plan_images/'.$media->model_id.'/';

        // Hero — скриншоты
        if ($media->model_type === HeroScreenshot::class) {
            return 'home/hero/screenshots/'.$media->model_id.'/';
        }

        // Wave — лого/иконки технологий (light/dark)
        if ($media->model_type === WaveTech::class) {
            return 'home/wave/tech/'.$media->model_id.'/';
        }

        // Quickstart — видео и постер
        if ($media->model_type === QuickstartSection::class) {
            return 'home/quickstart/sections/'.$media->model_id.'/';
        }

        // Demo — превью карточек (light/dark)
        if ($media->model_type === DemoItem::class) {
            return 'home/demos/items/'.$media->model_id.'/';
        }

        // Quality — изображения секции (light/dark) РАЗВОДИМ ПО КОЛЛЕКЦИЯМ
        if ($media->model_type === QualitySection::class) {
            return 'home/quality/sections/'.$media->model_id.'/'.$media->collection_name.'/';
        }

        // Component — изображения секции (light/dark)
        if ($media->model_type === ComponentTile::class) {
            return 'home/components/tiles/'.$media->model_id.'/';
        }

        // Дефолт
        return 'media/'.$media->model_id.'/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media).'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media).'responsive/';
    }
}
