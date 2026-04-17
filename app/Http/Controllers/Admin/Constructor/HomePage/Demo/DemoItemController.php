<?php

namespace App\Http\Controllers\Admin\Constructor\HomePage\Demo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Constructor\HomePage\Demo\DemoItemRequest;
use App\Models\Admin\Constructor\HomePage\Demo\DemoItem;
use Illuminate\Http\RedirectResponse;

class DemoItemController extends Controller
{
    /**
     * Обновить айтем: мета + загрузка картинок (light/dark) через Spatie.
     */
    public function update(DemoItemRequest $request, DemoItem $item): RedirectResponse
    {
        // Метаданные
        $item->update($request->safe()->only([
            'group_id', 'href', 'title', 'category', 'alt', 'sort', 'activity'
        ]));

        // Файлы (как в HeroScreenshotController::update)
        if ($request->hasFile('light')) {
            optional($item->getFirstMedia(DemoItem::MEDIA_COLLECTION_LIGHT))?->delete();
            $item->addMediaFromRequest('light')
                ->toMediaCollection(DemoItem::MEDIA_COLLECTION_LIGHT);
        }

        if ($request->hasFile('dark')) {
            optional($item->getFirstMedia(DemoItem::MEDIA_COLLECTION_DARK))?->delete();
            $item->addMediaFromRequest('dark')
                ->toMediaCollection(DemoItem::MEDIA_COLLECTION_DARK);
        }

        return back()->with('success', 'Демо-элемент обновлён.');
    }
}
