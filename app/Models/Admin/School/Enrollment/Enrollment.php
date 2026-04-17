<?php

namespace App\Models\Admin\School\Enrollment;

use App\Models\Admin\Finance\Order\Order;
use App\Models\Admin\School\Certificate\Certificate;
use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\CourseSchedule\CourseSchedule;
use App\Models\Admin\School\ProgressRecord\ProgressRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'enrollments';

    protected $fillable = [
        'user_id',             // FK -> users.id (кто зачислён)
        'course_id',           // FK -> courses.id (на какой курс)
        'course_schedule_id',  // FK -> course_schedules.id (какой поток, nullable)
        'order_id',            // FK -> orders.id (исходный заказ, nullable)
        'status',              // active|completed|cancelled|expired|paused
        'started_at',          // когда начался доступ
        'expires_at',          // когда истекает доступ (если задан)
        'completed_at',        // когда завершён курс
        'progress_percent',    // агрегированный прогресс (0..100)
        'notes',               // служебные комментарии
        'meta',                // произвольные метаданные (JSON)
    ];

    protected $casts = [
        'progress_percent' => 'int',
        'meta'             => 'array',
        'started_at'       => 'datetime',
        'expires_at'       => 'datetime',
        'completed_at'     => 'datetime',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
        'deleted_at'       => 'datetime', // опционально
    ];

    /* =============== Связи =============== */

    // Пользователь
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Курс
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // Поток/расписание (nullable)
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(CourseSchedule::class, 'course_schedule_id');
    }

    // Исходный заказ (nullable)
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Подробные записи прогресса (по модулям/урокам)
    public function progressRecords(): HasMany
    {
        return $this->hasMany(ProgressRecord::class);
    }

    // Итоговый сертификат (если в домене предполагается один сертификат на зачисление)
    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }

    /* =============== Скоупы =============== */

    // Активные (есть доступ сейчас)
    public function scopeActive($q)
    {
        return $q->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    // Завершённые
    public function scopeCompleted($q)
    {
        return $q->where('status', 'completed');
    }

    // По пользователю и курсу
    public function scopeForUserCourse($q, int $userId, int $courseId)
    {
        return $q->where('user_id', $userId)->where('course_id', $courseId);
    }

    /* =============== Хелперы =============== */

    // Есть ли доступ прямо сейчас
    public function getIsAccessibleAttribute(): bool
    {
        if ($this->status !== 'active') return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        return true;
    }

    // Сколько дней осталось до истечения (null — без срока)
    public function getDaysLeftAttribute(): ?int
    {
        return $this->expires_at ? now()->diffInDays($this->expires_at, false) : null;
    }
}
