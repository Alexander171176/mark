<?php

namespace App\Models\Admin\Market\MarketRecentlyViewedProduct;

use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketRecentlyViewedProduct extends Model
{
    use HasFactory;

    /**
     * Таблица модели.
     */
    protected $table = 'market_recently_viewed_products';

    /**
     * Поля, доступные для массового заполнения.
     */
    protected $fillable = [
        'user_id',
        'market_product_id',
        'viewed_at',
    ];

    /**
     * Преобразование типов.
     */
    protected $casts = [
        'user_id' => 'integer',
        'market_product_id' => 'integer',
        'viewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Пользователь, просмотревший товар.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /**
     * Просмотренный товар.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(
            MarketProduct::class,
            'market_product_id'
        );
    }
}
