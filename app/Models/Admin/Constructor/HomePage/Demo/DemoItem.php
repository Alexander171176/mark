<?php

namespace App\Models\Admin\Constructor\HomePage\Demo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DemoItem extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public const MEDIA_COLLECTION_LIGHT = 'light';
    public const MEDIA_COLLECTION_DARK  = 'dark';

    protected $table   = 'demo_items';
    protected $guarded = [];

    protected $casts = [
        'activity' => 'boolean',
        'sort'     => 'integer',
    ];

    protected $with = ['media'];

    protected $appends = [
        'light_image_url', 'light_webp_url', 'light_thumb_url',
        'dark_image_url',  'dark_webp_url',  'dark_thumb_url',
    ];

    /** При изменении превью трогаем updated_at у группы */
    protected $touches = ['group'];

    /* ========== Relations ========== */
    public function group(): BelongsTo
    {
        return $this->belongsTo(DemoGroup::class, 'group_id');
    }

    /* ========== Scopes ========== */
    public function scopeOrdered($q) { return $q->orderBy('sort')->orderBy('id'); }
    public function scopeActive($q)  { return $q->where('activity', true); }

    /* ========== Spatie Media ========== */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION_LIGHT)->singleFile();
        $this->addMediaCollection(self::MEDIA_COLLECTION_DARK)->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        // Как в HeroScreenshot — без Manipulations
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(78)
            ->performOnCollections(self::MEDIA_COLLECTION_LIGHT, self::MEDIA_COLLECTION_DARK);

        // thumb для карточек демо (примерное превью)
        $this->addMediaConversion('thumb')
            ->width(640)
            ->height(400)
            ->sharpen(8)
            ->performOnCollections(self::MEDIA_COLLECTION_LIGHT, self::MEDIA_COLLECTION_DARK);
    }

    /* ========== URL helpers + accessors ========== */
    protected function light(string $conv = ''): ?string
    {
        $m = $this->getFirstMedia(self::MEDIA_COLLECTION_LIGHT);
        return $m ? $m->getUrl($conv) : null;
    }

    protected function dark(string $conv = ''): ?string
    {
        $m = $this->getFirstMedia(self::MEDIA_COLLECTION_DARK);
        return $m ? $m->getUrl($conv) : null;
    }

    public function getLightImageUrlAttribute(): ?string { return $this->light(); }
    public function getLightWebpUrlAttribute(): ?string  { return $this->light('webp'); }
    public function getLightThumbUrlAttribute(): ?string { return $this->light('thumb'); }

    public function getDarkImageUrlAttribute(): ?string  { return $this->dark(); }
    public function getDarkWebpUrlAttribute(): ?string   { return $this->dark('webp'); }
    public function getDarkThumbUrlAttribute(): ?string  { return $this->dark('thumb'); }
}
