<?php

namespace App\Traits\Admin\Slider;

use App\Models\Admin\Slider\SliderSlide\SliderSlide;
use App\Models\Admin\Slider\SliderSlideAction\SliderSlideAction;
use App\Models\Admin\Slider\SliderSlideAdvantage\SliderSlideAdvantage;
use Illuminate\Validation\ValidationException;

/** Синхронизация только размещений; справочники действий и преимуществ не изменяются. */
trait HasSliderRelationsTrait
{
    /**
     * @throws ValidationException
     */
    protected function syncSlideActions(SliderSlide $slide, array $actions): void
    {
        $this->assertRelationItems($actions, SliderSlideAction::class, 'actions');

        if (collect($actions)->filter(fn ($item) => filter_var($item['is_primary'] ?? false, FILTER_VALIDATE_BOOLEAN))->count() > 1) {
            throw ValidationException::withMessages([
                'actions' => 'Для слайда допускается только одно основное действие.',
            ]);
        }

        $sync = [];
        foreach ($actions as $index => $item) {
            $sync[(int) $item['id']] = [
                'sort' => (int) ($item['sort'] ?? $index),
                'activity' => (bool) ($item['activity'] ?? true),
                'is_primary' => (bool) ($item['is_primary'] ?? false),
            ];
        }
        $slide->actions()->sync($sync);
    }

    /**
     * @throws ValidationException
     */
    protected function syncSlideAdvantages(SliderSlide $slide, array $advantages): void
    {
        $this->assertRelationItems($advantages, SliderSlideAdvantage::class, 'advantages');

        $sync = [];
        foreach ($advantages as $index => $item) {
            $sync[(int) $item['id']] = [
                'sort' => (int) ($item['sort'] ?? $index),
                'activity' => (bool) ($item['activity'] ?? true),
            ];
        }
        $slide->advantages()->sync($sync);
    }

    /**
     * @throws ValidationException
     */
    private function assertRelationItems(array $items, string $modelClass, string $field): void
    {
        $ids = [];
        foreach ($items as $index => $item) {
            if (! is_array($item) || ! isset($item['id']) || filter_var($item['id'], FILTER_VALIDATE_INT) === false || (int) $item['id'] < 1) {
                throw ValidationException::withMessages([
                    "{$field}.{$index}.id" => 'Укажите корректный идентификатор.',
                ]);
            }
            $ids[] = (int) $item['id'];
        }

        if (count($ids) !== count(array_unique($ids))) {
            throw ValidationException::withMessages([$field => 'Повторяющиеся элементы недопустимы.']);
        }

        if ($ids && $modelClass::query()->whereKey($ids)->count() !== count($ids)) {
            throw ValidationException::withMessages([$field => 'Некоторые элементы не найдены.']);
        }
    }
}
