<?php

namespace App\Traits\Admin\Form;

use Illuminate\Database\Eloquent\Model;

trait HasFormTranslationsTrait
{
    /**
     * Синхронизация переводов сущности.
     *
     * Список полей переводов определяется
     * конкретным Controller через свойство:
     *
     * protected array $translationFields = [...];
     *
     * - существующие переводы обновляются;
     * - новые создаются;
     * - отсутствующие в запросе удаляются.
     */
    protected function syncTranslations(
        Model $model,
        array $translations
    ): void {
        $locales = [];

        foreach (
            $translations
            as $locale => $translationData
        ) {
            $locale = (string) $locale;

            if (
                !in_array(
                    $locale,
                    $this->availableLocales(),
                    true
                )
            ) {
                continue;
            }

            $data = [];

            foreach (
                $this->translationFields
                as $field
            ) {
                $data[$field] =
                    $translationData[$field]
                    ?? null;
            }

            $model->translations()
                ->updateOrCreate(
                    [
                        'locale' => $locale,
                    ],
                    $data
                );

            $locales[] = $locale;
        }

        /*
        |--------------------------------------------------------------------------
        | Удаление отсутствующих переводов
        |--------------------------------------------------------------------------
        */

        if ($locales === []) {
            $model->translations()
                ->delete();

            return;
        }

        $model->translations()
            ->whereNotIn(
                'locale',
                $locales
            )
            ->delete();
    }
}
