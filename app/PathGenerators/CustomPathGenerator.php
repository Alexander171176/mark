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
        // === блог (новая мультиязычная архитектура) ===
        if ($media->model_type === BlogArticle::class)      return 'blog_articles/' . $media->model_id . '/';
        if ($media->model_type === BlogArticleImage::class) return 'blog_article_images/' . $media->model_id . '/';

        if ($media->model_type === BlogRubric::class)       return 'blog_rubrics/' . $media->model_id . '/';
        if ($media->model_type === BlogRubricImage::class)  return 'blog_rubric_images/' . $media->model_id . '/';

        if ($media->model_type === BlogBanner::class)       return 'blog_banners/' . $media->model_id . '/';
        if ($media->model_type === BlogBannerImage::class)  return 'blog_banner_images/' . $media->model_id . '/';

        if ($media->model_type === BlogVideo::class)        return 'blog_videos/' . $media->model_id . '/';
        if ($media->model_type === BlogVideoImage::class)   return 'blog_video_images/' . $media->model_id . '/';

        // === школа ===
        if ($media->model_type === InstructorProfile::class)      return 'instructor_profiles/' . $media->model_id . '/';
        if ($media->model_type === InstructorProfileImage::class) return 'instructor_profile_images/' . $media->model_id . '/';

        if ($media->model_type === LearningCategory::class)       return 'learning_categories/' . $media->model_id . '/';
        if ($media->model_type === LearningCategoryImage::class)  return 'learning_category_images/' . $media->model_id . '/';

        if ($media->model_type === Course::class)       return 'courses/' . $media->model_id . '/';
        if ($media->model_type === CourseImage::class)  return 'course_images/' . $media->model_id . '/';

        if ($media->model_type === Module::class)       return 'modules/' . $media->model_id . '/';
        if ($media->model_type === ModuleImage::class)  return 'module_images/' . $media->model_id . '/';

        if ($media->model_type === Lesson::class)       return 'lessons/' . $media->model_id . '/';
        if ($media->model_type === LessonImage::class)  return 'lesson_images/' . $media->model_id . '/';

        if ($media->model_type === Assignment::class)       return 'assignments/' . $media->model_id . '/';
        if ($media->model_type === AssignmentImage::class)  return 'assignment_images/' . $media->model_id . '/';

        if ($media->model_type === CourseSchedule::class)       return 'course_schedules/' . $media->model_id . '/';
        if ($media->model_type === CourseScheduleImage::class)  return 'course_schedule_images/' . $media->model_id . '/';

        if ($media->model_type === Quiz::class)       return 'quizzes/' . $media->model_id . '/';
        if ($media->model_type === QuizImage::class)  return 'quiz_images/' . $media->model_id . '/';

        if ($media->model_type === Bundle::class)       return 'bundles/' . $media->model_id . '/';
        if ($media->model_type === BundleImage::class)  return 'bundle_images/' . $media->model_id . '/';

        // === финансы ===
        if ($media->model_type === SubscriptionPlan::class)      return 'subscription_plans/' . $media->model_id . '/';
        if ($media->model_type === SubscriptionPlanImage::class) return 'subscription_plan_images/' . $media->model_id . '/';

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
