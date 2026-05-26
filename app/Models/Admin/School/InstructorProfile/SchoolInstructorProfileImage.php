<?php

namespace App\Models\Admin\School\InstructorProfile;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolInstructorProfileImage extends BaseImage
{
    use HasFactory;

    protected $table = 'school_instructor_profile_images';

    /* ======================== Relations ======================== */

    /** Профили инструкторов */
    public function profiles(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolInstructorProfile::class,
            'school_instructor_profile_has_images',
            'image_id',
            'school_instructor_profile_id'
        )
            ->withPivot('order')
            ->orderBy('school_instructor_profile_has_images.order', 'asc');
    }
}
