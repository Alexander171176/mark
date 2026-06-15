<?php

namespace App\Models\Admin\System\Permission;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Permission as SpatiePermission;

class AdminPermission extends SpatiePermission
{
    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('name')->orderByDesc('id');
    }

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
                        ->where('permissions.name', 'like', "%{$word}%")
                        ->orWhere('permissions.guard_name', 'like', "%{$word}%")
                        ->orWhereHas('roles', function (Builder $qq) use ($word) {
                            $qq->where('name', 'like', "%{$word}%")
                                ->orWhere('guard_name', 'like', "%{$word}%");
                        });
                });
            }
        });
    }

    public function scopeSortByParam(Builder $q, ?string $sort): Builder
    {
        return match ($sort) {
            'idAsc' => $q->orderBy('permissions.id', 'asc'),
            'idDesc' => $q->orderBy('permissions.id', 'desc'),

            'name', 'nameAsc' => $q->orderBy('permissions.name', 'asc')->orderByDesc('permissions.id'),
            'nameDesc' => $q->orderBy('permissions.name', 'desc')->orderByDesc('permissions.id'),

            'guardNameAsc' => $q->orderBy('permissions.guard_name', 'asc')->orderByDesc('permissions.id'),
            'guardNameDesc' => $q->orderBy('permissions.guard_name', 'desc')->orderByDesc('permissions.id'),

            'rolesAsc' => $q->withCount('roles')->orderBy('roles_count', 'asc')->orderByDesc('permissions.id'),
            'rolesDesc' => $q->withCount('roles')->orderBy('roles_count', 'desc')->orderByDesc('permissions.id'),

            'createdAtAsc' => $q->orderBy('permissions.created_at', 'asc')->orderByDesc('permissions.id'),
            'createdAtDesc' => $q->orderBy('permissions.created_at', 'desc')->orderByDesc('permissions.id'),

            'updatedAtAsc' => $q->orderBy('permissions.updated_at', 'asc')->orderByDesc('permissions.id'),
            'updatedAtDesc' => $q->orderBy('permissions.updated_at', 'desc')->orderByDesc('permissions.id'),

            default => $q->ordered(),
        };
    }
}
