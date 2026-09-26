<?php

namespace App\Models\Admin\Form\FormFieldOption;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormFieldOptionTranslation extends Model
{
    use HasFactory;

    protected $table = 'form_field_option_translations';

    protected $fillable = [
        'form_field_option_id',
        'locale',
        'label',
        'description',
    ];

    protected $casts = [
        'form_field_option_id' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Вариант поля, которому принадлежит перевод.
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(
            FormFieldOption::class,
            'form_field_option_id'
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
