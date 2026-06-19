<?php

namespace App\Traits\Admin\Market;

use Illuminate\Database\Eloquent\Model;

trait HasMarketTranslationsTrait
{
    protected function syncTranslations(Model $model, array $translations): void
    {
        $locales = array_keys($translations);

        foreach ($translations as $locale => $translationData) {
            $data = [];

            foreach ($this->translationFields as $field) {
                $data[$field] = $translationData[$field] ?? null;
            }

            $model->translations()->updateOrCreate(
                ['locale' => $locale],
                $data
            );
        }

        $model->translations()
            ->whereNotIn('locale', $locales)
            ->delete();
    }
}
