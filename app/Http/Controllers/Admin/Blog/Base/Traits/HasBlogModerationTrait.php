<?php

namespace App\Http\Controllers\Admin\Blog\Base\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait HasBlogModerationTrait
{
    /**
     * Изменение статуса модерации (только для администратора)
     */
    public function approve(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $user = auth()->user();

        // Проверка прав (только admin)
        if (!$user || !method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            abort(403);
        }

        // Валидация данных
        $validated = $request->validate([
            'moderation_status' => ['required', 'integer', 'in:0,1,2'],
            'moderation_note' => ['nullable', 'string', 'max:500'],
        ]);

        // Обновление записи
        $model = $this->baseQuery()->findOrFail($id);

        $model->update([
            'moderation_status' => $validated['moderation_status'],
            'moderated_by' => $user->id,
            'moderated_at' => now(),
            'moderation_note' => $validated['moderation_note'] ?? null,
        ]);

        $message = "Статус модерации {$this->entityLabel} обновлён.";

        // Поддержка JSON и обычного ответа
        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : back()->with('success', $message);
    }
}
