<?php

namespace App\Models\Admin\Form\FormField;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormFieldTranslation extends Model
{
    use HasFactory;

    protected $table = 'form_field_translations';

    protected $fillable = [
        'form_field_id',
        'locale',
        'label',
        'placeholder',
        'description',
    ];

    protected $casts = [
        'form_field_id' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Поле формы, которому принадлежит перевод.
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(
            FormField::class,
            'form_field_id'
        );
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
