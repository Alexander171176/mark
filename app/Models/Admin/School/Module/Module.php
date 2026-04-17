<?php

namespace App\Models\Admin\School\Module;

use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\Lesson\Lesson;
use App\Models\User\Like\ModuleLike;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Модуль курса — логическая группа уроков внутри Course.
 *
 * @property int         $id
 * @property int         $course_id           FK -> courses.id
 * @property string      $locale              Локаль
 *
 * @property string      $title               Заголовок модуля
 * @property string      $slug                Слаг (уникален в рамках course_id)
 * @property string|null $subtitle            Подзаголовок/оффер
 * @property string|null $short               Краткое описание
 * @property string|null $description         Полное описание (markdown/HTML)
 *
 * @property string|null $meta_title
 * @property string|null $meta_keywords
 * @property string|null $meta_desc
 *
 * @property string|null $status              draft|published|archived
 * @property string|null $availability        unlisted|public|private
 *
 * @property int|null    $difficulty          0..5
 * @property int|null    $duration    Общая длительность
 *
 * @property int         $lessons_count       Денорм: кол-во уроков
 * @property int         $popularity          Популярность
 * @property int         $rating_count        Кол-во оценок
 * @property float       $rating_avg          Средняя оценка 0.00–5.00
 * @property int         $views               Просмотры
 * @property int         $likes               Лайки
 *
 * @property bool        $activity            Активен/скрыт
 * @property int         $sort                Порядок внутри курса
 *
 * @property Carbon|null $published_at
 * @property Carbon      $created_at
 * @property Carbon      $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property-read Course                 $course
 * @property-read Collection|Lesson[]    $lessons
 * @property-read Collection|ModuleImage[] $images
 * @property-read ModuleImage|null       $primaryImage
 */
class Module extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'modules';

    protected $fillable = [
        'course_id',
        'locale',

        'title',
        'slug',
        'subtitle',
        'short',
        'description',

        'meta_title',
        'meta_keywords',
        'meta_desc',

        'status',
        'availability',
        'difficulty',
        'duration',

        'lessons_count',
        'popularity',
        'rating_count',
        'rating_avg',
        'views',
        'likes',

        'activity',
        'sort',

        'published_at',
    ];

    protected $casts = [
        'difficulty'        => 'int',
        'duration'          => 'int',

        'lessons_count'     => 'int',
        'popularity'        => 'int',
        'rating_count'      => 'int',
        'rating_avg'        => 'float',
        'views'             => 'int',
        'likes'             => 'int',

        'activity'          => 'bool',
        'sort'              => 'int',

        'published_at'      => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /** Родительский курс */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /** Уроки модуля (1:N) */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort');
    }

    /** Изображения модуля (через pivot module_has_images) */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            ModuleImage::class,
            'module_has_images',
            'module_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderBy('module_has_images.order', 'asc');
    }

    /** Лайк модуля */
    public function likes(): HasMany
    {
        return $this->hasMany(ModuleLike::class, 'module_id');
    }

    /* ======================== Scopes ======================== */

    /** Удобная сортировка: по sort ↑ затем по id ↓ (как в Course::scopeSorted) */
    public function scopeSorted(Builder $q): Builder
    {
        return $q->orderBy('sort')->orderByDesc('id');
    }

    /** Алиас старого scopeOrdered, если где-то уже используется */
    public function scopeOrdered(Builder $q): Builder
    {
        return $this->scopeSorted($q);
    }

    /** Только активные модули */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('activity', true);
    }

    /** По локали */
    public function scopeByLocale(Builder $q, string $locale): Builder
    {
        return $q->where('locale', $locale);
    }

    /**
     * Опубликованные и не private
     * status: published, availability: != private
     */
    public function scopePublished(Builder $q): Builder
    {
        return $q
            ->where('status', 'published')
            ->where('availability', '!=', 'private')
            ->whereNotNull('published_at');
    }

    /** Поиск по заголовку/подзаголовку/краткому/описанию модуля */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        if (!$term) {
            return $q;
        }

        return $q->where(function (Builder $qq) use ($term) {
            $qq->where('title', 'like', "%{$term}%")
                ->orWhere('subtitle', 'like', "%{$term}%")
                ->orWhere('short', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /** Сортировка по параметру */
    public function scopeSortByParam(Builder $q, ?string $sort): Builder
    {
        return match ($sort) {
            'sort_asc'        => $q->orderBy('sort', 'asc')->orderByDesc('id'),
            'sort_desc'       => $q->orderBy('sort', 'desc')->orderByDesc('id'),
            'date_asc'        => $q->orderBy('created_at', 'asc')->orderByDesc('id'),
            'date_desc'       => $q->orderBy('created_at', 'desc')->orderByDesc('id'),
            'views_asc'       => $q->orderBy('views', 'asc')->orderByDesc('id'),
            'views_desc'      => $q->orderBy('views', 'desc')->orderByDesc('id'),
            'likes_asc'       => $q->withCount('likes')->orderBy('likes_count', 'asc')->orderByDesc('id'),
            'likes_desc'      => $q->withCount('likes')->orderBy('likes_count', 'desc')->orderByDesc('id'),
            'rating_asc'      => $q->orderBy('rating_avg', 'asc')->orderByDesc('id'),
            'rating_desc'     => $q->orderBy('rating_avg', 'desc')->orderByDesc('id'),
            'popularity_asc'  => $q->orderBy('popularity', 'asc')->orderByDesc('id'),
            'popularity_desc' => $q->orderBy('popularity', 'desc')->orderByDesc('id'),
            'lessons_asc'     => $q->orderBy('lessons_count', 'asc')->orderByDesc('id'),
            'lessons_desc'    => $q->orderBy('lessons_count', 'desc')->orderByDesc('id'),
            'duration_asc'    => $q->orderBy('duration', 'asc')->orderByDesc('id'),
            'duration_desc'   => $q->orderBy('duration', 'desc')->orderByDesc('id'),
            'title_asc'       => $q->orderBy('title', 'asc')->orderByDesc('id'),
            'title_desc'      => $q->orderBy('title', 'desc')->orderByDesc('id'),
            default           => $q->sorted(),
        };
    }

    /* ======================== Accessors ======================== */

    /** Главное изображение модуля (с наименьшим order) */
    public function getPrimaryImageAttribute(): ?ModuleImage
    {
        $relLoaded = $this->relationLoaded('images')
            ? $this->images
            : $this->images()->take(1)->get();

        return $relLoaded->first();
    }

    /** (опционально) Route Key = slug */
    // public function getRouteKeyName(): string
    // {
    //     return 'slug';
    // }
}
