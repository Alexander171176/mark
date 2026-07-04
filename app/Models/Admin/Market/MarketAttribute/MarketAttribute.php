<?php

namespace App\Models\Admin\Market\MarketAttribute;

use App\Models\Admin\Market\MarketAttributeGroup\MarketAttributeGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MarketAttribute extends Model
{
    use HasFactory;

    protected $table = 'market_attributes';

    protected $fillable = [
        'market_attribute_group_id',
        'user_id',

        'code',
        'icon',
        'color',
        'type',
        'unit',

        'required',
        'filterable',
        'visible',

        'sort',
        'activity',

        'status',

        'moderation_status',
        'moderated_by',
        'moderated_at',
        'moderation_note',

        'published_at',
        'show_from_at',
        'show_to_at',
    ];

    protected $casts = [
        'market_attribute_group_id' => 'integer',
        'user_id' => 'integer',

        'required' => 'boolean',
        'filterable' => 'boolean',
        'visible' => 'boolean',

        'sort' => 'integer',
        'activity' => 'boolean',

        'moderation_status' => 'integer',
        'moderated_by' => 'integer',

        'published_at' => 'datetime',
        'show_from_at' => 'datetime',
        'show_to_at' => 'datetime',
        'moderated_at' => 'datetime',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ===================== Relations ===================== */

    /** Группа характеристики */
    public function group(): BelongsTo
    {
        return $this->belongsTo(
            MarketAttributeGroup::class,
            'market_attribute_group_id'
        );
    }

    /** Создатель характеристики */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Модератор характеристики */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /** Переводы */
    public function translations(): HasMany
    {
        return $this->hasMany(
            MarketAttributeTranslation::class,
            'market_attribute_id'
        );
    }

    /** Текущий перевод */
    public function translation(): HasOne
    {
        return $this->hasOne(
            MarketAttributeTranslation::class,
            'market_attribute_id'
        )->where('locale', app()->getLocale());
    }

    /** Значения характеристики */
    public function values(): HasMany
    {
        return $this->hasMany(
            \App\Models\Admin\Market\MarketAttributeValue\MarketAttributeValue::class,
            'market_attribute_id'
        );
    }

    /* ===================== Scopes ===================== */

    /** Только активные */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('activity', true);
    }

    /** Только опубликованные */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where('activity', true)
            ->whereNotNull('published_at');
    }

    /** Только прошедшие модерацию */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('moderation_status', 1);
    }

    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort')
            ->orderByDesc('id');
    }

    /** Попадает в окно показа */
    public function scopeInShowWindow(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $q) {
                $q->whereNull('show_from_at')
                    ->orWhere('show_from_at', '<=', now());
            })
            ->where(function (Builder $q) {
                $q->whereNull('show_to_at')
                    ->orWhere('show_to_at', '>=', now());
            });
    }

    /** Доступные характеристики */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query
            ->approved()
            ->published()
            ->inShowWindow();
    }

    /** Поиск */
    public function scopeSearch(
        Builder $query,
        ?string $term,
        ?string $locale = null
    ): Builder {
        if (!$term) {
            return $query;
        }

        $locale = $locale ?: app()->getLocale();

        return $query->where(function (Builder $q) use ($term, $locale) {

            $q->where('code', 'like', "%{$term}%")
                ->orWhere('icon', 'like', "%{$term}%")
                ->orWhere('color', 'like', "%{$term}%")
                ->orWhere('type', 'like', "%{$term}%")
                ->orWhere('unit', 'like', "%{$term}%")
                ->orWhere('status', 'like', "%{$term}%")
                ->orWhere('moderation_note', 'like', "%{$term}%")

                ->orWhereHas('translations', function (Builder $tq) use ($term, $locale) {
                    $tq->where('locale', $locale)
                        ->where(function (Builder $sq) use ($term) {
                            $sq->where('title', 'like', "%{$term}%")
                                ->orWhere('subtitle', 'like', "%{$term}%")
                                ->orWhere('short', 'like', "%{$term}%")
                                ->orWhere('description', 'like', "%{$term}%");
                        });
                })

                ->orWhereHas('owner', function (Builder $oq) use ($term) {
                    $oq->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
        });
    }
}
