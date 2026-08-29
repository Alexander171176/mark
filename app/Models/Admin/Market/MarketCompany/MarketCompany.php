<?php

namespace App\Models\Admin\Market\MarketCompany;

use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\Admin\Market\MarketShop\MarketShop;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'user_id' => 'integer',
        'moderated_by' => 'integer',

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

    /** Магазин компании */
    public function shop(): HasOne
    {
        return $this->hasOne(
            MarketShop::class,
            'market_company_id'
        );
    }

    /** Владелец компании */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /** Пользователь, выполнивший модерацию */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'moderated_by'
        );
    }

    /** Все переводы компании */
    public function translations(): HasMany
    {
        return $this->hasMany(
            MarketCompanyTranslation::class,
            'market_company_id'
        );
    }

    /** Все товары компании */
    public function products(): HasMany
    {
        return $this->hasMany(
            MarketProduct::class,
            'market_company_id'
        );
    }

    /* ======================== Translation helpers ======================== */

    /**
     * Получить перевод указанной локали
     * из коллекции translations.
     *
     * В Admin Index этот helper не используется:
     * Controller сам загружает translations,
     * ограниченные currentLocale.
     */
    public function translation(
        ?string $locale = null
    ): ?MarketCompanyTranslation {
        $locale = $locale
            ?: app()->getLocale();

        return $this->translations
            ->firstWhere(
                'locale',
                $locale
            );
    }

    /**
     * Получить перевод текущей локали
     * с fallback.
     *
     * Используется только там, где действительно
     * доступна полная коллекция translations.
     */
    public function translationOrFallback(
        ?string $locale = null,
        ?string $fallback = null
    ): ?MarketCompanyTranslation {
        $locale = $locale
            ?: app()->getLocale();

        $fallback = $fallback
            ?: config(
                'app.fallback_locale',
                'ru'
            );

        return $this->translations
            ->firstWhere(
                'locale',
                $locale
            )
            ?: $this->translations
                ->firstWhere(
                    'locale',
                    $fallback
                )
                ?: $this->translations
                    ->first();
    }

    /** Получить локализованный title */
    public function getTranslatedTitle(
        ?string $locale = null,
        ?string $fallback = null
    ): ?string {
        return $this
            ->translationOrFallback(
                locale: $locale,
                fallback: $fallback
            )
            ?->title;
    }

    /* ======================== Model events ======================== */

    protected static function booted(): void
    {
        static::saved(
            function (
                MarketCompany $company
            ): void {
                Log::info(
                    'Компания маркетплейса сохранена: '
                    . $company->id
                    . ' / '
                    . $company->url
                );
            }
        );

        static::deleted(
            function (
                MarketCompany $company
            ): void {
                Log::info(
                    'Компания маркетплейса удалена: '
                    . $company->id
                    . ' / '
                    . $company->url
                );
            }
        );
    }

    /* ======================== Helpers ======================== */

    /** Компания активна */
    public function isActive(): bool
    {
        return (bool) $this->activity;
    }

    /** Компания прошла модерацию */
    public function isApproved(): bool
    {
        return (int) $this->moderation_status === 1;
    }

    /** Компания находится в текущем окне показа */
    public function isPublishedNow(): bool
    {
        $now = now();

        if (
            $this->show_from_at
            && $now->lt(
                $this->show_from_at
            )
        ) {
            return false;
        }

        if (
            $this->show_to_at
            && $now->gt(
                $this->show_to_at
            )
        ) {
            return false;
        }

        return true;
    }

    /* ======================== Base scopes ======================== */

    /** Сортировка по умолчанию */
    public function scopeOrdered(
        Builder $query
    ): Builder {
        return $query
            ->orderBy(
                'sort'
            )
            ->orderByDesc(
                'id'
            );
    }

    /** Активные компании */
    public function scopeActive(
        Builder $query
    ): Builder {
        return $query->where(
            'activity',
            true
        );
    }

    /** Одобренные компании */
    public function scopeApproved(
        Builder $query
    ): Builder {
        return $query->where(
            'moderation_status',
            1
        );
    }

    /** Опубликованные компании */
    public function scopePublished(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            'published'
        );
    }

    /** Компании в текущем окне показа */
    public function scopeInShowWindow(
        Builder $query
    ): Builder {
        return $query
            ->where(
                function (
                    Builder $query
                ): void {
                    $query
                        ->whereNull(
                            'show_from_at'
                        )
                        ->orWhere(
                            'show_from_at',
                            '<=',
                            now()
                        );
                }
            )
            ->where(
                function (
                    Builder $query
                ): void {
                    $query
                        ->whereNull(
                            'show_to_at'
                        )
                        ->orWhere(
                            'show_to_at',
                            '>=',
                            now()
                        );
                }
            );
    }

    /** Публичные компании */
    public function scopeForPublic(
        Builder $query
    ): Builder {
        return $query
            ->approved()
            ->published()
            ->active()
            ->inShowWindow();
    }

    /** Компании левой колонки */
    public function scopeInLeft(
        Builder $query
    ): Builder {
        return $query->where(
            'left',
            true
        );
    }

    /** Компании главного блока */
    public function scopeInMain(
        Builder $query
    ): Builder {
        return $query->where(
            'main',
            true
        );
    }

    /** Компании правой колонки */
    public function scopeInRight(
        Builder $query
    ): Builder {
        return $query->where(
            'right',
            true
        );
    }

    /* ======================== Search ======================== */

    /**
     * Поиск компаний.
     *
     * Семантика совпадает с frontend Index:
     * - url;
     * - legal_name;
     * - bin_iin;
     * - email;
     * - phone;
     * - city;
     * - title текущего перевода;
     * - short текущего перевода;
     * - description текущего перевода;
     * - имя владельца;
     * - email владельца.
     *
     * Для переводимых полей используется
     * исключительно указанная locale.
     */
    public function scopeSearch(
        Builder $query,
        ?string $term,
        ?string $locale = null
    ): Builder {
        $term = trim(
            (string) $term
        );

        if ($term === '') {
            return $query;
        }

        $locale = $locale
            ?: app()->getLocale();

        $like = "%{$term}%";

        return $query->where(
            function (
                Builder $query
            ) use (
                $like,
                $locale
            ): void {
                $query
                    ->where(
                        'market_companies.url',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'market_companies.legal_name',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'market_companies.bin_iin',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'market_companies.email',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'market_companies.phone',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'market_companies.city',
                        'like',
                        $like
                    )

                    /** Перевод текущей локали */
                    ->orWhereHas(
                        'translations',
                        function (
                            Builder $translationQuery
                        ) use (
                            $locale,
                            $like
                        ): void {
                            $translationQuery
                                ->where(
                                    'locale',
                                    $locale
                                )
                                ->where(
                                    function (
                                        Builder $query
                                    ) use (
                                        $like
                                    ): void {
                                        $query
                                            ->where(
                                                'title',
                                                'like',
                                                $like
                                            )
                                            ->orWhere(
                                                'short',
                                                'like',
                                                $like
                                            )
                                            ->orWhere(
                                                'description',
                                                'like',
                                                $like
                                            );
                                    }
                                );
                        }
                    )

                    /** Владелец */
                    ->orWhereHas(
                        'owner',
                        function (
                            Builder $ownerQuery
                        ) use (
                            $like
                        ): void {
                            $ownerQuery
                                ->where(
                                    'name',
                                    'like',
                                    $like
                                )
                                ->orWhere(
                                    'email',
                                    'like',
                                    $like
                                );
                        }
                    );
            }
        );
    }

    /* ======================== Sorting ======================== */

    /**
     * Сортировка и фильтрация компаний
     * по параметру Index.
     */
    public function scopeSortByParam(
        Builder $query,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale = $locale
            ?: app()->getLocale();

        return match ($sort) {
            /** ID */
            'idAsc' =>
            $query->orderBy(
                'market_companies.id',
                'asc'
            ),

            'idDesc' =>
            $query->orderBy(
                'market_companies.id',
                'desc'
            ),

            /** Sort */
            'sortAsc' =>
            $query
                ->orderBy(
                    'market_companies.sort',
                    'asc'
                )
                ->orderBy(
                    'market_companies.id',
                    'asc'
                ),

            'sortDesc' =>
            $query
                ->orderBy(
                    'market_companies.sort',
                    'desc'
                )
                ->orderBy(
                    'market_companies.id',
                    'desc'
                ),

            /** Title текущей локали */
            'titleAsc' =>
            $query
                ->leftJoin(
                    'market_company_translations as mct_sort',
                    function (
                        $join
                    ) use (
                        $locale
                    ): void {
                        $join
                            ->on(
                                'mct_sort.market_company_id',
                                '=',
                                'market_companies.id'
                            )
                            ->where(
                                'mct_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect(
                    'market_companies.*'
                )
                ->orderBy(
                    'mct_sort.title',
                    'asc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'titleDesc' =>
            $query
                ->leftJoin(
                    'market_company_translations as mct_sort',
                    function (
                        $join
                    ) use (
                        $locale
                    ): void {
                        $join
                            ->on(
                                'mct_sort.market_company_id',
                                '=',
                                'market_companies.id'
                            )
                            ->where(
                                'mct_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect(
                    'market_companies.*'
                )
                ->orderBy(
                    'mct_sort.title',
                    'desc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            /** URL */
            'urlAsc' =>
            $query
                ->orderBy(
                    'market_companies.url',
                    'asc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'urlDesc' =>
            $query
                ->orderBy(
                    'market_companies.url',
                    'desc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            /** Юридическое название */
            'legalNameAsc' =>
            $query
                ->orderBy(
                    'market_companies.legal_name',
                    'asc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'legalNameDesc' =>
            $query
                ->orderBy(
                    'market_companies.legal_name',
                    'desc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            /** Тип компании */
            'companyTypeAsc' =>
            $query
                ->orderBy(
                    'market_companies.company_type',
                    'asc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'companyTypeDesc' =>
            $query
                ->orderBy(
                    'market_companies.company_type',
                    'desc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            /** Город */
            'cityAsc' =>
            $query
                ->orderBy(
                    'market_companies.city',
                    'asc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'cityDesc' =>
            $query
                ->orderBy(
                    'market_companies.city',
                    'desc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            /** Просмотры */
            'viewsAsc' =>
            $query
                ->orderBy(
                    'market_companies.views',
                    'asc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'viewsDesc' =>
            $query
                ->orderBy(
                    'market_companies.views',
                    'desc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            /** Дата публикации */
            'publishedAtAsc' =>
            $query
                ->orderBy(
                    'market_companies.published_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'publishedAtDesc' =>
            $query
                ->orderBy(
                    'market_companies.published_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            /** Начало показа */
            'showFromAtAsc' =>
            $query
                ->orderBy(
                    'market_companies.show_from_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'showFromAtDesc' =>
            $query
                ->orderBy(
                    'market_companies.show_from_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            /** Окончание показа */
            'showToAtAsc' =>
            $query
                ->orderBy(
                    'market_companies.show_to_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'showToAtDesc' =>
            $query
                ->orderBy(
                    'market_companies.show_to_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            /** Дата создания */
            'createdAtAsc',
            'dateAsc' =>
            $query
                ->orderBy(
                    'market_companies.created_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'createdAtDesc',
            'dateDesc' =>
            $query
                ->orderBy(
                    'market_companies.created_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            /** Дата обновления */
            'updatedAtAsc' =>
            $query
                ->orderBy(
                    'market_companies.updated_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'updatedAtDesc' =>
            $query
                ->orderBy(
                    'market_companies.updated_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            /** Активность */
            'activityAsc' =>
            $query
                ->orderBy(
                    'market_companies.activity',
                    'asc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'activityDesc' =>
            $query
                ->orderBy(
                    'market_companies.activity',
                    'desc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'activity' =>
            $query
                ->where(
                    'market_companies.activity',
                    true
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'inactive' =>
            $query
                ->where(
                    'market_companies.activity',
                    false
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            /** Left */
            'leftAsc' =>
            $query
                ->orderBy(
                    'market_companies.left',
                    'asc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'leftDesc' =>
            $query
                ->orderBy(
                    'market_companies.left',
                    'desc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'left' =>
            $query
                ->where(
                    'market_companies.left',
                    true
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'noLeft' =>
            $query
                ->where(
                    'market_companies.left',
                    false
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            /** Main */
            'mainAsc' =>
            $query
                ->orderBy(
                    'market_companies.main',
                    'asc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'mainDesc' =>
            $query
                ->orderBy(
                    'market_companies.main',
                    'desc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'main' =>
            $query
                ->where(
                    'market_companies.main',
                    true
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'noMain' =>
            $query
                ->where(
                    'market_companies.main',
                    false
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            /** Right */
            'rightAsc' =>
            $query
                ->orderBy(
                    'market_companies.right',
                    'asc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'rightDesc' =>
            $query
                ->orderBy(
                    'market_companies.right',
                    'desc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'right' =>
            $query
                ->where(
                    'market_companies.right',
                    true
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'noRight' =>
            $query
                ->where(
                    'market_companies.right',
                    false
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            /** Модерация */
            'moderationStatusAsc' =>
            $query
                ->orderBy(
                    'market_companies.moderation_status',
                    'asc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'moderationStatusDesc' =>
            $query
                ->orderBy(
                    'market_companies.moderation_status',
                    'desc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'moderationPending' =>
            $query
                ->where(
                    'market_companies.moderation_status',
                    0
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'moderationApproved' =>
            $query
                ->where(
                    'market_companies.moderation_status',
                    1
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'moderationRejected' =>
            $query
                ->where(
                    'market_companies.moderation_status',
                    2
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            /** Имя владельца */
            'ownerNameAsc' =>
            $query
                ->leftJoin(
                    'users as owner_sort',
                    'owner_sort.id',
                    '=',
                    'market_companies.user_id'
                )
                ->addSelect(
                    'market_companies.*'
                )
                ->orderBy(
                    'owner_sort.name',
                    'asc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'ownerNameDesc' =>
            $query
                ->leftJoin(
                    'users as owner_sort',
                    'owner_sort.id',
                    '=',
                    'market_companies.user_id'
                )
                ->addSelect(
                    'market_companies.*'
                )
                ->orderBy(
                    'owner_sort.name',
                    'desc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            /** Email владельца */
            'ownerEmailAsc' =>
            $query
                ->leftJoin(
                    'users as owner_sort',
                    'owner_sort.id',
                    '=',
                    'market_companies.user_id'
                )
                ->addSelect(
                    'market_companies.*'
                )
                ->orderBy(
                    'owner_sort.email',
                    'asc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            'ownerEmailDesc' =>
            $query
                ->leftJoin(
                    'users as owner_sort',
                    'owner_sort.id',
                    '=',
                    'market_companies.user_id'
                )
                ->addSelect(
                    'market_companies.*'
                )
                ->orderBy(
                    'owner_sort.email',
                    'desc'
                )
                ->orderByDesc(
                    'market_companies.id'
                ),

            default =>
            $query->ordered(),
        };
    }
}
