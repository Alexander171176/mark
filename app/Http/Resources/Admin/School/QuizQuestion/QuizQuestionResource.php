<?php

namespace App\Http\Resources\Admin\School\QuizQuestion;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizQuestionResource extends JsonResource
{
    /**
     * Представление вопроса квиза.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'quiz_id'        => $this->quiz_id,

            'sort'           => $this->sort,
            'question_type'  => $this->question_type, // single_choice|multiple_choice|true_false|open_text
            'question_text'  => $this->question_text,
            'explanation'    => $this->explanation,
            'points'         => $this->points,
            'meta'           => $this->meta,
            'activity'       => (bool) $this->activity,

            'created_at'     => optional($this->created_at)?->toISOString(),
            'updated_at'     => optional($this->updated_at)?->toISOString(),

            // Короткие данные по связям (если подгружены)
            'quiz' => $this->whenLoaded('quiz', fn () => [
                'id'    => $this->quiz->id,
                'title' => $this->quiz->title,
                'slug'  => $this->quiz->slug,
            ]),

            // Варианты ответов (если подгружены)
            'answers' => $this->whenLoaded('answers', function () {
                return $this->answers->map(fn ($a) => [
                    'id'               => $a->id,
                    'quiz_question_id' => $a->quiz_question_id,
                    'text'             => $a->text ?? null,
                    'is_correct'       => (bool) ($a->is_correct ?? false),
                    'sort'             => $a->sort ?? 0,
                    'meta'             => $a->meta ?? null,
                ]);
            }),

            // Счётчики при использовании withCount()
            'answers_count' => $this->whenCounted('answers', fn () => $this->answers_count),
        ];
    }
}
