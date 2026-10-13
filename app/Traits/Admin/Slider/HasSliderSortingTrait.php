<?php

namespace App\Traits\Admin\Slider;

use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

trait HasSliderSortingTrait
{
    public function updateSort(UpdateSortEntityRequest $request, int $id): RedirectResponse
    {
        $model = $this->baseQuery()->findOrFail($id);
        $model->update(['sort' => $request->validated('sort')]);
        return back()->with('success', 'Сортировка обновлена.');
    }

    public function updateSortBulk(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1', 'max:500'],
            'items.*.id' => ['required', 'integer', 'distinct'],
            'items.*.sort' => ['required', 'integer', 'min:0', 'max:4294967295'],
        ]);
        $items = collect($data['items'])->keyBy('id');
        $ids = $items->keys()->map(fn ($id) => (int) $id)->all();

        DB::transaction(function () use ($ids, $items) {
            $models = $this->baseQuery()->whereKey($ids)->lockForUpdate()->get();
            if ($models->count() !== count($ids)) {
                throw ValidationException::withMessages(['items' => 'Часть записей недоступна.']);
            }
            foreach ($models as $model) {
                $model->update(['sort' => (int) $items->get($model->getKey())['sort']]);
            }
        });

        return back()->with('success', 'Сортировка выбранных записей обновлена.');
    }
}
