<?php

namespace App\Models\Admin\Constructor\HomePage\Feature;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeatureSection extends Model
{
    use HasFactory;

    protected $table = 'feature_sections';

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

    protected $attributes = [
        'sort'     => 0,
        'is_dark'  => false,
        'activity' => true,
    ];

    /** Связь: секция -> элементы (иконки/фичи) */
    public function items(): HasMany
    {
        return $this->hasMany(FeatureItem::class, 'feature_section_id');
    }

    /** Активные секции */
    public function scopeActive($q)
    {
        return $q->where('activity', true);
    }

    /** Упорядочивание по sort, затем по id */
    public function scopeOrdered($q)
    {
        return $q->orderBy('sort')->orderBy('id');
    }

    /** Быстрый фильтр по локали */
    public function scopeForLocale($q, string $locale)
    {
        return $q->where('locale', $locale);
    }
}
