<?php

namespace App\Models\Admin\School\SchoolHashtag;

use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use App\Models\Admin\School\SchoolModule\SchoolModule;
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

    /** Сортировка по параметру */
    public function scopeSortByParam(Builder $q, ?string $sort, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return match ($sort) {
            'idAsc' => $q->orderBy('id', 'asc'),
            'idDesc' => $q->orderBy('id', 'desc'),

            'sortAsc' => $q->orderBy('sort', 'asc')->orderByDesc('id'),
            'sortDesc' => $q->orderBy('sort', 'desc')->orderByDesc('id'),

            'activityAsc' => $q->orderBy('activity', 'asc')->orderByDesc('id'),
            'activityDesc' => $q->orderBy('activity', 'desc')->orderByDesc('id'),
            'activity' => $q->where('activity', true)->orderByDesc('id'),
            'inactive' => $q->where('activity', false)->orderByDesc('id'),

            'nameAsc' => $q
                ->leftJoin('school_hashtag_translations as sht_sort', function ($join) use ($locale) {
                    $join->on('sht_sort.school_hashtag_id', '=', 'school_hashtags.id')
                        ->where('sht_sort.locale', '=', $locale);
                })
                ->orderBy('sht_sort.name', 'asc')
                ->orderByDesc('school_hashtags.id')
                ->select('school_hashtags.*'),

            'nameDesc' => $q
                ->leftJoin('school_hashtag_translations as sht_sort', function ($join) use ($locale) {
                    $join->on('sht_sort.school_hashtag_id', '=', 'school_hashtags.id')
                        ->where('sht_sort.locale', '=', $locale);
                })
                ->orderBy('sht_sort.name', 'desc')
                ->orderByDesc('school_hashtags.id')
                ->select('school_hashtags.*'),

            'slugAsc' => $q->orderBy('slug', 'asc')->orderByDesc('id'),
            'slugDesc' => $q->orderBy('slug', 'desc')->orderByDesc('id'),

            'colorAsc' => $q->orderBy('color', 'asc')->orderByDesc('id'),
            'colorDesc' => $q->orderBy('color', 'desc')->orderByDesc('id'),

            'viewsAsc' => $q->orderBy('views', 'asc')->orderByDesc('id'),
            'viewsDesc' => $q->orderBy('views', 'desc')->orderByDesc('id'),

            'likesAsc' => $q->orderBy('likes', 'asc')->orderByDesc('id'),
            'likesDesc' => $q->orderBy('likes', 'desc')->orderByDesc('id'),

            'coursesAsc' => $q->withCount('courses')
                ->orderBy('courses_count', 'asc')->orderByDesc('id'),
            'coursesDesc' => $q->withCount('courses')
                ->orderBy('courses_count', 'desc')->orderByDesc('id'),

            'modulesAsc' => $q->withCount('modules')
                ->orderBy('modules_count', 'asc')->orderByDesc('id'),
            'modulesDesc' => $q->withCount('modules')
                ->orderBy('modules_count', 'desc')->orderByDesc('id'),

            'lessonsAsc' => $q->withCount('lessons')
                ->orderBy('lessons_count', 'asc')->orderByDesc('id'),
            'lessonsDesc' => $q->withCount('lessons')
                ->orderBy('lessons_count', 'desc')->orderByDesc('id'),

            'createdAtAsc', 'dateAsc' => $q->orderBy('created_at', 'asc')->orderByDesc('id'),
            'createdAtDesc', 'dateDesc' => $q->orderBy('created_at', 'desc')->orderByDesc('id'),

            'updatedAtAsc' => $q->orderBy('updated_at', 'asc')->orderByDesc('id'),
            'updatedAtDesc' => $q->orderBy('updated_at', 'desc')->orderByDesc('id'),

            default => $q->ordered(),
        };
    }
}
