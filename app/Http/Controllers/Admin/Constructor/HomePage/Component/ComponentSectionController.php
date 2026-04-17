<?php

namespace App\Http\Controllers\Admin\Constructor\HomePage\Component;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Constructor\HomePage\Component\ComponentSectionRequest;
use App\Http\Resources\Admin\Constructor\HomePage\Component\ComponentFeatureResource;
use App\Http\Resources\Admin\Constructor\HomePage\Component\ComponentSectionResource;
use App\Http\Resources\Admin\Constructor\HomePage\Component\ComponentTabResource;
use App\Models\Admin\Constructor\HomePage\Component\ComponentSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ComponentSectionController extends Controller
{
    /**
     * Страница редактирования секции Component (Inertia) с переключением локали.
     */
    public function edit(Request $request, ComponentSection $section): InertiaResponse|RedirectResponse
    {
        $requested = (string) $request->query('locale', '');
        if ($requested && $requested !== $section->locale) {
            $target = ComponentSection::firstOrCreate(
                ['locale' => $requested],
                [
                    'subtitle' => null,
                    'title'    => null,
                    'cta_text' => 'All Components',
                    'cta_url'  => '/blocks',
                    'sort'     => 0,
                    'activity' => true,
                ]
            );

            if ($target->id !== $section->id) {
                return redirect()->route('admin.home-page.component.sections.edit', [
                    'section' => $target->id,
                ]);
            }

            $section = $target;
        }

        $section->load([
            'features'        => fn ($q) => $q->ordered(),
            'tabs'            => fn ($q) => $q->ordered(),
            'tabs.tiles'      => fn ($q) => $q->ordered(), // вложенно подгружаем тайлы
        ]);

        return Inertia::render('Admin/Constructor/HomePage/Component/Edit', [
            'component' => ComponentSectionResource::make($section),
            'features'  => ComponentFeatureResource::collection($section->features),
            'tabs'      => ComponentTabResource::collection($section->tabs),
        ]);
    }

    /**
     * Обновление секции Component.
     */
    public function update(ComponentSectionRequest $request, ComponentSection $section): RedirectResponse
    {
        $section->update($request->validated());
        return back()->with('success', 'Данные секции Component сохранены.');
    }
}
