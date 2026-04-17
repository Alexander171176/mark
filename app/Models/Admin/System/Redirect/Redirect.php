<?php

namespace App\Models\Admin\System\Redirect;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Redirect extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'redirects';

    protected $fillable = [
        'from_path',      // Откуда редиректим (относительный путь)
        'to_url',         // Куда редиректим (URL)
        'code',           // HTTP-код (301|302 и т.п.)
        'preserve_query', // Сохранять query string исходного запроса
        'locale',         // Ограничение по локали (nullable)
        'activity',      // Флаг активности
        'hits',           // Счётчик срабатываний
        'last_used_at',   // Последнее использование
        'notes',          // Заметки
        'meta',           // Произвольные метаданные (JSON)
    ];

    protected $casts = [
        'code'           => 'int',
        'preserve_query' => 'bool',
        'activity'       => 'bool',
        'hits'           => 'int',
        'last_used_at'   => 'datetime',
        'meta'           => 'array',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'deleted_at'     => 'datetime',
    ];

    /* ================= Scopes ================= */

    // Только активные
    public function scopeActive($q)
    {
        return $q->where('activity', true);
    }

    // Для конкретной локали или без привязки
    public function scopeForLocale($q, ?string $locale)
    {
        return $q->where(function ($qq) use ($locale) {
            $qq->whereNull('locale')->orWhere('locale', $locale);
        });
    }

    // По точному пути
    public function scopeForPath($q, string $fromPath)
    {
        return $q->where('from_path', $fromPath);
    }

    /* ================= Helpers ================= */

    // Увеличить счётчик и обновить last_used_at — удобно вызывать при применении редиректа
    public function touchHit(): void
    {
        $this->increment('hits');
        $this->forceFill(['last_used_at' => now()])->save();
    }
}
