<?php

namespace App\Http\Controllers\Admin\Constructor\HomePage\Hero;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Constructor\HomePage\Hero\HeroSectionRequest;
use App\Http\Resources\Admin\Constructor\HomePage\Hero\HeroIconResource;
use App\Http\Resources\Admin\Constructor\HomePage\Hero\HeroScreenshotResource;
use App\Http\Resources\Admin\Constructor\HomePage\Hero\HeroSectionResource;
use App\Models\Admin\Constructor\HomePage\Hero\HeroSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class HeroSectionController extends Controller
{
    /**
     * Страница редактирования Hero (Inertia).
     */
    public function edit(Request $request, HeroSection $section): InertiaResponse|RedirectResponse
    {
        $requested = (string) $request->query('locale', '');
        if ($requested && $requested !== $section->locale) {
            $target = HeroSection::firstOrCreate(
                ['locale' => $requested],
                [
                    'title' => null,
                    'subtitle' => null,
                    'badge_text' => null,
                    'description' => null,
                    'primary_btn_text' => null,
                    'primary_btn_url' => null,
                    'primary_btn_target' => '_self',
                    'secondary_btn_text' => null,
                    'secondary_btn_url' => null,
                    'secondary_btn_target' => '_self',
                    'is_dark' => false,
                    'activity' => true,
                ]
            );

            if ($target->id !== $section->id) {
                return redirect()->route('admin.home-page.hero.sections.edit',
                    ['section' => $target->id]);
            }

            $section = $target;
        }

        $section->load([
            'icons' => fn ($q) => $q->ordered(),
            'screenshots' => fn ($q) => $q->ordered(),
        ]);

        return Inertia::render('Admin/Constructor/HomePage/Hero/Edit', [
            'hero'        => HeroSectionResource::make($section),
            'icons'       => HeroIconResource::collection($section->icons),
            'screenshots' => HeroScreenshotResource::collection($section->screenshots),
            'targets'     => ['_self', '_blank'],
        ]);
    }

    /**
     * Обновление полей секции (без создания).
     */
    public function update(HeroSectionRequest $request, HeroSection $section): RedirectResponse
    {
        $section->update($request->validated());
        return back()->with('success', 'Данные секции Hero сохранены.');
    }
}
