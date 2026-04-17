<?php

namespace App\Models\Admin\Constructor\HomePage\Component;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ComponentSection extends Model
{
    use HasFactory;

    protected $table = 'component_sections';
    protected $guarded = [];

    protected $casts = [
        'activity' => 'boolean',
        'sort'     => 'integer',
    ];

    /* ---------- Relations ---------- */
    public function features(): HasMany
    {
        return $this->hasMany(ComponentFeature::class, 'section_id');
    }

    public function tabs(): HasMany
    {
        return $this->hasMany(ComponentTab::class, 'section_id');
    }

    public function tiles(): HasManyThrough
    {
        return $this->hasManyThrough(
            ComponentTile::class,    // конечная модель
            ComponentTab::class,     // промежуточная модель
            'section_id',            // FK на табах -> секция
            'tab_id',                // FK на тайлах -> таб
            'id',                    // локальный ключ секции
            'id'                     // локальный ключ таба
        );
    }

    /* ---------- Scopes ---------- */
    public function scopeOrdered($q) { return $q->orderBy('sort')->orderBy('id'); }
    public function scopeActive($q)  { return $q->where('activity', true); }
}
