<?php

namespace App\Models\Admin\Constructor\HomePage\Developer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeveloperSection extends Model
{
    use HasFactory;

    protected $table = 'developer_sections';

    protected $fillable = [
        'locale',
        'title',
        'subtitle',
        'sort',
        'is_dark',
        'activity',
    ];

    protected $casts = [
        'sort'     => 'integer',
        'is_dark'  => 'boolean',
        'activity' => 'boolean',
    ];

    /** Связанные пункты (карточки) */
    public function items(): HasMany
    {
        return $this->hasMany(DeveloperItem::class, 'developer_section_id');
    }

    /** Только активные записи секции */
    public function scopeActive($query)
    {
        return $query->where('activity', true);
    }

    /** Сортировка по sort ASC, затем id ASC (стабильная выдача) */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort')->orderBy('id');
    }
}
