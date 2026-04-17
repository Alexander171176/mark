<?php

namespace App\Models\Admin\Constructor\Cms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CmsBanner extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'cms_banners';

    protected $fillable = [
        'title',
        'subtitle',
        'placement',
        'link_type',     // url|route|none
        'link_url',
        'link_route',
        'link_params',
        'link_target',   // _self|_blank
        'button_label',
        'starts_at',
        'ends_at',
        'activity',
        'sort',
        'meta',
    ];

    protected $casts = [
        'link_params' => 'array',
        'meta'        => 'array',
        'activity'    => 'bool',
        'sort'        => 'int',
        'starts_at'   => 'datetime',
        'ends_at'     => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    // Media collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
        $this->addMediaCollection('image_mobile')->singleFile();
    }

    // Scopes
    public function scopePublished($q)
    {
        $now = now();
        return $q->where('activity', true)
            ->where(fn($qq) => $qq->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn($qq) => $qq->whereNull('ends_at')->orWhere('ends_at', '>=', $now));
    }

    public function scopeForPlacement($q, string $placement)
    {
        return $q->where('placement', $placement)->orderBy('sort')->orderBy('id');
    }

    // Helpers
    public function getHrefAttribute(): ?string
    {
        if ($this->link_type === 'route' && $this->link_route) {
            try {
                return route($this->link_route, $this->link_params ?? []);
            } catch (\Throwable) {
                return null;
            }
        }
        return $this->link_type === 'url' ? $this->link_url : null;
    }

    public function getIsClickableAttribute(): bool
    {
        if ($this->link_type === 'none') return false;
        return (bool) $this->href;
    }
}
