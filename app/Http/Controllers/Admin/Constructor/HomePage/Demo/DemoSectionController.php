<?php

namespace App\Http\Controllers\Admin\Constructor\HomePage\Demo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Constructor\HomePage\Demo\DemoSectionRequest;
use App\Http\Resources\Admin\Constructor\HomePage\Demo\DemoGroupResource;
use App\Http\Resources\Admin\Constructor\HomePage\Demo\DemoSectionResource;
use App\Models\Admin\Constructor\HomePage\Demo\DemoSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DemoSectionController extends Controller
{
    /**
     * Страница редактирования Demo (Inertia).
     * Поведение локалей — как в Hero: можно переключать ?locale=ru|en,
     * при отсутствии — создаём секцию для нужной локали.
     */
    public function edit(Request $request, DemoSection $section): InertiaResponse|RedirectResponse
    {
        $requested = (string) $request->query('locale', '');
        if ($requested && $requested !== $section->locale) {
            $target = DemoSection::firstOrCreate(
                ['locale' => $requested],
                [
                    'title'               => null,
                    'subtitle'            => null,
                    'search_placeholder'  => null,
                    'sort'                => 0,
                    'is_dark'             => false,
                    'activity'            => true,
                ]
            );

            if ($target->id !== $section->id) {
                return redirect()->route('admin.home-page.demo.sections.edit', ['section' => $target->id]);
            }

            $section = $target;
        }

        // Грузим группы с айтемами по порядку
        $section->load([
            'groups' => fn($q) => $q->ordered()->with([
                'items' => fn($q2) => $q2->ordered()
            ]),
        ]);

        return Inertia::render('Admin/Constructor/HomePage/Demo/Edit', [
            'demo'   => DemoSectionResource::make($section),
            'groups' => DemoGroupResource::collection($section->groups),
        ]);
    }

    /**
     * Обновление полей секции.
     */
    public function update(DemoSectionRequest $request, DemoSection $section): RedirectResponse
    {
        $section->update($request->validated());
        return back()->with('success', 'Данные секции Demo сохранены.');
    }
}
