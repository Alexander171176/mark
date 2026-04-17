<?php

namespace App\Http\Controllers\Admin\Constructor\HomePage\Hero;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Constructor\HomePage\Hero\HeroIconRequest;
use App\Models\Admin\Constructor\HomePage\Hero\HeroIcon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HeroIconController extends Controller
{
    /**
     * Обновить иконку (label, svg, sort, activity).
     */
    public function update(HeroIconRequest $request, HeroIcon $icon): RedirectResponse
    {
        $icon->update($request->validated());
        return back()->with('success', 'Иконка обновлена.');
    }

    /**
     * Быстрое включение/выключение активности иконки.
     */
    public function updateActivity(Request $request, HeroIcon $icon): RedirectResponse
    {
        $data = $request->validate(['activity' => ['required','boolean']]);
        $icon->update(['activity' => $data['activity']]);

        return back()->with('success', 'Активность иконки обновлена.');
    }

    /**
     * Удаление иконки (если понадобится).
     * По условиям проекта create/store не используем, но delete может пригодиться.
     */
    public function destroy(HeroIcon $icon): RedirectResponse
    {
        $icon->delete();
        return back()->with('success', 'Иконка удалена.');
    }
}
