<?php

namespace App\Models\Admin\System\Setting;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'sort',
        'activity',
        'type',
        'option',
        'value',
        'constant',
        'category',
        'description',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'sort' => 'integer',
        'activity' => 'boolean',
    ];

    /* ======================== Scopes ======================== */

    /**
     * Сортировка по умолчанию.
     */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q
            ->orderBy('settings.sort', 'asc')
            ->orderByDesc('settings.id');
    }

    /**
     * Поиск.
     */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $q;
        }

        $words = collect(
            preg_split(
                '/[\s:#№,"\'«»(){}\[\].!?\/\\\\|;+=*&^%$@<>`~_-]+/u',
                $term,
                -1,
                PREG_SPLIT_NO_EMPTY
            )
        )
            ->map(fn ($word) => trim($word))
            ->filter()
            ->values();

        if ($words->isEmpty()) {
            return $q;
        }

        return $q->where(function (Builder $query) use ($words) {
            foreach ($words as $word) {
                $query->where(function (Builder $query) use ($word) {
                    if (ctype_digit($word)) {
                        $query->where('settings.id', (int) $word)
                            ->orWhere('settings.type', 'like', "%{$word}%")
                            ->orWhere('settings.option', 'like', "%{$word}%")
                            ->orWhere('settings.value', 'like', "%{$word}%")
                            ->orWhere('settings.constant', 'like', "%{$word}%")
                            ->orWhere('settings.category', 'like', "%{$word}%")
                            ->orWhere('settings.description', 'like', "%{$word}%");

                        return;
                    }

                    $query
                        ->where('settings.type', 'like', "%{$word}%")
                        ->orWhere('settings.option', 'like', "%{$word}%")
                        ->orWhere('settings.value', 'like', "%{$word}%")
                        ->orWhere('settings.constant', 'like', "%{$word}%")
                        ->orWhere('settings.category', 'like', "%{$word}%")
                        ->orWhere('settings.description', 'like', "%{$word}%");
                });
            }
        });
    }

    /**
     * Сортировка по параметру.
     */
    public function scopeSortByParam(Builder $q, ?string $sort): Builder
    {
        return match ($sort) {
            'idAsc' =>
            $q->orderBy('settings.id', 'asc'),

            'idDesc' =>
            $q->orderBy('settings.id', 'desc'),

            'sortAsc', 'sort' =>
            $q->orderBy('settings.sort', 'asc')
                ->orderByDesc('settings.id'),

            'sortDesc' =>
            $q->orderBy('settings.sort', 'desc')
                ->orderByDesc('settings.id'),

            'categoryAsc', 'category' =>
            $q->orderBy('settings.category', 'asc')
                ->orderByDesc('settings.id'),

            'categoryDesc' =>
            $q->orderBy('settings.category', 'desc')
                ->orderByDesc('settings.id'),

            'typeAsc', 'type' =>
            $q->orderBy('settings.type', 'asc')
                ->orderByDesc('settings.id'),

            'typeDesc' =>
            $q->orderBy('settings.type', 'desc')
                ->orderByDesc('settings.id'),

            'optionAsc', 'option' =>
            $q->orderBy('settings.option', 'asc')
                ->orderByDesc('settings.id'),

            'optionDesc' =>
            $q->orderBy('settings.option', 'desc')
                ->orderByDesc('settings.id'),

            'activityAsc' =>
            $q->orderBy('settings.activity', 'asc')
                ->orderByDesc('settings.id'),

            'activityDesc' =>
            $q->orderBy('settings.activity', 'desc')
                ->orderByDesc('settings.id'),

            'activity' =>
            $q->where('settings.activity', true)
                ->orderByDesc('settings.id'),

            'inactive' =>
            $q->where('settings.activity', false)
                ->orderByDesc('settings.id'),

            default =>
            $q->ordered(),
        };
    }
}
