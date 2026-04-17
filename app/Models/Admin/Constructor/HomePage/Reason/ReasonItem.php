<?php

namespace App\Models\Admin\Constructor\HomePage\Reason;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// Spatie Media Library

class ReasonItem extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public const MEDIA_COLLECTION_LIGHT = 'light';
    public const MEDIA_COLLECTION_DARK  = 'dark';

    protected $table = 'reason_items';
    protected $guarded = [];

    protected $casts = [
        'activity' => 'boolean',
        'sort'     => 'integer',
    ];

    // часто хотим сразу иметь медиа
    protected $with = ['media'];

    // удобные URL-поля в JSON
    protected $appends = [
        'light_image_url', 'light_webp_url', 'light_thumb_url',
        'dark_image_url',  'dark_webp_url',  'dark_thumb_url',
    ];

    // при изменении айтема — трогаем updated_at секции
    protected $touches = ['section'];

    protected $attributes = [
        'align' => 'left',
    ];

    /* ---------- Relations ---------- */
    public function section(): BelongsTo
    {
        return $this->belongsTo(ReasonSection::class, 'section_id');
    }

    /* ---------- Spatie Media ---------- */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION_LIGHT)->singleFile();
        $this->addMediaCollection(self::MEDIA_COLLECTION_DARK)->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        // webp
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(78)
            ->nonQueued()
            ->performOnCollections(self::MEDIA_COLLECTION_LIGHT, self::MEDIA_COLLECTION_DARK);

        // thumb (16:9 для списков; при желании поменяем размер)
        $this->addMediaConversion('thumb')
            ->width(640)
            ->height(360)
            ->sharpen(8)
            ->nonQueued()
            ->performOnCollections(self::MEDIA_COLLECTION_LIGHT, self::MEDIA_COLLECTION_DARK);
    }

    /* ---------- URL helpers + accessors ---------- */
    protected function fromLight(string $conversion = ''): ?string
    {
        $m = $this->getFirstMedia(self::MEDIA_COLLECTION_LIGHT);
        return $m ? $m->getUrl($conversion) : null;
    }

    protected function fromDark(string $conversion = ''): ?string
    {
        $m = $this->getFirstMedia(self::MEDIA_COLLECTION_DARK);
        return $m ? $m->getUrl($conversion) : null;
    }

    public function getLightImageUrlAttribute(): ?string { return $this->fromLight(); }
    public function getLightWebpUrlAttribute(): ?string  { return $this->fromLight('webp'); }
    public function getLightThumbUrlAttribute(): ?string { return $this->fromLight('thumb'); }

    public function getDarkImageUrlAttribute(): ?string  { return $this->fromDark(); }
    public function getDarkWebpUrlAttribute(): ?string   { return $this->fromDark('webp'); }
    public function getDarkThumbUrlAttribute(): ?string  { return $this->fromDark('thumb'); }

    /* ---------- Scopes ---------- */
    public function scopeOrdered($q) { return $q->orderBy('sort')->orderBy('id'); }
    public function scopeActive($q)  { return $q->where('activity', true); }
}
