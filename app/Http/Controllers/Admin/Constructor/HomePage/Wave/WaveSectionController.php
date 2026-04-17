<?php

namespace App\Http\Controllers\Admin\Constructor\HomePage\Wave;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Constructor\HomePage\Wave\WaveSectionRequest;
use App\Http\Resources\Admin\Constructor\HomePage\Wave\WaveSectionResource;
use App\Http\Resources\Admin\Constructor\HomePage\Wave\WaveTechResource;
use App\Models\Admin\Constructor\HomePage\Wave\WaveSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class WaveSectionController extends Controller
{
    /**
     * Страница редактирования Wave (Inertia).
     */
    public function edit(Request $request, WaveSection $section): InertiaResponse|RedirectResponse
    {
        // Переключатель локали (как в Hero)
        $requested = (string) $request->query('locale', '');
        if ($requested && $requested !== $section->locale) {
            $target = WaveSection::firstOrCreate(
                ['locale' => $requested],
                [
                    'title'      => null,
                    'subtitle'   => null,
                    'left_text'  => null,
                    'right_text' => null,
                    'sort'       => 0,
                    'is_dark'    => false,
                    'activity'   => true,
                ]
            );

            // Перейдём на корректный id (чтобы URL соответствовал записи)
            if ($target->id !== $section->id) {
                return redirect()
                    ->route('admin.home-page.wave.sections.edit', ['section' => $target->id]);
            }

            $section = $target;
        }

        // Подтянем элементы + их media для ресурсов
        $section->load([
            'teches' => fn ($q) => $q->ordered()->with('media'),
        ]);

        return Inertia::render('Admin/Constructor/HomePage/Wave/Edit', [
            'wave'   => WaveSectionResource::make($section),
            'teches' => WaveTechResource::collection($section->teches),
        ]);
    }

    /**
     * Обновление полей секции (без создания).
     */
    public function update(WaveSectionRequest $request, WaveSection $section): RedirectResponse
    {
        $section->update($request->validated());
        return back()->with('success', 'Данные секции Wave сохранены.');
    }
}
