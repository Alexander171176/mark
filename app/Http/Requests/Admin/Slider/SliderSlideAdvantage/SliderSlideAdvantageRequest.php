<?php

namespace App\Http\Requests\Admin\Slider\SliderSlideAdvantage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SliderSlideAdvantageRequest extends FormRequest
{
    /**
     * Авторизация запроса.
     *
     * Права доступа проверяются через middleware / Policy.
     * При необходимости здесь можно дополнительно
     * проверять владельца родительского слайда.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Подготовка данных перед валидацией.
     */
    protected function prepareForValidation(): void
    {
        $prepared = [];

        /* =====================================================
         | СТРОКОВЫЕ ПОЛЯ
         ===================================================== */

        foreach ([
                     'icon_type',
                     'icon',
                     'icon_color',
                     'style',
                 ] as $field) {
            if ($this->exists($field)) {
                $prepared[$field] = $this->normalizeNullableString(
                    $this->input($field)
                );
            }
        }

        /* =====================================================
         | ЧИСЛОВЫЕ ПОЛЯ
         ===================================================== */

        foreach ([
                     'slider_slide_id',
                     'sort',
                 ] as $field) {
            if (! $this->exists($field)) {
                continue;
            }

            $value = $this->input($field);

            $prepared[$field] = $value === ''
                ? null
                : $value;
        }

        /* =====================================================
         | ЛОГИЧЕСКИЕ ПОЛЯ
         ===================================================== */

        if ($this->exists('activity')) {
            $value = $this->input('activity');

            if (in_array(
                $value,
                [true, 1, '1', 'true', 'on', 'yes'],
                true
            )) {
                $prepared['activity'] = true;
            } elseif (in_array(
                $value,
                [false, 0, '0', 'false', 'off', 'no'],
                true
            )) {
                $prepared['activity'] = false;
            }
        }

        /* =====================================================
         | ПЕРЕВОДЫ
         ===================================================== */

        if ($this->exists('translations')) {
            $translations = $this->input('translations');

            if (is_array($translations)) {
                $preparedTranslations = [];

                foreach ($translations as $locale => $translation) {
                    if (! is_array($translation)) {
                        $preparedTranslations[$locale] = $translation;
                        continue;
                    }

                    $preparedTranslations[$locale] = [
                        'title' => $this->normalizeNullableString(
                            Arr::get($translation, 'title')
                        ),

                        'text' => $this->normalizeNullableString(
                            Arr::get($translation, 'text')
                        ),
                    ];
                }

                $prepared['translations'] = $preparedTranslations;
            }
        }

        $this->merge($prepared);
    }

