<?php

namespace App\Models\Admin\Slider\SliderSlideAdvantage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SliderSlideAdvantageTranslation extends Model
{
    use HasFactory;

    protected $table = 'slider_slide_advantage_translations';

    protected $fillable = [
        'slider_slide_advantage_id',
        'locale',
        'title',
        'text',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /** Преимущество, которому принадлежит перевод */
    public function advantage(): BelongsTo
    {
        return $this->belongsTo(
            SliderSlideAdvantage::class,
            'slider_slide_advantage_id'
        );
    }
}
