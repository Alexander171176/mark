<?php

namespace App\Traits\Admin\System\Location;

use App\Http\Requests\Admin\System\UpdateActivityRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait HasLocationActivityTrait
{
    /**
     * Обновление активности одной Location.
     */
    public function updateActivity(
        UpdateActivityRequest $request,
        int $location
    ): RedirectResponse {
        $currentLocation =
            $this->baseQuery()
                ->findOrFail($location);

        $activity =
            $request->validated(
                'activity'
            );

        if (
            $currentLocation->is_default
            && !$activity
        ) {
            return back()->with(
                'error',
                'Локацию по умолчанию нельзя деактивировать.'
            );
        }

        $currentLocation->update([
            'activity' => $activity,
        ]);

        return back()->with(
            'success',
            "Активность {$this->entityLabel} обновлена."
        );
    }

    /**
     * Массовое обновление активности.
     */
    public function bulkUpdateActivity(
        Request $request
    ): RedirectResponse|JsonResponse {
        $table =
            (new $this->modelClass)
                ->getTable();

        $validated =
            $request->validate([
                'ids' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'ids.*' => [
                    'required',
                    'integer',
                    'distinct',
                    "exists:{$table},id",
                ],

                'activity' => [
                    'required',
                    'boolean',
                ],
            ]);

        /**
         * Нельзя массово отключить
         * default Location.
         */
        if (
            !$validated['activity']
            && $this->baseQuery()
                ->whereIn(
                    'id',
                    $validated['ids']
                )
                ->where(
                    'is_default',
                    true
                )
                ->exists()
        ) {
            $message =
                'Локацию по умолчанию нельзя деактивировать.';

            return $request->expectsJson()
                ? response()->json(
                    [
                        'message' =>
                            $message,
                    ],
                    422
                )
                : back()->with(
                    'error',
                    $message
                );
        }

        $this->modelClass::query()
            ->whereIn(
                'id',
                $validated['ids']
            )
            ->update([
                'activity' =>
                    $validated['activity'],
            ]);

        $message =
            "Активность выбранных {$this->entityLabel} обновлена.";

        return $request->expectsJson()
            ? response()->json([
                'message' => $message,
            ])
            : back()->with(
                'success',
                $message
            );
    }
}
