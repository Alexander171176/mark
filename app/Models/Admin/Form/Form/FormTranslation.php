<?php

namespace App\Models\Admin\Form\Form;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormTranslation extends Model
{
    use HasFactory;

    protected $table = 'form_translations';

    protected $fillable = [
        'form_id',
        'locale',
        'title',
        'subtitle',
        'description',
        'submit_text',
        'success_message',
        'error_message',
    ];

    protected $casts = [
        'form_id' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Форма, которой принадлежит перевод.
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Переводы указанной локали.
     */
    public function scopeLocale(
        Builder $query,
        ?string $locale = null
    ): Builder {
        return $query->where(
            'locale',
            $locale ?? app()->getLocale()
        );
    }
}
