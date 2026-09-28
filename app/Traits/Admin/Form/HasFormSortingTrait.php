<?php

namespace App\Traits\Admin\Form;

use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

trait HasFormSortingTrait
{
    /**
     * Обновление сортировки одной записи.
     */
    public function updateSort(
        UpdateSortEntityRequest $request,
        int $id
    ): RedirectResponse {
        $model = $this->baseQuery()
            ->findOrFail(
                $id
            );

        $model->update([
            'sort' =>
                $request->validated(
                    'sort'
                ),
        ]);

        return back()->with(
            'success',
            "Сортировка {$this->entityLabel} обновлена."
        );
    }

    /**
     * Массовое обновление сортировки.
     */
    public function updateSortBulk(
        Request $request
    ): RedirectResponse|JsonResponse {
        $table = (
        new $this->modelClass
        )->getTable();

        $validated = $request->validate([
            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.id' => [
                'required',
                'integer',
                'distinct',
                "exists:{$table},id",
            ],

            'items.*.sort' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        $items = $validated['items'];

        $ids = array_map(
            'intval',
            array_column(
                $items,
                'id'
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
                "Часть {$this->entityLabel} недоступна для сортировки.";

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

        try {
            DB::transaction(
                function () use ($items) {
                    foreach (
                        $items
                        as $item
                    ) {
                        $this->baseQuery()
                            ->whereKey(
                                (int) $item['id']
                            )
                            ->update([
                                'sort' =>
                                    (int) $item['sort'],
                            ]);
                    }
                }
            );

            $message =
                "Сортировка {$this->entityLabel} обновлена.";

            return $request->expectsJson()
                ? response()->json([
                    'message' => $message,
                ])
                : back()->with(
                    'success',
                    $message
                );
        } catch (Throwable $exception) {
            report(
                $exception
            );

            $message =
                "Ошибка обновления сортировки {$this->entityLabel}.";

            return $request->expectsJson()
                ? response()->json(
                    [
                        'message' => $message,
                    ],
                    500
                )
                : back()->with(
                    'error',
                    $message
                );
        }
    }
}
