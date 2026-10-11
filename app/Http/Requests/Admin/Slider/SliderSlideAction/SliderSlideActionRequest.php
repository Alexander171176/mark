<?php

namespace App\Http\Requests\Admin\Slider\SliderSlideAction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SliderSlideActionRequest extends FormRequest
{
    /**
     * Авторизация.
     *
     * Доступ к CRUD контролируется middleware / Policy.
     * Если используются права на уровне владельца,
     * их необходимо дополнительно проверять.
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
                     'action_type',
                     'action_value',
                     'target',
                     'style',
                     'icon',
                     'icon_position',
                     'css_class',
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
                     'is_primary',
                 ] as $field) {
            if (! $this->exists($field)) {
                continue;
            }

            $value = $this->input($field);

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

                        'aria_label' => $this->normalizeNullableString(
                            Arr::get($translation, 'aria_label')
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
     *     ru: { label, title, aria_label },
     *     kk: { label, title, aria_label },
     *     en: { label, title, aria_label }
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

            'is_primary' => [
                'required',
                'boolean',
            ],

            /* =================================================
             | ТИП ДЕЙСТВИЯ
             ================================================= */

            'action_type' => [
                'required',
                'string',
                'max:30',
                Rule::in([
                    'url',
                    'route',
                    'form',
                    'anchor',
                ]),
            ],

            'action_value' => [
                'required',
                'string',
                'max:2048',
            ],

            /* =================================================
             | ПАРАМЕТРЫ ИМЕНОВАННОГО МАРШРУТА
             ================================================= */

            'route_params' => [
                'nullable',
                'array',
            ],

            /* =================================================
             | СПОСОБ ОТКРЫТИЯ
             ================================================= */

            'target' => [
                'required',
                'string',
                'max:20',
                Rule::in([
                    '_self',
                    '_blank',
                ]),
            ],

            /* =================================================
             | ВНЕШНИЙ ВИД КНОПКИ
             ================================================= */

            'style' => [
                'nullable',
                'string',
                'max:50',
                Rule::in([
                    'primary',
                    'secondary',
                    'outline',
                    'ghost',
                    'link',
                    'danger',
                    'success',
                    'warning',
                ]),
            ],

            'icon' => [
                'nullable',
                'string',
                'max:100',
            ],

            'icon_position' => [
                'required',
                'string',
                'max:20',
                Rule::in([
                    'left',
                    'right',
                ]),
            ],

