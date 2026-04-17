<?php

namespace App\Http\Controllers\Admin\Constructor\HomePage\Quality;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Constructor\HomePage\Quality\QualityItemRequest;
use App\Models\Admin\Constructor\HomePage\Quality\QualityItem;
use Illuminate\Http\RedirectResponse;

class QualityItemController extends Controller
{
    /**
     * Обновление айтема (мета + svg как текст). Медиa здесь не грузим.
     */
    public function update(QualityItemRequest $request, QualityItem $item): RedirectResponse
    {
        $item->update(
            $request->safe()->except(['section_id']) // секцию не меняем из формы
        );

        return back()->with('success', 'Данные пункта Quality сохранены.');
    }
}
