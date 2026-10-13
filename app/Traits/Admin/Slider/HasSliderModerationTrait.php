<?php

namespace App\Traits\Admin\Slider;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait HasSliderModerationTrait
{
    /**
     * Изменение статуса модерации слайдера (только для администратора).
     *
     * Сохраняет модератора, время проверки и комментарий,
     * как в HasMarketModerationTrait.
     */
    public function approve(Request $request, int $slider): RedirectResponse|JsonResponse
    {
        abort_unless($this->isAdmin(), 403);

        $validated = $request->validate([
            'moderation_status' => ['required', 'integer', 'in:0,1,2'],
            'moderation_note' => ['nullable', 'string', 'max:500'],
        ]);

        $model = $this->baseQuery()->findOrFail($slider);

        $model->update([
            'moderation_status' => (int) $validated['moderation_status'],
            'moderated_by' => $request->user()->id,
            'moderated_at' => now(),
            'moderation_note' => $validated['moderation_note'] ?? null,
        ]);

        $message = 'Статус модерации слайдера обновлён.';

        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : back()->with('success', $message);
    }
}
