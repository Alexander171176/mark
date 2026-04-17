<?php

namespace App\Traits;

use App\Models\Admin\School\Hashtag\Hashtag;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HashtagsTrait
{
    /**
     * Полиморфные хештеги для любой сущности (Course, Module, Lesson и т.п.)
     */
    public function hashtags(): MorphToMany
    {
        return $this->morphToMany(
            Hashtag::class,
            'hashtagable',
            'hashtagables',     // <-- новая таблица
            'hashtagable_id',
            'hashtag_id'
        )->withTimestamps();
    }
}
