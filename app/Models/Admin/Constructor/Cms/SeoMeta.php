<?php

namespace App\Models\Admin\Constructor\Cms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeoMeta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'seo_metas';

    protected $fillable = [
        'seoable_type',     // полиморфная цель
        'seoable_id',
        'title',            // <title>
        'description',      // meta description
        'keywords',         // meta keywords (если используете)
        'robots_noindex',   // noindex
        'robots_nofollow',  // nofollow
        'canonical_url',    // canonical
        'og_title',         // OG
        'og_description',
        'og_image_url',
        'og_type',
        'twitter_card',     // twitter card тип
        'locale',           // локаль
        'activity',        // использовать ли мету
        'json_ld',          // JSON-LD
        'meta',             // произвольные данные
    ];

    protected $casts = [
        'robots_noindex' => 'bool',
        'robots_nofollow'=> 'bool',
        'activity'      => 'bool',
        'json_ld'        => 'array',
        'meta'           => 'array',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'deleted_at'     => 'datetime',
    ];

    /* ============== Связи ============== */

    // Любая сущность, к которой привязаны эти SEO-метаданные (Page, BlogPost и т.д.)
    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    /* ============== Скоупы/хелперы ============== */

    // Активные записи, соответствующие локали (если передана)
    public function scopeActive($q, ?string $locale = null)
    {
        $q->where('activity', true);
        if ($locale) {
            $q->where(function ($q) use ($locale) {
                $q->where('locale', $locale)->orWhereNull('locale');
            });
        }
        return $q;
    }

    // Сборка значения robots
    public function getRobotsAttribute(): string
    {
        $parts = [];
        $parts[] = $this->robots_noindex ? 'noindex' : 'index';
        $parts[] = $this->robots_nofollow ? 'nofollow' : 'follow';
        return implode(', ', $parts);
    }
}
