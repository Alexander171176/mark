<?php

namespace App\Models\Admin\Constructor\HomePage\Feature;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureItem extends Model
{
    use HasFactory;

    protected $table = 'feature_items';

    protected $fillable = [
        'feature_section_id',
        'title',
        'subtitle',
        'description',
        'image_light', // inline SVG / HTML (TEXT/MEDIUMTEXT)
        'image_dark',  // inline SVG / HTML (TEXT/MEDIUMTEXT)
        'alt',
        'sort',
        'activity',
    ];

    protected $casts = [
        'sort'     => 'integer',
        'activity' => 'boolean',
    ];

    protected $attributes = [
        'sort'     => 0,
        'activity' => true,
    ];

    /** Связь: элемент -> секция */
    public function section(): BelongsTo
    {
        return $this->belongsTo(FeatureSection::class, 'feature_section_id');
    }

    /** Активные элементы */
    public function scopeActive($q)
    {
        return $q->where('activity', true);
    }

    /** Упорядочивание по sort, затем по id */
    public function scopeOrdered($q)
    {
        return $q->orderBy('sort')->orderBy('id');
    }

    /** Есть ли иконка (inline SVG) */
    public function hasLightIcon(): bool
    {
        return !empty($this->image_light);
    }

    public function hasDarkIcon(): bool
    {
        return !empty($this->image_dark);
    }
}
