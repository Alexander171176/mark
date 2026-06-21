<?php

namespace App\Models\Admin\System\ImageProcessor;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImageProcessorProfile extends Model
{
    use HasFactory;

    protected $table = 'image_processor_profiles';

    protected $fillable = [
        'key',
        'name',
        'description',
        'activity',
        'sort',
    ];

    protected $casts = [
        'activity' => 'boolean',
        'sort' => 'integer',
    ];

    /** Варианты обработки профиля */
    public function variants(): HasMany
    {
        return $this->hasMany(
            ImageProcessorVariant::class,
            'image_processor_profile_id'
        );
    }

    /** Только активные профили */
    public function scopeActive(
        Builder $query
    ): Builder {
        return $query->where(
            'activity',
            true
        );
    }

    /** Поиск */
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
                ->where(
                    'key',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'name',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'description',
                    'like',
                    "%{$search}%"
                );

        });
    }

    /** Универсальная сортировка */
    public function scopeSortByParam(
        Builder $query,
        ?string $sort
    ): Builder {

        return match ($sort) {

            'idAsc'
            => $query->orderBy(
                'id'
            ),

            'idDesc'
            => $query->orderByDesc(
                'id'
            ),

            'sortAsc'
            => $query
                ->orderBy(
                    'sort'
                )
                ->orderBy(
                    'id'
                ),

            'sortDesc'
            => $query
                ->orderByDesc(
                    'sort'
                )
                ->orderByDesc(
                    'id'
                ),

            'name'
            => $query
                ->orderBy(
                    'name'
                )
                ->orderBy(
                    'id'
                ),

            'key'
            => $query
                ->orderBy(
                    'key'
                )
                ->orderBy(
                    'id'
                ),

            'activity'
            => $query
                ->orderByDesc(
                    'activity'
                )
                ->orderBy(
                    'sort'
                ),

            'inactive'
            => $query
                ->orderBy(
                    'activity'
                )
                ->orderBy(
                    'sort'
                ),

            'variants'
            => $query
                ->orderByDesc(
                    'variants_count'
                )
                ->orderBy(
                    'sort'
                ),

            default
            => $query
                ->orderBy(
                    'sort'
                )
                ->orderBy(
                    'id'
                ),
        };
    }

    /** Сортировка по умолчанию */
    public function scopeOrdered(
        Builder $query
    ): Builder {
        return $query
            ->orderBy(
                'sort'
            )
            ->orderBy(
                'id'
            );
    }
}
