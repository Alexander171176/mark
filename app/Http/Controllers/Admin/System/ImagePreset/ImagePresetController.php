<?php

namespace App\Http\Controllers\Admin\System\ImagePreset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\System\ImagePreset\ImagePresetRequest;
use App\Http\Resources\Admin\System\ImagePreset\ImagePresetResource;
use App\Models\Admin\System\ImagePreset\ImagePreset;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ImagePresetController extends Controller
{
    /** Список пресетов. */
    public function index(): Response
    {
        $settingsService = app(AdminSettingsService::class);

        $adminImagePresetsPerPage = $settingsService->int(
            'adminImagePresetsPerPage',
            6
        );

        $adminImagePresetsDefaultSort = $settingsService->string(
            'adminImagePresetsDefaultSort',
            'idDesc'
        );

        try {
            $presets = ImagePreset::query()
                ->orderBy('sort')
                ->orderBy('id')
                ->get();

            return Inertia::render('Admin/System/ImagePresets/Index', [
                'presets' => ImagePresetResource::collection($presets),
                'presetsCount' => $presets->count(),

                'adminImagePresetsPerPage' => $adminImagePresetsPerPage,
                'adminImagePresetsDefaultSort' => $adminImagePresetsDefaultSort,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки пресетов изображений: ' . $e->getMessage());

            return Inertia::render('Admin/System/ImagePresets/Index', [
                'presets' => [],
                'presetsCount' => 0,

                'adminImagePresetsPerPage' => $adminImagePresetsPerPage,
                'adminImagePresetsDefaultSort' => $adminImagePresetsDefaultSort,

                'error' => 'Ошибка загрузки пресетов изображений.',
            ]);
        }
    }

    /** Форма редактирования пресета. */
    public function edit(ImagePreset $imagePreset): Response
    {
        return Inertia::render('Admin/System/ImagePresets/Edit', [
            'preset' => new ImagePresetResource($imagePreset),
        ]);
    }

    /** Обновление пресета. */
    public function update(
        ImagePresetRequest $request,
        ImagePreset $imagePreset
    ): RedirectResponse {
        try {
            DB::transaction(function () use ($request, $imagePreset) {
                $imagePreset->update($request->validated());
            });

            return redirect()
                ->route('admin.imagePresets.index')
                ->with('success', 'Пресет обработки изображений обновлён.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления пресета изображения: ' . $e->getMessage());

            return back()
                ->withErrors(['general' => 'Ошибка обновления пресета обработки изображений.'])
                ->withInput();
        }
    }

    /** Обновление сортировки одного пресета. */
    public function updateSort(
        Request $request,
        ImagePreset $imagePreset
    ): RedirectResponse {
        $data = $request->validate([
            'sort' => ['required', 'integer', 'min:0'],
        ]);

        try {
            $imagePreset->update([
                'sort' => (int) $data['sort'],
            ]);

            return back()->with('success', 'Сортировка пресета обновлена.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления сортировки пресета: ' . $e->getMessage());

            return back()->withErrors([
                'sort' => 'Ошибка обновления сортировки пресета.',
            ]);
        }
    }

    /** Массовое обновление сортировки пресетов. */
    public function updateSortBulk(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'presets' => ['required', 'array', 'min:1'],
            'presets.*.id' => ['required', 'integer', 'exists:image_presets,id'],
            'presets.*.sort' => ['required', 'integer', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($data) {
                foreach ($data['presets'] as $preset) {
                    ImagePreset::whereKey($preset['id'])->update([
                        'sort' => (int) $preset['sort'],
                    ]);
                }
            });

            return back()->with('success', 'Сортировка пресетов обновлена.');
        } catch (Throwable $e) {
            Log::error('Ошибка массовой сортировки пресетов: ' . $e->getMessage());

            return back()->withErrors([
                'presets' => 'Ошибка обновления сортировки пресетов.',
            ]);
        }
    }
}
