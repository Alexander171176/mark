<?php

namespace App\Models\Admin\System\Role;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role as SpatieRole;

class AdminRole extends SpatieRole
{
    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('name')->orderByDesc('id');
    }

    /** Поиск */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $q;
        }

        $words = collect(preg_split('/[\s:#№,"\'«»(){}\[\].!?\/\\\\|;+=*&^%$@<>`~_-]+/u', $term))
            ->map(fn ($word) => trim($word))
            ->filter(fn ($word) => mb_strlen($word) >= 2)
            ->values();

        if ($words->isEmpty()) {
            return $q;
        }

        return $q->where(function (Builder $query) use ($words) {
            foreach ($words as $word) {
                $query->where(function (Builder $query) use ($word) {
                    $query
                        ->where('roles.name', 'like', "%{$word}%")
                        ->orWhere('roles.guard_name', 'like', "%{$word}%")
                        ->orWhereHas('permissions', function (Builder $qq) use ($word) {
                            $qq->where('name', 'like', "%{$word}%")
                                ->orWhere('guard_name', 'like', "%{$word}%");
                        });
                });
            }
        });
    }

    /** Сортировка по параметру */
    public function scopeSortByParam(Builder $q, ?string $sort): Builder
    {
        return match ($sort) {
            'idAsc' => $q->orderBy('roles.id', 'asc'),
            'idDesc' => $q->orderBy('roles.id', 'desc'),

            'name', 'nameAsc' => $q->orderBy('roles.name', 'asc')->orderByDesc('roles.id'),
            'nameDesc' => $q->orderBy('roles.name', 'desc')->orderByDesc('roles.id'),

            'guardNameAsc' => $q->orderBy('roles.guard_name', 'asc')->orderByDesc('roles.id'),
            'guardNameDesc' => $q->orderBy('roles.guard_name', 'desc')->orderByDesc('roles.id'),

            'permissionsAsc' => $q->withCount('permissions')->orderBy('permissions_count', 'asc')->orderByDesc('roles.id'),
            'permissionsDesc' => $q->withCount('permissions')->orderBy('permissions_count', 'desc')->orderByDesc('roles.id'),

            'createdAtAsc' => $q->orderBy('roles.created_at', 'asc')->orderByDesc('roles.id'),
            'createdAtDesc' => $q->orderBy('roles.created_at', 'desc')->orderByDesc('roles.id'),

            'updatedAtAsc' => $q->orderBy('roles.updated_at', 'asc')->orderByDesc('roles.id'),
            'updatedAtDesc' => $q->orderBy('roles.updated_at', 'desc')->orderByDesc('roles.id'),

            default => $q->ordered(),
        };
    }
}
