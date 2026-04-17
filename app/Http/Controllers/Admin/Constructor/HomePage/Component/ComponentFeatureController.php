<?php

namespace App\Http\Controllers\Admin\Constructor\HomePage\Component;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Constructor\HomePage\Component\ComponentFeatureRequest;
use App\Models\Admin\Constructor\HomePage\Component\ComponentFeature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ComponentFeatureController extends Controller
{
    /**
     * Обновить feature (box_class, title, text, svg, sort, activity).
     */
    public function update(ComponentFeatureRequest $request, ComponentFeature $feature): RedirectResponse
    {
        $feature->update($request->validated());
        return back()->with('success', 'Feature обновлён.');
    }

    /**
     * Быстрое переключение активности.
     */
    public function updateActivity(Request $request, ComponentFeature $feature): RedirectResponse
    {
        $data = $request->validate(['activity' => ['required','boolean']]);
        $feature->update(['activity' => $data['activity']]);
        return back()->with('success', 'Активность feature обновлена.');
    }

    /**
     * Удаление feature (если потребуется).
     */
    public function destroy(ComponentFeature $feature): RedirectResponse
    {
        $feature->delete();
        return back()->with('success', 'Feature удалён.');
    }
}
