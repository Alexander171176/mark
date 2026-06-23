<?php

namespace App\Models\Admin\System\ImagePreset;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagePreset extends Model
{
    use HasFactory;

    /** Таблица. */
    protected $table = 'image_presets';

    /** Массовое заполнение. */
    protected $fillable = [
        'key',
        'description',

        'shape',

        'width',
        'height',

        'image_rotation_enabled',
        'crop_rotation_enabled',

        'max_file_size_kb',

        'keep_original',

        'sort',
    ];

    /** Приведение типов. */
    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',

        'image_rotation_enabled' => 'boolean',
        'crop_rotation_enabled' => 'boolean',

        'max_file_size_kb' => 'integer',

        'keep_original' => 'boolean',

        'sort' => 'integer',
    ];

    /** Только квадратные изображения. */
    public function scopeSquare(
        Builder $query
    ): Builder {
        return $query->where('shape', 'square');
    }

    /** Только круглые изображения. */
    public function scopeCircle(
        Builder $query
    ): Builder {
        return $query->where('shape', 'circle');
    }

    /** Только прямоугольные изображения. */
    public function scopeRectangle(
        Builder $query
    ): Builder {
        return $query->where('shape', 'rectangle');
    }

    /** Поиск. */
    public function scopeSearch(
        Builder $query,
        ?string $search
    ): Builder {
        if (!$search) {
            return $query;
        }

        return $query->where(function (
            Builder $query
        ) use (
            $search
        ) {
            $query
                ->where('key', 'like', "%{$search}%")
                ->orWhere(
                    'description',
                    'like',
                    "%{$search}%"
                );
        });
    }

    /** Универсальная сортировка. */
    public function scopeSortByParam(
        Builder $query,
        ?string $sort
    ): Builder {
        return match ($sort) {

            'idAsc'
            => $query
                ->orderBy('id'),

            'idDesc'
            => $query
                ->orderByDesc('id'),

            'keyAsc'
            => $query
                ->orderBy('key'),

            'keyDesc'
            => $query
                ->orderByDesc('key'),

            'widthAsc'
            => $query
                ->orderBy('width'),

            'widthDesc'
            => $query
                ->orderByDesc('width'),

            'heightAsc'
            => $query
                ->orderBy('height'),

            'heightDesc'
            => $query
                ->orderByDesc('height'),

            'sizeAsc'
            => $query
                ->orderBy('max_file_size_kb'),

            'sizeDesc'
            => $query
                ->orderByDesc('max_file_size_kb'),

            'sortAsc'
            => $query
                ->orderBy('sort')
                ->orderBy('id'),

            'sortDesc'
            => $query
                ->orderByDesc('sort')
                ->orderByDesc('id'),

            'rectangle'
            => $query
                ->where('shape', 'rectangle'),

            'square'
            => $query
                ->where('shape', 'square'),

            'circle'
            => $query
                ->where('shape', 'circle'),

            'createdAtAsc'
            => $query
                ->orderBy('created_at'),

            'createdAtDesc'
            => $query
                ->orderByDesc('created_at'),

            'updatedAtAsc'
            => $query
                ->orderBy('updated_at'),

            'updatedAtDesc'
            => $query
                ->orderByDesc('updated_at'),

            default
            => $query
                ->orderBy('sort')
                ->orderBy('id'),
        };
    }

    /**
     * Размер одной стороной.
     * Для круга и квадрата.
     */
    public function getSingleSizeAttribute(): int
    {
        return max(
            $this->width,
            $this->height
        );
    }

    /** Разрешение. */
    public function getResolutionAttribute(): string
    {
        return "{$this->width}×{$this->height}";
    }
}
