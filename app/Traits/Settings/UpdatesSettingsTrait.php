<?php

namespace App\Traits\Settings;

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
     * @param string $configKey
     * @param string $settingType
     * @param string $settingCategory
     * @param string $successMessage
     * @param string $errorMessage
     * @return RedirectResponse
     */
    private function updateSettingAndRedirect(
        FormRequest $request,
        string $optionKey,
        string $configKey,
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

            config([$configKey => $newValue]);

            // важно: этот метод должен быть доступен из контроллера через trait
            $this->clearSettingsCache('setting_' . $optionKey);
            $this->clearSettingsCache();

            DB::commit();

            Log::info("Настройка '{$optionKey}' обновлена", [
                'value' => $newValue,
                'user_id' => $request->user()?->id,
            ]);

            return back()->with('success', $successMessage);

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка обновления настройки '{$optionKey}': {$e->getMessage()}");

            return back()->withInput()->withErrors([
                'value' => $errorMessage,
            ]);
        }
    }
}
