<?php

namespace App\Http\Controllers\Admin\Constructor\HomePage\Demo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Constructor\HomePage\Demo\DemoGroupRequest;
use App\Models\Admin\Constructor\HomePage\Demo\DemoGroup;
use Illuminate\Http\RedirectResponse;

class DemoGroupController extends Controller
{
    /**
     * Обновить группу: заголовок/описание/slug/alt/порядок/активность + inline SVG (как текст).
     * SVG НЕ через Spatie.
     */
    public function update(DemoGroupRequest $request, DemoGroup $group): RedirectResponse
    {
        $group->update($request->validated());
        return back()->with('success', 'Группа Demo обновлена.');
    }
}
