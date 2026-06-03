<?php

namespace App\Models\Admin\School\SchoolCoupon;

use App\Models\Admin\School\SchoolBundle\SchoolBundle;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolCoupon extends Model
{
    use HasFactory;

    protected $table = 'school_coupons';

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'currency',
        'min_order_total',
        'max_uses',
        'max_uses_per_user',
        'used_count',
        'applies_to',
        'starts_at',
        'ends_at',
        'activity',
        'stackable',
        'meta',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_total' => 'decimal:2',
        'max_uses' => 'integer',
        'max_uses_per_user' => 'integer',
        'used_count' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'activity' => 'boolean',
        'stackable' => 'boolean',
        'meta' => 'array',
    ];

    /* ======================== Relations ======================== */

    /** Курсы */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolCourse::class,
            'school_coupon_has_courses',
            'school_coupon_id',
            'school_course_id'
        );
    }

    /** Наборы курсов */
    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolBundle::class,
            'school_coupon_has_bundles',
            'school_coupon_id',
            'school_bundle_id'
        );
    }

    /* ======================== Scopes ======================== */

    /** Только активные */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('activity', true);
    }

    /** Действующие сейчас */
    public function scopeCurrent(Builder $q): Builder
    {
        $now = now();

        return $q
            ->active()
            ->where(function (Builder $query) use ($now) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->where(function (Builder $query) use ($now) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $now);
            });
    }

    /** По коду */
    public function scopeByCode(Builder $q, string $code): Builder
    {
        return $q->where('code', strtoupper(trim($code)));
    }

    /** Для курсов */
    public function scopeForCourses(Builder $q): Builder
    {
        return $q->whereIn('applies_to', ['any', 'courses']);
    }

    /** Для наборов */
    public function scopeForBundles(Builder $q): Builder
    {
        return $q->whereIn('applies_to', ['any', 'bundles']);
    }

    /* ======================== Accessors ======================== */

    /** Сейчас действителен */
    public function getIsCurrentlyValidAttribute(): bool
    {
        $now = now();

        $inWindow = (is_null($this->starts_at) || $this->starts_at->lte($now))
            && (is_null($this->ends_at) || $this->ends_at->gte($now));

        return $this->activity && $inWindow;
    }

    /** Исчерпан лимит */
    public function getIsUsageLimitReachedAttribute(): bool
    {
        return !is_null($this->max_uses)
            && $this->used_count >= $this->max_uses;
    }
}
