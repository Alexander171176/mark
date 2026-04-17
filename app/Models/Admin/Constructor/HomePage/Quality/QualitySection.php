<?php

namespace App\Models\Admin\Constructor\HomePage\Quality;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// Spatie Media Library

class QualitySection extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public const MEDIA_COLLECTION_LIGHT = 'light';
    public const MEDIA_COLLECTION_DARK  = 'dark';

    protected $table   = 'quality_sections';
    protected $guarded = [];

    protected $casts = [
        'is_dark'  => 'boolean',
        'activity' => 'boolean',
        'sort'     => 'integer',
    ];

    // Автоподгрузка медиа (как в DemoItem)
    protected $with = ['media'];

    // Автодобавляемые аксессоры к JSON
    protected $appends = [
        'light_image_url', 'light_webp_url', 'light_thumb_url',
        'dark_image_url',  'dark_webp_url',  'dark_thumb_url',
    ];

    /* ---------- Relations ---------- */
    public function items(): HasMany
    {
        return $this->hasMany(QualityItem::class, 'section_id');
    }

    /* ---------- Scopes ---------- */
    public function scopeOrdered($q) { return $q->orderBy('sort')->orderBy('id'); }
    public function scopeActive($q)  { return $q->where('activity', true); }

    /* ---------- Spatie Media ---------- */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION_LIGHT)->singleFile();
        $this->addMediaCollection(self::MEDIA_COLLECTION_DARK)->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(78)
            ->nonQueued() // <-- добавь
            ->performOnCollections(self::MEDIA_COLLECTION_LIGHT, self::MEDIA_COLLECTION_DARK);

        $this->addMediaConversion('thumb')
            ->width(640)
            ->height(360)
            ->sharpen(8)
            ->nonQueued() // <-- добавь
            ->performOnCollections(self::MEDIA_COLLECTION_LIGHT, self::MEDIA_COLLECTION_DARK);
    }

    /* ---------- URL helpers + accessors ---------- */
    protected function fromLight(string $conversion = ''): ?string
    {
        $m = $this->getFirstMedia(self::MEDIA_COLLECTION_LIGHT);
        return $m ? url($m->getUrl($conversion)) : null; // <-- оборачиваем
    }

    protected function fromDark(string $conversion = ''): ?string
    {
        $m = $this->getFirstMedia(self::MEDIA_COLLECTION_DARK);
        return $m ? url($m->getUrl($conversion)) : null; // <-- оборачиваем
    }

    public function getLightImageUrlAttribute(): ?string { return $this->fromLight(); }
    public function getLightWebpUrlAttribute(): ?string  { return $this->fromLight('webp'); }
    public function getLightThumbUrlAttribute(): ?string { return $this->fromLight('thumb'); }

    public function getDarkImageUrlAttribute(): ?string  { return $this->fromDark(); }
    public function getDarkWebpUrlAttribute(): ?string   { return $this->fromDark('webp'); }
    public function getDarkThumbUrlAttribute(): ?string  { return $this->fromDark('thumb'); }
}
