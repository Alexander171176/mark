<?php

namespace App\Models\Admin\School\Hashtag;

use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\Lesson\Lesson;
use App\Models\Admin\School\Module\Module;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hashtag extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hashtags';

    protected $fillable = [
        'sort',
        'activity',

        'name',
        'slug',
        'locale',

        'color',
        'short',
        'description',

        'views',
        'likes',          // ✅ добавили

        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    protected $casts = [
        'sort'      => 'int',
        'activity'  => 'bool',
        'views'     => 'int',
        'likes'     => 'int', // ✅ привели к int
    ];

    /* ======================== Relations (обратные) ======================== */

    /** Курсы, использующие этот хештег */
    public function courses(): MorphToMany
    {
        return $this->morphedByMany(
            Course::class,
            'hashtagable',
            'hashtagables',
            'hashtag_id',
            'hashtagable_id'
        );
    }

    /** Модули, использующие этот хештег */
    public function modules(): MorphToMany
    {
        return $this->morphedByMany(
            Module::class,
            'hashtagable',
            'hashtagables',
            'hashtag_id',
            'hashtagable_id'
        );
    }

    /** Уроки, использующие этот хештег */
    public function lessons(): MorphToMany
    {
        return $this->morphedByMany(
            Lesson::class,
            'hashtagable',
            'hashtagables',
            'hashtag_id',
            'hashtagable_id'
        );
    }

    // Позже можно добавить:
    // public function articles() { ... }
    // public function videos() { ... }
    // и т.д.

    /* ======================== Scopes ======================== */

    /** Только активные */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('activity', true);
    }

    /** По локали */
    public function scopeByLocale(Builder $q, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();
        return $q->where('locale', $locale);
    }

    /** Сортировка по sort ASC, затем по id DESC */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort')->orderByDesc('id');
    }

    /** Популярные */
    public function scopePopular(Builder $q, int $minViews = 0): Builder
    {
        return $q
            ->when($minViews > 0, fn (Builder $qq) => $qq->where('views', '>=', $minViews))
            ->orderByDesc('views');
    }

    /** Публичный набор: active + locale */
    public function scopeForPublic(Builder $q, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return $q
            ->active()
            ->byLocale($locale);
    }

    /** Облако хештегов */
    public function scopeForTagCloud(Builder $q, ?string $locale = null, int $minViews = 0): Builder
    {
        return $q
            ->forPublic($locale)
            ->popular($minViews)
            ->ordered();
    }

    /** Поиск по имени/описанию */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        if (!$term) {
            return $q;
        }

        return $q->where(function (Builder $qq) use ($term) {
            $qq->where('name', 'like', "%{$term}%")
                ->orWhere('short', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }
}
