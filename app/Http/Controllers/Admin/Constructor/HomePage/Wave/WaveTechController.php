<?php

namespace App\Http\Controllers\Admin\Constructor\HomePage\Wave;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Constructor\HomePage\Wave\WaveTechRequest;
use App\Models\Admin\Constructor\HomePage\Wave\WaveTech;
use Illuminate\Http\RedirectResponse;

class WaveTechController extends Controller
{
    /**
     * Обновление метаполей и/или SVG-файлов (light/dark) у tech-бейджа.
     */
    public function update(WaveTechRequest $request, WaveTech $tech): RedirectResponse
    {
        $tech->update($request->safe()->only([
            'wave_section_id','title','subtitle','description','alt','sort','activity',
        ]));

        try {
            if ($request->hasFile('image_light')) {
                optional($tech->getFirstMedia(WaveTech::MEDIA_COLLECTION_LIGHT))?->delete();

                $tech->addMediaFromRequest('image_light')
                    ->usingFileName($this->normalizeSvgName($tech, 'light'))
                    ->toMediaCollection(WaveTech::MEDIA_COLLECTION_LIGHT);
            }

            if ($request->hasFile('image_dark')) {
                optional($tech->getFirstMedia(WaveTech::MEDIA_COLLECTION_DARK))?->delete();

                $tech->addMediaFromRequest('image_dark')
                    ->usingFileName($this->normalizeSvgName($tech, 'dark'))
                    ->toMediaCollection(WaveTech::MEDIA_COLLECTION_DARK);
            }
        } catch (\Throwable $e) {
            report($e);
            return back()
                ->withErrors(['image_light' => 'SVG не принят: '.$e->getMessage()])
                ->withInput();
        }

        return back()->with('success', 'Технологический бейдж обновлён.');
    }

    /**
     * Опционально переименуем SVG (не обязательно, просто аккуратнее в файловой системе).
     */
    protected function normalizeSvgName(WaveTech $tech, string $mode): string
    {
        // ex: tech-15-light.svg
        $base = 'tech-'.$tech->getKey().'-'.$mode;
        return $base.'.svg';
    }
}
