<?php

namespace App\Http\Controllers\Admin\Blog\Base\Traits;

use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

trait HasBlogSortingTrait
{
    public function updateSort(UpdateSortEntityRequest $request, int $id): RedirectResponse
    {
        $model = $this->baseQuery()->findOrFail($id);

        $model->update([
            'sort' => $request->validated('sort'),
        ]);

        return back()->with('success', "Сортировка {$this->entityLabel} обновлена.");
    }

    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $table = (new $this->modelClass)->getTable();

        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer', "exists:{$table},id"],
            'items.*.sort' => ['required', 'integer', 'min:0'],
        ]);

        $items = $validated['items'];
        $ids = array_column($items, 'id');

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            $message = "Часть {$this->entityLabel} недоступна для сортировки.";

            return $request->expectsJson()
                ? response()->json(['message' => $message], 403)
                : back()->with('error', $message);
        }

        try {
            DB::transaction(function () use ($items) {
                foreach ($items as $item) {
                    $this->modelClass::whereKey($item['id'])->update([
                        'sort' => (int) $item['sort'],
                    ]);
                }
            });

            $message = "Сортировка {$this->entityLabel} обновлена.";

            return $request->expectsJson()
                ? response()->json(['message' => $message])
                : back()->with('success', $message);
        } catch (Throwable) {
            $message = "Ошибка обновления сортировки {$this->entityLabel}.";

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }
}
