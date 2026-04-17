<?php

namespace App\Models\Admin\School\LearningCategory;

use App\Models\Admin\School\Course\Course;
use App\Models\User\Like\TrackLike;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Категория обучения (иерархическая, локализованная)
 *
 * @property int                 $id
 * @property int|null            $parent_id
 * @property string              $locale         Двухбуквенная локаль (ru/en/kk)
 * @property string              $name
 * @property string              $slug
 * @property string|null         $short
 * @property string|null         $description
 * @property bool                $activity
 * @property int                 $sort
 * @property int                 $views
 * @property string|null         $meta_title
 * @property string|null         $meta_keywords
 * @property string|null         $meta_desc
 * @property Carbon              $created_at
 * @property Carbon              $updated_at
 * @property Carbon|null         $deleted_at
 */
class LearningCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'learning_categories';

    /**
     * Разрешённые к массовому заполнению поля.
     * (views меняем программно → оставим защищённым)
     */
    protected $fillable = [
        'parent_id',
        'locale',
        'name',
        'slug',
        'short',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_desc',
        'activity',
        'sort',
    ];

    protected $casts = [
        'activity' => 'bool',
        'sort'     => 'int',
        'views'    => 'int',
    ];

    /* ======================== Relations ======================== */

    /** Родительская категория */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** Дочерние категории (с сортировкой) */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort');
    }

    /**
     * Рекурсивная связь для загрузки всех дочерних элементов (опционально).
     * Используйте с осторожностью для глубоких деревьев из-за производительности.
     * Лучше использовать ->with('children', 'children.children', ...) или специализированные запросы.
     *
     * @return HasMany
     */
    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    /** Курсы, привязанные к категории */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'course_has_learning_category',
            'learning_category_id',
            'course_id'
        )->withTimestamps();
    }

    /**
     * Связь: Категория - Изображения (многие ко многим через CategoryImage)
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            LearningCategoryImage::class,
            'learning_category_has_images',
            'learning_category_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderBy('learning_category_has_images.order', 'asc');
    }

    /**
     * Лайки трека (если модель лайка существует).
     * Если лайки реализованы без модели (через query builder) — можно убрать.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(TrackLike::class, 'learning_category_id');
    }

    /* ======================== Scopes ======================== */

    /** По локали */
    public function scopeByLocale(Builder $q, string $locale): Builder
    {
        return $q->where('locale', $locale);
    }

    /**
     * Scope a query to only include root categories (those without a parent).
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /** Только опубликованные */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('activity', true);
    }

    /** Сортировка для дерева/меню внутри одного уровня */
    public function scopeOrdered(Builder $q): Builder
    {
        // сперва по sort, затем по name для стабильности
        return $q->orderBy('sort')->orderBy('name');
    }

    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        if (!$term) {
            return $q;
        }

        return $q->where(function (Builder $qq) use ($term) {
            $qq->where('name', 'like', "%{$term}%")
                ->orWhere('short', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('slug', 'like', "%{$term}%");
        });
    }

    public function scopeSortByParam(Builder $q, ?string $sort): Builder
    {
        return match ($sort) {
            'sort_asc'   => $q->orderBy('sort', 'asc')->orderBy('name'),
            'sort_desc'  => $q->orderBy('sort', 'desc')->orderBy('name'),
            'date_asc'   => $q->orderBy('created_at', 'asc')->orderByDesc('id'),
            'date_desc'  => $q->orderBy('created_at', 'desc')->orderByDesc('id'),
            'views_asc'  => $q->orderBy('views', 'asc')->orderByDesc('id'),
            'views_desc' => $q->orderBy('views', 'desc')->orderByDesc('id'),
            'name_asc'   => $q->orderBy('name', 'asc')->orderByDesc('id'),
            'name_desc'  => $q->orderBy('name', 'desc')->orderByDesc('id'),
            'likes_asc'  => $q->withCount('likes')->orderBy('likes_count', 'asc')->orderByDesc('id'),
            'likes_desc' => $q->withCount('likes')->orderBy('likes_count', 'desc')->orderByDesc('id'),
            default      => $q->ordered(),
        };
    }

    /**
     * Проверяет, является ли категория корневой.
     *
     * @return bool
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Проверяет, есть ли у категории дочерние элементы.
     * Полезно для отображения иконки "развернуть" в аккордеоне.
     *
     * @return bool
     */
    public function hasChildren(): bool
    {
        // Можно использовать exists() для производительности, если не нужны сами дети
        return $this->children()->exists();
        // Или, если дети уже загружены (через with('children'))
        // return $this->relationLoaded('children') && $this->children->isNotEmpty();
    }
}
