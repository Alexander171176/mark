<?php

namespace App\Http\Controllers\Admin\Constructor\HomePage\Component;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Constructor\HomePage\Component\ComponentTileRequest;
use App\Models\Admin\Constructor\HomePage\Component\ComponentTile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ComponentTileController extends Controller
{
    /**
     * Обновление полей тайла + опциональная загрузка light/dark изображений.
     */
    public function update(ComponentTileRequest $request, ComponentTile $tile): RedirectResponse
    {
        // метаполя
        $tile->update($request->safe()->only([
            'tab_id','href','title','light_alt','dark_alt','sort','activity'
        ]));

        // файлы — по аналогии с HeroScreenshotController
        if ($request->hasFile('light')) {
            optional($tile->getFirstMedia(ComponentTile::MEDIA_COLLECTION_LIGHT))?->delete();
            $tile->addMediaFromRequest('light')
                ->toMediaCollection(ComponentTile::MEDIA_COLLECTION_LIGHT);
        }

        if ($request->hasFile('dark')) {
            optional($tile->getFirstMedia(ComponentTile::MEDIA_COLLECTION_DARK))?->delete();
            $tile->addMediaFromRequest('dark')
                ->toMediaCollection(ComponentTile::MEDIA_COLLECTION_DARK);
        }

        return back()->with('success', 'Тайл обновлён.');
    }

    /**
     * Быстрое переключение активности.
     */
    public function updateActivity(Request $request, ComponentTile $tile): RedirectResponse
    {
        $data = $request->validate(['activity' => ['required','boolean']]);
        $tile->update(['activity' => $data['activity']]);
        return back()->with('success', 'Активность тайла обновлена.');
    }

    /**
     * Отдельная загрузка светлой версии.
     */
    public function uploadLight(Request $request, ComponentTile $tile): RedirectResponse
    {
        $request->validate([
            'light' => ['required','file','image','max:5120'],
        ]);

        $tile->addMediaFromRequest('light')
            ->toMediaCollection(ComponentTile::MEDIA_COLLECTION_LIGHT);

        return back()->with('success', 'Светлая версия загружена.');
    }

    /**
     * Отдельная загрузка тёмной версии.
     */
    public function uploadDark(Request $request, ComponentTile $tile): RedirectResponse
    {
        $request->validate([
            'dark' => ['required','file','image','max:5120'],
        ]);

        $tile->addMediaFromRequest('dark')
            ->toMediaCollection(ComponentTile::MEDIA_COLLECTION_DARK);

        return back()->with('success', 'Тёмная версия загружена.');
    }

    /**
     * Очистка светлой версии.
     */
    public function clearLight(ComponentTile $tile): RedirectResponse
    {
        optional($tile->getFirstMedia(ComponentTile::MEDIA_COLLECTION_LIGHT))?->delete();
        return back()->with('success', 'Светлая версия удалена.');
    }

    /**
     * Очистка тёмной версии.
     */
    public function clearDark(ComponentTile $tile): RedirectResponse
    {
        optional($tile->getFirstMedia(ComponentTile::MEDIA_COLLECTION_DARK))?->delete();
        return back()->with('success', 'Тёмная версия удалена.');
    }

    /**
     * Удаление тайла (Spatie почистит медиа).
     */
    public function destroy(ComponentTile $tile): RedirectResponse
    {
        $tile->delete();
        return back()->with('success', 'Тайл удалён.');
    }
}
