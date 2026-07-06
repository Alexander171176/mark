<?php

namespace App\Http\Controllers\Admin\Analytics\AnalyticsCleanup;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Analytics\AnalyticsCleanup\AnalyticsCleanupRequest;
use App\Models\Admin\Analytics\AnalyticsVisitorLog\AnalyticsVisitorLog;
use Illuminate\Http\RedirectResponse;

class AnalyticsCleanupController extends Controller
{
    /**
     * Очистка аналитики за выбранный период.
     */
    public function destroy(AnalyticsCleanupRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $deleted = AnalyticsVisitorLog::query()
            ->whereDate('visited_at', '>=', $data['date_from'])
            ->whereDate('visited_at', '<=', $data['date_to'])
            ->delete();

        return redirect()
            ->back()
            ->with('success', "Удалено записей аналитики: {$deleted}");
    }
}
