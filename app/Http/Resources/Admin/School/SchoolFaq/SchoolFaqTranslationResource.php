<?php

namespace App\Http\Resources\Admin\School\SchoolFaq;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolFaqTranslationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'school_faq_id' => $this->school_faq_id,
            'locale' => $this->locale,

            'question' => $this->question,
            'answer' => $this->answer,

            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
