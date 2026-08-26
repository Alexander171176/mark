<?php

namespace App\Http\Resources\Admin\School\SchoolQuizAnswer;

use App\Http\Resources\Admin\School\SchoolQuiz\SchoolQuizSharedResource;
use App\Http\Resources\Admin\School\SchoolQuizQuestion\SchoolQuizQuestionSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolQuizAnswerSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /**
         * Admin Controller заранее загружает
         * только выбранную locale:
         *
         * translations(currentLocale)
         *
         * Поэтому первый элемент коллекции
         * и является нужным переводом.
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

            'school_quiz_question_id' =>
                $this->school_quiz_question_id,

            /**
             * Перевод выбранной locale.
             *
             * Только из уже eager-loaded
             * translations.
             */
            'translation' => $translation
                ? [
                    'locale' =>
                        $translation->locale,

                    'text' =>
                        $translation->text,

                    /**
                     * Нужен frontend-поиску,
                     * поэтому оставляем также
                     * explanation.
                     */
                    'explanation' =>
                        $translation->explanation,
                ]
                : null,

            'quiz' =>
                new SchoolQuizSharedResource(
                    $this->whenLoaded(
                        'quiz'
                    )
                ),

            'question' =>
                new SchoolQuizQuestionSharedResource(
                    $this->whenLoaded(
                        'question'
                    )
                ),

            'attempt_items_count' => $this->when(
                isset($this->attempt_items_count),
                fn () => (int) $this->attempt_items_count
            ),

            'is_correct' =>
                (bool) $this->is_correct,

            'weight' =>
                (int) $this->weight,

            'sort' =>
                (int) $this->sort,

            'activity' =>
                (bool) $this->activity,
        ];
    }
}
