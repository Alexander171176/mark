<?php

namespace App\Traits\Admin\System\Location;

use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

trait HasLocationSortingTrait
{
    /**
     * Обновление sort одной Location.
     */
    public function updateSort(
        UpdateSortEntityRequest $request,
        int $location
    ): RedirectResponse {
        $currentLocation =
            $this->baseQuery()
                ->findOrFail($location);

        $currentLocation->update([
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
     *
     * Поддерживает также изменение parent_id
     * для drag-and-drop иерархии.
     */
    public function updateSortBulk(
        Request $request
    ): RedirectResponse|JsonResponse {
        $table =
            (new $this->modelClass)
                ->getTable();

        $validated =
            $request->validate([
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

                'items.*.parent_id' => [
                    'nullable',
                    'integer',
                    "exists:{$table},id",
                ],
            ]);

        $items =
            $validated['items'];

        try {
            DB::transaction(
                function () use ($items) {
                    foreach (
                        $items as $item
                    ) {
                        $location =
                            $this->baseQuery()
                                ->findOrFail(
                                    $item['id']
                                );

                        /**
                         * Если parent_id передан —
                         * проверяем иерархию.
                         */
                        if (
                            array_key_exists(
                                'parent_id',
                                $item
                            )
                        ) {
                            $this->ensureValidParent(
                                $item['parent_id'],
                                $location->id
                            );
                        }

                        $data = [
                            'sort' =>
                                (int) $item['sort'],
                        ];

                        if (
                            array_key_exists(
                                'parent_id',
                                $item
                            )
                        ) {
                            $data['parent_id'] =
                                $item['parent_id'];
                        }

                        $location->update(
                            $data
                        );
                    }
                }
            );

            $message =
                "Сортировка {$this->entityLabel} обновлена.";

            return $request->expectsJson()
                ? response()->json([
                    'message' =>
                        $message,
                ])
                : back()->with(
                    'success',
                    $message
                );
        } catch (Throwable $e) {
            $message =
                $e->getMessage()
                    ?: "Ошибка обновления сортировки {$this->entityLabel}.";

            return $request->expectsJson()
                ? response()->json(
                    [
                        'message' =>
                            $message,
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
