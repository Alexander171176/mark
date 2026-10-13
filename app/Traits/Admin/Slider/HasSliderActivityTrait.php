<?php

namespace App\Traits\Admin\Slider;

use App\Http\Requests\Admin\System\UpdateActivityRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

trait HasSliderActivityTrait
{
    public function updateActivity(UpdateActivityRequest $request, int $id): RedirectResponse
    {
        $model = $this->baseQuery()->findOrFail($id);
        $this->applyActivity($model, (bool) $request->validated('activity'));
        return back()->with('success', 'Активность обновлена.');
    }

    public function bulkUpdateActivity(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:500'],
            'ids.*' => ['required', 'integer', 'distinct'],
            'activity' => ['required', 'boolean'],
        ]);
        $ids = array_map('intval', $data['ids']);

        DB::transaction(function () use ($ids, $data) {
            $models = $this->baseQuery()->whereKey($ids)->lockForUpdate()->get();
            if ($models->count() !== count($ids)) {
                throw ValidationException::withMessages(['ids' => 'Часть записей недоступна.']);
            }
            foreach ($models as $model) {
                $this->applyActivity($model, (bool) $data['activity']);
            }
        });

        return back()->with('success', 'Активность выбранных записей обновлена.');
    }

    /** Переопределить в контроллере при необходимости повторной модерации. */
    protected function applyActivity($model, bool $activity): void
    {
        $model->update(['activity' => $activity]);
    }
}
