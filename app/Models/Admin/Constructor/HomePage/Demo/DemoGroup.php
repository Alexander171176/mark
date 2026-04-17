<?php

namespace App\Models\Admin\Constructor\HomePage\Demo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DemoGroup extends Model
{
    use HasFactory;

    protected $table   = 'demo_groups';
    protected $guarded = [];

    protected $casts = [
        'activity' => 'boolean',
        'sort'     => 'integer',
    ];

    /** При изменении группы обновляем updated_at у секции */
    protected $touches = ['section'];

    /* ========== Relations ========== */
    public function section(): BelongsTo
    {
        return $this->belongsTo(DemoSection::class, 'section_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DemoItem::class, 'group_id')
            ->orderBy('sort')->orderBy('id');
    }

    /* ========== Scopes ========== */
    public function scopeOrdered($q) { return $q->orderBy('sort')->orderBy('id'); }
    public function scopeActive($q)  { return $q->where('activity', true); }
}
