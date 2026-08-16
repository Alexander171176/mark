<?php

namespace App\Traits\Admin\Blog;

use App\Http\Requests\Admin\System\UpdateLeftRequest;
use App\Http\Requests\Admin\System\UpdateMainRequest;
use App\Http\Requests\Admin\System\UpdateRightRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait HasBlogPlacementTrait
{
    /**
     * Обновление позиции left.
     */
    public function updateLeft(
        UpdateLeftRequest $request,
        int $id
    ): RedirectResponse {
        return $this->updatePlacementField(
            $id,
            'left',
            $request->validated('left')
        );
    }

    /**
     * Обновление позиции main.
     */
    public function updateMain(
        UpdateMainRequest $request,
        int $id
    ): RedirectResponse {
        return $this->updatePlacementField(
            $id,
            'main',
            $request->validated('main')
        );
    }

    /**
     * Обновление позиции right.
     */
    public function updateRight(
        UpdateRightRequest $request,
        int $id
    ): RedirectResponse {
        return $this->updatePlacementField(
            $id,
            'right',
            $request->validated('right')
        );
    }

    /**
     * Массовое обновление позиции left.
     */
    public function bulkUpdateLeft(
        Request $request
    ): RedirectResponse|JsonResponse {
        return $this->bulkUpdatePlacementField(
            $request,
            'left'
        );
    }

    /**
     * Массовое обновление позиции main.
     */
    public function bulkUpdateMain(
        Request $request
    ): RedirectResponse|JsonResponse {
        return $this->bulkUpdatePlacementField(
            $request,
            'main'
        );
    }

    /**
     * Массовое обновление позиции right.
     */
    public function bulkUpdateRight(
        Request $request
    ): RedirectResponse|JsonResponse {
        return $this->bulkUpdatePlacementField(
            $request,
            'right'
        );
    }

    /**
     * Обновление одного boolean-поля позиции.
     */
    protected function updatePlacementField(
        int $id,
        string $field,
        bool $value
    ): RedirectResponse {
        $model = $this->baseQuery()
            ->findOrFail($id);

        $model->update([
            $field => $value,
        ]);

        return back()->with(
            'success',
            "Позиция {$field} обновлена."
        );
    }

    /**
     * Массовое обновление boolean-поля позиции.
     */
    protected function bulkUpdatePlacementField(
        Request $request,
        string $field
    ): RedirectResponse|JsonResponse {
        $table = (new $this->modelClass)->getTable();

        /**
         * Валидация входных данных.
         */
        $validated = $request->validate([
            'ids' => [
                'required',
                'array',
            ],
            'ids.*' => [
                'required',
                'integer',
                "exists:{$table},id",
            ],
            $field => [
                'required',
                'boolean',
            ],
        ]);

        /**
         * Проверяем доступность записей
         * с учётом прав пользователя.
         */
        $allowedIds = $this->baseQuery()
            ->whereIn(
                'id',
                $validated['ids']
            )
            ->pluck('id')
            ->toArray();

        if (
            count($allowedIds)
            !== count($validated['ids'])
        ) {
            $message =
                'Часть элементов недоступна для обновления.';

            return $request->expectsJson()
                ? response()->json([
                    'success' => false,
                    'message' => $message,
                ], 403)
                : back()->with(
                    'error',
                    $message
                );
        }

        /**
         * Массовое обновление.
         */
        $this->modelClass::query()
            ->whereIn(
                'id',
                $allowedIds
            )
            ->update([
                $field => $validated[$field],
            ]);

        $message =
            "Позиция {$field} выбранных {$this->entityLabel} обновлена.";

        return $request->expectsJson()
            ? response()->json([
                'success' => true,
                'message' => $message,
            ])
            : back()->with(
                'success',
                $message
            );
    }
}
