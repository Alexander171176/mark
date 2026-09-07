<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\SiteSettings\PublicSettingsService;

abstract class PublicTemplateController extends Controller
{
    /**
     * Текущий публичный шаблон сайта.
     */
    protected function currentTemplate(): string
    {
        return app(PublicSettingsService::class)
            ->string('siteLayout', 'Default');
    }

    /**
     * Определяет контроллер активного публичного шаблона.
     */
    protected function resolveTemplateController(string $controllerPath): string
    {
        $siteLayout = $this->currentTemplate();

        $controller = "App\\Http\\Controllers\\Public\\{$siteLayout}\\{$controllerPath}";

        if (class_exists($controller)) {
            return $controller;
        }

        $defaultController = "App\\Http\\Controllers\\Public\\Default\\{$controllerPath}";

        if (class_exists($defaultController)) {
            return $defaultController;
        }

        abort(404);
    }
}
