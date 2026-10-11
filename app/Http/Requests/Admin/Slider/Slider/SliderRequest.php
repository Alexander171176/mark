<?php

namespace App\Http\Requests\Admin\Slider\Slider;

use App\Models\Admin\Slider\Slider\Slider;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SliderRequest extends FormRequest
{
    /**
     * Авторизация запроса.
     *
     * Предполагается, что доступ к административным
     * маршрутам уже контролируется middleware.
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
         | ОСНОВНЫЕ ДАННЫЕ
         ===================================================== */

        if ($this->exists('code')) {
            $code = $this->normalizeNullableString(
                $this->input('code')
            );

            $prepared['code'] = $code !== null
                ? Str::slug($code)
                : null;
        }

        foreach (['type', 'status', 'effect'] as $field) {
            if ($this->exists($field)) {
                $prepared[$field] = $this->normalizeNullableString(
                    $this->input($field)
                );
            }
        }

        /* =====================================================
         | ЧИСЛОВЫЕ ПОЛЯ
         ===================================================== */

        $integerFields = [
            'sort',
            'moderation_status',
            'autoplay_delay',
            'speed',
            'space_between',
        ];

        foreach ($integerFields as $field) {
            if ($this->exists($field)) {
                $value = $this->input($field);

                $prepared[$field] = $value === ''
                    ? null
                    : $value;
            }
        }

        if ($this->exists('slides_per_view')) {
            $prepared['slides_per_view'] =
                $this->input('slides_per_view') === ''
                    ? null
                    : $this->input('slides_per_view');
        }

        /* =====================================================
         | ЛОГИЧЕСКИЕ ПОЛЯ
         ===================================================== */

        $booleanFields = [
            'activity',
            'autoplay_disable_on_interaction',
            'pause_on_hover',
            'loop',
            'keyboard',
            'allow_touch_move',
            'grab_cursor',
            'auto_height',
            'show_navigation',
            'show_pagination',
        ];

        foreach ($booleanFields as $field) {
            if (! $this->exists($field)) {
                continue;
            }

            $value = $this->input($field);

            // Преобразуем только известные значения.
            // Некорректные значения оставляем для валидации.
            if (in_array($value, [true, 1, '1', 'true', 'on', 'yes'], true)) {
                $prepared[$field] = true;
            } elseif (in_array($value, [false, 0, '0', 'false', 'off', 'no'], true)) {
                $prepared[$field] = false;
            }
        }

        /* =====================================================
         | ДАТЫ
         ===================================================== */

        foreach ([
                     'published_at',
                     'show_from_at',
                     'show_to_at',
                 ] as $field) {
            if ($this->exists($field)) {
                $prepared[$field] = $this->input($field) === ''
                    ? null
                    : $this->input($field);
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

                        'subtitle' => $this->normalizeNullableString(
                            Arr::get($translation, 'subtitle')
                        ),

                        'description' => $this->normalizeNullableText(
                            Arr::get($translation, 'description')
                        ),

                        'meta_title' => $this->normalizeNullableString(
                            Arr::get($translation, 'meta_title')
                        ),

                        'meta_description' => $this->normalizeNullableText(
                            Arr::get($translation, 'meta_description')
                        ),
                    ];
                }

                $prepared['translations'] = $preparedTranslations;
            }
        }

        $this->merge($prepared);
    }

    /**
     * Правила валидации создания и обновления.
     */
    public function rules(): array
    {
        $slider = $this->route('slider');

        $sliderId = $slider instanceof Slider
            ? $slider->getKey()
            : (is_numeric($slider) ? $slider : null);

        $availableLocales = config(
            'app.available_locales',
            ['ru']
        );

        $availableLocales = array_values(array_unique(
            array_filter(
                (array) $availableLocales,
                'is_string'
            )
        ));

        $isUpdate = in_array(
            strtoupper($this->method()),
            ['PUT', 'PATCH'],
            true
        );

        return [
                /* =================================================
                 | ОСНОВНЫЕ ДАННЫЕ
                 ================================================= */

                'code' => [
                    'required',
                    'string',
                    'max:150',
                    'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                    Rule::unique('sliders', 'code')->ignore($sliderId),
                ],

                'type' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::in(['hero', 'banner', 'carousel']),
                ],

                /* =================================================
                 | ПУБЛИКАЦИЯ И МОДЕРАЦИЯ
                 ================================================= */

                'status' => [
                    'required',
                    'string',
                    'max:30',
                    Rule::in(['draft', 'published', 'archived']),
                ],

                'moderation_status' => [
                    'sometimes',
                    'integer',
                    Rule::in([0, 1, 2]),
                ],

                'activity' => [
                    'required',
                    'boolean',
                ],

                'sort' => [
                    'required',
                    'integer',
                    'min:0',
                    'max:4294967295',
                ],

                /* =================================================
                 | ДАТЫ
                 ================================================= */

                'published_at' => [
                    'nullable',
                    'date',
                ],

                'show_from_at' => [
                    'nullable',
                    'date',
                ],

                'show_to_at' => [
                    'nullable',
                    'date',
                    'after_or_equal:show_from_at',
                ],

                /* =================================================
                 | АВТОМАТИЧЕСКОЕ ПЕРЕКЛЮЧЕНИЕ
                 ================================================= */

                'autoplay_delay' => [
                    'nullable',
                    'integer',
                    'min:1',
                    'max:4294967295',
                ],

                'autoplay_disable_on_interaction' => [
                    'required',
                    'boolean',
                ],

                'pause_on_hover' => [
                    'required',
                    'boolean',
                ],

                /* =================================================
                 | ЭФФЕКТЫ ПЕРЕКЛЮЧЕНИЯ
                 ================================================= */

                'effect' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::in([
                        'slide',
                        'fade',
                        'cube',
                        'coverflow',
                        'flip',
                        'creative',
                    ]),
                ],

                'speed' => [
                    'required',
                    'integer',
                    'min:0',
                    'max:4294967295',
                ],

                'effect_options' => [
                    'nullable',
                    'array',
                ],

                /* =================================================
                 | УПРАВЛЕНИЕ СЛАЙДЕРОМ
                 ================================================= */

                'loop' => [
                    'required',
                    'boolean',
                ],

                'keyboard' => [
                    'required',
                    'boolean',
                ],

                'allow_touch_move' => [
                    'required',
                    'boolean',
                ],

                'grab_cursor' => [
                    'required',
                    'boolean',
                ],

                /* =================================================
                 | РАЗМЕРЫ И РАСПОЛОЖЕНИЕ
                 ================================================= */

                'slides_per_view' => [
                    'required',
                    'numeric',
                    'min:0.01',
                    'max:999.99',
                    'decimal:0,2',
                ],

                'space_between' => [
                    'required',
                    'integer',
                    'min:0',
                    'max:4294967295',
                ],

                'auto_height' => [
                    'required',
                    'boolean',
                ],

                'breakpoints' => [
                    'nullable',
                    'array',
                ],

                /* =================================================
                 | НАВИГАЦИЯ
                 ================================================= */

                'show_navigation' => [
                    'required',
                    'boolean',
                ],

                'show_pagination' => [
                    'required',
                    'boolean',
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

                'translations.*.subtitle' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'translations.*.description' => [
                    'nullable',
                    'string',
                ],

                'translations.*.meta_title' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'translations.*.meta_description' => [
                    'nullable',
                    'string',
                ],
            ] + $this->localeRules($availableLocales);
    }

    /**
     * Дополнительная проверка структуры переводов.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $translations = $this->input('translations');

            if (! is_array($translations)) {
                return;
            }

            $availableLocales = config(
                'app.available_locales',
                ['ru']
            );

            foreach (array_keys($translations) as $locale) {
                if (! in_array($locale, $availableLocales, true)) {
                    $validator->errors()->add(
                        "translations.{$locale}",
                        'Указан неподдерживаемый язык перевода.'
                    );
                }
            }

            // Хотя бы один перевод должен иметь название.
            $hasTitle = false;

            foreach ($translations as $translation) {
                if (
                    is_array($translation)
                    && filled($translation['title'] ?? null)
                ) {
                    $hasTitle = true;
                    break;
                }
            }

            if (! $hasTitle) {
                $validator->errors()->add(
                    'translations',
                    'Необходимо указать название слайдера хотя бы на одном языке.'
                );
            }
        });
    }

    /**
     * Сообщения об ошибках на русском языке.
     */
    public function messages(): array
    {
        return [
            /* Основные данные */
            'code.required' => 'Необходимо указать уникальный код слайдера.',
            'code.max' => 'Код слайдера не должен превышать 150 символов.',
            'code.regex' => 'Код может содержать только строчные латинские буквы, цифры и дефисы.',
            'code.unique' => 'Слайдер с таким кодом уже существует.',

            'type.required' => 'Необходимо выбрать тип слайдера.',
            'type.in' => 'Указан недопустимый тип слайдера.',

            /* Публикация */
            'status.required' => 'Необходимо указать статус слайдера.',
            'status.in' => 'Недопустимый статус публикации.',

            'moderation_status.in' => 'Недопустимый статус модерации.',

            'activity.required' => 'Необходимо указать активность слайдера.',
            'activity.boolean' => 'Поле активности должно иметь логическое значение.',

            'sort.required' => 'Необходимо указать порядок сортировки.',
            'sort.integer' => 'Порядок сортировки должен быть целым числом.',
            'sort.min' => 'Порядок сортировки не может быть отрицательным.',

            /* Даты */
            'published_at.date' => 'Дата публикации имеет неверный формат.',
            'show_from_at.date' => 'Дата начала показа имеет неверный формат.',
            'show_to_at.date' => 'Дата окончания показа имеет неверный формат.',
            'show_to_at.after_or_equal' => 'Дата окончания показа не может быть раньше даты начала.',

            /* Автопрокрутка */
            'autoplay_delay.integer' => 'Интервал автопрокрутки должен быть целым числом.',
            'autoplay_delay.min' => 'Интервал автопрокрутки должен быть больше нуля.',

            'autoplay_disable_on_interaction.boolean' =>
                'Недопустимое значение настройки остановки автопрокрутки.',

            'pause_on_hover.boolean' =>
                'Недопустимое значение настройки паузы при наведении.',

            /* Эффекты */
            'effect.required' => 'Необходимо выбрать эффект переключения.',
            'effect.in' => 'Указан неподдерживаемый эффект переключения.',

            'speed.required' => 'Необходимо указать скорость переключения.',
            'speed.integer' => 'Скорость переключения должна быть целым числом.',
            'speed.min' => 'Скорость переключения не может быть отрицательной.',

            'effect_options.array' => 'Параметры эффекта должны быть массивом.',

            /* Управление */
            'loop.boolean' => 'Недопустимое значение циклического переключения.',
            'keyboard.boolean' => 'Недопустимое значение управления клавиатурой.',
            'allow_touch_move.boolean' => 'Недопустимое значение управления свайпами.',
            'grab_cursor.boolean' => 'Недопустимое значение курсора захвата.',

            /* Размеры */
            'slides_per_view.required' => 'Необходимо указать количество видимых слайдов.',
            'slides_per_view.numeric' => 'Количество видимых слайдов должно быть числом.',
            'slides_per_view.min' => 'Количество видимых слайдов должно быть больше нуля.',
            'slides_per_view.max' => 'Количество видимых слайдов превышает допустимое значение.',
            'slides_per_view.decimal' => 'Допускается не более двух знаков после запятой.',

            'space_between.integer' => 'Расстояние между слайдами должно быть целым числом.',
            'space_between.min' => 'Расстояние между слайдами не может быть отрицательным.',

            'auto_height.boolean' => 'Недопустимое значение автоматической высоты.',
            'breakpoints.array' => 'Адаптивные настройки должны быть массивом.',

            /* Навигация */
            'show_navigation.boolean' => 'Недопустимое значение настройки навигации.',
            'show_pagination.boolean' => 'Недопустимое значение настройки пагинации.',

            /* Дополнительные настройки */
            'settings.array' => 'Дополнительные настройки должны быть массивом.',

            /* Переводы */
            'translations.required' => 'Необходимо добавить хотя бы один перевод.',
            'translations.array' => 'Переводы должны быть массивом.',
            'translations.min' => 'Необходимо добавить хотя бы одну локаль.',

            'translations.*.title.max' => 'Название слайдера не должно превышать 255 символов.',
            'translations.*.subtitle.max' => 'Подзаголовок не должен превышать 255 символов.',
            'translations.*.meta_title.max' => 'SEO-заголовок не должен превышать 255 символов.',
        ];
    }

    /**
     * Правила для поддерживаемых локалей.
     */
    protected function localeRules(array $availableLocales): array
    {
        $rules = [];

        foreach ($availableLocales as $locale) {
            $rules["translations.{$locale}"] = [
                'sometimes',
                'array',
            ];
        }

        return $rules;
    }

    /**
     * Нормализация необязательной строки.
     */
    protected function normalizeNullableString(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if (! is_string($value)) {
            return $value;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    /**
     * Нормализация необязательного текста.
     */
    protected function normalizeNullableText(mixed $value): mixed
    {
        return $this->normalizeNullableString($value);
    }
}
