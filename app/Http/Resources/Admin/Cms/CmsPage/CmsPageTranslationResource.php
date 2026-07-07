<?php

namespace App\Http\Resources\Admin\Cms\CmsPage;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmsPageTranslationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cms_page_id' => $this->cms_page_id,

            /** Локаль */
            'locale' => $this->locale,

            /** Контент */
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'short' => $this->short,
            'description' => $this->description,

            /** SEO */
            'meta_title' => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_desc' => $this->meta_desc,
        ];
    }
}
