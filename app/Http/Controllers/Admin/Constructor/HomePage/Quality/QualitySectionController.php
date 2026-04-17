<?php

namespace App\Http\Controllers\Admin\Constructor\HomePage\Quality;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Constructor\HomePage\Quality\QualitySectionRequest;
use App\Http\Resources\Admin\Constructor\HomePage\Quality\QualityItemResource;
use App\Http\Resources\Admin\Constructor\HomePage\Quality\QualitySectionResource;
use App\Models\Admin\Constructor\HomePage\Quality\QualitySection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class QualitySectionController extends Controller
{
    /**
     * Страница редактирования Quality (+переключение локали через ?locale=ru|en)
     */
    public function edit(Request $request, QualitySection $section): InertiaResponse|RedirectResponse
    {
        $requested = (string) $request->query('locale', '');
        if ($requested && $requested !== $section->locale) {
            $target = QualitySection::firstOrCreate(
                ['locale' => $requested],
                [
                    'title'    => null,
                    'subtitle' => null,
                    'sort'     => 0,
                    'is_dark'  => false,
                    'activity' => true,
                ]
            );

            if ($target->id !== $section->id) {
                return redirect()->route('admin.home-page.quality.sections.edit', [
                    'section' => $target->id,
                ]);
            }

            $section = $target;
        }

        $section->load(['media', 'items' => fn ($q) => $q->ordered()]);

        return Inertia::render('Admin/Constructor/HomePage/Quality/Edit', [
            'quality' => QualitySectionResource::make($section),
            'items'   => QualityItemResource::collection($section->items),
        ]);
    }

    /**
     * Обновление секции (поля + загрузка скриншотов light/dark via Spatie).
     */
    public function update(QualitySectionRequest $request, QualitySection $section): RedirectResponse
    {
        // Обычные поля
        $section->update(
            $request->safe()->except(['light', 'dark'])
        );

        if ($file = $request->file('light')) {
            $section->clearMediaCollection(QualitySection::MEDIA_COLLECTION_LIGHT);
            $section->addMedia($file)->toMediaCollection(QualitySection::MEDIA_COLLECTION_LIGHT);
        }

        if ($file = $request->file('dark')) {
            $section->clearMediaCollection(QualitySection::MEDIA_COLLECTION_DARK);
            $section->addMedia($file)->toMediaCollection(QualitySection::MEDIA_COLLECTION_DARK);
        }

        return back()->with('success', 'Данные секции Quality сохранены.');
    }
}
