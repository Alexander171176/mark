<?php

namespace App\Http\Resources\Admin\School\QuizAnswer;

use App\Http\Resources\Admin\School\Quiz\SchoolQuizSharedResource;
use App\Http\Resources\Admin\School\QuizQuestion\SchoolQuizQuestionSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolQuizAnswerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'school_quiz_id' => $this->school_quiz_id,
            'school_quiz_question_id' => $this->school_quiz_question_id,

            'text' => $this->translation?->text,
            'explanation' => $this->translation?->explanation,

            'is_correct' => (bool) $this->is_correct,
            'weight' => (int) $this->weight,
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'translations' => SchoolQuizAnswerTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            'quiz' => new SchoolQuizSharedResource(
                $this->whenLoaded('quiz')
            ),

            'question' => new SchoolQuizQuestionSharedResource(
                $this->whenLoaded('question')
            ),

            'attempt_items' => $this->whenLoaded('attemptItems', fn () => $this->attemptItems->map(fn ($item) => [
                'id' => $item->id,
                'school_quiz_attempt_id' => $item->school_quiz_attempt_id,
                'school_quiz_question_id' => $item->school_quiz_question_id,

                'selected_answer_id' => $item->selected_answer_id,
                'selected_answer_ids' => $item->selected_answer_ids,
                'free_text_answer' => $item->free_text_answer,

                'is_correct' => (bool) $item->is_correct,
                'score' => (int) $item->score,
                'max_score' => (int) $item->max_score,
            ])),

            'attempt_items_count' => $this->when(
                isset($this->attempt_items_count),
                fn () => (int) $this->attempt_items_count
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
