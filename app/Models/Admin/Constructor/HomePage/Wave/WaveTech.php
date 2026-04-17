<?php

namespace App\Models\Admin\Constructor\HomePage\Wave;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class WaveTech extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'wave_teches';

    protected $fillable = [
        'wave_section_id',
        'title',
        'subtitle',
        'description',
        'image_light',   // оставляем поля как бэкап/совместимость
        'image_dark',
        'alt',
        'sort',
        'activity',
    ];

    protected $casts = [
        'sort'     => 'integer',
        'activity' => 'boolean',
    ];

    public const MEDIA_COLLECTION_LIGHT = 'light';
    public const MEDIA_COLLECTION_DARK  = 'dark';

    public function section(): BelongsTo
    {
        return $this->belongsTo(WaveSection::class, 'wave_section_id');
    }

    public function scopeOrdered($q) { return $q->orderBy('sort')->orderBy('id'); }
    public function scopeActive($q)  { return $q->where('activity', true); }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION_LIGHT)
            ->singleFile()
            ->acceptsMimeTypes(['image/svg+xml', 'image/png', 'image/webp']);

        $this->addMediaCollection(self::MEDIA_COLLECTION_DARK)
            ->singleFile()
            ->acceptsMimeTypes(['image/svg+xml', 'image/png', 'image/webp']);
    }

    /** URL-акцессоры: сначала Media Library, потом fallback на строковые поля */
    protected $appends = ['image_light_url', 'image_dark_url'];

    public function getImageLightUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl(self::MEDIA_COLLECTION_LIGHT)
            ?: ($this->image_light ? asset($this->image_light) : null);
    }

    public function getImageDarkUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl(self::MEDIA_COLLECTION_DARK)
            ?: ($this->image_dark ? asset($this->image_dark) : null);
    }
}
