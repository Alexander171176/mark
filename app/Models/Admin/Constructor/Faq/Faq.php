<?php

namespace App\Models\Admin\Constructor\Faq;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'faqs';

    protected $fillable = [
        'question',     // Вопрос
        'answer',       // Ответ
        'slug',         // ЧПУ
        'category',     // Категория/группа
        'locale',       // Локаль
        'activity',     // Опубликовано?
        'sort',         // Порядок
        'meta',         // Произвольные метаданные (JSON)
    ];

    protected $casts = [
        'activity'   => 'bool',
        'sort'       => 'int',
        'meta'       => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /* ======= Скоупы ======= */

    // Только опубликованные
    public function scopeActive($q)
    {
        return $q->where('activity', true);
    }

    // Сортировка по позиции, затем по id (стабильный порядок)
    public function scopeOrdered($q)
    {
        return $q->orderBy('sort')->orderBy('id');
    }

    // Фильтр по категории и/или локали
    public function scopeFilter($q, ?string $category = null, ?string $locale = null)
    {
        if ($category !== null) $q->where('category', $category);
        if ($locale   !== null) $q->where('locale', $locale);
        return $q;
    }
}
