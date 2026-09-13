<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PhpInfoController extends Controller
{
    /**
     * Просмотр информации о конфигурации PHP.
     *
     * Доступ разрешён только суперпользователю с ID = 1.
     */
    public function index(Request $request): Response
    {
        abort_unless(
            (int) $request->user()?->id === 1,
            403
        );

        ob_start();

        phpinfo();

        $phpinfo = ob_get_clean();

        if ($phpinfo === false) {
            abort(
                500,
                'Unable to retrieve PHP information'
            );
        }

        if (
            preg_match(
                '/<body[^>]*>(.*?)<\/body>/is',
                $phpinfo,
                $matches
            )
        ) {
            $phpinfo = $matches[1];
        }

        return Inertia::render(
            'Admin/System/PhpInfoPage',
            [
                'phpinfo' => $phpinfo,
            ]
        );
    }
}
