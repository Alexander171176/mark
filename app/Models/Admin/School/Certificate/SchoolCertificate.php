<?php

namespace App\Models\Admin\School\Certificate;

use App\Models\Admin\School\Course\SchoolCourse;
use App\Models\Admin\School\Enrollment\SchoolEnrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolCertificate extends Model
{
    use HasFactory;

    protected $table = 'school_certificates';

    protected $fillable = [
        'user_id',
        'school_course_id',
        'school_enrollment_id',
        'number',
        'verification_code',
        'issued_at',
        'expires_at',
        'score',
        'hours',
        'status',
        'revoked_at',
        'name_on_certificate',
        'notes',
        'meta',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'school_course_id' => 'integer',
        'school_enrollment_id' => 'integer',
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
        'score' => 'integer',
        'hours' => 'decimal:2',
        'meta' => 'array',
    ];

    /** Пользователь */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Курс */
    public function course(): BelongsTo
    {
        return $this->belongsTo(SchoolCourse::class, 'school_course_id');
    }

    /** Зачисление */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(SchoolEnrollment::class, 'school_enrollment_id');
    }

    /** Действительные */
    public function scopeValid(Builder $q): Builder
    {
        return $q
            ->where('status', 'issued')
            ->where(function (Builder $query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            });
    }

    /** По коду проверки */
    public function scopeByCode(Builder $q, string $code): Builder
    {
        return $q->where('verification_code', $code);
    }

    /** Истёк ли сертификат */
    public function getIsExpiredAttribute(): bool
    {
        return !is_null($this->expires_at) && $this->expires_at->lt(now());
    }

    /** Отозван ли сертификат */
    public function getIsRevokedAttribute(): bool
    {
        return $this->status === 'revoked' || !is_null($this->revoked_at);
    }
}
