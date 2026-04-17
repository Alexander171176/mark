<?php

namespace App\Models\Admin\School\CourseSchedule;

use App\Models\Admin\School\CohortEnrollment\CohortEnrollment;
use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\InstructorProfile\InstructorProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Расписание / поток курса.
 *
 * Один Course может иметь много CourseSchedule.
 * Студенты записываются на конкретный поток.
 */
class CourseSchedule extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'course_schedules';

    /**
     * Массово заполняемые поля — строго по миграции.
     */
    protected $fillable = [
        'course_id',
        'instructor_profile_id',

        'sort',
        'activity',
        'locale',

        'title',
        'slug',
        'subtitle',
        'short',
        'description',

        'meta_title',
        'meta_keywords',
        'meta_desc',

        'starts_at',
        'ends_at',
        'enroll_starts_at',
        'enroll_ends_at',

        'capacity',
        'is_online',
        'location',
        'meeting_url',
        'timezone',

        'status',
        'views',
        'notes',
    ];

    /**
     * Касты под новые поля.
     */
    protected $casts = [
        'course_id'             => 'int',
        'instructor_profile_id' => 'int',

        'sort'                  => 'int',
        'activity'              => 'bool',
        'capacity'              => 'int',
        'is_online'             => 'bool',
        'views'                 => 'int',

        'starts_at'             => 'datetime',
        'ends_at'               => 'datetime',
        'enroll_starts_at'      => 'datetime',
        'enroll_ends_at'        => 'datetime',

        'created_at'            => 'datetime',
        'updated_at'            => 'datetime',
        'deleted_at'            => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /**
     * Родительский курс.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Ведущий преподаватель потока.
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(InstructorProfile::class, 'instructor_profile_id');
    }

    /**
     * Записи/заявки на этот поток.
     */
    public function cohortEnrollments(): HasMany
    {
        return $this->hasMany(CohortEnrollment::class, 'course_schedule_id');
    }

    /**
     * Изображения, привязанные к потоку через pivot course_schedule_has_images.
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            CourseScheduleImage::class,
            'course_schedule_has_images',
            'course_schedule_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderBy('course_schedule_has_images.order', 'asc');
    }

    /* ======================== Media (Spatie) ======================== */

    /**
     * Медиа для потока: доп. материалы, вложения, PDF и т.п.
     */
    public function registerMediaCollections(): void
    {
        // Общая коллекция вложений для потока
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

    /* ======================== Scopes ======================== */

    /**
     * Только активные потоки по флагу activity.
     */
    public function scopeActive($q)
    {
        return $q->where('activity', true);
    }

    /**
     * Только опубликованные потоки.
     */
    public function scopePublished($q)
    {
        return $q->where('status', 'published')
            ->where('activity', true);
    }

    /**
     * Сортировка по sort, затем по id (стабильный порядок).
     */
    public function scopeOrdered($q)
    {
        return $q->orderBy('sort')->orderBy('id');
    }

    /**
     * Единообразная сортировка: sort ↑, id ↓ (как у курсов/уроков).
     */
    public function scopeSorted($q)
    {
        return $q->orderBy('sort')->orderByDesc('id');
    }

    /**
     * Фильтр по локали.
     */
    public function scopeByLocale($q, string $locale)
    {
        return $q->where('locale', $locale);
    }

    /**
     * Поиск по названию / подзаголовку / краткому и полному описанию.
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
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /**
     * Ближайшие будущие потоки (по датам).
     */
    public function scopeUpcomingDates($q)
    {
        return $q->where('status', 'published')
            ->whereNotNull('starts_at')
            ->where('starts_at', '>', now());
    }

    /**
     * Текущие идущие потоки (по датам начала/окончания).
     */
    public function scopeRunning($q)
    {
        return $q->where('status', 'published')
            ->whereNotNull('starts_at')
            ->whereNotNull('ends_at')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    /**
     * Потоки, доступные для записи (окно записи открыто).
     */
    public function scopeOpenForEnroll($q)
    {
        return $q->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('enroll_starts_at')
                    ->orWhere('enroll_starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('enroll_ends_at')
                    ->orWhere('enroll_ends_at', '>=', now());
            });
    }

    /* ======================== Helpers ======================== */

    /**
     * Атрибут: сейчас ли открыта запись на поток.
     */
    public function getIsEnrollmentOpenAttribute(): bool
    {
        $now = now();

        $okStart = is_null($this->enroll_starts_at) || $this->enroll_starts_at->lte($now);
        $okEnd   = is_null($this->enroll_ends_at)   || $this->enroll_ends_at->gte($now);

        return $this->status === 'published' && $okStart && $okEnd;
    }
}
