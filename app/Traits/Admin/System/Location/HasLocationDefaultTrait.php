<?php

namespace App\Traits\Admin\System\Location;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

trait HasLocationDefaultTrait
{
    /**
     * Подготовка данных при назначении
     * новой Location по умолчанию.
     *
     * Используется в store/update.
     */
    protected function prepareDefaultLocation(
        array &$data,
        ?int $exceptId = null
    ): void {
        if (
            !(
                $data['is_default']
                ?? false
            )
        ) {
            return;
        }

        /**
         * Location по умолчанию
         * всегда должна быть активной.
         */
        $data['activity'] = true;

        /**
         * Снимаем default-флаг
         * с предыдущей Location.
         */
        $query = $this->baseQuery()
            ->where(
                'is_default',
                true
            );

        if ($exceptId !== null) {
            $query->where(
                'id',
                '!=',
                $exceptId
            );
        }

        $query->update([
            'is_default' => false,
        ]);
    }

    /**
     * Назначение Location по умолчанию
     * непосредственно из списка.
     *
     * Правила:
     * - default может быть только одна Location;
     * - текущую default нельзя выключить напрямую;
     * - новая default автоматически становится активной.
     */
    public function makeDefault(
        int $location
    ): RedirectResponse {
        $currentLocation =
            $this->baseQuery()
                ->findOrFail(
                    $location
                );

        /**
         * Уже является Location по умолчанию.
         *
         * Ничего не изменяем, потому что
         * default нельзя просто отключить.
         */
        if ($currentLocation->is_default) {
            return back()->with(
                'warning',
                'Эта локация уже установлена по умолчанию.'
            );
        }

        try {
            DB::transaction(
                function () use (
                    $currentLocation
                ) {
                    /**
                     * Блокируем Location на время
                     * изменения default-флага.
                     *
                     * Это защищает от ситуации,
                     * когда два запроса одновременно
                     * назначают разные Location default.
                     */
                    $this->baseQuery()
                        ->lockForUpdate()
                        ->get();

                    /**
                     * Снимаем default-флаг
                     * со всех Location.
                     */
                    $this->baseQuery()
                        ->where(
                            'is_default',
                            true
                        )
                        ->update([
                            'is_default' => false,
                        ]);

                    /**
                     * Назначаем новую default Location.
                     *
                     * Она всегда должна быть активной.
                     */
                    $this->modelClass::query()
                        ->whereKey(
                            $currentLocation->id
                        )
                        ->update([
                            'is_default' => true,
                            'activity' => true,
                        ]);
                }
            );

            return back()->with(
                'success',
                'Локация по умолчанию успешно изменена.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка назначения Location по умолчанию ID '
                . $currentLocation->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Не удалось изменить локацию по умолчанию.'
            );
        }
    }

    /**
     * Запрет снятия default-флага
     * без назначения другой Location.
     *
     * Используется при обычном update.
     */
    protected function ensureDefaultCanBeRemoved(
        object $location,
        array $data
    ): void {
        if (
            $location->is_default
            && !(
                $data['is_default']
                ?? false
            )
        ) {
            throw new InvalidArgumentException(
                'Нельзя снять статус локации по умолчанию. Сначала назначьте другую локацию по умолчанию.'
            );
        }
    }

    /**
     * Запрет удаления Location
     * по умолчанию.
     */
    protected function ensureDefaultCanBeDeleted(
        object $location
    ): void {
        if ($location->is_default) {
            throw new InvalidArgumentException(
                'Нельзя удалить локацию по умолчанию. Сначала назначьте другую локацию по умолчанию.'
            );
        }
    }
}
