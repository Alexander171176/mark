<?php

namespace App\Http\Resources\Admin\School\SchoolQuizQuestion;

use App\Http\Resources\Admin\School\SchoolQuiz\SchoolQuizSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolQuizQuestionSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /**
         * Admin Index заранее загружает
         * только выбранную locale.
         */
        $translation = $this->relationLoaded(
            'translations'
        )
            ? $this->translations->first()
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
             * Перевод выбранной locale.
             */
            'translation' => $translation
                ? [
                    'locale' =>
                        $translation->locale,

                    'question_text' =>
                        $translation->question_text,

                    /**
                     * Нужен frontend-поиску.
                     */
                    'explanation' =>
                        $translation->explanation,
                ]
                : null,

            'points' =>
                (int) $this->points,

            'activity' =>
                (bool) $this->activity,

            /**
             * Родительский квиз.
             */
            'quiz' =>
                new SchoolQuizSharedResource(
                    $this->whenLoaded(
                        'quiz'
                    )
                ),

            /**
             * Ответы нужны Index для
             * отображения правильных ответов.
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
             * Counts нужны отображению
             * и frontend-сортировке.
             */
            'answers_count' => $this->when(
                isset($this->answers_count),
                fn () => (int) $this->answers_count
            ),

            'attempt_items_count' => $this->when(
                isset($this->attempt_items_count),
                fn () => (int) $this->attempt_items_count
            ),

            /**
             * Даты нужны frontend-сортировке.
             */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
