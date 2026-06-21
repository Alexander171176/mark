<?php

namespace App\Http\Controllers\Admin\System\ImageProcessor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\System\ImageProcessor\ImageProcessorVariantRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\System\ImageProcessor\ImageProcessorProfileResource;
use App\Http\Resources\Admin\System\ImageProcessor\ImageProcessorVariantResource;
use App\Models\Admin\System\ImageProcessor\ImageProcessorProfile;
use App\Models\Admin\System\ImageProcessor\ImageProcessorVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ImageProcessorVariantController extends Controller
{
    /** Список вариантов обработки */
    public function index(Request $request): Response
    {
        $variants = ImageProcessorVariant::query()
            ->with('profile')
            ->ordered()
            ->paginate($request->integer('per_page', 20))
            ->withQueryString();

        return Inertia::render('Admin/System/ImageProcessor/Variant/Index', [
            'variants' => ImageProcessorVariantResource::collection($variants),
        ]);
    }

    /** Форма создания */
    public function create(): Response
    {
        $profiles = ImageProcessorProfile::query()
            ->ordered()
            ->get();

        return Inertia::render('Admin/System/ImageProcessor/Variant/Create', [
            'profiles' => ImageProcessorProfileResource::collection($profiles),
        ]);
    }

    /** Создание варианта */
    public function store(ImageProcessorVariantRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            ImageProcessorVariant::create($request->validated());
        });

        return redirect()
            ->route('admin.imageProcessorVariants.index')
            ->with('success', 'Вариант обработки изображений успешно создан.');
    }

    /** Просмотр варианта */
    public function show(ImageProcessorVariant $imageProcessorVariant): Response
    {
        $imageProcessorVariant->load('profile');

        return Inertia::render('Admin/System/ImageProcessor/Variant/Show', [
            'variant' => new ImageProcessorVariantResource($imageProcessorVariant),
        ]);
    }

    /** Форма редактирования */
    public function edit(ImageProcessorVariant $imageProcessorVariant): Response
    {
        $imageProcessorVariant->load('profile');

        $profiles = ImageProcessorProfile::query()
            ->ordered()
            ->get();

        return Inertia::render('Admin/System/ImageProcessor/Variant/Edit', [
            'variant' => new ImageProcessorVariantResource($imageProcessorVariant),
            'profiles' => ImageProcessorProfileResource::collection($profiles),
        ]);
    }

    /** Обновление варианта */
    public function update(
        ImageProcessorVariantRequest $request,
        ImageProcessorVariant $imageProcessorVariant
    ): RedirectResponse {
        DB::transaction(function () use ($request, $imageProcessorVariant) {
            $imageProcessorVariant->update($request->validated());
        });

        return redirect()
            ->route('admin.imageProcessorVariants.edit', $imageProcessorVariant)
            ->with('success', 'Вариант обработки изображений обновлён.');
    }

    /** Удаление варианта */
    public function destroy(ImageProcessorVariant $imageProcessorVariant): RedirectResponse
    {
        DB::transaction(function () use ($imageProcessorVariant) {
            $imageProcessorVariant->delete();
        });

        return redirect()
            ->route('admin.imageProcessorVariants.index')
            ->with('success', 'Вариант обработки изображений удалён.');
    }

    /** Обновление активности варианта */
    public function updateActivity(
        UpdateActivityRequest $request,
        ImageProcessorVariant $imageProcessorVariant
    ): RedirectResponse {
        $imageProcessorVariant->update([
            'activity' => $request->validated('activity'),
        ]);

        return back()->with('success', 'Активность варианта обновлена.');
    }

    /** Массовое обновление активности вариантов */
    public function bulkUpdateActivity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => [
                'required',
                'integer',
                'exists:image_processor_variants,id',
            ],
            'activity' => ['required', 'boolean'],
        ]);

        ImageProcessorVariant::whereIn('id', $validated['ids'])
            ->update([
                'activity' => $validated['activity'],
            ]);

        return back()->with('success', 'Активность вариантов массово обновлена.');
    }

    /** Обновление сортировки варианта */
    public function updateSort(
        UpdateSortEntityRequest $request,
        ImageProcessorVariant $imageProcessorVariant
    ): RedirectResponse {
        $imageProcessorVariant->update([
            'sort' => $request->validated('sort'),
        ]);

        return back()->with('success', 'Сортировка варианта обновлена.');
    }

    /** Массовое обновление сортировки вариантов */
    public function updateSortBulk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'variants' => ['required', 'array'],
            'variants.*.id' => [
                'required',
                'integer',
                'exists:image_processor_variants,id',
            ],
            'variants.*.sort' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['variants'] as $variant) {
                ImageProcessorVariant::where('id', $variant['id'])
                    ->update([
                        'sort' => $variant['sort'],
                    ]);
            }
        });

        return back()->with('success', 'Сортировка вариантов массово обновлена.');
    }
}
