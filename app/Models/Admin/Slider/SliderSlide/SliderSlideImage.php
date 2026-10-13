<?php

namespace App\Models\Admin\Slider\SliderSlide;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SliderSlideImage extends BaseImage
{
    protected $table = 'slider_slide_images';

    /**
     * Автоматическая уменьшенная копия для телефонов.
     * Конверсия генерируется Spatie MediaLibrary при загрузке исходника.
     * При включённой очереди conversions может появиться асинхронно.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        parent::registerMediaConversions($media);

        $this->addMediaConversion('slider_mobile')
            ->width(640)
            ->format('webp')
            ->performOnCollections('images')
            ->nonQueued();
    }

    /** Слайды, использующие изображение */
    public function slides(): BelongsToMany
    {
        return $this->belongsToMany(
            SliderSlide::class,
            'slider_slide_has_images',
            'slider_slide_image_id',
            'slider_slide_id'
        )
            ->withPivot('purpose', 'order')
            ->orderByPivot('order');
    }
}
