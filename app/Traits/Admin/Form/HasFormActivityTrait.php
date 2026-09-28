<?php

namespace App\Traits\Admin\Form;

use App\Http\Requests\Admin\System\UpdateActivityRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait HasFormActivityTrait
{
    /**
     * Обновление активности одной записи.
     */
    public function updateActivity(
        UpdateActivityRequest $request,
        int $id
    ): RedirectResponse {
        $model = $this->baseQuery()
            ->findOrFail(
                $id
            );

        $model->update([
            'activity' =>
                $request->validated(
                    'activity'
                ),
        ]);

        return back()->with(
            'success',
            "Активность {$this->entityLabel} обновлена."
        );
    }

    /**
     * Массовое обновление активности.
     */
    public function bulkUpdateActivity(
        Request $request
    ): RedirectResponse|JsonResponse {
        $table = (
        new $this->modelClass
        )->getTable();

        $validated = $request->validate([
            'ids' => [
                'required',
                'array',
                'min:1',
            ],

            'ids.*' => [
                'required',
                'integer',
                'distinct',
                "exists:{$table},id",
            ],

            'activity' => [
                'required',
                'boolean',
            ],
        ]);

        $ids = array_values(
            array_unique(
                array_map(
                    'intval',
                    $validated['ids']
                )
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Проверяем доступность всех записей
        |--------------------------------------------------------------------------
        */

        $allowedIds = $this->baseQuery()
            ->whereIn(
                'id',
                $ids
            )
            ->pluck(
                'id'
            )
            ->map(
                fn ($id) => (int) $id
            )
            ->all();

        if (
            count($allowedIds)
            !== count($ids)
        ) {
            $message =
                "Часть {$this->entityLabel} недоступна.";

            return $request->expectsJson()
                ? response()->json(
                    [
                        'message' => $message,
                    ],
                    403
                )
                : back()->with(
                    'error',
                    $message
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Массовое обновление
        |--------------------------------------------------------------------------
        */

        $this->baseQuery()
            ->whereIn(
                'id',
                $allowedIds
            )
            ->update([
                'activity' =>
                    $validated['activity'],
            ]);

        $message =
            "Активность выбранных {$this->entityLabel} обновлена.";

        return $request->expectsJson()
            ? response()->json([
                'message' => $message,
            ])
            : back()->with(
                'success',
                $message
            );
    }
}
