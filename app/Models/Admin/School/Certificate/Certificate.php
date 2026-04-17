<?php

namespace App\Models\Admin\School\Certificate;

use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\Enrollment\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'certificates';

    protected $fillable = [
        'user_id',               // FK -> users.id
        'course_id',             // FK -> courses.id
        'enrollment_id',         // FK -> enrollments.id (nullable)
        'number',                // номер сертификата
        'verification_code',     // код/хэш для проверки
        'issued_at',             // выдан
        'expires_at',            // истекает (если применимо)
        'score',                 // итоговый процент/оценка
        'hours',                 // академические часы
        'status',                // draft|issued|revoked|expired
        'revoked_at',            // когда отозван
        'name_on_certificate',   // имя на сертификате
        'notes',                 // заметки
        'meta',                  // произвольные данные (JSON)
    ];

    protected $casts = [
        'issued_at'  => 'datetime',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
        'score'      => 'int',
        'hours'      => 'decimal:2',
        'meta'       => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /* ================= Связи ================= */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /* ================= Скоупы ================= */

    // Действительные (не отозваны и не истёк срок)
    public function scopeValid($q)
    {
        return $q->where('status', 'issued')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            });
    }

    // По коду проверки
    public function scopeByCode($q, string $code)
    {
        return $q->where('verification_code', $code);
    }

    /* ================= Хелперы ================= */

    public function getIsExpiredAttribute(): bool
    {
        return !is_null($this->expires_at) && $this->expires_at->lt(now());
    }

    public function getIsRevokedAttribute(): bool
    {
        return $this->status === 'revoked' || !is_null($this->revoked_at);
    }
}
