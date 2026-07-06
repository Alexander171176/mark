<?php

namespace App\Http\Controllers\Admin\Analytics\AnalyticsImport;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

class AnalyticsImportController extends Controller
{
    /**
     * Ручной импорт JSONL файлов аналитики в SQLite.
     */
    public function __invoke(): RedirectResponse
    {
        Artisan::call('analytics:import-visitor-logs');

        return redirect()
            ->back()
            ->with('success', 'Импорт аналитики выполнен.');
    }
}
