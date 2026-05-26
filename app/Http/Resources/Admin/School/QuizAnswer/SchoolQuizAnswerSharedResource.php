<?php

namespace App\Http\Resources\Admin\School\QuizAnswer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolQuizAnswerSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'school_quiz_id' => $this->school_quiz_id,
            'school_quiz_question_id' => $this->school_quiz_question_id,

            'text' => $this->translation?->text,

            'is_correct' => (bool) $this->is_correct,
            'weight' => (int) $this->weight,
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,
        ];
    }
}
