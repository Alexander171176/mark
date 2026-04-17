<?php

namespace App\Http\Controllers\Admin\Constructor\HomePage\Developer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Constructor\HomePage\Developer\DeveloperItemRequest;
use App\Models\Admin\Constructor\HomePage\Developer\DeveloperItem;
use Illuminate\Http\RedirectResponse;

class DeveloperItemController extends Controller
{
    /**
     * Обновление метаполей и inline-SVG (light/dark) у элемента Developer.
     */
    public function update(DeveloperItemRequest $request, DeveloperItem $item): RedirectResponse
    {
        $item->update($request->safe()->only([
            'developer_section_id',
            'title', 'subtitle', 'description',
            'image_light', 'image_dark', 'alt',
            'sort', 'activity',
        ]));

        return back()->with('success', 'Элемент секции Developer обновлён.');
    }
}
