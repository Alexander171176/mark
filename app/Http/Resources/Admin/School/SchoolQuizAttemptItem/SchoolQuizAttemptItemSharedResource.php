<?php

namespace App\Http\Resources\Admin\School\SchoolQuizAttemptItem;

use App\Http\Resources\Admin\School\SchoolQuizAnswer\SchoolQuizAnswerSharedResource;
use App\Http\Resources\Admin\School\SchoolQuizAttempt\SchoolQuizAttemptSharedResource;
use App\Http\Resources\Admin\School\SchoolQuizQuestion\SchoolQuizQuestionSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolQuizAttemptItemSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $selectedIds = $this->selected_answer_ids;

        if (is_string($selectedIds)) {
            $decoded = json_decode(
                $selectedIds,
                true
            );

            $selectedIds = json_last_error() === JSON_ERROR_NONE
                ? $decoded
                : [];
        }

        $selectedIds = is_array($selectedIds)
            ? array_values(
                array_filter(
                    $selectedIds,
                    fn ($id) => $id !== null && $id !== ''
                )
            )
            : [];

        /**
         * Multiple choice.
         *
         * Используем уже загруженные
         * question.answers.translations.
         */
        $selectedAnswers = [];

        if (
            $this->relationLoaded('question')
            && $this->question
            && $this->question->relationLoaded('answers')
        ) {
            $answersMap = $this->question
                ->answers
                ->keyBy('id');

            foreach ($selectedIds as $id) {
                $answer = $answersMap->get(
                    (int) $id
                );

                if (!$answer) {
                    continue;
                }

                $translation = $answer->relationLoaded('translations')
                    ? $answer->translations->first()
                    : null;

                $selectedAnswers[] = [
                    'id' =>
                        $answer->id,

                    'translation' => $translation
                        ? [
                            'locale' =>
                                $translation->locale,

                            'text' =>
                                $translation->text,
                        ]
                        : null,

                    'is_correct' =>
                        (bool) $answer->is_correct,
                ];
            }
        }

        return [
            'id' =>
                $this->id,

            'school_quiz_attempt_id' =>
                $this->school_quiz_attempt_id,

            'school_quiz_question_id' =>
                $this->school_quiz_question_id,

            'selected_answer_id' =>
                $this->selected_answer_id,

            'selected_answer_ids' =>
                $selectedIds,

            'selected_answers' =>
                $selectedAnswers,

            'free_text_answer' =>
                $this->free_text_answer,

            'is_correct' =>
                (bool) $this->is_correct,

            'score' =>
                (int) $this->score,

            'max_score' =>
                (int) $this->max_score,

            /**
             * Нужен frontend-поиску
             * и отображению.
             */
            'reviewer_comment' =>
                $this->reviewer_comment,

            'question' =>
                new SchoolQuizQuestionSharedResource(
                    $this->whenLoaded('question')
                ),

            'selected_answer' =>
                new SchoolQuizAnswerSharedResource(
                    $this->whenLoaded('selectedAnswer')
                ),

            'attempt' =>
                new SchoolQuizAttemptSharedResource(
                    $this->whenLoaded('attempt')
                ),

            /**
             * Нужны frontend-сортировке.
             */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
