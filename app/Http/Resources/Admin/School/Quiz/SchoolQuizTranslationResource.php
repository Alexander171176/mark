<?php

namespace App\Http\Resources\Admin\School\Quiz;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolQuizTranslationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'locale' => $this->locale,

            'title' => $this->title,
            'short' => $this->short,
            'description' => $this->description,
        ];
    }
}
