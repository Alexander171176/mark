<?php

namespace App\Models\Admin\School\Quiz;

use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\Lesson\Lesson;
use App\Models\Admin\School\Module\Module;
use App\Models\Admin\School\QuizAnswer\QuizAnswer;
use App\Models\Admin\School\QuizAttempt\QuizAttempt;
use App\Models\Admin\School\QuizQuestion\QuizQuestion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Квиз/тест. Может быть привязан к курсу/модулю/уроку.
 */
class Quiz extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'quizzes';

    protected $fillable = [
        'course_id',
        'module_id',
        'lesson_id',
        'sort',
        'activity',
        'left',
        'main',
        'right',
        'locale',
        'title',
        'slug',
        'short',
        'description',
        'type',
        'attempts_limit',
        'time_limit_minutes',
        'pass_score',
        'published_at',
    ];

    protected $casts = [
        'attempts_limit'     => 'int',
        'time_limit_minutes' => 'int',
        'pass_score'         => 'int',
        'sort'               => 'int',
        'activity'           => 'bool',
        'left'               => 'bool',
        'main'               => 'bool',
        'right'              => 'bool',
        'published_at'       => 'datetime',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
    ];

    /* ========= Отношения ========= */

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

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'quiz_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class, 'quiz_id');
    }

    /**
     * Изображения, привязанные к квизу через pivot quiz_has_images.
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            QuizImage::class,
            'quiz_has_images',
            'quiz_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderBy('quiz_has_images.order', 'asc');
    }

    /* ======================== Media (Spatie) ======================== */

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(225)
            ->nonQueued();
    }

    /* ========= Скоупы ========= */

    /**
     * Только опубликованные и видимые.
     */
    public function scopePublished($q)
    {
        return $q
            ->where('activity', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort')->orderBy('id');
    }

    public function scopeSorted($q)
    {
        return $q->orderBy('sort')->orderByDesc('id');
    }

    public function scopeActive($q)
    {
        return $q->where('activity', true);
    }

    public function scopeMain($q)
    {
        return $q->where('main', true);
    }

    public function scopeLeft($q)
    {
        return $q->where('left', true);
    }

    public function scopeRight($q)
    {
        return $q->where('right', true);
    }

    public function scopeByLocale($q, string $locale)
    {
        return $q->where('locale', $locale);
    }

    public function scopeSearch($q, ?string $term)
    {
        if (!$term) {
            return $q;
        }

        return $q->where(function ($qq) use ($term) {
            $qq->where('title', 'like', "%{$term}%")
                ->orWhere('short', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

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
}