            'css_class' => [
                'nullable',
                'string',
                'max:255',
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

            'translations.*.aria_label' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    /**
     * Дополнительные проверки.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            /* =================================================
             | ПРОВЕРКА ЯЗЫКОВ И ПЕРЕВОДОВ
             ================================================= */

            $translations = $this->input('translations');

            if (is_array($translations)) {
                $availableLocales = $this->availableLocales();

                $hasLabel = false;

                foreach ($translations as $locale => $translation) {
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

                    $label = $translation['label'] ?? null;
                    $title = $translation['title'] ?? null;
                    $ariaLabel = $translation['aria_label'] ?? null;

                    // Полностью пустой перевод пропускаем.
                    if (
                        blank($label)
                        && blank($title)
                        && blank($ariaLabel)
                    ) {
                        continue;
                    }

                    // Если перевод заполнен, label обязателен.
                    if (blank($label)) {
                        $validator->errors()->add(
                            "translations.{$locale}.label",
                            'Укажите текст кнопки для заполненного перевода.'
                        );
                    } else {
                        $hasLabel = true;
                    }
                }

                if (! $hasLabel) {
                    $validator->errors()->add(
                        'translations',
                        'Необходимо указать текст кнопки хотя бы на одном языке.'
                    );
                }
            }

            /* =================================================
             | ПРОВЕРКА ТИПА ДЕЙСТВИЯ
             ================================================= */

            $actionType = $this->input('action_type');
            $actionValue = $this->input('action_value');

            if (! is_string($actionValue) || $actionValue === '') {
                return;
            }

            switch ($actionType) {
                case 'url':
                    $this->validateUrlAction(
                        $validator,
                        $actionValue
                    );
                    break;

                case 'route':
                    $this->validateRouteAction(
                        $validator,
                        $actionValue
                    );
                    break;

                case 'form':
                    $this->validateFormAction(
                        $validator,
                        $actionValue
                    );
                    break;

                case 'anchor':
                    $this->validateAnchorAction(
                        $validator,
                        $actionValue
                    );
                    break;
            }

            /* =================================================
             | ПАРАМЕТРЫ МАРШРУТА
             ================================================= */

            if (
                $actionType !== 'route'
                && filled($this->input('route_params'))
            ) {
                $validator->errors()->add(
                    'route_params',
                    'Параметры маршрута разрешены только для действия типа route.'
                );
            }
        });
    }

    /**
     * Проверка URL.
     *
     * Разрешаем:
     * - абсолютные HTTP/HTTPS ссылки;
     * - внутренние относительные пути.
     *
     * Запрещаем javascript:, data: и подобные схемы.
     */
    protected function validateUrlAction(
        Validator $validator,
        string $value
    ): void {
        $isHttpUrl = filter_var(
                $value,
                FILTER_VALIDATE_URL
            ) && in_array(
                strtolower((string) parse_url($value, PHP_URL_SCHEME)),
                ['http', 'https'],
                true
            );

        $isRelativePath = str_starts_with($value, '/')
            && ! str_starts_with($value, '//')
            && ! str_contains($value, '\\')
            && ! preg_match('/[\x00-\x1F\x7F]/', $value);

        if (! $isHttpUrl && ! $isRelativePath) {
            $validator->errors()->add(
                'action_value',
                'Укажите корректный HTTP/HTTPS URL или внутренний путь, начинающийся с /.'
            );
        }
    }

    /**
     * Проверка именованного маршрута Laravel.
     *
     * Существование маршрута намеренно не проверяем:
     * некоторые маршруты могут регистрироваться
     * отдельными модулями приложения.
     */
    protected function validateRouteAction(
        Validator $validator,
        string $value
    ): void {
        if (! preg_match(
            '/^[a-zA-Z0-9_.-]+$/',
            $value
        )) {
            $validator->errors()->add(
                'action_value',
                'Имя маршрута может содержать латинские буквы, цифры, точки, дефисы и подчёркивания.'
            );
        }
    }

    /**
     * Проверка кода формы.
     */
    protected function validateFormAction(
        Validator $validator,
        string $value
    ): void {
        if (! preg_match(
            '/^[a-zA-Z0-9_-]+$/',
            $value
        )) {
            $validator->errors()->add(
                'action_value',
                'Код формы может содержать латинские буквы, цифры, дефисы и подчёркивания.'
            );
        }
    }

    /**
     * Проверка якоря.
     *
     * Допускаем:
     * contact
     * #contact
     */
    protected function validateAnchorAction(
        Validator $validator,
        string $value
    ): void {
        if (! preg_match(
            '/^#?[a-zA-Z][a-zA-Z0-9_-]*$/',
            $value
        )) {
            $validator->errors()->add(
                'action_value',
                'Якорь должен иметь формат contact или #contact.'
            );
        }
    }

    /**
     * Список поддерживаемых языков.
     *
     * Если в проекте используется другой конфиг,
     * достаточно изменить этот метод.
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

            /* Отображение */
            'sort.required' =>
                'Необходимо указать порядок сортировки.',

            'sort.integer' =>
                'Порядок сортировки должен быть целым числом.',

            'sort.min' =>
                'Порядок сортировки не может быть отрицательным.',

            'activity.required' =>
                'Необходимо указать активность кнопки.',

            'activity.boolean' =>
                'Некорректное значение активности кнопки.',

            'is_primary.required' =>
                'Необходимо указать тип оформления кнопки.',

            'is_primary.boolean' =>
                'Некорректное значение основной кнопки.',

            /* Действие */
            'action_type.required' =>
                'Необходимо выбрать тип действия.',

            'action_type.in' =>
                'Недопустимый тип действия.',

            'action_value.required' =>
                'Необходимо указать значение действия.',

            'action_value.max' =>
                'Значение действия не должно превышать 2048 символов.',

            /* Параметры маршрута */
            'route_params.array' =>
                'Параметры маршрута должны быть массивом.',

            /* Способ открытия */
            'target.required' =>
                'Необходимо указать способ открытия.',

            'target.in' =>
                'Допустимые значения: текущее или новое окно.',

            /* Оформление */
            'style.in' =>
                'Недопустимый стиль оформления кнопки.',

            'style.max' =>
                'Название стиля не должно превышать 50 символов.',

            'icon.max' =>
                'Название иконки не должно превышать 100 символов.',

            'icon_position.required' =>
                'Необходимо указать расположение иконки.',

            'icon_position.in' =>
                'Иконка может располагаться слева или справа.',

            'css_class.max' =>
                'CSS-класс не должен превышать 255 символов.',

            'settings.array' =>
                'Дополнительные настройки должны быть массивом.',

            /* Переводы */
            'translations.required' =>
                'Необходимо добавить переводы кнопки.',

            'translations.array' =>
                'Переводы должны быть массивом.',

            'translations.min' =>
                'Необходимо указать хотя бы один язык.',

            'translations.*.label.max' =>
                'Текст кнопки не должен превышать 255 символов.',

            'translations.*.title.max' =>
                'Подсказка кнопки не должна превышать 255 символов.',

            'translations.*.aria_label.max' =>
                'ARIA-описание не должно превышать 255 символов.',
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
