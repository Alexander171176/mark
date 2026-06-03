<?php

namespace App\Models\Admin\School\SchoolInstructorProfile;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolInstructorProfileTranslation extends Model
{
    use HasFactory;

    protected $table = 'school_instructor_profile_translations';

    protected $fillable = [
        'school_instructor_profile_id',
        'locale',
        'title',
        'short',
        'bio',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    /** Профиль инструктора */
    public function instructorProfile(): BelongsTo
    {
        return $this->belongsTo(SchoolInstructorProfile::class, 'school_instructor_profile_id');
    }
}
