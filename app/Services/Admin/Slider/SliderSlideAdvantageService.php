<?php

namespace App\Services\Admin\Slider;

use App\Models\Admin\Slider\SliderSlideAdvantage\SliderSlideAdvantage;
use App\Traits\Admin\Slider\HasSliderTranslationsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Бизнес-логика самостоятельного справочника преимуществ.
 * Связи с конкретными слайдами здесь не создаются и не изменяются.
 */
class SliderSlideAdvantageService
{
    use HasSliderTranslationsTrait;

    private const TRANSLATION_FIELDS = ['title', 'text'];

    private const ATTRIBUTE_FIELDS = [
        'activity', 'icon_type', 'icon', 'icon_color', 'style', 'settings',
    ];

    /** Создать преимущество и его переводы атомарно. */
    public function create(array $validated): SliderSlideAdvantage
    {
        return DB::transaction(function () use ($validated): Builder|Model {
            $advantage = SliderSlideAdvantage::query()->create(
                Arr::only($validated, self::ATTRIBUTE_FIELDS)
            );

            $this->syncTranslations(
                $advantage,
                $validated['translations'] ?? [],
                self::TRANSLATION_FIELDS
            );

            return $advantage->load('translations')->loadCount('slides');
        });
    }

    /** Обновить преимущество и только переданные локали. */
    public function update(SliderSlideAdvantage $advantage, array $validated): SliderSlideAdvantage
    {
        return DB::transaction(function () use ($advantage, $validated): SliderSlideAdvantage {
            $locked = SliderSlideAdvantage::query()
                ->lockForUpdate()
                ->findOrFail($advantage->getKey());

            $locked->update(Arr::only($validated, self::ATTRIBUTE_FIELDS));

            if (array_key_exists('translations', $validated)) {
                $this->syncTranslations(
                    $locked,
                    $validated['translations'],
                    self::TRANSLATION_FIELDS
                );
            }

            return $locked->refresh()->load('translations')->loadCount('slides');
        });
    }

    /**
     * Глобально удалить преимущество только если оно не используется.
     * Для удаления из одного слайда нужно detach() в сервисе слайдов.
     */
    public function delete(SliderSlideAdvantage $advantage): void
    {
        DB::transaction(function () use ($advantage): void {
            $locked = SliderSlideAdvantage::query()
                ->lockForUpdate()
                ->findOrFail($advantage->getKey());

            if ($locked->slides()->exists()) {
                throw ValidationException::withMessages([
                    'advantage' => 'Преимущество используется в слайдах. Сначала отвяжите его от всех слайдов.',
                ]);
            }

            // Переводы удаляются каскадно по внешнему ключу.
            $locked->delete();
        });
    }

    /**
     * Массовое удаление атомарно: если хотя бы одна запись используется,
     * ни одно преимущество из выбранных не будет удалено.
     *
     * @param array<int, int> $ids
     */
    public function deleteMany(array $ids): int
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));

        return DB::transaction(function () use ($ids): int {
            $advantages = SliderSlideAdvantage::query()
                ->whereKey($ids)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($advantages->count() !== count($ids)) {
                throw ValidationException::withMessages([
                    'ids' => 'Часть выбранных преимуществ не найдена.',
                ]);
            }

            foreach ($advantages as $advantage) {
                if ($advantage->slides()->exists()) {
                    throw ValidationException::withMessages([
                        'ids' => "Преимущество #{$advantage->getKey()} используется в слайдах. Сначала отвяжите его.",
                    ]);
                }
            }

            foreach ($advantages as $advantage) {
                $advantage->delete();
            }

            return $advantages->count();
        });
    }
}
