<?php

namespace App\Services\Admin\Slider;

use App\Models\Admin\Slider\Slider\Slider;
use App\Models\Admin\Slider\SliderSlide\SliderSlide;
use App\Models\Admin\Slider\SliderSlide\SliderSlideImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Единое сохранение дерева слайдера.
 *
 * Контракт: отсутствие slides/actions/advantages/images/translations означает
 * «не изменять коллекцию»; переданный пустой массив означает «очистить».
 * Вложенные ID принадлежат исключительно текущему родителю.
 * Данные обязательно должны пройти FormRequest перед вызовом сервиса.
 * Принимает изображения по ID или новые UploadedFile.
 * Файлы существующих изображений при отвязке не удаляются автоматически.
 */
class SliderBuilderService
{
    private const SLIDE_FIELDS = [
        'activity', 'sort', 'is_main', 'status', 'published_at',
        'show_from_at', 'show_to_at', 'background_color', 'text_color',
        'accent_color', 'overlay_color', 'overlay_opacity', 'image_position',
        'content_position', 'animation_type', 'animation_duration',
        'animation_delay', 'animation_stagger', 'animation_once',
        'animation_settings', 'settings',
    ];
    private const ACTION_FIELDS = [
        'sort', 'activity', 'is_primary', 'action_type', 'action_value',
        'route_params', 'target', 'style', 'icon', 'icon_position',
        'css_class', 'settings',
    ];
    private const ADVANTAGE_FIELDS = [
        'sort', 'activity', 'icon_type', 'icon', 'icon_color', 'style', 'settings',
    ];
    private const SLIDE_TRANSLATIONS = ['label', 'title', 'accent', 'description'];
    private const ACTION_TRANSLATIONS = ['label', 'title', 'aria_label'];
    private const ADVANTAGE_TRANSLATIONS = ['title', 'text'];

    /** @var array<int, SliderSlideImage> Изображения, созданные текущим запросом. */
    private array $createdMedia = [];

    /** @var array<int, int> ID изображений, которые могли стать сиротами. */
    private array $orphanCandidates = [];

    public function __construct(
        private readonly SliderService $sliders,
        private readonly SliderImageLifecycleService $imageLifecycle,
    ) {}

    /**
     * SQL rollback не удаляет файлы Spatie. Запоминаем Media и очищаем
     * загруженные файлы при ошибке. Удаление старых файлов — только после COMMIT.
     * @throws Throwable
     */
    private function withImageCleanup(callable $callback): Slider
    {
        $this->createdMedia = [];
        $this->orphanCandidates = [];

        try {
            $result = DB::transaction(function () use ($callback): Slider {
                $slider = $callback();
                $this->imageLifecycle->deleteOrphansAfterCommit($this->orphanCandidates);
                return $slider;
            });
            $this->createdMedia = [];
            $this->orphanCandidates = [];
            return $result;
        } catch (Throwable $exception) {
            $this->imageLifecycle->cleanupFailedUploads($this->createdMedia);
            $this->createdMedia = [];
            $this->orphanCandidates = [];
            throw $exception;
        }
    }

