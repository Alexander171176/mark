<?php

namespace App\Models\User\Like;

use App\Models\Admin\School\SchoolTrack\SchoolTrack;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolTrackLike extends Model
{
    use HasFactory;

    protected $table = 'school_track_likes';

    protected $fillable = [
        'user_id',
        'school_track_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'school_track_id' => 'integer',
    ];

    public function track(): BelongsTo
    {
        return $this->belongsTo(SchoolTrack::class, 'school_track_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
