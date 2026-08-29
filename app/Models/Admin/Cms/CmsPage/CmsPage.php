<?php

namespace App\Models\Admin\Cms\CmsPage;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CmsPage extends Model
{
    use HasFactory;

    protected $table = 'cms_pages';

    protected $fillable = [
        'user_id',
        'parent_id',
        'level',

        'url',
        'icon',

        'in_menu',
        'in_footer',
        'show_content',
        'show_seo',

        'sort',
        'activity',
        'status',

        'published_at',
        'show_from_at',
        'show_to_at',

        'views',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'parent_id' => 'integer',
        'level' => 'integer',

        'in_menu' => 'boolean',
        'in_footer' => 'boolean',
        'show_content' => 'boolean',
        'show_seo' => 'boolean',

        'sort' => 'integer',
        'activity' => 'boolean',

        'published_at' => 'datetime',
        'show_from_at' => 'datetime',
        'show_to_at' => 'datetime',

        'views' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /** Создатель страницы */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /** Родительская страница */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'parent_id'
        );
    }

    /** Дочерние страницы */
    public function children(): HasMany
    {
        return $this->hasMany(
            self::class,
            'parent_id'
        )
            ->orderBy('sort')
            ->orderByDesc('id');
    }

    /**
     * Дочерние страницы рекурсивно.
     *
     * Relation отвечает только за построение дерева.
     * Переводы намеренно здесь не загружаются.
     *
     * Нужные translations и другие relations
     * должен определить Controller в зависимости
     * от текущей локали и контекста использования.
     */
    public function childrenRecursive(): HasMany
    {
        return $this->children()
            ->with([
                'childrenRecursive',
            ])
            ->withCount([
                'children',
            ]);
    }

    /** Все переводы страницы */
    public function translations(): HasMany
    {
        return $this->hasMany(
            CmsPageTranslation::class,
            'cms_page_id'
        );
    }

    /**
     * Перевод текущей локали.
     *
     * Relation сохраняется для существующей
     * публичной логики и других мест приложения.
     *
     * Для Admin Index используем translations
     * с ограничением locale из Controller.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(
            CmsPageTranslation::class,
            'cms_page_id'
        )->where(
            'locale',
            app()->getLocale()
        );
    }

    /* ======================== Translation helpers ======================== */

    /**
     * Перевод текущей локали с fallback.
     *
     * Используется там, где действительно загружена
     * полная коллекция translations.
     */
    public function translationOrFallback(
        ?string $locale = null,
        ?string $fallback = null
    ): ?CmsPageTranslation {
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

    /** Получить title из текущего перевода */
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

    /* ======================== Public tree relations ======================== */

    /**
     * Публичные дочерние страницы для меню.
     *
     * Текущую публичную архитектуру здесь
     * намеренно не меняем.
     */
    public function publicMenuChildren(): HasMany
    {
        return $this->children()
            ->forPublic()
            ->inMenu()
            ->with([
                'translations',
                'publicMenuChildren',
            ])
            ->withCount([
                'children',
            ]);
    }

    /**
     * Публичные дочерние страницы для футера.
     *
     * Текущую публичную архитектуру здесь
     * намеренно не меняем.
     */
    public function publicFooterChildren(): HasMany
    {
        return $this->children()
            ->forPublic()
            ->inFooter()
            ->with([
                'translations',
                'publicFooterChildren',
            ])
            ->withCount([
                'children',
            ]);
    }

    /* ======================== Base scopes ======================== */

    /** Активные страницы */
    public function scopeActive(
        Builder $query
    ): Builder {
        return $query->where(
            'activity',
            true
        );
    }

    /** Опубликованные страницы */
    public function scopePublished(
        Builder $query
    ): Builder {
        return $query
            ->where(
                'status',
                'published'
            )
            ->where(
                'activity',
                true
            )
            ->whereNotNull(
                'published_at'
            );
    }

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

    /** Корневые страницы */
    public function scopeRoot(
        Builder $query
    ): Builder {
        return $query->whereNull(
            'parent_id'
        );
    }

    /** Страницы для меню */
    public function scopeInMenu(
        Builder $query
    ): Builder {
        return $query->where(
            'in_menu',
            true
        );
    }

    /** Страницы для футера */
    public function scopeInFooter(
        Builder $query
    ): Builder {
        return $query->where(
            'in_footer',
            true
        );
    }

    /** Страницы со своим CMS-контентом */
    public function scopeWithOwnContent(
        Builder $query
    ): Builder {
        return $query->where(
            'show_content',
            true
        );
    }

    /** Страницы со своими SEO-полями */
    public function scopeWithOwnSeo(
        Builder $query
    ): Builder {
        return $query->where(
            'show_seo',
            true
        );
    }

    /** Окно показа */
    public function scopeInShowWindow(
        Builder $query
    ): Builder {
        return $query
            ->where(
                function (
                    Builder $q
                ): void {
                    $q->whereNull(
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
                    Builder $q
                ): void {
                    $q->whereNull(
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

    /** Публичные страницы */
    public function scopeForPublic(
        Builder $query
    ): Builder {
        return $query
            ->published()
            ->inShowWindow();
    }

    /** Публичные страницы меню */
    public function scopeForMenu(
        Builder $query
    ): Builder {
        return $query
            ->forPublic()
            ->inMenu()
            ->ordered();
    }

    /** Публичные страницы футера */
    public function scopeForFooter(
        Builder $query
    ): Builder {
        return $query
            ->forPublic()
            ->inFooter()
            ->ordered();
    }

    /* ======================== Search ======================== */

    /**
     * Поиск CMS страниц.
     *
     * Семантика приведена к текущему frontend Index:
     * - id;
     * - url;
     * - icon;
     * - views;
     * - status;
     * - title;
     * - short;
     * - description;
     * - title родителя;
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

        return $query->where(
            function (
                Builder $query
            ) use (
                $term,
                $locale
            ): void {
                $like = "%{$term}%";

                $query
                    ->where(
                        'cms_pages.id',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'cms_pages.url',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'cms_pages.icon',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'cms_pages.views',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'cms_pages.status',
                        'like',
                        $like
                    )

                    /** Текущий перевод страницы */
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

                    /** Название родительской страницы */
                    ->orWhereHas(
                        'parent.translations',
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
                                    'title',
                                    'like',
                                    $like
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
     * Сортировка и фильтрация по параметру.
     *
     * Набор параметров соответствует SortSelect.vue
     * и frontend-сортировке Index.vue.
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
            'idAsc' => $query
                ->orderBy(
                    'cms_pages.id',
                    'asc'
                ),

            'idDesc' => $query
                ->orderBy(
                    'cms_pages.id',
                    'desc'
                ),

            /** Sort */
            'sortAsc' => $query
                ->orderBy(
                    'cms_pages.sort',
                    'asc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'sortDesc' => $query
                ->orderBy(
                    'cms_pages.sort',
                    'desc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            /** Level */
            'levelAsc' => $query
                ->orderBy(
                    'cms_pages.level',
                    'asc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'levelDesc' => $query
                ->orderBy(
                    'cms_pages.level',
                    'desc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            /** Parent */
            'parentAsc' => $query
                ->orderBy(
                    'cms_pages.parent_id',
                    'asc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'parentDesc' => $query
                ->orderBy(
                    'cms_pages.parent_id',
                    'desc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            /** URL */
            'urlAsc' => $query
                ->orderBy(
                    'cms_pages.url',
                    'asc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'urlDesc' => $query
                ->orderBy(
                    'cms_pages.url',
                    'desc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            /** Views */
            'viewsAsc' => $query
                ->orderBy(
                    'cms_pages.views',
                    'asc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'viewsDesc' => $query
                ->orderBy(
                    'cms_pages.views',
                    'desc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            /** Activity */
            'activityAsc' => $query
                ->orderBy(
                    'cms_pages.activity',
                    'asc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'activityDesc' => $query
                ->orderBy(
                    'cms_pages.activity',
                    'desc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'activity' => $query
                ->where(
                    'cms_pages.activity',
                    true
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'inactive' => $query
                ->where(
                    'cms_pages.activity',
                    false
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            /** Menu */
            'inMenuAsc' => $query
                ->orderBy(
                    'cms_pages.in_menu',
                    'asc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'inMenuDesc' => $query
                ->orderBy(
                    'cms_pages.in_menu',
                    'desc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'inMenu' => $query
                ->where(
                    'cms_pages.in_menu',
                    true
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'notInMenu' => $query
                ->where(
                    'cms_pages.in_menu',
                    false
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            /** Footer */
            'inFooterAsc' => $query
                ->orderBy(
                    'cms_pages.in_footer',
                    'asc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'inFooterDesc' => $query
                ->orderBy(
                    'cms_pages.in_footer',
                    'desc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'inFooter' => $query
                ->where(
                    'cms_pages.in_footer',
                    true
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'notInFooter' => $query
                ->where(
                    'cms_pages.in_footer',
                    false
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            /** HTML */
            'showContentAsc' => $query
                ->orderBy(
                    'cms_pages.show_content',
                    'asc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'showContentDesc' => $query
                ->orderBy(
                    'cms_pages.show_content',
                    'desc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'showContent' => $query
                ->where(
                    'cms_pages.show_content',
                    true
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'notShowContent' => $query
                ->where(
                    'cms_pages.show_content',
                    false
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            /** SEO */
            'showSeoAsc' => $query
                ->orderBy(
                    'cms_pages.show_seo',
                    'asc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'showSeoDesc' => $query
                ->orderBy(
                    'cms_pages.show_seo',
                    'desc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'showSeo' => $query
                ->where(
                    'cms_pages.show_seo',
                    true
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'notShowSeo' => $query
                ->where(
                    'cms_pages.show_seo',
                    false
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            /** Status */
            'statusAsc' => $query
                ->orderBy(
                    'cms_pages.status',
                    'asc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'statusDesc' => $query
                ->orderBy(
                    'cms_pages.status',
                    'desc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'statusDraft' => $query
                ->where(
                    'cms_pages.status',
                    'draft'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'statusPublished' => $query
                ->where(
                    'cms_pages.status',
                    'published'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'statusArchived' => $query
                ->where(
                    'cms_pages.status',
                    'archived'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            /** Owner name */
            'ownerNameAsc' => $query
                ->leftJoin(
                    'users as owner_sort',
                    'owner_sort.id',
                    '=',
                    'cms_pages.user_id'
                )
                ->addSelect(
                    'cms_pages.*'
                )
                ->orderBy(
                    'owner_sort.name',
                    'asc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'ownerNameDesc' => $query
                ->leftJoin(
                    'users as owner_sort',
                    'owner_sort.id',
                    '=',
                    'cms_pages.user_id'
                )
                ->addSelect(
                    'cms_pages.*'
                )
                ->orderBy(
                    'owner_sort.name',
                    'desc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            /** Owner email */
            'ownerEmailAsc' => $query
                ->leftJoin(
                    'users as owner_sort',
                    'owner_sort.id',
                    '=',
                    'cms_pages.user_id'
                )
                ->addSelect(
                    'cms_pages.*'
                )
                ->orderBy(
                    'owner_sort.email',
                    'asc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'ownerEmailDesc' => $query
                ->leftJoin(
                    'users as owner_sort',
                    'owner_sort.id',
                    '=',
                    'cms_pages.user_id'
                )
                ->addSelect(
                    'cms_pages.*'
                )
                ->orderBy(
                    'owner_sort.email',
                    'desc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            /** Published */
            'publishedAtAsc' => $query
                ->orderBy(
                    'cms_pages.published_at',
                    'asc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'publishedAtDesc' => $query
                ->orderBy(
                    'cms_pages.published_at',
                    'desc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            /** Created */
            'createdAtAsc',
            'dateAsc' => $query
                ->orderBy(
                    'cms_pages.created_at',
                    'asc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'createdAtDesc',
            'dateDesc' => $query
                ->orderBy(
                    'cms_pages.created_at',
                    'desc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            /** Updated */
            'updatedAtAsc' => $query
                ->orderBy(
                    'cms_pages.updated_at',
                    'asc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'updatedAtDesc' => $query
                ->orderBy(
                    'cms_pages.updated_at',
                    'desc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            /** Children */
            'childrenAsc' => $query
                ->withCount(
                    'children'
                )
                ->orderBy(
                    'children_count',
                    'asc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'childrenDesc' => $query
                ->withCount(
                    'children'
                )
                ->orderBy(
                    'children_count',
                    'desc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            /** Title текущей локали */
            'titleAsc' => $query
                ->leftJoin(
                    'cms_page_translations as cpt_sort',
                    function (
                        $join
                    ) use (
                        $locale
                    ): void {
                        $join
                            ->on(
                                'cpt_sort.cms_page_id',
                                '=',
                                'cms_pages.id'
                            )
                            ->where(
                                'cpt_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect(
                    'cms_pages.*'
                )
                ->orderBy(
                    'cpt_sort.title',
                    'asc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            'titleDesc' => $query
                ->leftJoin(
                    'cms_page_translations as cpt_sort',
                    function (
                        $join
                    ) use (
                        $locale
                    ): void {
                        $join
                            ->on(
                                'cpt_sort.cms_page_id',
                                '=',
                                'cms_pages.id'
                            )
                            ->where(
                                'cpt_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect(
                    'cms_pages.*'
                )
                ->orderBy(
                    'cpt_sort.title',
                    'desc'
                )
                ->orderByDesc(
                    'cms_pages.id'
                ),

            /** Default */
            default => $query->ordered(),
        };
    }

    /* ======================== Helpers ======================== */

    /** Страница корневая */
    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }

    /** Есть дочерние страницы */
    public function hasChildren(): bool
    {
        return $this
            ->children()
            ->exists();
    }

    /** Страница активна */
    public function isActive(): bool
    {
        return (bool) $this->activity;
    }

    /** Страница опубликована */
    public function isPublished(): bool
    {
        return $this->status === 'published'
            && (bool) $this->activity
            && $this->published_at !== null;
    }

    /** Использовать собственный HTML-контент */
    public function hasOwnContent(): bool
    {
        return (bool) $this->show_content;
    }

    /** Использовать собственные SEO-поля */
    public function hasOwnSeo(): bool
    {
        return (bool) $this->show_seo;
    }
}
