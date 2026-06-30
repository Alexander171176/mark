<?php

namespace App\Models\Admin\Market\MarketTag;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MarketTag extends Model
{
    use HasFactory;

    protected $table = 'market_tags';

    protected $fillable = [
        'user_id',

        'url',
        'icon',
        'color',

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

        'views',
    ];

    protected $casts = [
        'user_id' => 'integer',

        'sort' => 'integer',
        'activity' => 'boolean',

        'moderation_status' => 'integer',
        'moderated_by' => 'integer',

        'published_at' => 'datetime',
        'show_from_at' => 'datetime',
        'show_to_at' => 'datetime',

        'moderated_at' => 'datetime',

        'views' => 'integer',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ===================== Relations ===================== */

    /** Создатель тега */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Модератор тега */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /** Переводы */
    public function translations(): HasMany
    {
        return $this->hasMany(
            MarketTagTranslation::class,
            'market_tag_id'
        );
    }

    /** Текущий перевод */
    public function translation(): HasOne
    {
        return $this->hasOne(
            MarketTagTranslation::class,
            'market_tag_id'
        )->where('locale', app()->getLocale());
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

    /** Сортировка по параметру списка */
    public function scopeSortByParam(
        Builder $query,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale = $locale ?: app()->getLocale();

        return match ($sort) {
            'idAsc' => $query->orderBy('id', 'asc'),
            'idDesc' => $query->orderBy('id', 'desc'),

            'sortAsc' => $query->orderBy('sort', 'asc')->orderByDesc('id'),
            'sortDesc' => $query->orderBy('sort', 'desc')->orderByDesc('id'),

            'titleAsc' => $query
                ->leftJoin('market_tag_translations as sort_translations', function ($join) use ($locale) {
                    $join->on('market_tags.id', '=', 'sort_translations.market_tag_id')
                        ->where('sort_translations.locale', '=', $locale);
                })
                ->select('market_tags.*')
                ->orderBy('sort_translations.title', 'asc')
                ->orderByDesc('market_tags.id'),

            'titleDesc' => $query
                ->leftJoin('market_tag_translations as sort_translations', function ($join) use ($locale) {
                    $join->on('market_tags.id', '=', 'sort_translations.market_tag_id')
                        ->where('sort_translations.locale', '=', $locale);
                })
                ->select('market_tags.*')
                ->orderBy('sort_translations.title', 'desc')
                ->orderByDesc('market_tags.id'),

            'urlAsc' => $query->orderBy('url', 'asc')->orderByDesc('id'),
            'urlDesc' => $query->orderBy('url', 'desc')->orderByDesc('id'),

            'colorAsc' => $query->orderBy('color', 'asc')->orderByDesc('id'),
            'colorDesc' => $query->orderBy('color', 'desc')->orderByDesc('id'),

            'activityAsc' => $query->orderBy('activity', 'asc')->orderByDesc('id'),
            'activityDesc' => $query->orderBy('activity', 'desc')->orderByDesc('id'),
            'activity' => $query->where('activity', true)->orderByDesc('id'),
            'inactive' => $query->where('activity', false)->orderByDesc('id'),

            'viewsAsc' => $query->orderBy('views', 'asc')->orderByDesc('id'),
            'viewsDesc' => $query->orderBy('views', 'desc')->orderByDesc('id'),

            'statusAsc' => $query->orderBy('status', 'asc')->orderByDesc('id'),
            'statusDesc' => $query->orderBy('status', 'desc')->orderByDesc('id'),
            'statusDraft' => $query->where('status', 'draft')->orderByDesc('id'),
            'statusPublished' => $query->where('status', 'published')->orderByDesc('id'),
            'statusArchived' => $query->where('status', 'archived')->orderByDesc('id'),

            'publishedAtAsc' => $query->orderBy('published_at', 'asc')->orderByDesc('id'),
            'publishedAtDesc' => $query->orderBy('published_at', 'desc')->orderByDesc('id'),

            'showFromAtAsc' => $query->orderBy('show_from_at', 'asc')->orderByDesc('id'),
            'showFromAtDesc' => $query->orderBy('show_from_at', 'desc')->orderByDesc('id'),

            'showToAtAsc' => $query->orderBy('show_to_at', 'asc')->orderByDesc('id'),
            'showToAtDesc' => $query->orderBy('show_to_at', 'desc')->orderByDesc('id'),

            'createdAtAsc', 'dateAsc' => $query->orderBy('created_at', 'asc')->orderByDesc('id'),
            'createdAtDesc', 'dateDesc' => $query->orderBy('created_at', 'desc')->orderByDesc('id'),

            'updatedAtAsc' => $query->orderBy('updated_at', 'asc')->orderByDesc('id'),
            'updatedAtDesc' => $query->orderBy('updated_at', 'desc')->orderByDesc('id'),

            'moderationPending' => $query->where('moderation_status', 0)->orderByDesc('id'),
            'moderationApproved' => $query->where('moderation_status', 1)->orderByDesc('id'),
            'moderationRejected' => $query->where('moderation_status', 2)->orderByDesc('id'),
            'moderationStatusAsc' => $query->orderBy('moderation_status', 'asc')->orderByDesc('id'),
            'moderationStatusDesc' => $query->orderBy('moderation_status', 'desc')->orderByDesc('id'),

            'ownerNameAsc' => $query
                ->leftJoin('users as sort_users', 'market_tags.user_id', '=', 'sort_users.id')
                ->select('market_tags.*')
                ->orderBy('sort_users.name', 'asc')
                ->orderByDesc('market_tags.id'),

            'ownerNameDesc' => $query
                ->leftJoin('users as sort_users', 'market_tags.user_id', '=', 'sort_users.id')
                ->select('market_tags.*')
                ->orderBy('sort_users.name', 'desc')
                ->orderByDesc('market_tags.id'),

            'ownerEmailAsc' => $query
                ->leftJoin('users as sort_users', 'market_tags.user_id', '=', 'sort_users.id')
                ->select('market_tags.*')
                ->orderBy('sort_users.email', 'asc')
                ->orderByDesc('market_tags.id'),

            'ownerEmailDesc' => $query
                ->leftJoin('users as sort_users', 'market_tags.user_id', '=', 'sort_users.id')
                ->select('market_tags.*')
                ->orderBy('sort_users.email', 'desc')
                ->orderByDesc('market_tags.id'),

            default => $query->orderByDesc('id'),
        };
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

    /** Публичные теги */
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

            $q->where('url', 'like', "%{$term}%")
                ->orWhere('icon', 'like', "%{$term}%")
                ->orWhere('color', 'like', "%{$term}%")
                ->orWhere('status', 'like', "%{$term}%")
                ->orWhere('moderation_note', 'like', "%{$term}%")

                ->orWhereHas('translations', function (Builder $tq) use ($term, $locale) {

                    $tq->where('locale', $locale)
                        ->where(function (Builder $sq) use ($term) {

                            $sq->where('title', 'like', "%{$term}%")
                                ->orWhere('subtitle', 'like', "%{$term}%")
                                ->orWhere('short', 'like', "%{$term}%")
                                ->orWhere('description', 'like', "%{$term}%")
                                ->orWhere('meta_title', 'like', "%{$term}%")
                                ->orWhere('meta_keywords', 'like', "%{$term}%")
                                ->orWhere('meta_desc', 'like', "%{$term}%");
                        });
                })

                ->orWhereHas('owner', function (Builder $oq) use ($term) {

                    $oq->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
        });
    }
}
