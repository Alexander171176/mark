<?php

namespace App\Models\Admin\Constructor\HomePage\Component;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComponentTab extends Model
{
    use HasFactory;

    protected $table = 'component_tabs';
    protected $guarded = [];

    protected $casts = [
        'activity' => 'boolean',
        'sort'     => 'integer',
    ];

    // при изменении таба трогаем updated_at секции
    protected $touches = ['section'];

    /* ---------- Relations ---------- */
    public function section(): BelongsTo
    {
        return $this->belongsTo(ComponentSection::class, 'section_id');
    }

    public function tiles(): HasMany
    {
        return $this->hasMany(ComponentTile::class, 'tab_id');
    }

    /* ---------- Scopes ---------- */
    public function scopeOrdered($q) { return $q->orderBy('sort')->orderBy('id'); }
    public function scopeActive($q)  { return $q->where('activity', true); }
}