    /**
     * Правила валидации для создания и обновления.
     *
     * Формат переводов:
     *
     * translations: {
     *     ru: { title, text },
     *     kk: { title, text },
     *     en: { title, text }
     * }
     */
    public function rules(): array
    {
        return [
            /* =================================================
             | РОДИТЕЛЬСКИЙ СЛАЙД
             ================================================= */

            'slider_slide_id' => [
                'required',
                'integer',
                Rule::exists('slider_slides', 'id'),
            ],

            /* =================================================
             | УПРАВЛЕНИЕ ОТОБРАЖЕНИЕМ
             ================================================= */

            'sort' => [
                'required',
                'integer',
                'min:0',
                'max:4294967295',
            ],

            'activity' => [
                'required',
                'boolean',
            ],

            /* =================================================
             | ТИП ИКОНКИ
             ================================================= */

            'icon_type' => [
                'required',
                'string',
                'max:30',
                Rule::in([
                    'lucide',
                    'heroicon',
                    'class',
                    'none',
                ]),
            ],

            /* =================================================
             | НАЗВАНИЕ ИКОНКИ
             ================================================= */

            'icon' => [
                Rule::requiredIf(
                    fn () => in_array(
                        $this->input('icon_type'),
                        ['lucide', 'heroicon', 'class'],
                        true
                    )
                ),
                'nullable',
                'string',
                'max:100',
            ],

            /* =================================================
             | ЦВЕТ ИКОНКИ
             ================================================= */

            'icon_color' => [
                'nullable',
                'string',
                'max:30',
            ],

            /* =================================================
             | ВАРИАНТ ОФОРМЛЕНИЯ
             ================================================= */

            'style' => [
                'nullable',
                'string',
                'max:50',
                Rule::in([
                    'default',
                    'card',
                    'compact',
                    'inline',
                    'minimal',
                    'outlined',
                ]),
            ],

            /* =================================================
             | ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ
             ================================================= */

            'settings' => [
                'nullable',
                'array',
            ],

            /* =================================================
             | ПЕРЕВОДЫ
             ================================================= */

            'translations' => [
                'required',
                'array',
                'min:1',
            ],

            'translations.*' => [
                'required',
                'array',
            ],

            'translations.*.title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'translations.*.text' => [
                'nullable',
                'string',
            ],
        ];
    }

    /**
     * Дополнительные проверки после основной валидации.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {

            /* =================================================
             | ПРОВЕРКА ПЕРЕВОДОВ
             ================================================= */

            $translations = $this->input('translations');

            if (is_array($translations)) {
                $availableLocales = $this->availableLocales();

                $hasContent = false;

                foreach ($translations as $locale => $translation) {

                    // Проверяем допустимость языка.
                    if (! in_array(
                        (string) $locale,
                        $availableLocales,
                        true
                    )) {
                        $validator->errors()->add(
                            "translations.{$locale}",
                            'Указан неподдерживаемый язык перевода.'
                        );

                        continue;
                    }

                    if (! is_array($translation)) {
                        continue;
                    }

                    $title = $translation['title'] ?? null;
                    $text = $translation['text'] ?? null;

                    // Достаточно заголовка или описания.
                    if (filled($title) || filled($text)) {
                        $hasContent = true;
                    }
                }

                if (! $hasContent) {
                    $validator->errors()->add(
                        'translations',
                        'Заполните заголовок или описание преимущества хотя бы на одном языке.'
                    );
                }
            }

            /* =================================================
             | ПРОВЕРКА ИКОНКИ
             ================================================= */

            $iconType = $this->input('icon_type');
            $icon = $this->input('icon');

            // Для типа none иконка не должна передаваться.
            if ($iconType === 'none' && filled($icon)) {
                $validator->errors()->add(
                    'icon',
                    'Для типа none необходимо очистить название иконки.'
                );
            }

            // Проверяем название иконки Lucide / Heroicon.
            if (
                in_array(
                    $iconType,
                    ['lucide', 'heroicon'],
                    true
                )
                && is_string($icon)
                && $icon !== ''
            ) {
                if (! preg_match(
                    '/^[a-zA-Z][a-zA-Z0-9_-]*$/',
                    $icon
                )) {
                    $validator->errors()->add(
                        'icon',
                        'Название иконки может содержать латинские буквы, цифры, дефисы и подчёркивания.'
                    );
                }
            }

            /* =================================================
             | ПРОВЕРКА CSS-КЛАССА ИКОНКИ
             ================================================= */

            if (
                $iconType === 'class'
                && is_string($icon)
                && $icon !== ''
            ) {
                if (! preg_match(
                    '/^[a-zA-Z0-9_\-: ]+$/',
                    $icon
                )) {
                    $validator->errors()->add(
                        'icon',
                        'CSS-класс иконки содержит недопустимые символы.'
                    );
                }
            }

            /* =================================================
             | ПРОВЕРКА ЦВЕТА ИКОНКИ
             ================================================= */

            $iconColor = $this->input('icon_color');

            if (
                is_string($iconColor)
                && $iconColor !== ''
                && ! preg_match(
                    '/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/',
                    $iconColor
                )
            ) {
                $validator->errors()->add(
                    'icon_color',
                    'Укажите цвет в HEX-формате, например #FFFFFF или #2563EB.'
                );
            }
        });
    }

    /**
     * Список поддерживаемых языков.
     *
     * При необходимости заменяем источник языков
     * на существующий конфиг приложения.
     */
    protected function availableLocales(): array
    {
        $locales = config(
            'app.available_locales',
            ['ru', 'kk', 'en']
        );

        return array_values(array_unique(
            array_filter(
                (array) $locales,
                'is_string'
            )
        ));
    }

    /**
     * Сообщения об ошибках.
     */
    public function messages(): array
    {
        return [
            /* Родительский слайд */
            'slider_slide_id.required' =>
                'Необходимо выбрать слайд.',

            'slider_slide_id.integer' =>
                'Некорректный идентификатор слайда.',

            'slider_slide_id.exists' =>
                'Указанный слайд не найден.',

            /* Сортировка */
            'sort.required' =>
                'Необходимо указать порядок сортировки.',

            'sort.integer' =>
                'Порядок сортировки должен быть целым числом.',

            'sort.min' =>
                'Порядок сортировки не может быть отрицательным.',

            'sort.max' =>
                'Превышено максимально допустимое значение сортировки.',

            /* Активность */
            'activity.required' =>
                'Необходимо указать активность преимущества.',

            'activity.boolean' =>
                'Некорректное значение активности.',

            /* Тип иконки */
            'icon_type.required' =>
                'Необходимо выбрать тип иконки.',

            'icon_type.in' =>
                'Недопустимый тип иконки.',

            /* Иконка */
            'icon.required' =>
                'Укажите название иконки или CSS-класс.',

            'icon.max' =>
                'Название иконки не должно превышать 100 символов.',

            /* Цвет */
            'icon_color.max' =>
                'Значение цвета не должно превышать 30 символов.',

            /* Стиль */
            'style.in' =>
                'Недопустимый вариант оформления преимущества.',

            'style.max' =>
                'Название стиля не должно превышать 50 символов.',

            /* Настройки */
            'settings.array' =>
                'Дополнительные настройки должны быть массивом.',

            /* Переводы */
            'translations.required' =>
                'Необходимо добавить переводы преимущества.',

            'translations.array' =>
                'Переводы должны быть массивом.',

            'translations.min' =>
                'Необходимо указать хотя бы один язык.',

            'translations.*.title.max' =>
                'Заголовок преимущества не должен превышать 255 символов.',

            'translations.*.title.string' =>
                'Заголовок преимущества должен быть строкой.',

            'translations.*.text.string' =>
                'Описание преимущества должно быть строкой.',
        ];
    }

    /**
     * Нормализация необязательных строк.
     */
    protected function normalizeNullableString(
        mixed $value
    ): mixed {
        if ($value === null) {
            return null;
        }

        if (! is_string($value)) {
            return $value;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
