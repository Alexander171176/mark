<?php

namespace App\Http\Resources\Admin\School\QuizAnswer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizAnswerResource extends JsonResource
{
    /**
     * Представление варианта ответа.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'quiz_id'          => $this->quiz_id,
            'quiz_question_id' => $this->quiz_question_id,

            'text'        => $this->text,
            'is_correct'  => (bool) $this->is_correct,
            'weight'      => $this->weight,
            'sort'        => $this->sort,
            'explanation' => $this->explanation,
            'activity'    => (bool) $this->activity,

            'created_at'  => optional($this->created_at)?->toISOString(),
            'updated_at'  => optional($this->updated_at)?->toISOString(),

            // Короткая информация по связям (если загружены)
            'quiz' => $this->whenLoaded('quiz', fn () => [
                'id'    => $this->quiz->id,
                'title' => $this->quiz->title,
                'slug'  => $this->quiz->slug,
            ]),
            'question' => $this->whenLoaded('question', fn () => [
                'id'            => $this->question->id,
                'question_type' => $this->question->question_type,
                'sort'          => $this->question->sort,
                'question_text' => $this->question->question_text, // ← Добавляем!
            ]),
        ];
    }
}
