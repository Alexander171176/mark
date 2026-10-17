<?php

namespace App\Traits\Admin\Slider;

use App\Models\Admin\Slider\Slider\Slider;
use App\Models\Admin\Slider\SliderSlide\SliderSlide;
use Illuminate\Validation\ValidationException;

/** Управляет только размещениями глобальных слайдов, не изменяя сами слайды. */
trait HasSliderSlidesTrait
{
    protected function syncSliderSlides(Slider $slider, array $slides): bool
    {
        $ids = [];
        $sync = [];
        $mainCount = 0;

        foreach ($slides as $index => $item) {
            if (! is_array($item)) {
                throw ValidationException::withMessages(["slides.$index" => 'Неверный формат слайда.']);
            }

            $id = filter_var($item['id'] ?? null, FILTER_VALIDATE_INT);
            if ($id === false || $id < 1) {
                throw ValidationException::withMessages(["slides.$index.id" => 'Выберите существующий слайд.']);
            }
            if (isset($ids[$id])) {
                throw ValidationException::withMessages(["slides.$index.id" => 'Слайд указан повторно.']);
            }
            $ids[$id] = true;

            $main = (bool) ($item['is_main'] ?? false);
            $mainCount += (int) $main;
            if ($mainCount > 1) {
                throw ValidationException::withMessages(['slides' => 'Главным может быть только один слайд.']);
            }

            $sync[$id] = [
                'sort' => (int) ($item['sort'] ?? $index),
                'activity' => (bool) ($item['activity'] ?? true),
                'is_main' => $main,
            ];
        }

        if ($ids !== []) {
            $found = SliderSlide::query()->whereKey(array_keys($ids))->count();
            if ($found !== count($ids)) {
                throw ValidationException::withMessages(['slides' => 'Некоторые слайды не найдены.']);
            }
        }

        $current = $slider->slides()->get()->mapWithKeys(static function (SliderSlide $slide): array {
            return [(int) $slide->getKey() => [
                'sort' => (int) $slide->pivot->sort,
                'activity' => (bool) $slide->pivot->activity,
                'is_main' => (bool) $slide->pivot->is_main,
            ]];
        })->all();

        ksort($current);
        $comparison = $sync;
        ksort($comparison);
        if ($current === $comparison) {
            return false;
        }

        $slider->slides()->sync($sync);
        return true;
    }
}
