<?php

namespace Tests\Feature\Admin\Slider;

use App\Models\Admin\Slider\Slider\Slider;
use App\Models\Admin\Slider\SliderSlide\SliderSlide;
use App\Models\Admin\Slider\SliderSlide\SliderSlideImage;
use App\Models\User;
use App\Services\Admin\Slider\SliderBuilderService;
use App\Services\Admin\Slider\SliderImageLifecycleService;
use App\Services\Admin\Slider\SliderService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Интеграционные проверки жизненного цикла изображений.
 * Запускать на отдельной тестовой БД с выполненными миграциями проекта.
 * Для проверки Spatie используется локальный fake-диск media.
 */
class SliderImageLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Регистрируем тестовый диск ДО Storage::fake(): Spatie проверяет
        // его наличие через config('filesystems.disks').
        config()->set('filesystems.disks.media', [
            'driver' => 'local',
            'root' => storage_path('framework/testing/disks/media'),
            'throw' => true,
        ]);
        Storage::fake('media');
        config()->set('media-library.disk_name', 'media');
        config()->set('media-library.queue_conversions_by_default', false);
    }

    private function slider(): Builder|Model
    {
        return Slider::query()->create([
            'code' => 'test-slider-' . uniqid(),
            'type' => 'hero',
            'status' => 'draft',
        ]);
    }

    private function slide(Slider $slider): Model
    {
        return $slider->slides()->create(['status' => 'draft', 'activity' => true]);
    }

    private function image(SliderSlide $slide): Builder|Model
    {
        $image = SliderSlideImage::query()->create(['alt' => 'Тест']);
        $image->addMedia(UploadedFile::fake()->image('desktop.jpg', 1200, 800))
            ->toMediaCollection('images', 'media');
        $slide->images()->attach($image->id, ['purpose' => 'desktop', 'order' => 0]);
        return $image;
    }

    private function mediaPath(SliderSlideImage $image): string
    {
        return $image->getFirstMedia('images')->getPathRelativeToRoot();
    }

    public function test_upload_creates_media_and_mobile_conversion(): void
    {
        $slider = $this->slider();
        $slide = $this->slide($slider);
        $builder = app(SliderBuilderService::class);

        $builder->update($slider, [
            'slides' => [[
                'id' => $slide->id,
                'images' => [['file' => UploadedFile::fake()->image('banner.jpg', 1200, 800)]],
            ]],
        ], true);

        $image = $slide->images()->firstOrFail();
        $this->assertSame(1, $slide->images()->count());
        $this->assertSame('desktop', $image->pivot->purpose);
        $this->assertNotNull($image->getFirstMedia('images'));
        $this->assertTrue(Storage::disk('media')->exists($this->mediaPath($image)));
        $this->assertTrue($image->getFirstMedia('images')->hasGeneratedConversion('slider_mobile'));
    }

    public function test_detaching_shared_image_keeps_file_until_last_reference_is_removed(): void
    {
        $slider = $this->slider();
        $first = $this->slide($slider);
        $second = $this->slide($slider);
        $image = $this->image($first);
        $second->images()->attach($image->id, ['purpose' => 'desktop', 'order' => 0]);
        $path = $this->mediaPath($image);

        app(SliderBuilderService::class)->update($slider, [
            'slides' => [
                ['id' => $first->id, 'images' => []],
                ['id' => $second->id, 'images' => [['id' => $image->id]]],
            ],
        ], true);

        $this->assertDatabaseHas('slider_slide_images', ['id' => $image->id]);
        $this->assertTrue(Storage::disk('media')->exists($path));
        $this->assertSame(1, $image->fresh()->slides()->count());
    }

    public function test_detaching_last_reference_removes_orphan_after_commit(): void
    {
        $slider = $this->slider();
        $slide = $this->slide($slider);
        $image = $this->image($slide);

        $imageId = $image->id;
        $path = $this->mediaPath($image);

        $this->assertTrue(Storage::disk('media')->exists($path));

        app(SliderBuilderService::class)->update($slider, [
            'slides' => [
                [
                    'id' => $slide->id,
                    'images' => [],
                ],
            ],
        ], true);

        // Проверяем удаление связи.
        $this->assertDatabaseMissing('slider_slide_has_images', [
            'slider_slide_id' => $slide->id,
            'slider_slide_image_id' => $imageId,
        ]);

        // После COMMIT изображение-сирота должно быть удалено.
        $this->assertDatabaseMissing('slider_slide_images', [
            'id' => $imageId,
        ]);

        // Spatie MediaLibrary должен удалить физический файл.
        $this->assertFalse(Storage::disk('media')->exists($path));
    }

    public function test_orphan_cleanup_deletes_unreferenced_image_and_media(): void
    {
        $slider = $this->slider();
        $slide = $this->slide($slider);
        $image = $this->image($slide);
        $path = $this->mediaPath($image);
        $slide->images()->detach($image->id);

        app(SliderImageLifecycleService::class)->deleteOrphans([$image->id]);

        $this->assertDatabaseMissing('slider_slide_images', ['id' => $image->id]);
        $this->assertFalse(Storage::disk('media')->exists($path));
    }

    public function test_cleanup_does_not_delete_an_image_still_in_use(): void
    {
        $slider = $this->slider();
        $slide = $this->slide($slider);
        $image = $this->image($slide);

        app(SliderImageLifecycleService::class)->deleteOrphans([$image->id]);

        $this->assertDatabaseHas('slider_slide_images', ['id' => $image->id]);
        $this->assertTrue(Storage::disk('media')->exists($this->mediaPath($image)));
    }

    public function test_foreign_image_id_is_rejected_without_changing_existing_links(): void
    {
        $sliderA = $this->slider();
        $slideA = $this->slide($sliderA);
        $sliderB = $this->slider();
        $slideB = $this->slide($sliderB);
        $foreign = $this->image($slideB);

        try {
            app(SliderBuilderService::class)->update($sliderA, [
                'slides' => [['id' => $slideA->id, 'images' => [['id' => $foreign->id]]]],
            ], true);
            $this->fail('Ожидалась ValidationException');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('slides.0.images.0.id', $exception->errors());
        }

        $this->assertSame(1, $slideB->images()->count());
        $this->assertSame(0, $slideA->images()->count());
    }

    public function test_deleting_parent_slider_detaches_images(): void
    {
        $slider = $this->slider();
        $slide = $this->slide($slider);
        $image = $this->image($slide);

        app(SliderService::class)->delete($slider);

        $this->assertDatabaseMissing('sliders', ['id' => $slider->id]);
        $this->assertDatabaseMissing('slider_slide_has_images', ['slider_slide_image_id' => $image->id]);
    }

    public function test_failed_nested_save_rolls_back_pivot_and_image_records(): void
    {
        $slider = $this->slider();
        $slide = $this->slide($slider);
        $original = $this->image($slide);

        try {
            app(SliderBuilderService::class)->update($slider, [
                'slides' => [[
                    'id' => $slide->id,
                    'images' => [
                        ['file' => UploadedFile::fake()->image('new.jpg', 1200, 800)],
                        ['id' => 999999999],
                    ],
                ]],
            ], true);
            $this->fail('Ожидалась ValidationException');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('slides.0.images.1.id', $exception->errors());
        }

        $this->assertSame([$original->id], $slide->images()->pluck('slider_slide_images.id')->all());
        $this->assertSame(1, SliderSlideImage::query()->count());
    }
}
