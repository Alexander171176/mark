<?php

namespace App\Http\Controllers\Admin\Constructor\HomePage\Feature;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Constructor\HomePage\Feature\FeatureItemRequest;
use App\Models\Admin\Constructor\HomePage\Feature\FeatureItem;
use Illuminate\Http\RedirectResponse;

class FeatureItemController extends Controller
{
    /**
     * Обновление метаполей и inline-SVG (light/dark) у карточки фичи.
     */
    public function update(FeatureItemRequest $request, FeatureItem $item): RedirectResponse
    {
        $item->update($request->safe()->only([
            'feature_section_id',
            'title', 'subtitle', 'description',
            'image_light', 'image_dark', 'alt',
            'sort', 'activity',
        ]));

        return back()->with('success', 'Элемент фичи обновлён.');
    }
}
