<?php

namespace App\Models\Admin\System\Location;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    /**
     * Массово заполняемые поля.
     */
    protected $fillable = [
        'parent_id',
        'type',
        'slug',
        'code',
        'latitude',
        'longitude',
        'timezone',
        'activity',
        'is_default',
        'sort',
    ];

    /**
     * Приведение типов.
     */
    protected $casts = [
        'parent_id' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'activity' => 'boolean',
        'is_default' => 'boolean',
        'sort' => 'integer',
    ];

    /**
     * Родительская локация.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'parent_id'
        );
    }

    /**
     * Дочерние локации.
     */
    public function children(): HasMany
    {
        return $this->hasMany(
            self::class,
            'parent_id'
        )->orderBy('sort');
    }

    /**
     * Дочерние локации со всей вложенной иерархией.
     */
    public function childrenRecursive(): HasMany
    {
        return $this->children()
            ->with('childrenRecursive');
    }

    /**
     * Переводы локации.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(
            LocationTranslation::class
        );
    }

    /**
     * Перевод для текущей локали.
     */
    public function translation(): ?LocationTranslation
    {
        $locale = app()->getLocale();

        return $this->translations
            ->firstWhere('locale', $locale);
    }

    /**
     * Перевод для текущей локали или fallback-локали.
     */
    public function translationOrFallback(
        ?string $locale = null
    ): ?LocationTranslation {
        $locale ??= app()->getLocale();

        $translation = $this->translations
            ->firstWhere('locale', $locale);

        if ($translation) {
            return $translation;
        }

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        return $this->translations
            ->firstWhere(
                'locale',
                $fallbackLocale
            );
    }

    /**
     * Только активные локации.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(
            'activity',
            true
        );
    }

    /**
     * Фильтрация по типу локации.
     */
    public function scopeOfType(
        Builder $query,
        string $type
    ): Builder {
        return $query->where(
            'type',
            $type
        );
    }

    /**
     * Только корневые локации.
     */
    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull(
            'parent_id'
        );
    }

    /**
     * Локация по умолчанию.
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where(
            'is_default',
            true
        );
    }

    /**
     * Сортировка по установленному порядку.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort')
            ->orderBy('id');
    }
}
