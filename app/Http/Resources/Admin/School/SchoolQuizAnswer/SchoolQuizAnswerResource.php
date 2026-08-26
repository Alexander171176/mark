<?php

namespace App\Http\Resources\Admin\School\SchoolQuizAnswer;

use App\Http\Resources\Admin\School\SchoolQuiz\SchoolQuizSharedResource;
use App\Http\Resources\Admin\School\SchoolQuizQuestion\SchoolQuizQuestionSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolQuizAnswerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        /**
         * Текущий перевод определяем
         * только из уже загруженной
         * коллекции translations.
         *
         * Никакого обращения
         * к relation translation().
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

            'school_quiz_id' =>
                $this->school_quiz_id,

            'school_quiz_question_id' =>
                $this->school_quiz_question_id,

            /**
             * Текущий перевод.
             */
            'translation' => $translation
                ? new SchoolQuizAnswerTranslationResource(
                    $translation
                )
                : null,

            /**
             * Все переводы нужны Edit
             * самой сущности SchoolQuizAnswer.
             *
             * Если Controller загрузил только
             * currentLocale, Resource использует
             * только эту уже загруженную коллекцию.
             */
            'translations' =>
                SchoolQuizAnswerTranslationResource::collection(
                    $this->whenLoaded(
                        'translations'
                    )
                ),

            'is_correct' =>
                (bool) $this->is_correct,

            'weight' =>
                (int) $this->weight,

            'sort' =>
                (int) $this->sort,

            'activity' =>
                (bool) $this->activity,

            /**
             * Родительский Quiz.
             */
            'quiz' =>
                new SchoolQuizSharedResource(
                    $this->whenLoaded(
                        'quiz'
                    )
                ),

            /**
             * Родительский Question.
             */
            'question' =>
                new SchoolQuizQuestionSharedResource(
                    $this->whenLoaded(
                        'question'
                    )
                ),

            /**
             * Использования ответа
             * в попытках.
             */
            'attempt_items' => $this->whenLoaded(
                'attemptItems',
                fn () => $this->attemptItems->map(
                    fn ($item) => [
                        'id' =>
                            $item->id,

                        'school_quiz_attempt_id' =>
                            $item->school_quiz_attempt_id,

                        'school_quiz_question_id' =>
                            $item->school_quiz_question_id,

                        'selected_answer_id' =>
                            $item->selected_answer_id,

                        'selected_answer_ids' =>
                            $item->selected_answer_ids,

                        'free_text_answer' =>
                            $item->free_text_answer,

                        'is_correct' =>
                            (bool) $item->is_correct,

                        'score' =>
                            (int) $item->score,

                        'max_score' =>
                            (int) $item->max_score,
                    ]
                )
            ),

            'attempt_items_count' => $this->when(
                isset($this->attempt_items_count),
                fn () => (int) $this->attempt_items_count
            ),

            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
