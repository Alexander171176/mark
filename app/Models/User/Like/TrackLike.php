<?php

namespace App\Models\User\Like;

use App\Models\Admin\School\LearningCategory\LearningCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackLike extends Model
{
    use HasFactory;

    protected $table = 'track_likes';

    protected $fillable = [
        'user_id',
        'learning_category_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'learning_category_id' => 'integer',
    ];

    /**
     * Связь с категорией обучения (track)
     */
    public function track(): BelongsTo
    {
        return $this->belongsTo(LearningCategory::class, 'learning_category_id');
    }

    /**
     * Пользователь
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
