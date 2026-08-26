<?php

namespace App\Http\Resources\Admin\School\SchoolQuizQuestion;

use App\Http\Resources\Admin\School\SchoolQuiz\SchoolQuizSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolQuizQuestionResource extends JsonResource
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
         * все translations вопроса.
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

            'sort' =>
                (int) $this->sort,

            'question_type' =>
                $this->question_type,

            /**
             * Текущий перевод.
             *
             * Только из уже загруженной
             * коллекции translations.
             */
            'translation' => $translation
                ? new SchoolQuizQuestionTranslationResource(
                    $translation
                )
                : null,

            /**
             * Все переводы нужны Edit.
             */
            'translations' =>
                SchoolQuizQuestionTranslationResource::collection(
                    $this->whenLoaded(
                        'translations'
                    )
                ),

            'points' =>
                (int) $this->points,

            'meta' =>
                $this->meta,

            'activity' =>
                (bool) $this->activity,

            /**
             * Родительский квиз.
             *
             * Controller загружает только
             * выбранную locale.
             */
            'quiz' =>
                new SchoolQuizSharedResource(
                    $this->whenLoaded(
                        'quiz'
                    )
                ),

            /**
             * Ответы.
             *
             * Для связанной сущности нужен
             * только перевод текущей locale.
             */
            'answers' => $this->whenLoaded(
                'answers',
                fn () => $this->answers->map(
                    function ($answer) {
                        $translation = $answer->relationLoaded(
                            'translations'
                        )
                            ? $answer->translations->first()
                            : null;

                        return [
                            'id' =>
                                $answer->id,

                            'school_quiz_id' =>
                                $answer->school_quiz_id,

                            'school_quiz_question_id' =>
                                $answer->school_quiz_question_id,

                            'translation' => $translation
                                ? [
                                    'locale' =>
                                        $translation->locale,

                                    'text' =>
                                        $translation->text,

                                    'explanation' =>
                                        $translation->explanation,
                                ]
                                : null,

                            'is_correct' =>
                                (bool) $answer->is_correct,

                            'weight' =>
                                (int) $answer->weight,

                            'sort' =>
                                (int) $answer->sort,

                            'activity' =>
                                (bool) $answer->activity,
                        ];
                    }
                )
            ),

            /**
             * Элементы попыток.
             */
            'attempt_items' => $this->whenLoaded(
                'attemptItems',
                fn () => $this->attemptItems->map(
                    fn ($item) => [
                        'id' =>
                            $item->id,

                        'school_quiz_attempt_id' =>
                            $item->school_quiz_attempt_id,

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

            /**
             * Counts.
             */
            'answers_count' => $this->when(
                isset($this->answers_count),
                fn () => (int) $this->answers_count
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
