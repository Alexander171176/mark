<?php

namespace App\Models\Admin\Constructor\NavigationMenu;

use App\Models\Admin\Constructor\NavigationItem\NavigationItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class NavigationMenu extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'navigation_menus';

    protected $fillable = [
        'name',          // Имя меню для админки
        'slug',          // Уникальный ключ для обращения с фронта
        'location',      // header|footer|sidebar|custom
        'activity',      // Флаг активности
        'sort',          // Сортировка
        'meta',          // Доп. настройки в JSON
    ];

    protected $casts = [
        'activity'   => 'bool',
        'sort'       => 'int',
        'meta'       => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /* ============ Связи ============ */

    // Пункты меню, отсортированные по позиции
    public function items(): HasMany
    {
        return $this->hasMany(NavigationItem::class, 'menu_id')
            ->orderBy('sort')
            ->orderBy('id');
    }

    /* ============ Скоупы ============ */

    // Только активные меню данной зоны
    public function scopeActiveFor($q, string $location)
    {
        return $q->where('location', $location)->where('activity', true);
    }
}
