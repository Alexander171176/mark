<?php

namespace App\Traits\Admin\Settings;

use App\Models\Admin\System\Setting\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

trait UpdatesSettingsTrait
{
    /**
     * Обновляет настройку и возвращает RedirectResponse.
     *
     * @param FormRequest $request
     * @param string $optionKey
     * @param string $settingType
     * @param string $settingCategory
     * @param string $successMessage
     * @param string $errorMessage
     * @return RedirectResponse
     */
    private function updateSettingAndRedirect(
        FormRequest $request,
        string $optionKey,
        string $settingType,
        string $settingCategory,
        string $successMessage,
        string $errorMessage
    ): RedirectResponse {
        $validated = $request->validated();
        $newValue = $validated['value'];

        try {
            DB::beginTransaction();

            Setting::updateOrCreate(
                ['option' => $optionKey],
                [
                    'value' => (string) $newValue,
                    'type' => $settingType,
                    'constant' => strtoupper($optionKey),
                    'category' => $settingCategory,
                    'activity' => true,
                ]
            );

            DB::commit();

            Log::info("Настройка '{$optionKey}' обновлена", [
                'value' => $newValue,
                'user_id' => $request->user()?->id,
            ]);

            return back()->with('success', $successMessage);

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка обновления настройки '{$optionKey}': {$e->getMessage()}");

            return back()
                ->withInput()
                ->withErrors([
                    'value' => $errorMessage,
                ]);
        }
    }
}
