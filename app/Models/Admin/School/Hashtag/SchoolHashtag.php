<?php

namespace App\Models\Admin\School\Hashtag;

use App\Models\Admin\School\Course\SchoolCourse;
use App\Models\Admin\School\Lesson\SchoolLesson;
use App\Models\Admin\School\Module\SchoolModule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class SchoolHashtag extends Model
{
    use HasFactory;

    protected $table = 'school_hashtags';

    protected $fillable = [
        'sort',
        'activity',
        'slug',
        'color',
        'views',
        'likes',
    ];

    protected $casts = [
        'sort' => 'int',
        'activity' => 'bool',
        'views' => 'int',
        'likes' => 'int',
    ];

    /* ======================== Translations ======================== */

    /** Все переводы */
    public function translations(): HasMany
    {
        return $this->hasMany(SchoolHashtagTranslation::class, 'school_hashtag_id');
    }

    /** Перевод по текущей локали */
    public function translation(): HasOne
    {
        return $this->hasOne(SchoolHashtagTranslation::class, 'school_hashtag_id')
            ->where('locale', app()->getLocale());
    }

    /* ======================== Relations ======================== */

    /** Курсы с этим хештегом */
    public function courses(): MorphToMany
    {
        return $this->morphedByMany(
            SchoolCourse::class,
            'hashtaggable',
            'school_hashtaggables',
            'school_hashtag_id',
            'hashtaggable_id'
        )->withTimestamps();
    }

    /** Модули с этим хештегом */
    public function modules(): MorphToMany
    {
        return $this->morphedByMany(
            SchoolModule::class,
            'hashtaggable',
            'school_hashtaggables',
            'school_hashtag_id',
            'hashtaggable_id'
        )->withTimestamps();
    }

    /** Уроки с этим хештегом */
    public function lessons(): MorphToMany
    {
        return $this->morphedByMany(
            SchoolLesson::class,
            'hashtaggable',
            'school_hashtaggables',
            'school_hashtag_id',
            'hashtaggable_id'
        )->withTimestamps();
    }

    /* ======================== Scopes ======================== */

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

    /** Популярные */
    public function scopePopular(Builder $q, int $minViews = 0): Builder
    {
        return $q
            ->when($minViews > 0, fn ($qq) => $qq->where('views', '>=', $minViews))
            ->orderByDesc('views');
    }

    /** Подгрузка перевода */
    public function scopeWithLocale(Builder $q, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return $q->with([
            'translations' => fn ($query) => $query->where('locale', $locale),
        ]);
    }

    /** Публичный набор */
    public function scopeForPublic(Builder $q, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return $q
            ->active()
            ->whereHas('translations', fn ($qq) => $qq->where('locale', $locale))
            ->withLocale($locale);
    }

    /** Облако тегов */
    public function scopeForTagCloud(Builder $q, ?string $locale = null, int $minViews = 0): Builder
    {
        return $q
            ->forPublic($locale)
            ->popular($minViews)
            ->ordered();
    }

    /** Поиск */
    public function scopeSearch(Builder $q, ?string $term, ?string $locale = null): Builder
    {
        if (!$term) return $q;

        $locale = $locale ?: app()->getLocale();

        return $q->whereHas('translations', function ($qq) use ($term, $locale) {
            $qq->where('locale', $locale)
                ->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', "%{$term}%")
                        ->orWhere('short', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                });
        });
    }
}
