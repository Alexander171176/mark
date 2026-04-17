<?php

namespace App\Models\Admin\Constructor\HomePage\Developer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeveloperItem extends Model
{
    use HasFactory;

    protected $table = 'developer_items';

    protected $fillable = [
        'developer_section_id',
        'title',
        'subtitle',
        'description',
        // inline SVG (TEXT) — для больших SVG храним прямо в БД
        'image_light',
        'image_dark',
        'alt',
        'sort',
        'activity',
    ];

    protected $casts = [
        'sort'     => 'integer',
        'activity' => 'boolean',
    ];

    /** Родительская секция */
    public function section(): BelongsTo
    {
        return $this->belongsTo(DeveloperSection::class, 'developer_section_id');
    }

    /** Только активные карточки */
    public function scopeActive($query)
    {
        return $query->where('activity', true);
    }

    /** Сортировка по sort ASC, затем id ASC */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort')->orderBy('id');
    }
}
