<?php

namespace App\Models\Admin\Market\MarketCompany;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class MarketCompany extends Model
{
    use HasFactory;

    protected $table = 'market_companies';

    protected $fillable = [
        'user_id',

        'url',
        'company_type',
        'bin_iin',
        'legal_name',
        'director_name',

        'email',
        'phone',
        'website',

        'logo',
        'signature',
        'stamp',

        'country',
        'region',
        'city',
        'legal_address',
        'actual_address',
        'latitude',
        'longitude',

        'bank_name',
        'bank_account',
        'bank_account_secondary',
        'bank_bik',
        'bank_iban',

        'vat_enabled',
        'vat_rate',

        'social_links',

        'sort',
        'activity',
        'left',
        'main',
        'right',

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
        'sort' => 'integer',

        'activity' => 'boolean',
        'left' => 'boolean',
        'main' => 'boolean',
        'right' => 'boolean',

        'moderation_status' => 'integer',
        'moderated_at' => 'datetime',

        'published_at' => 'date',
        'show_from_at' => 'datetime',
        'show_to_at' => 'datetime',

        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',

        'vat_enabled' => 'boolean',
        'vat_rate' => 'decimal:2',

        'social_links' => 'array',

        'views' => 'integer',
    ];

    /* ======================== Relations ======================== */

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(MarketCompanyTranslation::class, 'market_company_id');
    }

    public function translation(?string $locale = null): ?MarketCompanyTranslation
    {
        $locale = $locale ?: app()->getLocale();

        return $this->translations
            ->where('locale', $locale)
            ->first();
    }

    public function translationOrFallback(?string $locale = null, string $fallback = 'ru'): ?MarketCompanyTranslation
    {
        $locale = $locale ?: app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?: $this->translations->firstWhere('locale', $fallback)
                ?: $this->translations->first();
    }

    /* ======================== MODEL EVENTS ======================== */

    protected static function booted(): void
    {
        static::saved(function (MarketCompany $company) {
            Log::info('Компания маркетплейса сохранена: ' . $company->id . ' / ' . $company->url);
        });

        static::deleted(function (MarketCompany $company) {
            Log::info('Компания маркетплейса удалена: ' . $company->id . ' / ' . $company->url);
        });
    }

    /* ======================== HELPERS ======================== */

    public function isActive(): bool
    {
        return (bool) $this->activity;
    }

    public function isApproved(): bool
    {
        return (int) $this->moderation_status === 1;
    }

    public function isPublishedNow(): bool
    {
        $now = now();

        if ($this->show_from_at && $now->lt($this->show_from_at)) {
            return false;
        }

        if ($this->show_to_at && $now->gt($this->show_to_at)) {
            return false;
        }

        return true;
    }

    public function getTranslatedTitle(?string $locale = null, string $fallback = 'ru'): ?string
    {
        return $this->translationOrFallback($locale, $fallback)?->title;
    }

    /* ======================== Scopes ======================== */

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderByDesc('id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('activity', true);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('moderation_status', 1);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

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

    public function scopeForPublic(Builder $query): Builder
    {
        return $query
            ->approved()
            ->published()
            ->active()
            ->inShowWindow();
    }

    public function scopeInLeft(Builder $query): Builder
    {
        return $query->where('left', true);
    }

    public function scopeInMain(Builder $query): Builder
    {
        return $query->where('main', true);
    }

    public function scopeInRight(Builder $query): Builder
    {
        return $query->where('right', true);
    }

    public function scopeSearch(Builder $query, ?string $term, ?string $locale = null): Builder
    {
        if (!$term) {
            return $query;
        }

        $locale = $locale ?: app()->getLocale();

        return $query->where(function (Builder $q) use ($term, $locale) {
            $q->where('url', 'like', "%{$term}%")
                ->orWhere('legal_name', 'like', "%{$term}%")
                ->orWhere('bin_iin', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
                ->orWhere('city', 'like', "%{$term}%")
                ->orWhereHas('translations', function (Builder $tq) use ($term, $locale) {
                    $tq->where('locale', $locale)
                        ->where(function (Builder $sq) use ($term) {
                            $sq->where('title', 'like', "%{$term}%")
                                ->orWhere('subtitle', 'like', "%{$term}%")
                                ->orWhere('short', 'like', "%{$term}%")
                                ->orWhere('description', 'like', "%{$term}%");
                        });
                });
        });
    }

    public function scopeSortByParam(Builder $query, ?string $sort, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return match ($sort) {
            'idAsc' => $query->orderBy('id', 'asc'),
            'idDesc' => $query->orderBy('id', 'desc'),

            'sortAsc' => $query->orderBy('sort', 'asc')->orderBy('id', 'asc'),
            'sortDesc' => $query->orderBy('sort', 'desc')->orderBy('id', 'desc'),

            'titleAsc' => $query
                ->leftJoin('market_company_translations as mct_sort', function ($join) use ($locale) {
                    $join->on('mct_sort.market_company_id', '=', 'market_companies.id')
                        ->where('mct_sort.locale', '=', $locale);
                })
                ->orderBy('mct_sort.title', 'asc')
                ->orderByDesc('market_companies.id')
                ->select('market_companies.*'),

            'titleDesc' => $query
                ->leftJoin('market_company_translations as mct_sort', function ($join) use ($locale) {
                    $join->on('mct_sort.market_company_id', '=', 'market_companies.id')
                        ->where('mct_sort.locale', '=', $locale);
                })
                ->orderBy('mct_sort.title', 'desc')
                ->orderByDesc('market_companies.id')
                ->select('market_companies.*'),

            'urlAsc' => $query->orderBy('url', 'asc')->orderByDesc('id'),
            'urlDesc' => $query->orderBy('url', 'desc')->orderByDesc('id'),

            'legalNameAsc' => $query->orderBy('legal_name', 'asc')->orderByDesc('id'),
            'legalNameDesc' => $query->orderBy('legal_name', 'desc')->orderByDesc('id'),

            'companyTypeAsc' => $query->orderBy('company_type', 'asc')->orderByDesc('id'),
            'companyTypeDesc' => $query->orderBy('company_type', 'desc')->orderByDesc('id'),

            'cityAsc' => $query->orderBy('city', 'asc')->orderByDesc('id'),
            'cityDesc' => $query->orderBy('city', 'desc')->orderByDesc('id'),

            'viewsAsc' => $query->orderBy('views', 'asc')->orderByDesc('id'),
            'viewsDesc' => $query->orderBy('views', 'desc')->orderByDesc('id'),

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

            'activityAsc' => $query->orderBy('activity', 'asc')->orderByDesc('id'),
            'activityDesc' => $query->orderBy('activity', 'desc')->orderByDesc('id'),
            'activity' => $query->where('activity', true)->orderByDesc('id'),
            'inactive' => $query->where('activity', false)->orderByDesc('id'),

            'leftAsc' => $query->orderBy('left', 'asc')->orderByDesc('id'),
            'leftDesc' => $query->orderBy('left', 'desc')->orderByDesc('id'),
            'left' => $query->where('left', true)->orderByDesc('id'),
            'noLeft' => $query->where('left', false)->orderByDesc('id'),

            'mainAsc' => $query->orderBy('main', 'asc')->orderByDesc('id'),
            'mainDesc' => $query->orderBy('main', 'desc')->orderByDesc('id'),
            'main' => $query->where('main', true)->orderByDesc('id'),
            'noMain' => $query->where('main', false)->orderByDesc('id'),

            'rightAsc' => $query->orderBy('right', 'asc')->orderByDesc('id'),
            'rightDesc' => $query->orderBy('right', 'desc')->orderByDesc('id'),
            'right' => $query->where('right', true)->orderByDesc('id'),
            'noRight' => $query->where('right', false)->orderByDesc('id'),

            'moderationStatusAsc' => $query->orderBy('moderation_status', 'asc')->orderByDesc('id'),
            'moderationStatusDesc' => $query->orderBy('moderation_status', 'desc')->orderByDesc('id'),
            'moderationPending' => $query->where('moderation_status', 0)->orderByDesc('id'),
            'moderationApproved' => $query->where('moderation_status', 1)->orderByDesc('id'),
            'moderationRejected' => $query->where('moderation_status', 2)->orderByDesc('id'),

            'ownerNameAsc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_companies.user_id')
                ->orderBy('owner_sort.name', 'asc')
                ->orderByDesc('market_companies.id')
                ->select('market_companies.*'),

            'ownerNameDesc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_companies.user_id')
                ->orderBy('owner_sort.name', 'desc')
                ->orderByDesc('market_companies.id')
                ->select('market_companies.*'),

            'ownerEmailAsc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_companies.user_id')
                ->orderBy('owner_sort.email', 'asc')
                ->orderByDesc('market_companies.id')
                ->select('market_companies.*'),

            'ownerEmailDesc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_companies.user_id')
                ->orderBy('owner_sort.email', 'desc')
                ->orderByDesc('market_companies.id')
                ->select('market_companies.*'),

            default => $query->ordered(),
        };
    }
}
