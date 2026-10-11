<?php

namespace App\Models\Admin\Slider\SliderSlide;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SliderSlideImage extends BaseImage
{
    protected $table = 'slider_slide_images';

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
