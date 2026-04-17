<?php

namespace App\Http\Controllers\Admin\System\Snapshot;

use App\Http\Controllers\Controller;
use App\Services\SiteSettings\SnapshotBuilder;
use Illuminate\Http\JsonResponse;

/**
 * Контроллер генерации снапшотов настроек системы
 */
class SettingsSnapshotController extends Controller
{
    /**
     * Генерация массива настроек из БД для публичной части в файл public.php
     *
     * @return JsonResponse
     */
    public function buildPublic(): JsonResponse
    {
        try {
            $path = SnapshotBuilder::buildPublic();

            return response()->json([
                'success' => true,
                'built' => 'public',
                'path' => $path,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка создания public снапшота: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Генерация массива настроек из БД для административной части в файл admin.php
     *
     * @return JsonResponse
     */
    public function buildAdmin(): JsonResponse
    {
        try {
            $path = SnapshotBuilder::buildAdmin();

            return response()->json([
                'success' => true,
                'built' => 'admin',
                'path' => $path,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка создания admin снапшота: ' . $e->getMessage(),
            ], 500);
        }
    }
}
