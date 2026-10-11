<?php

namespace App\Models\Admin\Slider\SliderSlideAction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SliderSlideActionTranslation extends Model
{
    use HasFactory;

    protected $table = 'slider_slide_action_translations';

    protected $fillable = [
        'slider_slide_action_id',
        'locale',
        'label',
        'title',
        'aria_label',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /** Кнопка, которой принадлежит перевод */
    public function action(): BelongsTo
    {
        return $this->belongsTo(
            SliderSlideAction::class,
            'slider_slide_action_id'
        );
    }
}
