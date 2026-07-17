<?php

namespace App\Models\User\Like;

use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketProductLike extends Model
{
    use HasFactory;

    protected $table = 'market_product_likes';

    protected $fillable = [
        'user_id',
        'market_product_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'market_product_id' => 'integer',
    ];

    /** Товар */
    public function product(): BelongsTo
    {
        return $this->belongsTo(
            MarketProduct::class,
            'market_product_id'
        );
    }

    /** Пользователь, поставивший лайк */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}
