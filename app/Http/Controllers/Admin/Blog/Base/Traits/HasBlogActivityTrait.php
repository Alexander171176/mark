<?php

namespace App\Http\Controllers\Admin\Blog\Base\Traits;

use App\Http\Requests\Admin\System\UpdateActivityRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait HasBlogActivityTrait
{
    /**
     * Обновление активности одной записи
     */
    public function updateActivity(UpdateActivityRequest $request, int $id): RedirectResponse
    {
        $model = $this->baseQuery()->findOrFail($id);

        $model->update([
            'activity' => $request->validated('activity'),
        ]);

        return back()->with('success', "Активность {$this->entityLabel} обновлена.");
    }

    /**
     * Массовое обновление активности
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $table = (new $this->modelClass)->getTable();

        // Валидация входных данных
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', "exists:{$table},id"],
            'activity' => ['required', 'boolean'],
        ]);

        // Проверка доступности записей (учёт прав пользователя)
        $allowedIds = $this->baseQuery()
            ->whereIn('id', $validated['ids'])
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($validated['ids'])) {
            return back()->with('error', "Часть {$this->entityLabel} недоступна.");
        }

        // Массовое обновление
        $this->modelClass::whereIn('id', $allowedIds)->update([
            'activity' => $validated['activity'],
        ]);

        $message = "Активность выбранных {$this->entityLabel} обновлена.";

        // Поддержка JSON и обычного ответа
        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : back()->with('success', $message);
    }
}
