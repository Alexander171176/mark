<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; // Если используете верификацию email
use App\Models\Admin\Blog\Comment\Comment;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfile;
use App\Models\User\Like\BlogArticleLike;
use App\Models\User\Like\BlogVideoLike;
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
    // --- КОНЕЦ НОВЫХ СВЯЗЕЙ ---

    /* ======================== Scopes ======================== */

    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderByDesc('id');
    }

    /** Поиск */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $q;
        }

        $words = collect(preg_split('/[\s:#№,"\'«»(){}\[\].!?\/\\\\|;+=*&^%$@<>`~_-]+/u', $term))
            ->map(fn ($word) => trim($word))
            ->filter(fn ($word) => mb_strlen($word) >= 2)
            ->values();

        if ($words->isEmpty()) {
            return $q;
        }

        return $q->where(function (Builder $query) use ($words) {
            foreach ($words as $word) {
                $query->where(function (Builder $query) use ($word) {
                    $query
                        ->where('users.name', 'like', "%{$word}%")
                        ->orWhere('users.email', 'like', "%{$word}%")
                        ->orWhereHas('roles', function (Builder $qq) use ($word) {
                            $qq->where('name', 'like', "%{$word}%");
                        })
                        ->orWhereHas('permissions', function (Builder $qq) use ($word) {
                            $qq->where('name', 'like', "%{$word}%");
                        });
                });
            }
        });
    }

    /** Сортировка по параметру */
    public function scopeSortByParam(Builder $q, ?string $sort): Builder
    {
        return match ($sort) {
            'idAsc' => $q->orderBy('users.id', 'asc'),
            'idDesc' => $q->orderBy('users.id', 'desc'),

            'name', 'nameAsc' => $q->orderBy('users.name', 'asc')->orderByDesc('users.id'),
            'nameDesc' => $q->orderBy('users.name', 'desc')->orderByDesc('users.id'),

            'emailAsc' => $q->orderBy('users.email', 'asc')->orderByDesc('users.id'),
            'emailDesc' => $q->orderBy('users.email', 'desc')->orderByDesc('users.id'),

            'rolesAsc' => $q->withCount('roles')->orderBy('roles_count', 'asc')->orderByDesc('users.id'),
            'rolesDesc' => $q->withCount('roles')->orderBy('roles_count', 'desc')->orderByDesc('users.id'),

            'permissionsAsc' => $q->withCount('permissions')->orderBy('permissions_count', 'asc')->orderByDesc('users.id'),
            'permissionsDesc' => $q->withCount('permissions')->orderBy('permissions_count', 'desc')->orderByDesc('users.id'),

            'createdAtAsc' => $q->orderBy('users.created_at', 'asc')->orderByDesc('users.id'),
            'createdAtDesc' => $q->orderBy('users.created_at', 'desc')->orderByDesc('users.id'),

            'updatedAtAsc' => $q->orderBy('users.updated_at', 'asc')->orderByDesc('users.id'),
            'updatedAtDesc' => $q->orderBy('users.updated_at', 'desc')->orderByDesc('users.id'),

            default => $q->ordered(),
        };
    }
}
