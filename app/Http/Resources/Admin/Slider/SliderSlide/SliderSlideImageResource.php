<?php

namespace App\Http\Resources\Admin\Slider\SliderSlide;

use App\Http\Resources\Admin\Image\BaseImageResource;
use Illuminate\Http\Request;

class SliderSlideImageResource extends BaseImageResource
{
    /**
     * Ресурс изображения слайда.
     *
     * Наследует все поля BaseImageResource
     * и добавляет назначение изображения.
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        /** Назначение изображения внутри слайда */
        $data['purpose'] = $this->resource->pivot?->purpose;
        // Не инициируем lazy loading Spatie MediaLibrary.
        $media = $this->resource->relationLoaded('media')
            ? $this->resource->getRelation('media')->firstWhere('collection_name', 'images')
            : null;
        $data['desktop_url'] = $media?->getUrl();
        $data['mobile_url'] = $media && $media->hasGeneratedConversion('slider_mobile')
            ? $media->getUrl('slider_mobile')
            : $media?->getUrl();

        return $data;
    }
}
