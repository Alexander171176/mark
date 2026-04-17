<?php

namespace App\Models\Admin\Constructor\HomePage\Quickstart;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class QuickstartSection extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /** Имёна коллекций Spatie */
    public const COLLECTION_VIDEO  = 'video';
    public const COLLECTION_POSTER = 'poster';

    protected $table = 'quickstart_sections';
    protected $guarded = [];

    protected $casts = [
        'video_options'             => 'array',
        'is_dark'                   => 'bool',
        'activity'                  => 'bool',
        'secondary_popover_enabled' => 'bool',
        'sort'                      => 'int',
    ];

    protected $appends = ['video_url', 'poster_url'];

    /* ------------ Scopes ------------ */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort')->orderBy('id');
    }

    /* ------------ Media Library ------------ */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::COLLECTION_VIDEO)->singleFile();
        $this->addMediaCollection(self::COLLECTION_POSTER)->singleFile();
    }

    /* ------------ Accessors ------------ */
    public function getVideoUrlAttribute(): ?string
    {
        $url = $this->getFirstMediaUrl(self::COLLECTION_VIDEO);
        return $url !== '' ? $url : null;
    }

    public function getPosterUrlAttribute(): ?string
    {
        $url = $this->getFirstMediaUrl(self::COLLECTION_POSTER);
        return $url !== '' ? $url : null;
    }
}
