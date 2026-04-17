<?php

namespace App\Http\Controllers\Admin\Constructor\HomePage\Reason;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Constructor\HomePage\Reason\ReasonItemRequest;
use App\Models\Admin\Constructor\HomePage\Reason\ReasonItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReasonItemController extends Controller
{
    /**
     * Обновление айтема Reason: поля + загрузка изображений (одиночное или light/dark).
     */
    public function update(ReasonItemRequest $request, ReasonItem $item): RedirectResponse
    {
        // 1) Собираем поля для БД (убираем то, чего в таблице нет)
        $payload = $request->safe()->except([
            'section_id', 'image', 'light', 'dark', // файлы
            'layout', 'light_alt', 'dark_alt',      // не-колонки
        ]);

        // 2) Обязательно положим align (из prepareForValidation уже "left"/"right")
        $payload['align'] = $request->input('align', $item->align);

        // Свернём ALT: image_alt либо light_alt/dark_alt, либо оставляем текущее
        $imageAlt = $request->input('image_alt');
        if ($imageAlt === null || $imageAlt === '') {
            $imageAlt = $request->input('light_alt')
                ?: $request->input('dark_alt')
                    ?: $item->image_alt;
        }
        $payload['image_alt'] = $imageAlt;

        // Обновляем простые поля
        $item->update($payload);

        // 3) Медиа: light
        if ($file = $request->file('light')) {
            $item->clearMediaCollection(ReasonItem::MEDIA_COLLECTION_LIGHT);
            $media = $item->addMedia($file)->toMediaCollection(ReasonItem::MEDIA_COLLECTION_LIGHT);
            if ($request->filled('light_alt')) {
                $media->setCustomProperty('alt', $request->input('light_alt'));
                $media->save();
            }
        }

        // Медиа: dark
        if ($file = $request->file('dark')) {
            $item->clearMediaCollection(ReasonItem::MEDIA_COLLECTION_DARK);
            $media = $item->addMedia($file)->toMediaCollection(ReasonItem::MEDIA_COLLECTION_DARK);
            if ($request->filled('dark_alt')) {
                $media->setCustomProperty('alt', $request->input('dark_alt'));
                $media->save();
            }
        }

        // Если когда-нибудь используешь одиночное изображение `image`
        if ($file = $request->file('image')) {
            $collection = defined(ReasonItem::class.'::MEDIA_COLLECTION_IMAGE')
                ? ReasonItem::MEDIA_COLLECTION_IMAGE
                : 'image';

            $item->clearMediaCollection($collection);
            $media = $item->addMedia($file)->toMediaCollection($collection);
            if (!empty($imageAlt)) {
                $media->setCustomProperty('alt', $imageAlt);
                $media->save();
            }
        }

        return back()->with('success', 'Данные блока Reason сохранены.');
    }

    /**
     * Быстрое переключение активности.
     */
    public function updateActivity(Request $request, ReasonItem $item): RedirectResponse
    {
        $data = $request->validate(['activity' => ['required', 'boolean']]);
        $item->update(['activity' => $data['activity']]);

        return back()->with('success', 'Активность блока Reason обновлена.');
    }
}
