<?php

namespace App\Models\Admin\Constructor\HomePage\Hero;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HeroSection extends Model
{
    use HasFactory;

    /**
     * Таблица: hero_sections (по умолчанию совпадает с именем модели, можно не указывать)
     * protected $table = 'hero_sections';
     */
    protected $table = 'hero_sections';

    /**
     * Разрешённые к массовому заполнению поля.
     * Держим список явным, чтобы безопасно использовать create()/update().
     */
    protected $fillable = [
        'locale',
        'title',
        'subtitle',
        'badge_text',
        'description',
        'primary_btn_text',
        'primary_btn_url',
        'primary_btn_target',
        'secondary_btn_text',
        'secondary_btn_url',
        'secondary_btn_target',
        'is_dark',
        'activity',
    ];

    /**
     * Касты для булевых флагов.
     */
    protected $casts = [
        'is_dark'  => 'boolean',
        'activity' => 'boolean',
    ];

    /* ==========================
     |   Relations
     ========================== */

    /**
     * Все иконки стека для секции.
     */
    public function icons(): HasMany
    {
        return $this->hasMany(HeroIcon::class)->orderBy('sort');
    }

    /**
     * Только активные иконки стека (с сортировкой).
     */
    public function activeIcons(): HasMany
    {
        return $this->hasMany(HeroIcon::class)
            ->where('activity', true)
            ->orderBy('sort');
    }

    /**
     * Скриншоты для секции.
     */
    public function screenshots(): HasMany
    {
        return $this->hasMany(HeroScreenshot::class)->orderBy('sort');
    }

    /**
     * Только активные скриншоты (с сортировкой).
     */
    public function activeScreenshots(): HasMany
    {
        return $this->hasMany(HeroScreenshot::class)
            ->where('activity', true)
            ->orderBy('sort');
    }

    /* ==========================
     |   Scopes
     ========================== */

    /**
     * Скоуп: только активные секции.
     */
    public function scopeActive($query)
    {
        return $query->where('activity', true);
    }

    /**
     * Скоуп: отбор по локали (использую имя forLocale, чтобы не конфликтовать с полем).
     */
    public function scopeForLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }

    /**
     * Скоуп: только тёмные варианты.
     */
    public function scopeDark($query)
    {
        return $query->where('is_dark', true);
    }
}
