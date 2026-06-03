<?php

namespace App\Models\Admin\School\SchoolFaq;

use App\Models\Admin\School\SchoolFaqCategory\SchoolFaqCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchoolFaq extends Model
{
    use HasFactory;

    protected $table = 'school_faqs';

    protected $fillable = [
        'school_faq_category_id',
        'sort',
        'activity',
    ];

    protected $casts = [
        'school_faq_category_id' => 'integer',
        'sort' => 'integer',
        'activity' => 'boolean',
    ];

    /** Категория */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SchoolFaqCategory::class, 'school_faq_category_id');
    }

    /** Все переводы */
    public function translations(): HasMany
    {
        return $this->hasMany(SchoolFaqTranslation::class, 'school_faq_id');
    }

    /** Перевод по текущей локали */
    public function translation(): HasOne
    {
        return $this->hasOne(SchoolFaqTranslation::class, 'school_faq_id')
            ->where('locale', app()->getLocale());
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

    /** По категории */
    public function scopeForCategory(Builder $q, int $categoryId): Builder
    {
        return $q->where('school_faq_category_id', $categoryId);
    }

    /** Подгрузка перевода */
    public function scopeWithLocale(Builder $q, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return $q->with([
            'translations' => fn ($query) => $query->where('locale', $locale),
        ]);
    }

    /** Для публичной части */
    public function scopeForPublic(Builder $q, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return $q
            ->active()
            ->whereHas('translations', fn ($qq) => $qq->where('locale', $locale))
            ->withLocale($locale)
            ->ordered();
    }

    /** Поиск */
    public function scopeSearch(Builder $q, ?string $term, ?string $locale = null): Builder
    {
        if (!$term) {
            return $q;
        }

        $locale = $locale ?: app()->getLocale();

        return $q->whereHas('translations', function (Builder $query) use ($term, $locale) {
            $query->where('locale', $locale)
                ->where(function (Builder $sub) use ($term) {
                    $sub->where('question', 'like', "%{$term}%")
                        ->orWhere('answer', 'like', "%{$term}%");
                });
        });
    }
}
