<?php

namespace App\Http\Controllers\Admin\Constructor\HomePage\Reason;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Constructor\HomePage\Reason\ReasonSectionRequest;
use App\Http\Resources\Admin\Constructor\HomePage\Reason\ReasonItemResource;
use App\Http\Resources\Admin\Constructor\HomePage\Reason\ReasonSectionResource;
use App\Models\Admin\Constructor\HomePage\Reason\ReasonSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ReasonSectionController extends Controller
{
    /**
     * Экран редактирования Reason (+переключение локали через ?locale=ru|en).
     */
    public function edit(Request $request, ReasonSection $section): InertiaResponse|RedirectResponse
    {
        $requested = (string) $request->query('locale', '');
        if ($requested && $requested !== $section->locale) {
            $target = ReasonSection::firstOrCreate(
                ['locale' => $requested],
                [
                    'subtitle'       => null,
                    'title'          => null,
                    'cta_title'      => null,
                    'cta_btn_text'   => null,
                    'cta_btn_url'    => null,
                    'cta_btn_target' => '_self',
                    'sort'           => 0,
                    'activity'       => true,
                ]
            );

            if ($target->id !== $section->id) {
                return redirect()->route('admin.home-page.reason.sections.edit', [
                    'section' => $target->id,
                ]);
            }

            $section = $target;
        }

        // Грузим айтемы c медиа (картинки светлая/тёмная/одиночная — как настроено в модели)
        $section->load([
            'items' => fn($q) => $q->ordered()->with('media'),
        ]);

        return Inertia::render('Admin/Constructor/HomePage/Reason/Edit', [
            'reason' => ReasonSectionResource::make($section),
            'items'  => ReasonItemResource::collection($section->items),
        ]);
    }

    /**
     * Обновление секции (чисто поля, без файлов).
     */
    public function update(ReasonSectionRequest $request, ReasonSection $section): RedirectResponse
    {
        $section->update($request->validated());

        return back()->with('success', 'Данные секции Reason сохранены.');
    }
}
