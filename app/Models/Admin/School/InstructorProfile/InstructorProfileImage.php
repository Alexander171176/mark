<?php

namespace App\Models\Admin\School\InstructorProfile;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class InstructorProfileImage extends BaseImage
{
    use HasFactory;

    protected $table = 'instructor_profile_images';

    public function profiles(): BelongsToMany
    {
        return $this->belongsToMany(
            InstructorProfile::class,
            'instructor_profile_has_images',
            'image_id',                 // FK на текущую модель (instructor_images.id)
            'instructor_profile_id')    // FK на instructors.id
        ->withPivot('order')
            ->orderBy('instructor_profile_has_images.order', 'asc');
    }
}
