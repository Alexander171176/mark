<?php

namespace App\Models\Admin\School\SubscriptionPlan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolSubscriptionPlanTranslation extends Model
{
    use HasFactory;

    protected $table = 'school_subscription_plan_translations';

    protected $fillable = [
        'school_subscription_plan_id',
        'locale',

        'title',
        'subtitle',
        'short',
        'description',

        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    /* ======================== Relations ======================== */

    /** Тариф */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SchoolSubscriptionPlan::class, 'school_subscription_plan_id');
    }
}
