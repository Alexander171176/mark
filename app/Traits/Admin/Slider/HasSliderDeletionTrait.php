<?php

namespace App\Traits\Admin\Slider;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

trait HasSliderDeletionTrait
{
    /**
     * Массовое удаление слайдеров.
     *
     * Использует существующий SliderService,
     * как и одиночное удаление.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:500'],
            'ids.*' => ['required', 'integer', 'distinct'],
        ]);

        $ids = array_map('intval', $validated['ids']);

        $sliders = $this->baseQuery()
            ->whereKey($ids)
            ->get();

        if ($sliders->count() !== count($ids)) {
            throw ValidationException::withMessages([
                'ids' => 'Часть слайдеров недоступна для удаления.',
            ]);
        }

        $deleted = 0;
        $failed = 0;

        foreach ($sliders as $slider) {
            try {
                $this->service->delete($slider);
                $deleted++;
            } catch (Throwable $exception) {
                $failed++;

                Log::error('Ошибка удаления слайдера.', [
                    'slider_id' => $slider->id,
                    'exception' => $exception,
                ]);
            }
        }

        if ($failed > 0) {
            return back()->with(
                'error',
                "Удалено слайдеров: {$deleted}. Не удалось удалить: {$failed}."
            );
        }

        return back()->with(
            'success',
            "Успешно удалено слайдеров: {$deleted}."
        );
    }
}
