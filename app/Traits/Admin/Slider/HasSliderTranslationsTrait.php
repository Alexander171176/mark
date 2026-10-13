<?php

namespace App\Traits\Admin\Slider;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use InvalidArgumentException;

trait HasSliderTranslationsTrait
{
    /**
     * Отсутствующая локаль не удаляется. Переданная пустая локаль удаляется.
     * Вызывать внутри транзакции сервиса. $fields — список полей конкретной сущности.
     */
    protected function syncTranslations(Model $model, array $translations, array $fields): void
    {
        $available = array_values((array) config('app.available_locales', []));

        foreach ($translations as $locale => $translation) {
            if (! is_string($locale) || ! in_array($locale, $available, true)) {
                throw new InvalidArgumentException('Недопустимая локаль перевода.');
            }
            if (! is_array($translation)) {
                throw new InvalidArgumentException('Перевод должен быть массивом.');
            }

            $data = Arr::only($translation, $fields);
            foreach ($data as $key => $value) {
                if (is_string($value)) {
                    $data[$key] = trim($value) === '' ? null : trim($value);
                }
            }

            if (! collect($data)->contains(fn ($value) => filled($value))) {
                $model->translations()->where('locale', $locale)->delete();
                continue;
            }

            $model->translations()->updateOrCreate(['locale' => $locale], $data);
        }
    }
}
