<?php

namespace App\Models\Admin\Image;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Базовая модель для всех сущностей изображений
 * (CourseImage, ModuleImage, LessonImage, AssignmentImage и т.д.)
 *
 * Требования к наследникам:
 *  - задать protected $table
 *  - добавить свои отношения (lessons(), assignments() и т.п.)
 */
abstract class BaseImage extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'order',
        'alt',
        'caption',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /* =============== Media (Spatie) =============== */

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(80)
            ->performOnCollections('images');

        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->performOnCollections('images');
    }

    /* =============== Accessors (удобные URL) =============== */

    public function getImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('images');
    }

    public function getWebpUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('images', 'webp');
    }

    public function getThumbUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('images', 'thumb');
    }
}
