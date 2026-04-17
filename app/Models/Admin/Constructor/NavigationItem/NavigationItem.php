<?php

namespace App\Models\Admin\Constructor\NavigationItem;

use App\Models\Admin\Constructor\NavigationMenu\NavigationMenu;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class NavigationItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'navigation_items';

    protected $fillable = [
        'menu_id',       // FK -> navigation_menus.id
        'parent_id',     // self FK -> navigation_items.id (вложенность)
        'title',         // Текст ссылки
        'type',          // custom|internal|route
        'url',           // URL (для custom/internal)
        'route_name',    // Имя роута (для type=route)
        'route_params',  // Параметры роута (JSON)
        'target',        // _self|_blank
        'icon',          // Класс/ключ иконки
        'activity',      // Флаг активности
        'sort',          // Порядок среди соседей
        'meta',          // Произвольные атрибуты
    ];

    protected $casts = [
        'route_params' => 'array',
        'meta'         => 'array',
        'activity'     => 'bool',
        'sort'         => 'int',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    /* ============ Связи ============ */

    // Меню, которому принадлежит пункт
    public function menu(): BelongsTo
    {
        return $this->belongsTo(NavigationMenu::class, 'menu_id');
    }

    // Родительский пункт
    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavigationItem::class, 'parent_id');
    }

    // Дочерние пункты
    public function children(): HasMany
    {
        return $this->hasMany(NavigationItem::class, 'parent_id')
            ->orderBy('sort')
            ->orderBy('id');
    }

    /* ============ Скоупы ============ */

    // Только активные
    public function scopeActive($q)
    {
        return $q->where('activity', true);
    }

    // Только корневые пункты конкретного меню
    public function scopeRootsOfMenu($q, int $menuId)
    {
        return $q->where('menu_id', $menuId)->whereNull('parent_id');
    }

    // Упорядоченные (по sort, затем id)
    public function scopeOrdered($q)
    {
        return $q->orderBy('sort')->orderBy('id');
    }

    /* ============ Хелперы ============ */

    // Сформировать конечный href (если это route — собрать URL по имени/параметрам)
    public function getHrefAttribute(): ?string
    {
        if ($this->type === 'route' && $this->route_name) {
            try {
                return route($this->route_name, $this->route_params ?? []);
            } catch (\Throwable $e) {
                return null; // если роут не существует — не падать
            }
        }
        return $this->url;
    }

    // Быстрый признак, что пункт кликабельный
    public function getIsClickableAttribute(): bool
    {
        if ($this->type === 'route') {
            return !empty($this->route_name);
        }
        return !empty($this->url);
    }
}
