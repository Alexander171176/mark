<?php

namespace App\Models\Admin\School\SchoolTrack;

use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\User\Like\SchoolTrackLike;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchoolTrack extends Model
{
    use HasFactory;

    protected $table = 'school_tracks';

    protected $fillable = [
        'parent_id',
        'sort',
        'activity',
        'slug',
        'views',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'sort' => 'integer',
        'activity' => 'boolean',
        'views' => 'integer',
    ];

    /* ======================== Translations ======================== */

    /** Все переводы */
    public function translations(): HasMany
    {
        return $this->hasMany(SchoolTrackTranslation::class, 'school_track_id');
    }

    /** Перевод по текущей локали */
    public function translation(): HasOne
    {
        return $this->hasOne(SchoolTrackTranslation::class, 'school_track_id')
            ->where('locale', app()->getLocale());
    }

    /* ======================== Relations ======================== */

    /** Родительский трек */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** Дочерние треки */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort');
    }

    /** Дочерние треки рекурсивно */
    public function childrenRecursive(): HasMany
    {
        return $this->children()
            ->with([
                'translation',
                'translations',
                'images',
                'childrenRecursive',
            ])
            ->withCount([
                'children',
                'courses',
                'images',
                'likes',
            ]);
    }

    /** Курсы трека */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolCourse::class,
            'school_course_has_tracks',
            'school_track_id',
            'school_course_id'
        )->withTimestamps();
    }

    /** Изображения трека */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolTrackImage::class,
            'school_track_has_images',
            'school_track_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderBy('school_track_has_images.order', 'asc');
    }

    /** Лайки трека */
    public function likes(): HasMany
    {
        return $this->hasMany(SchoolTrackLike::class, 'school_track_id');
    }

    /* ======================== Scopes ======================== */

    /** Только корневые */
    public function scopeRoot(Builder $q): Builder
    {
        return $q->whereNull('parent_id');
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

    /** Публичный набор */
    public function scopeForPublic(Builder $q, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return $q
            ->active()
            ->whereHas('translations', fn ($qq) => $qq->where('locale', $locale))
            ->withLocale($locale);
    }

    /** Поиск */
    public function scopeSearch(Builder $q, ?string $term, ?string $locale = null): Builder
    {
        if (!$term) {
            return $q;
        }

        $locale = $locale ?: app()->getLocale();

        return $q->where(function (Builder $query) use ($term, $locale) {
            $query->where('slug', 'like', "%{$term}%")
                ->orWhereHas('translations', function (Builder $qq) use ($term, $locale) {
                    $qq->where('locale', $locale)
                        ->where(function (Builder $sub) use ($term) {
                            $sub->where('name', 'like', "%{$term}%")
                                ->orWhere('short', 'like', "%{$term}%")
                                ->orWhere('description', 'like', "%{$term}%");
                        });
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

            'parentAsc' => $q->orderBy('parent_id', 'asc')->orderByDesc('id'),
            'parentDesc' => $q->orderBy('parent_id', 'desc')->orderByDesc('id'),

            'parentNameAsc' => $q
                ->leftJoin('school_tracks as parent_tracks_sort', function ($join) {
                    $join->on('parent_tracks_sort.id', '=', 'school_tracks.parent_id');
                })
                ->leftJoin('school_track_translations as parent_stt_sort', function ($join) use ($locale) {
                    $join->on('parent_stt_sort.school_track_id', '=', 'parent_tracks_sort.id')
                        ->where('parent_stt_sort.locale', '=', $locale);
                })
                ->orderBy('parent_stt_sort.name', 'asc')
                ->orderByDesc('school_tracks.id')
                ->select('school_tracks.*'),

            'parentNameDesc' => $q
                ->leftJoin('school_tracks as parent_tracks_sort', function ($join) {
                    $join->on('parent_tracks_sort.id', '=', 'school_tracks.parent_id');
                })
                ->leftJoin('school_track_translations as parent_stt_sort', function ($join) use ($locale) {
                    $join->on('parent_stt_sort.school_track_id', '=', 'parent_tracks_sort.id')
                        ->where('parent_stt_sort.locale', '=', $locale);
                })
                ->orderBy('parent_stt_sort.name', 'desc')
                ->orderByDesc('school_tracks.id')
                ->select('school_tracks.*'),

            'slugAsc' => $q->orderBy('slug', 'asc')->orderByDesc('id'),
            'slugDesc' => $q->orderBy('slug', 'desc')->orderByDesc('id'),

            'viewsAsc' => $q->orderBy('views', 'asc')->orderByDesc('id'),
            'viewsDesc' => $q->orderBy('views', 'desc')->orderByDesc('id'),

            'likesAsc' => $q->withCount('likes')->orderBy('likes_count', 'asc')->orderByDesc('id'),
            'likesDesc' => $q->withCount('likes')->orderBy('likes_count', 'desc')->orderByDesc('id'),

            'coursesAsc' => $q->withCount('courses')->orderBy('courses_count', 'asc')->orderByDesc('id'),
            'coursesDesc' => $q->withCount('courses')->orderBy('courses_count', 'desc')->orderByDesc('id'),

            'childrenAsc' => $q->withCount('children')->orderBy('children_count', 'asc')->orderByDesc('id'),
            'childrenDesc' => $q->withCount('children')->orderBy('children_count', 'desc')->orderByDesc('id'),

            'imagesAsc' => $q->withCount('images')->orderBy('images_count', 'asc')->orderByDesc('id'),
            'imagesDesc' => $q->withCount('images')->orderBy('images_count', 'desc')->orderByDesc('id'),

            'activityAsc' => $q->orderBy('activity', 'asc')->orderByDesc('id'),
            'activityDesc' => $q->orderBy('activity', 'desc')->orderByDesc('id'),
            'activity' => $q->where('activity', true)->orderByDesc('id'),
            'inactive' => $q->where('activity', false)->orderByDesc('id'),

            'createdAtAsc', 'dateAsc' => $q->orderBy('created_at', 'asc')->orderByDesc('id'),
            'createdAtDesc', 'dateDesc' => $q->orderBy('created_at', 'desc')->orderByDesc('id'),

            'updatedAtAsc' => $q->orderBy('updated_at', 'asc')->orderByDesc('id'),
            'updatedAtDesc' => $q->orderBy('updated_at', 'desc')->orderByDesc('id'),

            'nameAsc' => $q
                ->leftJoin('school_track_translations as stt_sort', function ($join) use ($locale) {
                    $join->on('stt_sort.school_track_id', '=', 'school_tracks.id')
                        ->where('stt_sort.locale', '=', $locale);
                })
                ->orderBy('stt_sort.name', 'asc')
                ->orderByDesc('school_tracks.id')
                ->select('school_tracks.*'),

            'nameDesc' => $q
                ->leftJoin('school_track_translations as stt_sort', function ($join) use ($locale) {
                    $join->on('stt_sort.school_track_id', '=', 'school_tracks.id')
                        ->where('stt_sort.locale', '=', $locale);
                })
                ->orderBy('stt_sort.name', 'desc')
                ->orderByDesc('school_tracks.id')
                ->select('school_tracks.*'),

            default => $q->ordered(),
        };
    }

    /* ======================== Helpers ======================== */

    /** Проверка корневого трека */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    /** Проверка дочерних треков */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }
}
