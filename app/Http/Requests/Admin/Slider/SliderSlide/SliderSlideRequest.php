<?php

namespace App\Http\Requests\Admin\Slider\SliderSlide;

use App\Models\Admin\Slider\SliderSlide\SliderSlide;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SliderSlideRequest extends FormRequest
{
    /**
     * Авторизация.
     *
     * Доступ к административным маршрутам
     * должен контролироваться middleware и Policy.
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
         | ОСНОВНЫЕ ПОЛЯ
         ===================================================== */

        foreach ([
                     'status',
                     'background_color',
                     'text_color',
                     'accent_color',
                     'overlay_color',
                     'image_position',
                     'content_position',
                     'animation_type',
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
                     'slider_id',
                     'sort',
                     'overlay_opacity',
                     'animation_duration',
                     'animation_delay',
                     'animation_stagger',
                 ] as $field) {
            if ($this->exists($field)) {
                $value = $this->input($field);

                $prepared[$field] = $value === ''
                    ? null
                    : $value;
            }
        }

        /* =====================================================
         | ЛОГИЧЕСКИЕ ПОЛЯ
         ===================================================== */

        foreach ([
                     'activity',
                     'is_main',
                     'animation_once',
                 ] as $field) {
            if (! $this->exists($field)) {
                continue;
            }

            $value = $this->input($field);

            // Преобразуем только корректные значения.
            // Остальные проверит валидатор.
            if (in_array(
                $value,
                [true, 1, '1', 'true', 'on', 'yes'],
                true
            )) {
                $prepared[$field] = true;
            } elseif (in_array(
                $value,
                [false, 0, '0', 'false', 'off', 'no'],
                true
            )) {
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
                        'label' => $this->normalizeNullableString(
                            Arr::get($translation, 'label')
                        ),

                        'title' => $this->normalizeNullableString(
                            Arr::get($translation, 'title')
                        ),

                        'accent' => $this->normalizeNullableString(
                            Arr::get($translation, 'accent')
                        ),

                        'description' => $this->normalizeNullableText(
                            Arr::get($translation, 'description')
                        ),
                    ];
                }

                $prepared['translations'] = $preparedTranslations;
            }
        }

        /* =====================================================
         | ИЗОБРАЖЕНИЯ
         ===================================================== */

        if ($this->exists('images')) {
            $images = $this->input('images');

            if (is_array($images)) {
                $preparedImages = [];

                foreach ($images as $index => $image) {
                    if (! is_array($image)) {
                        $preparedImages[$index] = $image;
                        continue;
                    }

                    // Не заменяем весь массив изображения:
                    // UploadedFile должен сохраниться.
                    $preparedImages[$index] = array_merge(
                        $image,
                        [
                            'purpose' => $this->normalizeNullableString(
                                Arr::get($image, 'purpose')
                            ) ?: 'desktop',

                            'order' => Arr::get($image, 'order', 0),

                            'alt' => $this->normalizeNullableString(
                                Arr::get($image, 'alt')
                            ),

                            'caption' => $this->normalizeNullableString(
                                Arr::get($image, 'caption')
                            ),
                        ]
                    );
                }

                $prepared['images'] = $preparedImages;
            }
        }

        $this->merge($prepared);
    }

    /**
     * Правила валидации.
     */
    public function rules(): array
    {
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

        $isCreate = $this->isMethod('POST');

        $slide = $this->route('sliderSlide')
            ?? $this->route('slide');

        $slideId = $slide instanceof SliderSlide
            ? $slide->getKey()
            : (is_numeric($slide) ? (int) $slide : null);

        return [
                /* =================================================
                 | РОДИТЕЛЬСКИЙ СЛАЙДЕР
                 ================================================= */

                'slider_id' => [
                    'required',
                    'integer',
                    Rule::exists('sliders', 'id'),
                ],

                /* =================================================
                 | ОТОБРАЖЕНИЕ
                 ================================================= */

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

                'is_main' => [
                    'required',
                    'boolean',
                ],

                /* =================================================
                 | ПУБЛИКАЦИЯ
                 ================================================= */

                'status' => [
                    'required',
                    'string',
                    'max:30',
                    Rule::in([
                        'draft',
                        'published',
                        'archived',
                    ]),
                ],

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
                 | ФОН И ОФОРМЛЕНИЕ
                 ================================================= */

                'background_color' => [
                    'nullable',
                    'string',
                    'max:30',
                ],

                'text_color' => [
                    'nullable',
                    'string',
                    'max:30',
                ],

                'accent_color' => [
                    'nullable',
                    'string',
                    'max:30',
                ],

                'overlay_color' => [
                    'nullable',
                    'string',
                    'max:30',
                ],

                'overlay_opacity' => [
                    'nullable',
                    'integer',
                    'between:0,100',
                ],

                'image_position' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::in([
                        'object-center',
                        'object-top',
                        'object-bottom',
                        'object-left',
                        'object-right',
                        'object-left-top',
                        'object-left-bottom',
                        'object-right-top',
                        'object-right-bottom',
                    ]),
                ],

                'content_position' => [
                    'required',
                    'string',
                    'max:30',
                    Rule::in([
                        'left',
                        'center',
                        'right',
                    ]),
                ],

                /* =================================================
                 | АНИМАЦИЯ СОДЕРЖИМОГО
                 ================================================= */

                'animation_type' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::in([
                        'none',
                        'fade',
                        'fade-up',
                        'fade-down',
                        'fade-left',
                        'fade-right',
                        'zoom-in',
                        'zoom-out',
                        'slide-up',
                        'slide-down',
                        'slide-left',
                        'slide-right',
                    ]),
                ],

                'animation_duration' => [
                    'required',
                    'integer',
                    'min:0',
                    'max:4294967295',
                ],

                'animation_delay' => [
                    'required',
                    'integer',
                    'min:0',
                    'max:4294967295',
                ],

                'animation_stagger' => [
                    'required',
                    'integer',
                    'min:0',
                    'max:4294967295',
                ],

                'animation_once' => [
                    'required',
                    'boolean',
                ],

                'animation_settings' => [
                    'nullable',
                    'array',
                ],

                'settings' => [
                    'nullable',
                    'array',
                ],

                /* =================================================
                 | ИЗОБРАЖЕНИЯ СЛАЙДА
                 ================================================= */

                'images' => [
                    'sometimes',
                    'array',
                ],

                'images.*' => [
                    'required',
                    'array',
                ],

                'images.*.id' => [
                    'nullable',
                    'integer',
                    Rule::exists('slider_slide_images', 'id'),
                    Rule::prohibitedIf($isCreate),
                ],

                'images.*.purpose' => [
                    'required',
                    'string',
                    'max:30',
                    Rule::in([
                        'desktop',
                        'mobile',
                        'tablet',
                    ]),
                ],

                'images.*.order' => [
                    'required',
                    'integer',
                    'min:0',
                    'max:4294967295',
                ],

                'images.*.alt' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'images.*.caption' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'images.*.file' => [
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png,webp,gif',
                    'max:10240',
                ],

                'deletedImages' => [
                    'sometimes',
                    'array',
                ],

                'deletedImages.*' => [
                    'integer',
                    Rule::exists('slider_slide_images', 'id'),
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

                'translations.*.label' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'translations.*.title' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'translations.*.accent' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'translations.*.description' => [
                    'nullable',
                    'string',
                ],
            ] + $this->localeRules($availableLocales);
    }

    /**
     * Дополнительные проверки.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            /* ===============================================
             | ПРОВЕРКА ЛОКАЛЕЙ
             =============================================== */

            $translations = $this->input('translations');

            if (is_array($translations)) {
                $availableLocales = config(
                    'app.available_locales',
                    ['ru']
                );

                $hasContent = false;

                foreach ($translations as $locale => $translation) {
                    if (! in_array($locale, $availableLocales, true)) {
                        $validator->errors()->add(
                            "translations.{$locale}",
                            'Указан неподдерживаемый язык перевода.'
                        );
                    }

                    if (! is_array($translation)) {
                        continue;
                    }

                    if (
                        filled($translation['title'] ?? null)
                        || filled($translation['accent'] ?? null)
                        || filled($translation['description'] ?? null)
                    ) {
                        $hasContent = true;
                    }
                }

                if (! $hasContent) {
                    $validator->errors()->add(
                        'translations',
                        'Необходимо заполнить заголовок, акцент или описание хотя бы на одном языке.'
                    );
                }
            }

            /* ===============================================
             | ПРОВЕРКА ИЗОБРАЖЕНИЙ
             =============================================== */

            $images = $this->input('images');

            if (is_array($images)) {
                foreach ($images as $index => $image) {
                    if (! is_array($image)) {
                        continue;
                    }

                    $hasId = filled($image['id'] ?? null);

                    $hasFile = $this->hasFile(
                        "images.{$index}.file"
                    );

                    if (! $hasId && ! $hasFile) {
                        $validator->errors()->add(
                            "images.{$index}.file",
                            'Необходимо загрузить изображение или указать существующее изображение.'
                        );
                    }
                }
            }

            /* ===============================================
             | ПРИВЯЗКА СУЩЕСТВУЮЩИХ ИЗОБРАЖЕНИЙ
             =============================================== */

            $slide = $this->route('sliderSlide')
                ?? $this->route('slide');

            if ($slide instanceof SliderSlide) {
                $slideId = $slide->getKey();
            } elseif (is_numeric($slide)) {
                $slideId = (int) $slide;
            } else {
                $slideId = null;
            }

            if ($slideId !== null && is_array($images)) {
                $existingIds = \Illuminate\Support\Facades\DB::table(
                    'slider_slide_has_images'
                )
                    ->where('slider_slide_id', $slideId)
                    ->pluck('slider_slide_image_id')
                    ->map(fn ($id) => (int) $id)
                    ->all();

                foreach ($images as $index => $image) {
                    if (! is_array($image)) {
                        continue;
                    }

                    $imageId = $image['id'] ?? null;

                    if (
                        filled($imageId)
                        && ! in_array((int) $imageId, $existingIds, true)
                    ) {
                        $validator->errors()->add(
                            "images.{$index}.id",
                            'Изображение не принадлежит редактируемому слайду.'
                        );
                    }
                }

                foreach ((array) $this->input('deletedImages', []) as $index => $imageId) {
                    if (
                        ! in_array((int) $imageId, $existingIds, true)
                    ) {
                        $validator->errors()->add(
                            "deletedImages.{$index}",
                            'Удаляемое изображение не принадлежит редактируемому слайду.'
                        );
                    }
                }
            }
        });
    }

    /**
     * Правила поддерживаемых языков.
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
     * Сообщения об ошибках.
     */
    public function messages(): array
    {
        return [
            /* Основные поля */
            'slider_id.required' => 'Необходимо выбрать родительский слайдер.',
            'slider_id.exists' => 'Указанный слайдер не найден.',

            'activity.required' => 'Необходимо указать активность слайда.',
            'activity.boolean' => 'Поле активности должно быть логическим значением.',

            'sort.required' => 'Необходимо указать порядок сортировки.',
            'sort.integer' => 'Порядок сортировки должен быть целым числом.',
            'sort.min' => 'Порядок сортировки не может быть отрицательным.',

            'is_main.boolean' => 'Поле главного слайда должно быть логическим значением.',

            /* Публикация */
            'status.required' => 'Необходимо указать статус публикации.',
            'status.in' => 'Недопустимый статус публикации.',

            'published_at.date' => 'Неверный формат даты публикации.',
            'show_from_at.date' => 'Неверный формат начала показа.',
            'show_to_at.date' => 'Неверный формат окончания показа.',
            'show_to_at.after_or_equal' => 'Дата окончания показа не может быть раньше даты начала.',

            /* Оформление */
            'background_color.max' => 'Цвет фона не должен превышать 30 символов.',
            'text_color.max' => 'Цвет текста не должен превышать 30 символов.',
            'accent_color.max' => 'Цвет акцента не должен превышать 30 символов.',
            'overlay_color.max' => 'Цвет затемнения не должен превышать 30 символов.',

            'overlay_opacity.integer' => 'Прозрачность должна быть целым числом.',
            'overlay_opacity.between' => 'Прозрачность должна находиться в диапазоне от 0 до 100.',

            'image_position.in' => 'Недопустимая позиция изображения.',
            'content_position.in' => 'Недопустимое расположение содержимого.',

            /* Анимация */
            'animation_type.in' => 'Недопустимый тип анимации.',
            'animation_duration.integer' => 'Длительность анимации должна быть целым числом.',
            'animation_delay.integer' => 'Задержка анимации должна быть целым числом.',
            'animation_stagger.integer' => 'Интервал анимации должен быть целым числом.',
            'animation_once.boolean' => 'Недопустимое значение настройки однократной анимации.',

            'animation_settings.array' => 'Настройки анимации должны быть массивом.',
            'settings.array' => 'Дополнительные настройки должны быть массивом.',

            /* Изображения */
            'images.array' => 'Изображения должны быть массивом.',
            'images.*.id.exists' => 'Указанное изображение не найдено.',
            'images.*.id.prohibited_if' => 'При создании слайда нельзя передавать существующий ID изображения.',

            'images.*.purpose.required' => 'Необходимо указать назначение изображения.',
            'images.*.purpose.in' => 'Недопустимое назначение изображения.',

            'images.*.order.integer' => 'Порядок изображения должен быть целым числом.',
            'images.*.order.min' => 'Порядок изображения не может быть отрицательным.',

            'images.*.alt.max' => 'Alt изображения не должен превышать 255 символов.',
            'images.*.caption.max' => 'Подпись изображения не должна превышать 255 символов.',

            'images.*.file.image' => 'Файл должен быть изображением.',
            'images.*.file.mimes' => 'Разрешены JPG, JPEG, PNG, WEBP и GIF.',
            'images.*.file.max' => 'Размер изображения не должен превышать 10 МБ.',

            'deletedImages.*.exists' => 'Удаляемое изображение не найдено.',

            /* Переводы */
            'translations.required' => 'Необходимо добавить хотя бы один перевод.',
            'translations.array' => 'Переводы должны быть массивом.',
            'translations.min' => 'Необходимо добавить хотя бы один язык.',

            'translations.*.label.max' => 'Надпись не должна превышать 255 символов.',
            'translations.*.title.max' => 'Заголовок не должен превышать 255 символов.',
            'translations.*.accent.max' => 'Акцентный текст не должен превышать 255 символов.',
        ];
    }

    /**
     * Нормализация необязательных строк.
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
     * Нормализация длинного текста.
     */
    protected function normalizeNullableText(mixed $value): mixed
    {
        return $this->normalizeNullableString($value);
    }
}
