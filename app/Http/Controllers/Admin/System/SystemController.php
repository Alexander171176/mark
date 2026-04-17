<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Services\Public\Blog\BlogSidebarService;
use App\Services\Public\Blog\RubricTreeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class SystemController extends Controller
{
    /**
     * Очищает различные кэши приложения.
     *
     * @param BlogSidebarService $blogSidebarService
     * @param RubricTreeService $rubricTreeService
     * @return RedirectResponse
     */
    public function clearCache(
        BlogSidebarService $blogSidebarService,
        RubricTreeService $rubricTreeService
    ): RedirectResponse {
        $messages = [];

        try {
            /**
             * 1. Очистка доменных кэшей приложения
             */
            try {
                $blogSidebarService->forgetAll();
                $messages[] = 'Кэш боковых колонок блога очищен.';
                Log::info('Кэш BlogSidebarService очищен пользователем: ' . auth()->id());
            } catch (Throwable $e) {
                Log::error('Ошибка очистки кэша BlogSidebarService: ' . $e->getMessage());
                $messages[] = 'Ошибка очистки кэша боковых колонок блога.';
            }

            try {
                $rubricTreeService->forget();
                $messages[] = 'Кэш дерева рубрик очищен.';
                Log::info('Кэш RubricTreeService очищен пользователем: ' . auth()->id());
            } catch (Throwable $e) {
                Log::error('Ошибка очистки кэша RubricTreeService: ' . $e->getMessage());
                $messages[] = 'Ошибка очистки кэша дерева рубрик.';
            }

            /**
             * 2. Очистка cache store Laravel
             *
             * Работает для file / redis / memcached согласно текущему CACHE_DRIVER.
             * Без прямого flushAll() Redis.
             */
            try {
                Artisan::call('cache:clear');
                $messages[] = 'Кэш приложения Laravel очищен.';
                Log::info('Laravel cache:clear выполнен пользователем: ' . auth()->id(), [
                    'driver' => config('cache.default'),
                ]);
            } catch (Throwable $e) {
                Log::error('Ошибка при выполнении cache:clear: ' . $e->getMessage());
                $messages[] = 'Ошибка очистки кэша приложения Laravel.';
            }

            /**
             * 3. Очистка системных кэшей Laravel
             */
            try {
                Artisan::call('config:clear');
                $messages[] = 'Кэш конфигурации очищен.';
            } catch (Throwable $e) {
                Log::error('Ошибка очистки config cache: ' . $e->getMessage());
                $messages[] = 'Ошибка очистки кэша конфигурации.';
            }

            try {
                Artisan::call('route:clear');
                $messages[] = 'Кэш маршрутов очищен.';
            } catch (Throwable $e) {
                Log::error('Ошибка очистки route cache: ' . $e->getMessage());
                $messages[] = 'Ошибка очистки кэша маршрутов.';
            }

            try {
                Artisan::call('view:clear');
                $messages[] = 'Кэш представлений очищен.';
            } catch (Throwable $e) {
                Log::error('Ошибка очистки view cache: ' . $e->getMessage());
                $messages[] = 'Ошибка очистки кэша представлений.';
            }

            /**
             * При необходимости можно включить и это:
             */
            // try {
            //     Artisan::call('event:clear');
            //     $messages[] = 'Кэш событий очищен.';
            // } catch (Throwable $e) {
            //     Log::error('Ошибка очистки event cache: ' . $e->getMessage());
            //     $messages[] = 'Ошибка очистки кэша событий.';
            // }

            $finalMessage = implode(' ', $messages);

            return back()->with(
                'success',
                $finalMessage ?: 'Кэш успешно очищен.'
            );
        } catch (Throwable $e) {
            Log::critical('Критическая ошибка в процессе очистки кэша: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Произошла критическая ошибка при очистке кэша.');
        }
    }
}
