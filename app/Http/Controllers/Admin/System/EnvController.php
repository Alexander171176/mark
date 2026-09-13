<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Inertia\Response;

class EnvController extends Controller
{
    /**
     * Просмотр конфигурации .env.
     *
     * Доступ разрешён только суперпользователю с ID = 1.
     */
    public function index(Request $request): Response
    {
        abort_unless(
            (int) $request->user()?->id === 1,
            403
        );

        $path = base_path('.env');

        if (!File::exists($path)) {
            abort(
                404,
                '.env file not found'
            );
        }

        $content = File::get($path);

        $env = collect(
            preg_split(
                '/\r\n|\r|\n/',
                $content
            )
        )
            ->map(
                fn ($line) => trim($line)
            )
            ->filter(
                fn ($line) =>
                    $line !== '' &&
                    !str_starts_with(
                        $line,
                        '#'
                    )
            )
            ->map(function ($line) {
                $parts = explode(
                    '=',
                    $line,
                    2
                );

                return [
                    'key' => trim(
                        $parts[0]
                    ),

                    'value' => isset(
                        $parts[1]
                    )
                        ? trim(
                            $parts[1]
                        )
                        : '',
                ];
            })
            ->filter(
                fn ($item) =>
                    $item['key'] !== ''
            )
            ->values();

        return Inertia::render(
            'Admin/System/EnvInfoPage',
            [
                'env' => $env,
            ]
        );
    }
}
