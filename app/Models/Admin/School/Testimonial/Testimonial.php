<?php

namespace App\Models\Admin\School\Testimonial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'testimonials';

    protected $fillable = [
        'quote',         // Текст отзыва
        'author_name',   // Имя автора
        'author_title',  // Должность/роль
        'company',       // Компания
        'avatar_url',    // Картинка автора (если без медиа-библиотеки)
        'source_url',    // Ссылка на источник
        'rating',        // Оценка 1..5 (опционально)
        'activity',      // Опубликовано?
        'sort',          // Порядок сортировки
        'locale',        // Локаль (ru/en/...)
        'meta',          // Произвольные метаданные (JSON)
    ];

    protected $casts = [
        'rating'     => 'int',
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

    // Порядок показа
    public function scopeOrdered($q)
    {
        return $q->orderBy('sort')->orderBy('id');
    }

    // Фильтр по локали
    public function scopeForLocale($q, ?string $locale)
    {
        if ($locale === null) return $q;
        return $q->where('locale', $locale);
    }
}
