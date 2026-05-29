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
use App\Models\Admin\School\Assignment\SchoolAssignment;
use App\Models\Admin\School\Assignment\SchoolAssignmentImage;
use App\Models\Admin\School\Bundle\SchoolBundle;
use App\Models\Admin\School\Bundle\SchoolBundleImage;
use App\Models\Admin\School\Course\SchoolCourse;
use App\Models\Admin\School\Course\SchoolCourseImage;
use App\Models\Admin\School\CourseSchedule\SchoolCourseSchedule;
use App\Models\Admin\School\CourseSchedule\SchoolCourseScheduleImage;
use App\Models\Admin\School\InstructorProfile\SchoolInstructorProfile;
use App\Models\Admin\School\InstructorProfile\SchoolInstructorProfileImage;
use App\Models\Admin\School\Lesson\SchoolLesson;
use App\Models\Admin\School\Lesson\SchoolLessonImage;
use App\Models\Admin\School\Module\SchoolModule;
use App\Models\Admin\School\Module\SchoolModuleImage;
use App\Models\Admin\School\Quiz\SchoolQuiz;
use App\Models\Admin\School\Quiz\SchoolQuizImage;
use App\Models\Admin\School\SubscriptionPlan\SubscriptionPlan;
use App\Models\Admin\School\SubscriptionPlan\SubscriptionPlanImage;
use App\Models\Admin\School\Track\SchoolTrack;
use App\Models\Admin\School\Track\SchoolTrackImage;
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
        if ($media->model_type === SchoolInstructorProfile::class)      return 'school_instructor_profiles/' . $media->model_id . '/';
        if ($media->model_type === SchoolInstructorProfileImage::class) return 'school_instructor_profile_images/' . $media->model_id . '/';

        if ($media->model_type === SchoolTrack::class)       return 'school_tracks/' . $media->model_id . '/';
        if ($media->model_type === SchoolTrackImage::class)  return 'school_track_images/' . $media->model_id . '/';

        if ($media->model_type === SchoolCourse::class)       return 'school_courses/' . $media->model_id . '/';
        if ($media->model_type === SchoolCourseImage::class)  return 'school_course_images/' . $media->model_id . '/';

        if ($media->model_type === SchoolModule::class)       return 'school_modules/' . $media->model_id . '/';
        if ($media->model_type === SchoolModuleImage::class)  return 'school_module_images/' . $media->model_id . '/';

        if ($media->model_type === SchoolLesson::class)       return 'school_lessons/' . $media->model_id . '/';
        if ($media->model_type === SchoolLessonImage::class)  return 'school_lesson_images/' . $media->model_id . '/';

        if ($media->model_type === SchoolAssignment::class)       return 'school_assignments/' . $media->model_id . '/';
        if ($media->model_type === SchoolAssignmentImage::class)  return 'school_assignment_images/' . $media->model_id . '/';

        if ($media->model_type === SchoolCourseSchedule::class)       return 'school_course_schedules/' . $media->model_id . '/';
        if ($media->model_type === SchoolCourseScheduleImage::class)  return 'school_course_schedule_images/' . $media->model_id . '/';

        if ($media->model_type === SchoolQuiz::class)       return 'school_quizzes/' . $media->model_id . '/';
        if ($media->model_type === SchoolQuizImage::class)  return 'school_quiz_images/' . $media->model_id . '/';

        if ($media->model_type === SchoolBundle::class)       return 'school_bundles/' . $media->model_id . '/';
        if ($media->model_type === SchoolBundleImage::class)  return 'school_bundle_images/' . $media->model_id . '/';

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
