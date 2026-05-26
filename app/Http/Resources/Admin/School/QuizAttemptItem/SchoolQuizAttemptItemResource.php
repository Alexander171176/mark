<?php

namespace App\Http\Resources\Admin\School\QuizAttemptItem;

use App\Http\Resources\Admin\School\QuizAnswer\SchoolQuizAnswerSharedResource;
use App\Http\Resources\Admin\School\QuizAttempt\SchoolQuizAttemptResource;
use App\Http\Resources\Admin\School\QuizQuestion\SchoolQuizQuestionSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolQuizAttemptItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $selectedIds = $this->selected_answer_ids;

        if (is_string($selectedIds)) {
            $decoded = json_decode($selectedIds, true);
            $selectedIds = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        $selectedIds = is_array($selectedIds)
            ? array_values(array_filter($selectedIds))
            : [];

        $selectedAnswers = [];

        if (
            $this->relationLoaded('question')
            && $this->question
            && $this->question->relationLoaded('answers')
        ) {
            $map = $this->question->answers->keyBy('id');

            foreach ($selectedIds as $id) {
                $answer = $map->get((int) $id);

                if ($answer) {
                    $selectedAnswers[] = [
                        'id' => $answer->id,
                        'text' => $answer->translation?->text,
                        'is_correct' => (bool) $answer->is_correct,
                    ];
                }
            }
        }

        return [
            'id' => $this->id,

            'school_quiz_attempt_id' => $this->school_quiz_attempt_id,
            'school_quiz_question_id' => $this->school_quiz_question_id,

            'selected_answer_id' => $this->selected_answer_id,
            'selected_answer_ids' => $selectedIds,
            'selected_answers' => $selectedAnswers,

            'free_text_answer' => $this->free_text_answer,

            'is_correct' => (bool) $this->is_correct,
            'score' => (int) $this->score,
            'max_score' => (int) $this->max_score,
            'reviewer_comment' => $this->reviewer_comment,

            'question' => new SchoolQuizQuestionSharedResource(
                $this->whenLoaded('question')
            ),

            'selected_answer' => new SchoolQuizAnswerSharedResource(
                $this->whenLoaded('selectedAnswer')
            ),

            'attempt' => new SchoolQuizAttemptResource(
                $this->whenLoaded('attempt')
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
