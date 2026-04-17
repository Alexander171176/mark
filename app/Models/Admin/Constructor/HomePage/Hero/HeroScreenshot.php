<?php

namespace App\Models\Admin\Constructor\HomePage\Hero;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class HeroScreenshot extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /** Коллекции медиа (одна запись = 2 файла: светлый/тёмный варианты) */
    public const MEDIA_COLLECTION_LIGHT = 'light';
    public const MEDIA_COLLECTION_DARK  = 'dark';

    /** Массово заполняемые поля */
    protected $fillable = [
        'hero_section_id',
        'alt',
        'sort',
        'activity',
    ];

    /** Касты */
    protected $casts = [
        'sort'     => 'integer',
        'activity' => 'boolean',
    ];

    /** При изменении скриншота трогаем updated_at у секции */
    protected $touches = ['section'];

    /** Чтобы не ловить N+1 при выборке с URL-ами */
    protected $with = ['media'];

    /** Чтобы URL-ы сразу приходили в JSON */
    protected $appends = [
        'light_image_url', 'light_webp_url', 'light_thumb_url',
        'dark_image_url',  'dark_webp_url',  'dark_thumb_url',
    ];

    /* ==========================
     |   Relations
     ========================== */

    public function section(): BelongsTo
    {
        return $this->belongsTo(HeroSection::class, 'hero_section_id');
    }

    /* ==========================
     |   Scopes
     ========================== */

    public function scopeActive($query)
    {
        return $query->where('activity', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort');
    }

    public function scopeForSection($query, int $sectionId)
    {
        return $query->where('hero_section_id', $sectionId);
    }

    /* ==========================
     |   Spatie Media Library
     ========================== */

    /** Две коллекции, по одному файлу в каждой */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION_LIGHT)->singleFile();
        $this->addMediaCollection(self::MEDIA_COLLECTION_DARK)->singleFile();
    }

    /** Конверсии одинаковые для обеих коллекций */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(80)
            ->performOnCollections(self::MEDIA_COLLECTION_LIGHT, self::MEDIA_COLLECTION_DARK);

        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->performOnCollections(self::MEDIA_COLLECTION_LIGHT, self::MEDIA_COLLECTION_DARK);
    }

    /* ==========================
     |   URL helpers + accessors
     ========================== */

    public function lightUrl(string $conversion = ''): ?string
    {
        $m = $this->getFirstMedia(self::MEDIA_COLLECTION_LIGHT);
        return $m ? $m->getUrl($conversion) : null;
    }

    public function darkUrl(string $conversion = ''): ?string
    {
        $m = $this->getFirstMedia(self::MEDIA_COLLECTION_DARK);
        return $m ? $m->getUrl($conversion) : null;
    }

    // Автодобавляемые аксессоры в JSON
    public function getLightImageUrlAttribute(): ?string
    {
        return $this->lightUrl();
    }

    public function getLightWebpUrlAttribute(): ?string
    {
        return $this->lightUrl('webp');
    }

    public function getLightThumbUrlAttribute(): ?string
    {
        return $this->lightUrl('thumb');
    }

    public function getDarkImageUrlAttribute(): ?string
    {
        return $this->darkUrl();
    }

    public function getDarkWebpUrlAttribute(): ?string
    {
        return $this->darkUrl('webp');
    }

    public function getDarkThumbUrlAttribute(): ?string
    {
        return $this->darkUrl('thumb');
    }
}
