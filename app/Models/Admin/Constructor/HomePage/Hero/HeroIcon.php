<?php

namespace App\Models\Admin\Constructor\HomePage\Hero;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeroIcon extends Model
{
    use HasFactory;

    protected $table = 'hero_icons';

    /**
     * Разрешённые к массовому заполнению поля.
     */
    protected $fillable = [
        'hero_section_id',
        'label',
        'svg',
        'sort',
        'activity',
    ];

    /**
     * Касты.
     */
    protected $casts = [
        'sort'     => 'integer',
        'activity' => 'boolean',
    ];

    /**
     * При изменении иконки трогаем updated_at у родительской секции.
     */
    protected $touches = ['section'];

    /* ==========================
     |   Relations
     ========================== */

    public function section(): BelongsTo
    {
        return $this->belongsTo(HeroSection::class, 'hero_section_id');
    }

    /* ==========================
     |   Scopes
     ========================== */

    /**
     * Только активные иконки.
     */
    public function scopeActive($query)
    {
        return $query->where('activity', true);
    }

    /**
     * Сортировка по полю sort по возрастанию.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort');
    }

    /**
     * Отбор по секции.
     */
    public function scopeForSection($query, int $sectionId)
    {
        return $query->where('hero_section_id', $sectionId);
    }
}
