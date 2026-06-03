<?php

namespace App\Http\Resources\Admin\School\SchoolQuizAnswer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolQuizAnswerTranslationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'locale' => $this->locale,

            'text' => $this->text,
            'explanation' => $this->explanation,
        ];
    }
}
