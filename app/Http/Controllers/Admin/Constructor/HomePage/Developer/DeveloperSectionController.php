<?php

namespace App\Http\Controllers\Admin\Constructor\HomePage\Developer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Constructor\HomePage\Developer\DeveloperSectionRequest;
use App\Http\Resources\Admin\Constructor\HomePage\Developer\DeveloperItemResource;
use App\Http\Resources\Admin\Constructor\HomePage\Developer\DeveloperSectionResource;
use App\Models\Admin\Constructor\HomePage\Developer\DeveloperSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DeveloperSectionController extends Controller
{
    /**
     * Страница редактирования Developer (Inertia).
     */
    public function edit(Request $request, DeveloperSection $section): InertiaResponse|RedirectResponse
    {
        // Переключение локали (как в Feature/Wave/Hero)
        $requested = (string) $request->query('locale', '');
        if ($requested && $requested !== $section->locale) {
            $target = DeveloperSection::firstOrCreate(
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
                return redirect()->route('admin.home-page.developer.sections.edit', [
                    'section' => $target->id,
                ]);
            }

            $section = $target;
        }

        // Подтянуть элементы (items) в нужном порядке
        $section->load([
            'items' => fn ($q) => $q->ordered(),
        ]);

        return Inertia::render('Admin/Constructor/HomePage/Developer/Edit', [
            'developer' => DeveloperSectionResource::make($section),
            'items'     => DeveloperItemResource::collection($section->items),
        ]);
    }

    /**
     * Обновить поля секции.
     */
    public function update(DeveloperSectionRequest $request, DeveloperSection $section): RedirectResponse
    {
        $section->update($request->validated());

        return back()->with('success', 'Данные секции Developer сохранены.');
    }
}
