<?php

namespace App\Models\Admin\School\SchoolFaqCategory;

use App\Models\Admin\School\SchoolFaq\SchoolFaq;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchoolFaqCategory extends Model
{
    use HasFactory;

    protected $table = 'school_faq_categories';

    protected $fillable = [
        'sort',
        'activity',
        'slug',
    ];

    protected $casts = [
        'sort' => 'integer',
        'activity' => 'boolean',
    ];

    /** Все переводы */
    public function translations(): HasMany
    {
        return $this->hasMany(SchoolFaqCategoryTranslation::class, 'school_faq_category_id');
    }

    /** Перевод по текущей локали */
    public function translation(): HasOne
    {
        return $this->hasOne(SchoolFaqCategoryTranslation::class, 'school_faq_category_id')
            ->where('locale', app()->getLocale());
    }

    /** Вопросы категории */
    public function faqs(): HasMany
    {
        return $this->hasMany(SchoolFaq::class, 'school_faq_category_id');
    }

    /** Только активные */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('activity', true);
    }

    /** Сортировка */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort')->orderByDesc('id');
    }

    /** Подгрузка перевода */
    public function scopeWithLocale(Builder $q, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return $q->with([
            'translations' => fn ($query) => $query->where('locale', $locale),
        ]);
    }
}
