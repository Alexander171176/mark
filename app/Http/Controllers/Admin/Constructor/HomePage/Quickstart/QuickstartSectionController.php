<?php

namespace App\Http\Controllers\Admin\Constructor\HomePage\Quickstart;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Constructor\HomePage\Quickstart\QuickstartSectionRequest;
use App\Http\Resources\Admin\Constructor\HomePage\Quickstart\QuickstartSectionResource;
use App\Models\Admin\Constructor\HomePage\Quickstart\QuickstartSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class QuickstartSectionController extends Controller
{
    /**
     * Страница редактирования Quickstart (Inertia) + переключение локали.
     */
    public function edit(Request $request, QuickstartSection $section): InertiaResponse|RedirectResponse
    {
        $requested = (string) $request->query('locale', '');
        if ($requested && $requested !== $section->locale) {
            $target = QuickstartSection::firstOrCreate(
                ['locale' => $requested],
                [
                    'title'     => null,
                    'subtitle'  => null,
                    'primary_label'  => null,
                    'primary_url'    => null,
                    'primary_icon'   => null,
                    'secondary_label' => null,
                    'secondary_url'   => null,
                    'secondary_icon'  => null,
                    'secondary_popover_enabled' => false,
                    'secondary_popover_title'   => null,
                    'secondary_popover_text'    => null,
                    'video_alt'     => null,
                    'video_caption' => null,
                    'video_options' => [
                        'controls' => true,
                        'muted'    => false,
                        'autoplay' => false,
                        'loop'     => false,
                    ],
                    'sort'     => 0,
                    'is_dark'  => false,
                    'activity' => true,
                ]
            );

            if ($target->id !== $section->id) {
                return redirect()->route('admin.home-page.quickstart.sections.edit', [
                    'section' => $target->id,
                ]);
            }

            $section = $target;
        }

        $section->load('media'); // <- чтобы resource дал poster_media/video_media

        return Inertia::render('Admin/Constructor/HomePage/Quickstart/Edit', [
            'quickstart' => QuickstartSectionResource::make($section),
        ]);
    }

    /**
     * Обновить данные секции + обработать Spatie Media (poster / video).
     */
    public function update(QuickstartSectionRequest $request, QuickstartSection $section): RedirectResponse
    {
        // Обновляем обычные поля
        $section->update(
            $request->safe()->except(['poster', 'video'])
        );

        // Poster
        if ($request->hasFile('poster')) {
            $section->clearMediaCollection(QuickstartSection::COLLECTION_POSTER);
            $section
                ->addMediaFromRequest('poster')
                ->toMediaCollection(QuickstartSection::COLLECTION_POSTER);
        }

        // Video
        if ($request->hasFile('video')) {
            $section->clearMediaCollection(QuickstartSection::COLLECTION_VIDEO);
            $section
                ->addMediaFromRequest('video')
                ->toMediaCollection(QuickstartSection::COLLECTION_VIDEO);
        }

        return back()->with('success', 'Данные секции Quickstart сохранены.');
    }
}
