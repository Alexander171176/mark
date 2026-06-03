<?php

namespace App\Http\Resources\Admin\School\SchoolQuizQuestion;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolQuizQuestionSharedResource extends JsonResource
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
            'activity' => (bool) $this->activity,
        ];
    }
}
