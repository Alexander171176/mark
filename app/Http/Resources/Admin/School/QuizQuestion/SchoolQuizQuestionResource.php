<?php

namespace App\Http\Resources\Admin\School\QuizQuestion;

use App\Http\Resources\Admin\School\Quiz\SchoolQuizSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolQuizQuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'school_quiz_id' => $this->school_quiz_id,

            'sort' => (int) $this->sort,
            'question_type' => $this->question_type,

            'question_text' => $this->translation?->question_text,
            'explanation' => $this->translation?->explanation,

            'points' => (int) $this->points,
            'meta' => $this->meta,
            'activity' => (bool) $this->activity,

            'translations' => SchoolQuizQuestionTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            'quiz' => new SchoolQuizSharedResource(
                $this->whenLoaded('quiz')
            ),

            'answers' => $this->whenLoaded('answers', fn () => $this->answers->map(fn ($answer) => [
                'id' => $answer->id,
                'school_quiz_id' => $answer->school_quiz_id,
                'school_quiz_question_id' => $answer->school_quiz_question_id,

                'text' => $answer->translation?->text,
                'explanation' => $answer->translation?->explanation,

                'is_correct' => (bool) $answer->is_correct,
                'weight' => (int) $answer->weight,
                'sort' => (int) $answer->sort,
                'activity' => (bool) $answer->activity,
            ])),

            'attempt_items' => $this->whenLoaded('attemptItems', fn () => $this->attemptItems->map(fn ($item) => [
                'id' => $item->id,
                'school_quiz_attempt_id' => $item->school_quiz_attempt_id,
                'selected_answer_id' => $item->selected_answer_id,
                'selected_answer_ids' => $item->selected_answer_ids,
                'free_text_answer' => $item->free_text_answer,
                'is_correct' => (bool) $item->is_correct,
                'score' => (int) $item->score,
                'max_score' => (int) $item->max_score,
            ])),

            'answers_count' => $this->when(
                isset($this->answers_count),
                fn () => (int) $this->answers_count
            ),

            'attempt_items_count' => $this->when(
                isset($this->attempt_items_count),
                fn () => (int) $this->attempt_items_count
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
