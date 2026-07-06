<?php

namespace App\Models\Admin\Privacy\PrivacyUserConsent;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivacyUserConsent extends Model
{
    use HasFactory;

    /**
     * Название таблицы.
     *
     * @var string
     */
    protected $table = 'privacy_user_consents';

    /**
     * Атрибуты, доступные для массового заполнения.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'locale',
        'policy_version',
        'policy_url',
        'policy_hash',
        'accepted',
        'accepted_at',
        'revoked_at',
    ];

    /**
     * Приведение типов атрибутов.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'accepted' => 'boolean',
        'policy_version' => 'integer',
        'accepted_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Пользователь, принявший политику конфиденциальности.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
