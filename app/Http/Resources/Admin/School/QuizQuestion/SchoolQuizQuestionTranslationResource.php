<?php

namespace App\Http\Resources\Admin\School\QuizQuestion;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolQuizQuestionTranslationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'locale' => $this->locale,

            'question_text' => $this->question_text,
            'explanation' => $this->explanation,
        ];
    }
}
