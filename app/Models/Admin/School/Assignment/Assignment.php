<?php

namespace App\Models\Admin\School\Assignment;

use App\Models\Admin\School\AssignmentSubmission\AssignmentSubmission;
use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\InstructorProfile\InstructorProfile;
use App\Models\Admin\School\Lesson\Lesson;
use App\Models\Admin\School\Module\Module;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Assignment extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'assignments';

    /**
     * Массово заполняемые поля — строго по миграции.
     */
    protected $fillable = [
        'course_id',              // FK -> courses.id
        'module_id',              // FK -> modules.id
        'lesson_id',              // FK -> lessons.id
        'instructor_profile_id',  // FK -> instructor_profiles.id

        'sort',                   // Сортировка
        'activity',               // Флаг активности
        'left',                   // Флаг активности в левой колонке
        'main',                   // Флаг активности по центру
        'right',                  // Флаг активности в правой колонке
        'locale',                 // Локаль

        'title',                  // Заголовок задания
        'slug',                   // ЧПУ
        'subtitle',               // Подзаголовок
        'short',                  // Краткое описание
        'description',            // Описание
        'instructions',           // Инструкции
        'published_at',           // Публикация
        'status',                 // draft|published|archived
        'visibility',             // public|enrolled|private
        'attempts_limit',         // Лимит попыток (0 = без ограничений)
        'grading_type',           // manual|auto
        'max_score',              // Максимальный балл
        'due_at',                 // Дедлайн
    ];

    /**
     * Касты под новые поля.
     */
    protected $casts = [
        'course_id'      => 'int',
        'module_id'      => 'int',
        'lesson_id'      => 'int',
        'sort'           => 'int',
        'activity'       => 'bool',
        'left'           => 'bool',
        'main'           => 'bool',
        'right'          => 'bool',
        'max_score'      => 'int',
        'attempts_limit' => 'int',
        'due_at'         => 'datetime',
        'published_at'   => 'datetime',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'deleted_at'     => 'datetime',
    ];

    /* =================== Relations =================== */

    // Контекст привязки
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    // Автор (преподаватель)
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(InstructorProfile::class, 'instructor_profile_id');
    }

    // Связанные отправки студентов (создадим эту модель позже)
    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    /**
     * Изображения, привязанные к заданию через pivot assignment_has_images.
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            AssignmentImage::class,
            'assignment_has_images',
            'assignment_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderBy('assignment_has_images.order', 'asc');
    }

    /* ======================== Media (Spatie) ======================== */

    /**
     * Медиа для заданий: доп. материалы, вложения, PDF и т.п.
     */
    public function registerMediaCollections(): void
    {
        // Общая коллекция вложений для урока
        $this->addMediaCollection('attachments');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        // Простейший thumb для изображений во вложениях
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(225)
            ->nonQueued();
    }

    /* =================== Scopes =================== */

    /**
     * Только опубликованные и видимые (по статусу).
     */
    public function scopePublished($q)
    {
        return $q->where('status', 'published')
            ->where('activity', true)
            ->whereNotNull('published_at');
    }

    /**
     * Сортировка по sort, затем по id (для стабильного порядка).
     * (оставляем твой Ordered, но ещё добавим Sorted по аналогии с курсами)
     */
    public function scopeOrdered($q)
    {
        return $q->orderBy('sort')->orderBy('id');
    }

    /**
     * Единообразная сортировка: sort ↑, id ↓.
     */
    public function scopeSorted($q)
    {
        return $q->orderBy('sort')->orderByDesc('id');
    }

    /**
     * Только активные.
     */
    public function scopeActive($q)
    {
        return $q->where('activity', true);
    }

    /**
     * Скоуп для главных (main = 1).
     */
    public function scopeMain($q)
    {
        return $q->where('main', true);
    }

    /**
     * Скоуп для левой колонки (left = 1).
     */
    public function scopeLeft($q)
    {
        return $q->where('left', true);
    }

    /**
     * Скоуп для правой колонки (right = 1).
     */
    public function scopeRight($q)
    {
        return $q->where('right', true);
    }

    /**
     * Фильтр по локали.
     */
    public function scopeByLocale($q, string $locale)
    {
        return $q->where('locale', $locale);
    }

    /**
     * Поиск по заголовку/подзаголовку/краткому и полному описанию.
     */
    public function scopeSearch($q, ?string $term)
    {
        if (!$term) {
            return $q;
        }

        return $q->where(function ($qq) use ($term) {
            $qq->where('title', 'like', "%{$term}%")
                ->orWhere('subtitle', 'like', "%{$term}%")
                ->orWhere('short', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('instructions', 'like', "%{$term}%");
        });
    }

    // По курсу/модулю/уроку
    public function scopeForCourse($q, int $courseId)
    {
        return $q->where('course_id', $courseId);
    }

    public function scopeForModule($q, int $moduleId)
    {
        return $q->where('module_id', $moduleId);
    }

    public function scopeForLesson($q, int $lessonId)
    {
        return $q->where('lesson_id', $lessonId);
    }

    // Ближайшие к дедлайну (например, в течение 7 дней)
    public function scopeDueSoon($q, int $days = 7)
    {
        return $q->whereNotNull('due_at')
            ->whereBetween('due_at', [now(), now()->addDays($days)]);
    }

    /** сортировка */
    public function scopeSortByParam($q, ?string $sort)
    {
        return match ($sort) {
            'sort_asc'   => $q->orderBy('sort', 'asc')->orderByDesc('id'),
            'sort_desc'  => $q->orderBy('sort', 'desc')->orderByDesc('id'),
            'date_asc'   => $q->orderBy('published_at', 'asc')->orderByDesc('id'),
            'date_desc'  => $q->orderBy('published_at', 'desc')->orderByDesc('id'),
            'title_asc'  => $q->orderBy('title', 'asc')->orderByDesc('id'),
            'title_desc' => $q->orderBy('title', 'desc')->orderByDesc('id'),
            'score_asc'  => $q->orderBy('max_score', 'asc')->orderByDesc('id'),
            'score_desc' => $q->orderBy('max_score', 'desc')->orderByDesc('id'),
            'due_asc'    => $q->orderBy('due_at', 'asc')->orderByDesc('id'),
            'due_desc'   => $q->orderBy('due_at', 'desc')->orderByDesc('id'),
            default      => $q->sorted(),
        };
    }

    /* =================== Accessors/Helpers =================== */

    // Просрочено ли задание (по состоянию на сейчас)
    public function getIsOverdueAttribute(): bool
    {
        return !is_null($this->due_at) && $this->due_at->isPast();
    }
}
