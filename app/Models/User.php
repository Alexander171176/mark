<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; // Если используете верификацию email
use App\Models\Admin\Blog\Comment\Comment;
use App\Models\Admin\Market\MarketCompany\MarketCompany;
use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\Admin\Market\MarketRecentlyViewedProduct\MarketRecentlyViewedProduct;
use App\Models\Admin\Market\MarketShop\MarketShop;
use App\Models\Admin\Review\Review;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfile;
use App\Models\User\Like\BlogArticleLike;
use App\Models\User\Like\BlogVideoLike;
use App\Models\User\Like\MarketProductLike;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

// Если используете MustVerifyEmail, раскомментируйте его и implements
class User extends Authenticatable /* implements MustVerifyEmail */
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles; // От Spatie/Permission

    protected string $guard_name = 'sanctum'; // ✅ важно для корректной записи в model_has_roles

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'current_team_id', // Часто скрывают ID текущей команды
        'profile_photo_path', // Скрываем путь, т.к. используем appends profile_photo_url
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Добавляем каст для автоматического хеширования пароля (рекомендовано в Laravel 10+)
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    // --- НОВЫЕ СВЯЗИ ---

    /**
     * Комментарии, оставленные пользователем.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    /**
     * Лайки статей, поставленные пользователем.
     */
    public function articleLikes(): HasMany
    {
        return $this->hasMany(BlogArticleLike::class, 'user_id');
    }

    /**
     * Лайки видео, поставленные пользователем.
     */
    public function videoLikes(): HasMany
    {
        return $this->hasMany(BlogVideoLike::class, 'user_id');
    }

    public function instructorProfiles(): HasMany
    {
        return $this->hasMany(SchoolInstructorProfile::class, 'user_id');
    }

    /** Компании, промодерированные пользователем */
    public function moderatedMarketCompanies(): HasMany
    {
        return $this->hasMany(
            MarketCompany::class,
            'moderated_by'
        );
    }

    /** Магазины, промодерированные пользователем */
    public function moderatedMarketShops(): HasMany
    {
        return $this->hasMany(
            MarketShop::class,
            'moderated_by'
        );
    }

    /** Товары, промодерированные пользователем */
    public function moderatedMarketProducts(): HasMany
    {
        return $this->hasMany(
            MarketProduct::class,
            'moderated_by'
        );
    }

    /** Отзывы товаров, промодерированные пользователем */
    public function moderatedMarketProductReviews(): HasMany
    {
        return $this->hasMany(
            Review::class,
            'moderated_by'
        );
    }

    /* ======================== Marketplace Relations ======================== */

    /** Компании маркетплейса, созданные пользователем */
    public function marketCompanies(): HasMany
    {
        return $this->hasMany(
            MarketCompany::class,
            'user_id'
        );
    }

    /** Магазины маркетплейса, созданные пользователем */
    public function marketShops(): HasMany
    {
        return $this->hasMany(
            MarketShop::class,
            'user_id'
        );
    }

    /** Товары маркетплейса, созданные пользователем */
    public function marketProducts(): HasMany
    {
        return $this->hasMany(
            MarketProduct::class,
            'user_id'
        );
    }

    /** Лайки товаров, поставленные пользователем */
    public function marketProductLikes(): HasMany
    {
        return $this->hasMany(
            MarketProductLike::class,
            'user_id'
        );
    }

    /** Отзывы на товары, оставленные пользователем */
    public function marketProductReviews(): HasMany
    {
        return $this->hasMany(
            Review::class,
            'user_id'
        );
    }

    /**
     * История просмотренных товаров маркетплейса.
     */
    public function recentlyViewedMarketProducts(): HasMany
    {
        return $this->hasMany(
            MarketRecentlyViewedProduct::class,
            'user_id'
        );
    }

    /* ======================== Scopes ======================== */

    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderByDesc('users.id');
    }

    /**
     * Поиск по данным, доступным в Admin User Index.
     *
     * Поиск выполняется по:
     * - ID пользователя;
     * - имени;
     * - email;
     * - названиям ролей;
     * - названиям прямых разрешений.
     *
     * Несколько слов объединяются по принципу AND:
     * каждое слово должно найти совпадение хотя бы в одном из полей/relations.
     */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $q;
        }

        $words = collect(
            preg_split('/[\s:#№,"\'«»(){}\[\].!?\/\\\\|;+=*&^%$@<>`~_-]+/u', $term)
        )
            ->map(fn ($word) => trim($word))
            ->filter(fn ($word) => $word !== '')
            ->values();

        if ($words->isEmpty()) {
            return $q;
        }

        return $q->where(function (Builder $query) use ($words) {
            foreach ($words as $word) {
                $query->where(function (Builder $query) use ($word) {
                    if (ctype_digit($word)) {
                        $query->orWhere('users.id', (int) $word);
                    }

                    $query
                        ->orWhere('users.name', 'like', "%{$word}%")
                        ->orWhere('users.email', 'like', "%{$word}%")
                        ->orWhereHas('roles', function (Builder $query) use ($word) {
                            $query->where('name', 'like', "%{$word}%");
                        })
                        ->orWhereHas('permissions', function (Builder $query) use ($word) {
                            $query->where('name', 'like', "%{$word}%");
                        });
                });
            }
        });
    }

    /**
     * Сортировка для Admin User Index.
     *
     * roles_count и permissions_count добавляются в UserController::indexQuery(),
     * поэтому повторный withCount() здесь не нужен.
     */
    public function scopeSortByParam(Builder $q, ?string $sort): Builder
    {
        return match ($sort) {
            'idAsc' => $q->orderBy('users.id', 'asc'),
            'idDesc' => $q->orderBy('users.id', 'desc'),

            'name', 'nameAsc' => $q
                ->orderBy('users.name', 'asc')
                ->orderByDesc('users.id'),

            'nameDesc' => $q
                ->orderBy('users.name', 'desc')
                ->orderByDesc('users.id'),

            'emailAsc' => $q
                ->orderBy('users.email', 'asc')
                ->orderByDesc('users.id'),

            'emailDesc' => $q
                ->orderBy('users.email', 'desc')
                ->orderByDesc('users.id'),

            'rolesAsc' => $q
                ->orderBy('roles_count', 'asc')
                ->orderByDesc('users.id'),

            'rolesDesc' => $q
                ->orderBy('roles_count', 'desc')
                ->orderByDesc('users.id'),

            'permissionsAsc' => $q
                ->orderBy('permissions_count', 'asc')
                ->orderByDesc('users.id'),

            'permissionsDesc' => $q
                ->orderBy('permissions_count', 'desc')
                ->orderByDesc('users.id'),

            'createdAtAsc' => $q
                ->orderBy('users.created_at', 'asc')
                ->orderByDesc('users.id'),

            'createdAtDesc' => $q
                ->orderBy('users.created_at', 'desc')
                ->orderByDesc('users.id'),

            'updatedAtAsc' => $q
                ->orderBy('users.updated_at', 'asc')
                ->orderByDesc('users.id'),

            'updatedAtDesc' => $q
                ->orderBy('users.updated_at', 'desc')
                ->orderByDesc('users.id'),

            default => $q->ordered(),
        };
    }
}
