<?php

namespace App\Http\Controllers\Admin\Constructor\HomePage\Feature;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Constructor\HomePage\Feature\FeatureSectionRequest;
use App\Http\Resources\Admin\Constructor\HomePage\Feature\FeatureItemResource;
use App\Http\Resources\Admin\Constructor\HomePage\Feature\FeatureSectionResource;
use App\Models\Admin\Constructor\HomePage\Feature\FeatureSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class FeatureSectionController extends Controller
{
    /**
     * Страница редактирования Feature (Inertia).
     */
    public function edit(Request $request, FeatureSection $section): InertiaResponse|RedirectResponse
    {
        // Переключение локали (как в Wave/Hero)
        $requested = (string) $request->query('locale', '');
        if ($requested && $requested !== $section->locale) {
            $target = FeatureSection::firstOrCreate(
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
                return redirect()->route('admin.home-page.feature.sections.edit',
                    ['section' => $target->id]);
            }

            $section = $target;
        }

        // Элементы фич, упорядоченные
        $section->load([
            'items' => fn ($q) => $q->ordered(),
        ]);

        return Inertia::render('Admin/Constructor/HomePage/Feature/Edit', [
            'feature' => FeatureSectionResource::make($section),
            'items'   => FeatureItemResource::collection($section->items),
        ]);
    }

    /**
     * Обновить поля секции.
     */
    public function update(FeatureSectionRequest $request, FeatureSection $section): RedirectResponse
    {
        $section->update($request->validated());

        return back()->with('success', 'Данные секции Feature сохранены.');
    }
}