    /** Отмечаем изображения, для которых после COMMIT проверим отсутствие связей. */
    private function markOrphanCandidates(array $ids): void
    {
        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $this->orphanCandidates[$id] = $id;
            }
        }
    }

    /**
     * @throws Throwable
     */
    public function create(array $validated, int $userId, bool $isAdmin = false): Slider
    {
        return $this->withImageCleanup(function () use ($validated, $userId, $isAdmin): Slider {
            $slider = $this->sliders->create(Arr::except($validated, ['slides']), $userId, $isAdmin);
            if (array_key_exists('slides', $validated)) {
                $this->syncSlides($slider, $validated['slides']);
            }
            return $this->reload($slider);
        });
    }

    /**
     * @throws Throwable
     */
    public function update(Slider $slider, array $validated, bool $isAdmin = false): Slider
    {
        return $this->withImageCleanup(function () use ($slider, $validated, $isAdmin): Slider {
            // Блокировка родителя сериализует параллельные изменения конструктора.
            $locked = Slider::query()->lockForUpdate()->findOrFail($slider->getKey());
            $before = $locked->moderation_status;
            $nestedChanged = array_key_exists('slides', $validated)
                && $this->treeDiffers($locked, $validated['slides']);

            $updated = $this->sliders->update($locked, Arr::except($validated, ['slides']), $isAdmin);

            if (array_key_exists('slides', $validated)) {
                $this->syncSlides($updated, $validated['slides']);
            }

            // Даже изменение только вложенных сущностей требует повторной модерации.
            // Администратор вправе сохранить существующее решение.
            if (! $isAdmin && $nestedChanged && (int) $updated->moderation_status !== 0) {
                $updated->forceFill([
                    'moderation_status' => 0,
                    'moderated_by' => null,
                    'moderated_at' => null,
                    'moderation_note' => null,
                ])->save();
            }

            return $this->reload($updated);
        });
    }

    /**
     * @throws ValidationException
     */
    private function syncSlides(Slider $slider, array $items): void
    {
        $this->assertUniqueIds($items, 'slides');
        $keep = [];
        foreach ($items as $position => $data) {
            $slide = $this->ownedOrNew($slider->slides(), $data, "slides.$position.id");
            $slide->fill(Arr::only($data, self::SLIDE_FIELDS));
            // Порядок массива — единый источник порядка в конструкторе.
            $slide->sort = $position;
            if (! $slide->exists && ! array_key_exists('activity', $data)) {
                $slide->activity = true;
            }
            $slide->save();

            if (array_key_exists('translations', $data)) {
                $this->syncTranslations($slide, $data['translations'], self::SLIDE_TRANSLATIONS);
            }
            if (array_key_exists('actions', $data)) {
                $this->syncChildren($slide, 'actions', $data['actions'], self::ACTION_FIELDS, self::ACTION_TRANSLATIONS, "slides.$position.actions");
            }
            if (array_key_exists('advantages', $data)) {
                $this->syncChildren($slide, 'advantages', $data['advantages'], self::ADVANTAGE_FIELDS, self::ADVANTAGE_TRANSLATIONS, "slides.$position.advantages");
            }
            if (array_key_exists('images', $data)) {
                $this->syncImages($slider, $slide, $data['images'], "slides.$position.images");
            }
            $keep[] = $slide->getKey();
        }

        // Удаление выполняется только после успешной проверки всех вложенных ID.
        // При удалении слайдов pivot-связи исчезают по FK cascade.
        // Запоминаем изображения заранее; после COMMIT удалим лишь сироты.
        $removedSlideIds = $slider->slides()->whereNotIn('id', $keep)->pluck('id');
        if ($removedSlideIds->isNotEmpty()) {
            $this->markOrphanCandidates(DB::table('slider_slide_has_images')
                ->whereIn('slider_slide_id', $removedSlideIds)
                ->pluck('slider_slide_image_id')->all());
            $slider->slides()->whereIn('id', $removedSlideIds)->delete();
        }
    }

    /**
     * @throws ValidationException
     */
    private function syncChildren(SliderSlide $slide, string $relation, array $items, array $fields, array $translationFields, string $path): void
    {
        $this->assertUniqueIds($items, $path);
        $keep = [];
        foreach ($items as $position => $data) {
            $child = $this->ownedOrNew($slide->{$relation}(), $data, "$path.$position.id");
            $child->fill(Arr::only($data, $fields));
            $child->sort = $position;
            $child->save();
            if (array_key_exists('translations', $data)) {
                $this->syncTranslations($child, $data['translations'], $translationFields);
            }
            $keep[] = $child->getKey();
        }
        $slide->{$relation}()->whereNotIn('id', $keep)->delete();
    }

    private function syncTranslations(Model $parent, array $translations, array $fields): void
    {
        $keepLocales = [];

        foreach ($translations as $locale => $data) {
            if (! is_string($locale) || ! is_array($data)) {
                throw ValidationException::withMessages([
                    'translations' => 'Неверный формат переводов.',
                ]);
            }

            $values = Arr::only($data, $fields);
            foreach ($values as $field => $value) {
                if (is_string($value)) {
                    $value = trim($value);
                }
                $values[$field] = $value === '' ? null : $value;
            }

            // Не создаём строку в БД для полностью пустого языка.
            if (! collect($values)->contains(fn ($value) => $value !== null)) {
                continue;
            }

            // У кнопок label в БД NOT NULL. Не допускаем SQL-исключения.
            if (in_array('aria_label', $fields, true) && blank($values['label'] ?? null)) {
                throw ValidationException::withMessages([
                    'translations' => "Укажите название кнопки для языка [$locale].",
                ]);
            }

            $parent->translations()->updateOrCreate(
                ['locale' => $locale],
                $values
            );
            $keepLocales[] = $locale;
        }

        $parent->translations()->whereNotIn('locale', $keepLocales)->delete();
    }

    /**
     * Каждая запись images[*] — одна Desktop-картинка.
     * Mobile-версия создаётся конверсией slider_mobile в SliderSlideImage.
     * Существующие картинки передаются по id, новые — по file.
     * @throws ValidationException
     */
    private function syncImages(Slider $slider, SliderSlide $slide, array $images, string $path): void
    {
        $ids = [];
        $sync = [];
        $previousIds = $slide->images()->pluck('slider_slide_images.id')->all();

        foreach ($images as $position => $data) {
            if (! is_array($data)) {
                throw ValidationException::withMessages(["$path.$position" => 'Некорректное изображение.']);
            }

            $hasId = ! empty($data['id']);
            $hasFile = ($data['file'] ?? null) instanceof UploadedFile;
            if ($hasId === $hasFile) {
                throw ValidationException::withMessages(["$path.$position" => 'Укажите либо id, либо новый файл.']);
            }

            if ($hasId) {
                $id = (int) $data['id'];
                // Нельзя присоединять изображения другого слайдера или другой сущности.
                $belongs = SliderSlideImage::query()->whereKey($id)
                    ->whereHas('slides', fn ($query) => $query->where('slider_id', $slider->getKey()))
                    ->exists();
                if (! $belongs) {
                    throw ValidationException::withMessages(["$path.$position.id" => 'Изображение не принадлежит текущему слайдеру.']);
                }
                $image = SliderSlideImage::query()->findOrFail($id);
                // Обновляем подписи только у изображения текущего слайда.
                // Если оно используется другими слайдами, не меняем общую запись.
                $attachedHere = $slide->images()->whereKey($id)->exists();
                if (! $attachedHere) {
                    throw ValidationException::withMessages(["$path.$position.id" => 'Изображение не принадлежит текущему слайду.']);
                }
                if (array_key_exists('alt', $data) || array_key_exists('caption', $data)) {
                    $shared = $image->slides()->whereKeyNot($slide->getKey())->exists();
                    if ($shared) {
                        throw ValidationException::withMessages(["$path.$position.id" => 'Нельзя изменить подписи изображения, используемого другими слайдами.']);
                    }
                    $image->fill(Arr::only($data, ['alt', 'caption']))->save();
                }
            } else {
                $image = new SliderSlideImage();
                $image->order = $position;
                $image->alt = $data['alt'] ?? null;
                $image->caption = $data['caption'] ?? null;
                $image->save();
                // Если addMedia выбросит исключение до создания Media, запись
                // откатится SQL-транзакцией. Успешные Media сохраняем для rollback.
                $media = $image->addMedia($data['file'])->toMediaCollection('images');
                $this->createdMedia[] = $media;
                $id = (int) $image->getKey();
            }

            if (in_array($id, $ids, true)) {
                throw ValidationException::withMessages(["$path.$position.id" => 'Повторяющийся ID изображения.']);
            }
            $ids[] = $id;
            $sync[$id] = ['purpose' => 'desktop', 'order' => $position];

            // Подписи редактируются только у изображения, связанного с текущим слайдом.
            // Общие изображения не меняем, чтобы не затронуть другие слайды.
        }

        $slide->images()->sync($sync);
        $this->markOrphanCandidates(array_diff(array_map('intval', $previousIds), $ids));
    }

    /**
     * @throws ValidationException
     */
    private function ownedOrNew($relation, array $data, string $path): Model
    {
        if (empty($data['id'])) {
            return $relation->make();
        }
        $id = filter_var($data['id'], FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) {
            throw ValidationException::withMessages([$path => 'Некорректный ID.']);
        }
        $model = $relation->whereKey($id)->first();
        if (! $model) {
            throw ValidationException::withMessages([$path => 'Элемент не принадлежит текущему родителю.']);
        }
        return $model;
    }

    /**
     * @throws ValidationException
     */
    private function assertUniqueIds(array $items, string $path): void
    {
        $seen = [];
        foreach ($items as $index => $item) {
            if (! is_array($item)) {
                throw ValidationException::withMessages(["$path.$index" => 'Ожидается объект.']);
            }
            if (! empty($item['id'])) {
                $id = (string) $item['id'];
                if (isset($seen[$id])) {
                    throw ValidationException::withMessages(["$path.$index.id" => 'Повторяющийся ID.']);
                }
                $seen[$id] = true;
            }
        }
    }

    private function treeDiffers(Slider $slider, array $incoming): bool
    {
        // Консервативная политика: переданное дерево считаем изменением.
        // Это безопасно для модерации, но при повторном сохранении без изменений
        // статус также будет сброшен. Позже можно заменить сравнением снапшотов.
        return true;
    }

    private function reload(Slider $slider): Slider
    {
        return $slider->refresh()->load([
            'translations', 'slides.translations',
            'slides.images', 'slides.actions.translations',
            'slides.advantages.translations',
        ]);
    }
}
