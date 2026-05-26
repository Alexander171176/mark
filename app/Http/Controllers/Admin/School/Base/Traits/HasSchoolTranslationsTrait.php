<?php

namespace App\Http\Controllers\Admin\School\Base\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasSchoolTranslationsTrait
{
    protected function syncTranslations(Model $model, array $translations): void
    {
        $locales = array_keys($translations);

        foreach ($translations as $locale => $translationData) {
            if (!in_array($locale, $this->availableLocales(), true)) {
                continue;
            }

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
