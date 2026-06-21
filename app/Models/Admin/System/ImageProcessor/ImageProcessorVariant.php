<?php

namespace App\Models\Admin\System\ImageProcessor;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImageProcessorVariant extends Model
{
    use HasFactory;

    protected $table = 'image_processor_variants';

    protected $fillable = [
        'image_processor_profile_id',
        'key',
        'name',
        'description',
        'activity',
        'width',
        'height',
        'allow_rotate',
        'quality',
        'format',
        'fit',
        'shape',
        'background_light',
        'background_dark',
        'keep_original',
        'sort',
    ];

    protected $casts = [
        'image_processor_profile_id' => 'integer',
        'activity' => 'boolean',
        'width' => 'integer',
        'height' => 'integer',
        'allow_rotate' => 'boolean',
        'quality' => 'integer',
        'keep_original' => 'boolean',
        'sort' => 'integer',
    ];

    /** Профиль обработки */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(ImageProcessorProfile::class, 'image_processor_profile_id');
    }

    /** Только активные варианты */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('activity', true);
    }

    /** Сортировка по умолчанию */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort')->orderBy('id');
    }
}
