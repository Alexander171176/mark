<?php

namespace App\Models\Admin\Constructor\HomePage\Wave;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaveSection extends Model
{
    use HasFactory;

    protected $table = 'wave_sections';

    protected $fillable = [
        'locale',
        'title',
        'subtitle',
        'left_text',
        'right_text',
        'sort',
        'is_dark',
        'activity',
    ];

    protected $casts = [
        'sort'     => 'integer',
        'is_dark'  => 'boolean',
        'activity' => 'boolean',
    ];

    /** Связь: элементы тех-стека этой секции */
    public function teches(): HasMany
    {
        return $this->hasMany(WaveTech::class, 'wave_section_id');
    }

    /** Удобный скоуп сортировки секций */
    public function scopeOrdered($q)
    {
        return $q->orderBy('sort')->orderBy('id');
    }

    /** Активные секции */
    public function scopeActive($q)
    {
        return $q->where('activity', true);
    }

    /** Секция по локали */
    public function scopeLocale($q, string $locale)
    {
        return $q->where('locale', $locale);
    }
}
