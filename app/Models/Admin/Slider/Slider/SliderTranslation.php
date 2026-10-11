<?php

namespace App\Models\Admin\Slider\Slider;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SliderTranslation extends Model
{
    use HasFactory;

    protected $table = 'slider_translations';

    protected $fillable = [
        'slider_id',
        'locale',
        'title',
        'subtitle',
        'description',
        'meta_title',
        'meta_description',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /** Слайдер, которому принадлежит перевод */
    public function slider(): BelongsTo
    {
        return $this->belongsTo(
            Slider::class,
            'slider_id'
        );
    }
}
