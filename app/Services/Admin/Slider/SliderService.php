<?php

namespace App\Services\Admin\Slider;

use App\Models\Admin\Slider\Slider\Slider;
use App\Models\Admin\Slider\SliderSlide\SliderSlideImage;
use App\Traits\Admin\Slider\HasSliderTranslationsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/** Сервис создания, обновления и удаления слайдеров. */
class SliderService
{
    use HasSliderTranslationsTrait;

    private const TRANSLATION_FIELDS = [
        'title',
        'subtitle',
        'description',
        'meta_title',
        'meta_description',
    ];

    private const MODERATION_FIELDS = [
        'moderation_status',
        'moderated_by',
        'moderated_at',
        'moderation_note',
    ];

    /** Создание слайдера и переводов в одной транзакции. */
    public function create(array $validated, int $userId, bool $isAdmin = false): Slider
    {
        return DB::transaction(function () use ($validated, $userId, $isAdmin): Builder|Model {
            $translations = $validated['translations'] ?? [];

            // Служебные поля модерации не записываем напрямую из формы.
            $attributes = Arr::except($validated, [
                'translations',
                'user_id',
                ...self::MODERATION_FIELDS,
            ]);

            $attributes['user_id'] = $userId;
            $attributes['moderation_status'] = 0;
            $attributes['moderated_by'] = null;
            $attributes['moderated_at'] = null;
            $attributes['moderation_note'] = null;

            // Администратор может сразу принять решение по модерации.
            if ($isAdmin && array_key_exists('moderation_status', $validated)) {
                $this->applyModeration($attributes, $validated, $userId);
            }

            $this->normalizePublication($attributes);

            $slider = Slider::query()->create($attributes);
            $this->syncTranslations($slider, $translations, self::TRANSLATION_FIELDS);

            return $slider->load('translations');
        });
    }

    /** Обновление настроек и переданных переводов без смены владельца. */
    public function update(Slider $slider, array $validated, bool $isAdmin = false): Slider
    {
        return DB::transaction(function () use ($slider, $validated, $isAdmin): Slider {
            $translations = $validated['translations'] ?? [];

            $attributes = Arr::except($validated, [
                'translations',
                'user_id',
                ...self::MODERATION_FIELDS,
            ]);

            $this->normalizePublication($attributes, $slider);

            if ($isAdmin) {
                // Изменение статуса фиксируем как решение текущего администратора.
                if (array_key_exists('moderation_status', $validated)) {
                    $this->applyModeration(
                        $attributes,
                        $validated,
                        (int) auth()->id()
                    );
                }
            } elseif ($this->hasContentChanges($slider, $attributes, $translations)) {
                // Изменения владельца требуют новой проверки с чистой историей решения.
                $this->resetModeration($attributes);
            }

            $slider->update($attributes);
            $this->syncTranslations($slider, $translations, self::TRANSLATION_FIELDS);

            return $slider->refresh()->load('translations');
        });
    }

    /** Записать решение модератора, не доверяя ID и дате из запроса. */
    private function applyModeration(array &$attributes, array $validated, int $moderatorId): void
    {
        $status = (int) $validated['moderation_status'];
        $attributes['moderation_status'] = $status;

        if ($status === 0) {
            $this->resetModeration($attributes);
            return;
        }

        $attributes['moderated_by'] = $moderatorId;
        $attributes['moderated_at'] = now();
        $attributes['moderation_note'] = $this->normalizeText(
            $validated['moderation_note'] ?? null
        );
    }

    /** Сбросить результат предыдущей модерации. */
    private function resetModeration(array &$attributes): void
    {
        $attributes['moderation_status'] = 0;
        $attributes['moderated_by'] = null;
        $attributes['moderated_at'] = null;
        $attributes['moderation_note'] = null;
    }

    /** Удалить слайдер и изображения, больше не используемые другими слайдами. */
    public function delete(Slider $slider): void
    {
        $orphanIds = DB::transaction(function () use ($slider): array {
            $slideIds = $slider->slides()->pluck('id');

            $candidateIds = DB::table('slider_slide_has_images')
                ->whereIn('slider_slide_id', $slideIds)
                ->distinct()
                ->pluck('slider_slide_image_id')
                ->all();

            $slider->delete();

            return SliderSlideImage::query()
                ->whereIn('id', $candidateIds)
                ->whereDoesntHave('slides')
                ->pluck('id')
                ->all();
        });

        foreach ($orphanIds as $id) {
            try {
                DB::transaction(function () use ($id): void {
                    $image = SliderSlideImage::query()->lockForUpdate()->find($id);

                    if ($image && ! $image->slides()->exists()) {
                        $image->delete();
                    }
                });
            } catch (Throwable $exception) {
                Log::error('Не удалось очистить изображение удалённого слайдера', [
                    'image_id' => $id,
                    'exception' => $exception,
                ]);
            }
        }
    }

    /** Установить дату первой публикации, если она ещё не задана. */
    private function normalizePublication(array &$attributes, ?Slider $slider = null): void
    {
        if (($attributes['status'] ?? $slider?->status) !== 'published') {
            return;
        }

        if (empty($attributes['published_at']) && ! $slider?->published_at) {
            $attributes['published_at'] = now();
        }
    }

    /** Сравнить настройки и переводы, считая null и пустую строку равными. */
    private function hasContentChanges(Slider $slider, array $attributes, array $translations): bool
    {
        $copy = clone $slider;
        $copy->fill($attributes);

        if ($copy->isDirty()) {
            return true;
        }

        $existing = $slider->translations()->get()->keyBy('locale');

        foreach ($translations as $locale => $fields) {
            $old = $existing->get($locale);

            foreach (self::TRANSLATION_FIELDS as $field) {
                // Отсутствующее поле не означает команду очистки.
                if (! array_key_exists($field, $fields)) {
                    continue;
                }

                if ($this->normalizeText($old?->{$field}) !== $this->normalizeText($fields[$field])) {
                    return true;
                }
            }
        }

        return false;
    }

    /** Нормализовать необязательное текстовое поле. */
    private function normalizeText(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $text = trim((string) $value);

        return $text === '' ? null : $text;
    }
}
