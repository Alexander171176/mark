<?php

namespace App\Http\Controllers\Admin\Blog\Base\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasBlogTranslationsTrait
{
    /**
     * Синхронизация переводов записи
     */
    protected function syncTranslations(Model $model, array $translations): void
    {
        $locales = array_keys($translations);

        foreach ($translations as $locale => $translationData) {
            $data = [];

            // Собираем только разрешённые поля перевода
            foreach ($this->translationFields as $field) {
                $data[$field] = $translationData[$field] ?? null;
            }

            // Создаём или обновляем перевод локали
            $model->translations()->updateOrCreate(
                ['locale' => $locale],
                $data
            );
        }

        // Удаляем переводы, которых больше нет в форме
        $model->translations()
            ->whereNotIn('locale', $locales)
            ->delete();
    }
}
