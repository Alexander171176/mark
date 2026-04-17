<?php

namespace App\Http\Controllers\Admin\Constructor\HomePage\Hero;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Constructor\HomePage\Hero\HeroScreenshotRequest;
use App\Models\Admin\Constructor\HomePage\Hero\HeroScreenshot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HeroScreenshotController extends Controller
{
    /**
     * Обновление текстовых полей (alt, sort, activity).
     */
    public function update(HeroScreenshotRequest $request, HeroScreenshot $screenshot): RedirectResponse
    {
        // метаполя
        $screenshot->update($request->safe()->only(['alt','sort','activity']));

        // файлы опционально
        if ($request->hasFile('light')) {
            optional($screenshot->getFirstMedia(HeroScreenshot::MEDIA_COLLECTION_LIGHT))?->delete();
            $screenshot->addMediaFromRequest('light')
                ->toMediaCollection(HeroScreenshot::MEDIA_COLLECTION_LIGHT);
        }

        if ($request->hasFile('dark')) {
            optional($screenshot->getFirstMedia(HeroScreenshot::MEDIA_COLLECTION_DARK))?->delete();
            $screenshot->addMediaFromRequest('dark')
                ->toMediaCollection(HeroScreenshot::MEDIA_COLLECTION_DARK);
        }

        return back()->with('success', 'Скриншот обновлён.');
    }

    /**
     * Загрузка светлой версии изображения (Spatie).
     */
    public function uploadLight(Request $request, HeroScreenshot $screenshot): RedirectResponse
    {
        $request->validate([
            'light' => ['required','file','image','max:5120'], // до ~5 МБ
        ]);

        $screenshot->addMediaFromRequest('light')
            ->toMediaCollection(HeroScreenshot::MEDIA_COLLECTION_LIGHT);

        return back()->with('success', 'Светлая версия загружена.');
    }

    /**
     * Загрузка тёмной версии изображения (Spatie).
     */
    public function uploadDark(Request $request, HeroScreenshot $screenshot): RedirectResponse
    {
        $request->validate([
            'dark' => ['required','file','image','max:5120'],
        ]);

        $screenshot->addMediaFromRequest('dark')
            ->toMediaCollection(HeroScreenshot::MEDIA_COLLECTION_DARK);

        return back()->with('success', 'Тёмная версия загружена.');
    }

    /**
     * Очистить светлую версию.
     */
    public function clearLight(HeroScreenshot $screenshot): RedirectResponse
    {
        optional($screenshot->getFirstMedia(HeroScreenshot::MEDIA_COLLECTION_LIGHT))?->delete();
        return back()->with('success', 'Светлая версия удалена.');
    }

    /**
     * Очистить тёмную версию.
     */
    public function clearDark(HeroScreenshot $screenshot): RedirectResponse
    {
        optional($screenshot->getFirstMedia(HeroScreenshot::MEDIA_COLLECTION_DARK))?->delete();
        return back()->with('success', 'Тёмная версия удалена.');
    }

    /**
     * Удалить запись скриншота (без медиа — Spatie сам подчистит).
     */
    public function destroy(HeroScreenshot $screenshot): RedirectResponse
    {
        $screenshot->delete();
        return back()->with('success', 'Скриншот удалён.');
    }
}
