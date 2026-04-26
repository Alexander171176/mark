<?php

namespace App\Http\Controllers\Admin\Blog\Base\Traits;

use App\Http\Requests\Admin\System\UpdateLeftRequest;
use App\Http\Requests\Admin\System\UpdateMainRequest;
use App\Http\Requests\Admin\System\UpdateRightRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait HasBlogPlacementTrait
{
    /**
     * Обновление позиции left
     */
    public function updateLeft(UpdateLeftRequest $request, int $id): RedirectResponse
    {
        return $this->updatePlacementField($id, 'left', $request->validated('left'));
    }

    /**
     * Обновление позиции main
     */
    public function updateMain(UpdateMainRequest $request, int $id): RedirectResponse
    {
        return $this->updatePlacementField($id, 'main', $request->validated('main'));
    }

    /**
     * Обновление позиции right
     */
    public function updateRight(UpdateRightRequest $request, int $id): RedirectResponse
    {
        return $this->updatePlacementField($id, 'right', $request->validated('right'));
    }

    /**
     * Массовое обновление позиции left
     */
    public function bulkUpdateLeft(Request $request): JsonResponse
    {
        return $this->bulkUpdatePlacementField($request, 'left');
    }

    /**
     * Массовое обновление позиции main
     */
    public function bulkUpdateMain(Request $request): JsonResponse
    {
        return $this->bulkUpdatePlacementField($request, 'main');
    }

    /**
     * Массовое обновление позиции right
     */
    public function bulkUpdateRight(Request $request): JsonResponse
    {
        return $this->bulkUpdatePlacementField($request, 'right');
    }

    /**
     * Обновление одного boolean-поля позиции
     */
    protected function updatePlacementField(int $id, string $field, bool $value): RedirectResponse
    {
        $model = $this->baseQuery()->findOrFail($id);

        $model->update([
            $field => $value,
        ]);

        return back()->with('success', "Позиция {$field} обновлена.");
    }

    /**
     * Массовое обновление boolean-поля позиции
     */
    protected function bulkUpdatePlacementField(Request $request, string $field): JsonResponse
    {
        $table = (new $this->modelClass)->getTable();

        // Валидация входных данных
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', "exists:{$table},id"],
            $field => ['required', 'boolean'],
        ]);

        // Проверка доступности записей
        $allowedIds = $this->baseQuery()
            ->whereIn('id', $validated['ids'])
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($validated['ids'])) {
            return response()->json([
                'success' => false,
                'message' => 'Часть элементов недоступна для обновления.',
            ], 403);
        }

        // Массовое обновление
        $this->modelClass::whereIn('id', $allowedIds)->update([
            $field => $validated[$field],
        ]);

        return response()->json([
            'success' => true,
            'message' => "Позиция {$field} обновлена.",
        ]);
    }
}
