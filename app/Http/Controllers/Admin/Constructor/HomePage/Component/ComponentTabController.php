<?php

namespace App\Http\Controllers\Admin\Constructor\HomePage\Component;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Constructor\HomePage\Component\ComponentTabRequest;
use App\Models\Admin\Constructor\HomePage\Component\ComponentTab;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ComponentTabController extends Controller
{
    /**
     * Обновить таб (slug, label, sort, activity).
     */
    public function update(ComponentTabRequest $request, ComponentTab $tab): RedirectResponse
    {
        $tab->update($request->validated());
        return back()->with('success', 'Вкладка обновлена.');
    }

    /**
     * Быстрое переключение активности.
     */
    public function updateActivity(Request $request, ComponentTab $tab): RedirectResponse
    {
        $data = $request->validate(['activity' => ['required','boolean']]);
        $tab->update(['activity' => $data['activity']]);
        return back()->with('success', 'Активность вкладки обновлена.');
    }

    /**
     * Удаление вкладки (при необходимости).
     */
    public function destroy(ComponentTab $tab): RedirectResponse
    {
        $tab->delete();
        return back()->with('success', 'Вкладка удалена.');
    }
}
