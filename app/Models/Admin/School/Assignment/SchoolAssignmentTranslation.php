<?php

namespace App\Models\Admin\School\Assignment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolAssignmentTranslation extends Model
{
    use HasFactory;

    protected $table = 'school_assignment_translations';

    protected $fillable = [
        'school_assignment_id',
        'locale',
        'title',
        'subtitle',
        'short',
        'description',
        'instructions',
    ];

    /** Задание */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(SchoolAssignment::class, 'school_assignment_id');
    }
}
