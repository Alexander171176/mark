<?php

namespace App\Http\Resources\Admin\School\QuizAttemptItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizAttemptItemResource extends JsonResource
{
    /**
     * Представление ответа пользователя в рамках попытки квиза.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $selectedIds = $this->selected_answer_ids;

        // на всякий случай нормализуем к массиву int
        if (is_string($selectedIds)) {
            $decoded = json_decode($selectedIds, true);
            $selectedIds = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }
        $selectedIds = is_array($selectedIds) ? array_values(array_filter($selectedIds)) : [];

        // Соберём выбранные ответы (multiple) из question.answers (если загружены)
        $selectedAnswers = [];
        if ($this->relationLoaded('question') && $this->question && $this->question->relationLoaded('answers')) {
            $map = $this->question->answers->keyBy('id');
            foreach ($selectedIds as $id) {
                $a = $map->get((int) $id);
                if ($a) {
                    $selectedAnswers[] = [
                        'id'         => $a->id,
                        'text'       => $a->text,
                        'is_correct' => (bool) $a->is_correct,
                    ];
                }
            }
        }

        return [
            'id'                   => $this->id,
            'quiz_attempt_id'      => $this->quiz_attempt_id,
            'quiz_question_id'     => $this->quiz_question_id,

            'selected_answer_id'   => $this->selected_answer_id,
            'selected_answer_ids'  => $selectedIds,
            'selected_answers'     => $selectedAnswers, // ✅ для Edit.vue

            'free_text_answer'     => $this->free_text_answer,

            'is_correct'           => (bool) $this->is_correct,
            'score'                => $this->score,
            'max_score'            => $this->max_score,
            'reviewer_comment'     => $this->reviewer_comment,

            'created_at'           => optional($this->created_at)?->toISOString(),
            'updated_at'           => optional($this->updated_at)?->toISOString(),

            'question' => $this->whenLoaded('question', fn () => [
                'id'     => $this->question->id,
                'type'   => $this->question->question_type,
                'text'   => $this->question->question_text,
                'points' => $this->question->points,
            ]),

            'selected_answer' => $this->whenLoaded('selectedAnswer', fn () => [
                'id'         => $this->selectedAnswer->id,
                'text'       => $this->selectedAnswer->text,
                'is_correct' => (bool) $this->selectedAnswer->is_correct,
            ]),

            'attempt' => $this->whenLoaded('attempt', function () {
                return [
                    'id'             => $this->attempt->id,
                    'user_id'        => $this->attempt->user_id,
                    'quiz_id'        => $this->attempt->quiz_id,
                    'attempt_number' => $this->attempt->attempt_number,
                    'status'         => $this->attempt->status,

                    'user' => ($this->attempt->relationLoaded('user') && $this->attempt->user) ? [
                        'id'    => $this->attempt->user->id,
                        'name'  => $this->attempt->user->name,
                        'email' => $this->attempt->user->email,
                    ] : null,

                    'quiz' => ($this->attempt->relationLoaded('quiz') && $this->attempt->quiz) ? [
                        'id'    => $this->attempt->quiz->id,
                        'title' => $this->attempt->quiz->title,
                        'slug'  => $this->attempt->quiz->slug,
                    ] : null,
                ];
            }),
        ];
    }

}
