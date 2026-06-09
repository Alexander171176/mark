<?php

namespace App\Models\User\Like;

use App\Models\Admin\School\SchoolModule\SchoolModule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolModuleLike extends Model
{
    use HasFactory;

    protected $table = 'school_module_likes';

    protected $fillable = [
        'user_id',
        'school_module_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'school_module_id' => 'integer',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(SchoolModule::class, 'school_module_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
