<?php

namespace App\Models\Admin\Constructor\HomePage\Reason;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReasonSection extends Model
{
    use HasFactory;

    protected $table = 'reason_sections';
    protected $guarded = [];

    protected $casts = [
        'activity' => 'boolean',
        'sort'     => 'integer',
    ];

    /* ---------- Relations ---------- */
    public function items(): HasMany
    {
        return $this->hasMany(ReasonItem::class, 'section_id');
    }

    /* ---------- Scopes ---------- */
    public function scopeOrdered($q) { return $q->orderBy('sort')->orderBy('id'); }
    public function scopeActive($q)  { return $q->where('activity', true); }
    public function scopeLocale($q, string $locale) { return $q->where('locale', $locale); }
}
