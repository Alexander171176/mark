<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Inertia\Response;
use JsonException;

class ComposerController extends Controller
{
    /**
     * Просмотр конфигурации composer.json.
     *
     * Доступ разрешён только суперпользователю с ID = 1.
     */
    public function index(Request $request): Response
    {
        abort_unless(
            (int) $request->user()?->id === 1,
            403
        );

        $path = base_path('composer.json');

        if (!File::exists($path)) {
            abort(
                404,
                'composer.json not found'
            );
        }

        try {
            $composer = json_decode(
                File::get($path),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException $exception) {
            abort(
                500,
                'composer.json contains invalid JSON: ' . $exception->getMessage()
            );
        }

        if (!is_array($composer)) {
            abort(
                500,
                'composer.json has an invalid structure'
            );
        }

        return Inertia::render(
            'Admin/System/ComposerInfoPage',
            [
                'composer' => $composer,
            ]
        );
    }
}
