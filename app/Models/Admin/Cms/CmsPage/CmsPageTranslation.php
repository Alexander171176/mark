<?php

namespace App\Models\Admin\Cms\CmsPage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsPageTranslation extends Model
{
    use HasFactory;

    protected $table = 'cms_page_translations';

    protected $fillable = [
        'cms_page_id',
        'locale',
        'title',
        'subtitle',
        'short',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /** CMS страница перевода */
    public function page(): BelongsTo
    {
        return $this->belongsTo(
            CmsPage::class,
            'cms_page_id'
        );
    }
}
