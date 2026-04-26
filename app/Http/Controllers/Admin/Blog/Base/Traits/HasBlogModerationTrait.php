<?php

namespace App\Http\Controllers\Admin\Blog\Base\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait HasBlogModerationTrait
{
    public function approve(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $user = auth()->user();

        if (!$user || !method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'moderation_status' => ['required', 'integer', 'in:0,1,2'],
            'moderation_note' => ['nullable', 'string', 'max:500'],
        ]);

        $model = $this->baseQuery()->findOrFail($id);

        $model->update([
            'moderation_status' => $validated['moderation_status'],
            'moderated_by' => $user->id,
            'moderated_at' => now(),
            'moderation_note' => $validated['moderation_note'] ?? null,
        ]);

        $message = "Статус модерации {$this->entityLabel} обновлён.";

        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : back()->with('success', $message);
    }
}
