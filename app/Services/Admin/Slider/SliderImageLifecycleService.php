<?php

namespace App\Services\Admin\Slider;

use App\Models\Admin\Slider\SliderSlide\SliderSlideImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

/**
 * Управление жизненным циклом изображений конструктора.
 * Удаление файлов выполняется только после фиксации SQL-транзакции.
 */
class SliderImageLifecycleService
{
    /**
     * Очистить медиа, созданные неуспешным запросом.
     * Модель Media может уже отсутствовать в БД после rollback, но Spatie
     * использует переданный экземпляр для удаления физических файлов.
     * При ошибке очистки фиксируем её в журнале, не маскируя первичную ошибку.
     *
     * @param array<int, Media> $media
     */
    public function cleanupFailedUploads(array $media): void
    {
        foreach ($media as $item) {
            try {
                $item->delete();
            } catch (Throwable $exception) {
                Log::error('Slider: не удалось очистить файл после rollback', [
                    'media_id' => $item->getKey(),
                    'file_name' => $item->file_name,
                    'exception' => $exception->getMessage(),
                ]);
            }
        }
    }

    /**
     * Отложить удаление возможных сирот до COMMIT.
     * Если транзакция откатится, callback не выполнится.
     *
     * @param array<int, int|string> $ids
     */
    public function deleteOrphansAfterCommit(array $ids): void
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), fn ($id) => $id > 0)));
        if ($ids === []) {
            return;
        }

        DB::afterCommit(function () use ($ids): void {
            $this->deleteOrphans($ids);
        });
    }

    /**
     * Проверка ссылок перед удалением. Если изображение ещё используется,
     * оно остаётся в базе и в MediaLibrary.
     *
     * @param array<int, int> $ids
     */
    public function deleteOrphans(array $ids): void
    {
        foreach (array_unique($ids) as $id) {
            try {
                DB::transaction(function () use ($id): void {
                    $image = SliderSlideImage::query()->lockForUpdate()->find($id);
                    if (! $image || $image->slides()->exists()) {
                        return;
                    }

                    // Spatie InteractsWithMedia удаляет связанные Media при delete().
                    $image->delete();
                });
            } catch (Throwable $exception) {
                Log::error('Slider: не удалось удалить неиспользуемое изображение', [
                    'image_id' => $id,
                    'exception' => $exception->getMessage(),
                ]);
            }
        }
    }
}
