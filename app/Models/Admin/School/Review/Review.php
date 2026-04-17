<?php

namespace App\Models\Admin\School\Review;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reviews';

    protected $fillable = [
        'user_id',           // FK -> users.id
        'reviewable_type',   // morph type (App\Models\Admin\Course\Course и т.п.)
        'reviewable_id',     // morph id
        'rating',            // 1..5
        'title',             // заголовок
        'body',              // текст
        'status',            // pending|approved|rejected
        'is_public',         // флаг видимости
        'published_at',      // когда опубликован
        'helpful_count',     // счётчик "полезно"
        'reported_count',    // счётчик жалоб
        'meta',              // произвольные данные (JSON)
    ];

    protected $casts = [
        'rating'         => 'int',
        'is_public'      => 'bool',
        'helpful_count'  => 'int',
        'reported_count' => 'int',
        'meta'           => 'array',
        'published_at'   => 'datetime',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'deleted_at'     => 'datetime',
    ];

    /* ================= Связи ================= */

    // Автор отзыва
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Полиморфная связь к целевой сущности (курс/модуль/урок/бандл/…)
    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    /* ================= Скоупы ================= */

    // Только прошедшие модерацию и публичные
    public function scopePublished($q)
    {
        return $q->where('status', 'approved')
            ->where('is_public', true)
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    // Фильтр по рейтингу
    public function scopeWithMinRating($q, int $min = 4)
    {
        return $q->where('rating', '>=', $min);
    }

    /* ================= Хелперы ================= */

    public function approve(): void
    {
        $this->status = 'approved';
        $this->published_at = now();
        $this->save();
    }

    public function reject(?string $reason = null): void
    {
        $this->status = 'rejected';
        if ($reason) {
            $meta = $this->meta ?? [];
            $meta['reject_reason'] = $reason;
            $this->meta = $meta;
        }
        $this->save();
    }

    public function markHelpful(): void
    {
        $this->increment('helpful_count');
    }

    public function report(): void
    {
        $this->increment('reported_count');
    }
}
