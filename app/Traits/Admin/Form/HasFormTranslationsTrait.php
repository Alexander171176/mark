<?php

namespace App\Traits\Admin\Form;

use Illuminate\Database\Eloquent\Model;

trait HasFormTranslationsTrait
{
    /**
     * Синхронизация переводов сущности.
     *
     * По умолчанию используются поля переводов
     * текущего Controller из $translationFields.
     *
     * Для вложенных сущностей можно передать
     * собственный список полей переводов.
     */
    protected function syncTranslations(
        Model $model,
        array $translations,
        ?array $translationFields = null
    ): void {
        $fields = $translationFields ?? $this->translationFields;
        $locales = [];

        foreach ($translations as $locale => $translationData) {
            $locale = (string) $locale;

            if (!in_array($locale, $this->availableLocales(), true)) {
                continue;
            }

            $data = [];

            foreach ($fields as $field) {
                $data[$field] = $translationData[$field] ?? null;
            }

            $model->translations()->updateOrCreate(
                ['locale' => $locale],
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
            $model->translations()->delete();
            return;
        }

        $model->translations()
            ->whereNotIn('locale', $locales)
            ->delete();
    }
}
