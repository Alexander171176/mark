<?php

namespace App\Models\Admin\Slider\SliderSlide;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SliderSlideTranslation extends Model
{
    use HasFactory;

    protected $table = 'slider_slide_translations';

    protected $fillable = [
        'slider_slide_id',
        'locale',
        'label',
        'title',
        'accent',
        'description',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /** Слайд, которому принадлежит перевод */
    public function slide(): BelongsTo
    {
        return $this->belongsTo(
            SliderSlide::class,
            'slider_slide_id'
        );
    }
}
