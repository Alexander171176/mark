<?php

namespace App\Models\Admin\System\Role;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role as SpatieRole;

class AdminRole extends SpatieRole
{
    /**
     * Сортировка по умолчанию.
     */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q
            ->orderBy('roles.name', 'asc')
            ->orderByDesc('roles.id');
    }

    /**
     * Поиск по данным, доступным в Admin Role Index.
     *
     * Поиск выполняется по:
     * - ID роли;
     * - имени роли;
     * - guard_name роли;
     * - именам связанных разрешений.
     *
     * Несколько слов объединяются по принципу AND:
     * каждое слово должно найти совпадение хотя бы в одном из полей/relations.
     */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $q;
        }

        $words = collect(
            preg_split('/[\s:#№,"\'«»(){}\[\].!?\/\\\\|;+=*&^%$@<>`~_-]+/u', $term)
        )
            ->map(fn ($word) => trim($word))
            ->filter(fn ($word) => $word !== '')
            ->values();

        if ($words->isEmpty()) {
            return $q;
        }

        return $q->where(function (Builder $query) use ($words) {
            foreach ($words as $word) {
                $query->where(function (Builder $query) use ($word) {
                    if (ctype_digit($word)) {
                        $query->orWhere('roles.id', (int) $word);
                    }

                    $query
                        ->orWhere('roles.name', 'like', "%{$word}%")
                        ->orWhere('roles.guard_name', 'like', "%{$word}%")
                        ->orWhereHas('permissions', function (Builder $query) use ($word) {
                            $query->where('permissions.name', 'like', "%{$word}%");
                        });
                });
            }
        });
    }

    /**
     * Сортировка для Admin Role Index.
     *
     * permissions_count добавляется в RoleController::indexQuery(),
     * поэтому повторный withCount() здесь не нужен.
     */
    public function scopeSortByParam(Builder $q, ?string $sort): Builder
    {
        return match ($sort) {
            'idAsc' => $q->orderBy('roles.id', 'asc'),
            'idDesc' => $q->orderBy('roles.id', 'desc'),

            'name', 'nameAsc' => $q
                ->orderBy('roles.name', 'asc')
                ->orderByDesc('roles.id'),

            'nameDesc' => $q
                ->orderBy('roles.name', 'desc')
                ->orderByDesc('roles.id'),

            'guardNameAsc' => $q
                ->orderBy('roles.guard_name', 'asc')
                ->orderByDesc('roles.id'),

            'guardNameDesc' => $q
                ->orderBy('roles.guard_name', 'desc')
                ->orderByDesc('roles.id'),

            'permissionsAsc' => $q
                ->orderBy('permissions_count', 'asc')
                ->orderByDesc('roles.id'),

            'permissionsDesc' => $q
                ->orderBy('permissions_count', 'desc')
                ->orderByDesc('roles.id'),

            'createdAtAsc' => $q
                ->orderBy('roles.created_at', 'asc')
                ->orderByDesc('roles.id'),

            'createdAtDesc' => $q
                ->orderBy('roles.created_at', 'desc')
                ->orderByDesc('roles.id'),

            'updatedAtAsc' => $q
                ->orderBy('roles.updated_at', 'asc')
                ->orderByDesc('roles.id'),

            'updatedAtDesc' => $q
                ->orderBy('roles.updated_at', 'desc')
                ->orderByDesc('roles.id'),

            default => $q->ordered(),
        };
    }
}
