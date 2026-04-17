<?php

namespace App\Models\User\Like;

use App\Models\Admin\School\Module\Module;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleLike extends Model
{
    use HasFactory;

    protected $table = 'module_likes';

    protected $fillable = [
        'user_id',
        'module_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'module_id' => 'integer',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
