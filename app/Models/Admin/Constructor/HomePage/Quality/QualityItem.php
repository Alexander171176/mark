<?php

namespace App\Models\Admin\Constructor\HomePage\Quality;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualityItem extends Model
{
    use HasFactory;

    protected $table   = 'quality_items';
    protected $guarded = [];

    protected $casts = [
        'activity'  => 'boolean',
        'sort'      => 'integer',
        'delay'     => 'integer',
        'threshold' => 'float',
        'distance'  => 'integer',
    ];

    // При изменении айтема затрагиваем updated_at секции
    protected $touches = ['section'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(QualitySection::class, 'section_id');
    }

    /* Скоупы */
    public function scopeOrdered($q) { return $q->orderBy('sort')->orderBy('id'); }
    public function scopeActive($q)  { return $q->where('activity', true); }
}
