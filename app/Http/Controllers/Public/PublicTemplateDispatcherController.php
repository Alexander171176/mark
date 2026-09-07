<?php

namespace App\Http\Controllers\Public;

use App\Services\SiteSettings\PublicSettingsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicTemplateDispatcherController extends PublicTemplateController
{
    /**
     * Передаёт запрос контроллеру активного публичного шаблона.
     */
    public function dispatch(
        Request $request,
        PublicSettingsService $publicSettings
    ) {
        /**
         * Режим технических работ.
         *
         * Проверка выполняется до запуска контроллера
         * активного публичного шаблона.
         */
        if (
            $publicSettings->string(
                'downtimeSite',
                'false'
            ) === 'true'
        ) {
            return Inertia::render('Maintenance')
                ->toResponse($request)
                ->setStatusCode(503);
        }

        $controllerPath =
            $request->route('_templateController');

        $action =
            $request->route('_templateAction');

        $controller =
            $this->resolveTemplateController(
                $controllerPath
            );

        $parameters =
            $request->route()->parameters();

        unset(
            $parameters['_templateController'],
            $parameters['_templateAction']
        );

        return app()->call(
            [app($controller), $action],
            $parameters
        );
    }
}
