<?php

namespace App\Http\Requests\Admin\Slider\SliderSlide;

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
     * Слайд независим от слайдеров: slider_id, sort, is_main относятся к pivot.
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

        // Глобальные поля сортировки и модерации (не pivot.sort).
        foreach (['sort', 'moderation_status', 'moderated_by'] as $field) {
            if ($this->exists($field) && $this->input($field) === '') {
                $prepared[$field] = null;
            }
        }

        if ($this->exists('moderated_at') && $this->input('moderated_at') === '') {
            $prepared['moderated_at'] = null;
        }

        if ($this->exists('moderation_note')) {
            $prepared['moderation_note'] = $this->normalizeNullableString(
                $this->input('moderation_note')
            );
        }

        /* =====================================================
         | ЛОГИЧЕСКИЕ ПОЛЯ
         ===================================================== */

        foreach ([
                     'activity',
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

                    $preparedTranslations[$locale] = array_replace($translation, [
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
                    ]);
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

        /* =====================================================
         | ДЕЙСТВИЯ И ПРЕИМУЩЕСТВА (PIVOT)
         ===================================================== */

        foreach (['actions', 'advantages'] as $relation) {
            if (! $this->exists($relation)) {
                continue;
            }

            $items = $this->input($relation);

            if (! is_array($items)) {
                continue;
            }

            foreach ($items as $index => $item) {
                if (! is_array($item)) {
                    continue;
                }

                foreach (['activity', 'is_primary'] as $field) {
                    if ($field === 'is_primary' && $relation !== 'actions') {
                        continue;
                    }

                    if (! array_key_exists($field, $item)) {
                        continue;
                    }

                    $value = $item[$field];

                    if (in_array($value, [true, 1, '1', 'true', 'on', 'yes'], true)) {
                        $items[$index][$field] = true;
                    } elseif (in_array($value, [false, 0, '0', 'false', 'off', 'no'], true)) {
                        $items[$index][$field] = false;
                    }
                }
            }

            $prepared[$relation] = $items;
        }

        $this->merge($prepared);
    }

    /**
     * Правила валидации.
     */
    public function rules(): array
    {
        return [
            /* =================================================
             | ОТОБРАЖЕНИЕ
             ================================================= */

            'sort' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'moderation_status' => ['sometimes', 'integer', Rule::in([0, 1, 2])],
            'moderated_by' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'moderated_at' => ['nullable', 'date'],
            'moderation_note' => ['nullable', 'string'],

            'activity' => [
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
                'array:id,purpose,order,alt,caption,file',
            ],

            'images.*.id' => [
                'nullable',
                'integer',
                Rule::exists('slider_slide_images', 'id'),
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
             | ДЕЙСТВИЯ И ПРЕИМУЩЕСТВА СЛАЙДА
             ================================================= */

            'actions' => ['sometimes', 'array'],
            'actions.*' => ['required', 'array:id,sort,activity,is_primary'],
            'actions.*.id' => ['required', 'integer', 'min:1', 'distinct', Rule::exists('slider_slide_actions', 'id')],
            'actions.*.sort' => ['sometimes', 'integer', 'min:0', 'max:4294967295'],
            'actions.*.activity' => ['sometimes', 'boolean'],
            'actions.*.is_primary' => ['sometimes', 'boolean'],

            'advantages' => ['sometimes', 'array'],
            'advantages.*' => ['required', 'array:id,sort,activity'],
            'advantages.*.id' => ['required', 'integer', 'min:1', 'distinct', Rule::exists('slider_slide_advantages', 'id')],
            'advantages.*.sort' => ['sometimes', 'integer', 'min:0', 'max:4294967295'],
            'advantages.*.activity' => ['sometimes', 'boolean'],

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
                'array:label,title,accent,description',
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
        ];
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
                $availableLocales = $this->availableLocales();

                $hasContent = false;

                foreach ($translations as $locale => $translation) {
                    if (! in_array((string) $locale, $availableLocales, true)) {
                        $validator->errors()->add(
                            "translations.{$locale}",
                            'Указан неподдерживаемый язык перевода.'
                        );
                        continue;
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
             | ОДНО ОСНОВНОЕ ДЕЙСТВИЕ
             =============================================== */

            $actions = $this->input('actions');

            if (is_array($actions)) {
                $primaryCount = 0;

                foreach ($actions as $action) {
                    if (is_array($action) && ($action['is_primary'] ?? false) === true) {
                        $primaryCount++;
                    }
                }

                if ($primaryCount > 1) {
                    $validator->errors()->add(
                        'actions',
                        'Для слайда допускается только одно основное действие.'
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

            // Удаление из слайда означает detach, а не удаление общего файла.
            $slide = $this->route('sliderSlide') ?? $this->route('slide');
            $slideId = $slide instanceof \App\Models\Admin\Slider\SliderSlide\SliderSlide
                ? $slide->getKey()
                : (is_numeric($slide) ? (int) $slide : null);

            $deletedImages = $this->input('deletedImages', []);
            if (is_array($deletedImages) && count($deletedImages) > 0) {
                if ($slideId === null) {
                    $validator->errors()->add('deletedImages', 'Отвязать изображения можно только при редактировании слайда.');
                } else {
                    $linkedIds = \Illuminate\Support\Facades\DB::table('slider_slide_has_images')
                        ->where('slider_slide_id', $slideId)
                        ->pluck('slider_slide_image_id')->map(fn ($id) => (int) $id)->all();
                    foreach ($deletedImages as $index => $imageId) {
                        if (filter_var($imageId, FILTER_VALIDATE_INT) === false || ! in_array((int) $imageId, $linkedIds, true)) {
                            $validator->errors()->add("deletedImages.{$index}", 'Изображение не подключено к редактируемому слайду.');
                        }
                    }
                }
            }
        });
    }

    /** Поддерживаемые локали берутся исключительно из конфигурации приложения. */
    protected function availableLocales(): array
    {
        $locales = config('app.available_locales', []);

        return array_values(array_unique(array_filter(
            is_array($locales) ? $locales : [],
            fn (mixed $locale): bool => is_string($locale) && $locale !== ''
        )));
    }

    /**
     * Сообщения об ошибках.
     */
    public function messages(): array
    {
        return [
            'sort.required' => 'Необходимо указать порядок сортировки.',
            'sort.integer' => 'Порядок сортировки должен быть целым числом.',
            'sort.min' => 'Порядок сортировки не может быть отрицательным.',
            'sort.max' => 'Порядок сортировки превышает допустимое значение.',
            'moderation_status.integer' => 'Статус модерации должен быть целым числом.',
            'moderation_status.in' => 'Недопустимый статус модерации.',
            'moderated_by.integer' => 'Идентификатор модератора должен быть целым числом.',
            'moderated_by.exists' => 'Указанный модератор не найден.',
            'moderated_at.date' => 'Дата модерации имеет неверный формат.',
            'moderation_note.string' => 'Комментарий модератора должен быть текстом.',

            /* Основные поля */

            'activity.required' => 'Необходимо указать активность слайда.',
            'activity.boolean' => 'Поле активности должно быть логическим значением.',



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
            'images.*.id.integer' => 'ID изображения должен быть целым числом.',

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

            /* Действия и преимущества */
            'actions.array' => 'Действия должны быть массивом.',
            'actions.*.array' => 'Недопустимые поля действия.',
            'actions.*.id.required' => 'Выберите действие.',
            'actions.*.id.integer' => 'ID действия должен быть целым числом.',
            'actions.*.id.exists' => 'Действие не найдено.',
            'actions.*.id.distinct' => 'Действия не должны повторяться.',
            'actions.*.sort.integer' => 'Порядок действия должен быть целым числом.',
            'actions.*.activity.boolean' => 'Активность действия должна быть логическим значением.',
            'actions.*.is_primary.boolean' => 'Признак основного действия должен быть логическим значением.',

            'advantages.array' => 'Преимущества должны быть массивом.',
            'advantages.*.array' => 'Недопустимые поля преимущества.',
            'advantages.*.id.required' => 'Выберите преимущество.',
            'advantages.*.id.integer' => 'ID преимущества должен быть целым числом.',
            'advantages.*.id.exists' => 'Преимущество не найдено.',
            'advantages.*.id.distinct' => 'Преимущества не должны повторяться.',
            'advantages.*.sort.integer' => 'Порядок преимущества должен быть целым числом.',
            'advantages.*.activity.boolean' => 'Активность преимущества должна быть логическим значением.',

            /* Переводы */
            'translations.required' => 'Необходимо добавить хотя бы один перевод.',
            'translations.array' => 'Переводы должны быть массивом.',
            'translations.min' => 'Необходимо добавить хотя бы один язык.',
            'translations.*.array' => 'Допустимы только поля label, title, accent, description.',

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
