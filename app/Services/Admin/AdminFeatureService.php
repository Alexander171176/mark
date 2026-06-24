<?php

namespace App\Services\Admin;

use App\Services\SiteSettings\AdminSettingsService;

class AdminFeatureService
{
    public function imageProcessorEnabled(): bool
    {
        return app(AdminSettingsService::class)->int(
                'imageProcessorEnabled',
                1
            ) === 1;
    }
}
