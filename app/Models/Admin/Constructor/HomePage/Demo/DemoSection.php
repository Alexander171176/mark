<?php

namespace App\Models\Admin\Constructor\HomePage\Demo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DemoSection extends Model
{
    use HasFactory;

    protected $table   = 'demo_sections';
    protected $guarded = [];

    protected $casts = [
        'is_dark'  => 'bool',
        'activity' => 'bool',
        'sort'     => 'int',
    ];

    /** Relations */
    public function groups(): HasMany
    {
        return $this->hasMany(DemoGroup::class, 'section_id')
            ->orderBy('sort')
            ->orderBy('id');
    }

    /** Scopes */
    public function scopeOrdered($q)
    {
        return $q->orderBy('sort')->orderBy('id');
    }

    public function scopeActive($q)
    {
        return $q->where('activity', true);
    }
}
