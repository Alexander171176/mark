<?php

namespace App\Http\Resources\Admin\School\SchoolQuiz;

use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolLesson\SchoolLessonSharedResource;
use App\Http\Resources\Admin\School\SchoolModule\SchoolModuleSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolQuizResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        /**
         * Для Edit Controller загружает
         * все translations.
         *
         * Из них определяем текущий перевод
         * с fallback.
         */
        $translation = $this->relationLoaded('translations')
            ? (
            $this->translations->firstWhere(
                'locale',
                $locale
            )
                ?: $this->translations->firstWhere(
                'locale',
                $fallbackLocale
            )
                ?: $this->translations->first()
            )
            : null;

        return [
            'id' =>
                $this->id,

            'school_course_id' =>
                $this->school_course_id,

            'school_module_id' =>
                $this->school_module_id,

            'school_lesson_id' =>
                $this->school_lesson_id,

            'slug' =>
                $this->slug,

            /**
             * Текущий перевод.
             *
             * Только из уже загруженной
             * коллекции translations.
             */
            'translation' => $translation
                ? new SchoolQuizTranslationResource(
                    $translation
                )
                : null,

            /**
             * Все переводы нужны Edit.
             */
            'translations' =>
                SchoolQuizTranslationResource::collection(
                    $this->whenLoaded(
                        'translations'
                    )
                ),

            'type' =>
                $this->type,

            'attempts_limit' =>
                (int) $this->attempts_limit,

            'time_limit_minutes' =>
                $this->time_limit_minutes !== null
                    ? (int) $this->time_limit_minutes
                    : null,

            'pass_score' =>
                (int) $this->pass_score,

            'sort' =>
                (int) $this->sort,

            'activity' =>
                (bool) $this->activity,

            'left' =>
                (bool) $this->left,

            'main' =>
                (bool) $this->main,

            'right' =>
                (bool) $this->right,

            'published_at' =>
                $this->published_at?->toIso8601String(),

            /**
             * Изображения.
             *
             * Controller обязан загрузить
             * images.media.
             */
            'primary_image' => $this->whenLoaded(
                'images',
                fn () => $this->primary_image
                    ? new SchoolQuizImageResource(
                        $this->primary_image
                    )
                    : null
            ),

            'images' =>
                SchoolQuizImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            /**
             * Иерархия квиза.
             *
             * Для связанных сущностей Controller
             * загружает только выбранную локаль.
             */
            'course' =>
                new SchoolCourseSharedResource(
                    $this->whenLoaded(
                        'course'
                    )
                ),

            'module' =>
                new SchoolModuleSharedResource(
                    $this->whenLoaded(
                        'module'
                    )
                ),

            'lesson' =>
                new SchoolLessonSharedResource(
                    $this->whenLoaded(
                        'lesson'
                    )
                ),

            /**
             * Вопросы.
             *
             * Пока сохраняем существующую структуру
             * ответа API.
             */
            'questions' => $this->whenLoaded(
                'questions',
                fn () => $this->questions->map(
                    function ($question) {
                        $translation = $question->relationLoaded(
                            'translations'
                        )
                            ? $question->translations->first()
                            : null;

                        return [
                            'id' =>
                                $question->id,

                            'school_quiz_id' =>
                                $question->school_quiz_id,

                            'sort' =>
                                (int) $question->sort,

                            'question_type' =>
                                $question->question_type,

                            'points' =>
                                (int) $question->points,

                            'activity' =>
                                (bool) $question->activity,

                            'question_text' =>
                                $translation?->question_text,
                        ];
                    }
                )
            ),

            /**
             * Попытки.
             */
            'attempts' => $this->whenLoaded(
                'attempts',
                fn () => $this->attempts->map(
                    fn ($attempt) => [
                        'id' =>
                            $attempt->id,

                        'user_id' =>
                            $attempt->user_id,

                        'attempt_number' =>
                            (int) $attempt->attempt_number,

                        'score' =>
                            (int) $attempt->score,

                        'max_score' =>
                            (int) $attempt->max_score,

                        'percent' =>
                            (int) $attempt->percent,

                        'status' =>
                            $attempt->status,

                        'started_at' =>
                            optional(
                                $attempt->started_at
                            )->toIso8601String(),

                        'finished_at' =>
                            optional(
                                $attempt->finished_at
                            )->toIso8601String(),
                    ]
                )
            ),

            /**
             * Counts.
             */
            'questions_count' => $this->when(
                isset($this->questions_count),
                fn () => (int) $this->questions_count
            ),

            'attempts_count' => $this->when(
                isset($this->attempts_count),
                fn () => (int) $this->attempts_count
            ),

            'images_count' => $this->when(
                isset($this->images_count),
                fn () => (int) $this->images_count
            ),

            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
