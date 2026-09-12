<?php

namespace App\Http\Requests\Admin\System\Location;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized
     * to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Подготовка входных данных
     * перед валидацией.
     */
    public function prepareForValidation(): void
    {
        $supportedLocales = config(
            'app.available_locales',
            ['ru']
        );

        $translations = $this->input(
            'translations',
            []
        );

        if (!is_array($translations)) {
            $translations = [];
        }

        $preparedTranslations = [];

        foreach (
            $translations as $locale => $translation
        ) {
            if (
                !in_array(
                    $locale,
                    $supportedLocales,
                    true
                )
            ) {
                continue;
            }

            if (!is_array($translation)) {
                continue;
            }

            $preparedTranslations[$locale] = [
                'title' =>
                    $this->normalizeNullableString(
                        Arr::get(
                            $translation,
                            'title'
                        )
                    ),

                'title_in' =>
                    $this->normalizeNullableString(
                        Arr::get(
                            $translation,
                            'title_in'
                        )
                    ),

                'title_from' =>
                    $this->normalizeNullableString(
                        Arr::get(
                            $translation,
                            'title_from'
                        )
                    ),

                'short' =>
                    $this->normalizeNullableString(
                        Arr::get(
                            $translation,
                            'short'
                        )
                    ),

                'description' =>
                    $this->normalizeNullableText(
                        Arr::get(
                            $translation,
                            'description'
                        )
                    ),

                'meta_title' =>
                    $this->normalizeNullableString(
                        Arr::get(
                            $translation,
                            'meta_title'
                        )
                    ),

                'meta_keywords' =>
                    $this->normalizeNullableString(
                        Arr::get(
                            $translation,
                            'meta_keywords'
                        )
                    ),

                'meta_desc' =>
                    $this->normalizeNullableText(
                        Arr::get(
                            $translation,
                            'meta_desc'
                        )
                    ),
            ];
        }

        $this->merge([
            'parent_id' =>
                $this->filled('parent_id')
                    ? (int) $this->input(
                    'parent_id'
                )
                    : null,

            'type' =>
                $this->normalizeNullableString(
                    $this->input('type')
                ),

            'slug' =>
                $this->filled('slug')
                    ? Str::slug(
                    trim(
                        (string) $this->input(
                            'slug'
                        )
                    )
                )
                    : null,

            'code' =>
                $this->normalizeNullableString(
                    $this->input('code')
                ),

            'latitude' =>
                $this->filled('latitude')
                    ? (float) $this->input(
                    'latitude'
                )
                    : null,

            'longitude' =>
                $this->filled('longitude')
                    ? (float) $this->input(
                    'longitude'
                )
                    : null,

            'timezone' =>
                $this->normalizeNullableString(
                    $this->input('timezone')
                ),

            'activity' =>
                filter_var(
                    $this->input(
                        'activity',
                        true
                    ),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                ) ?? true,

            'is_default' =>
                filter_var(
                    $this->input(
                        'is_default',
                        false
                    ),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                ) ?? false,

            'sort' =>
                $this->filled('sort')
                    ? (int) $this->input(
                    'sort'
                )
                    : 0,

            'translations' =>
                $preparedTranslations,
        ]);
    }

    /**
     * Get the validation rules
     * that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $locationId =
            $this->route('location')?->id
            ?? $this->route('location')
            ?? $this->route('id');

        $availableLocales = config(
            'app.available_locales',
            ['ru']
        );

        return [
                /*
                 * Основные поля.
                 */
                'parent_id' => [
                    'nullable',
                    'integer',
                    'exists:locations,id',
                ],

                'type' => [
                    'required',
                    'string',
                    'max:50',
                ],

                'slug' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',

                    Rule::unique(
                        'locations',
                        'slug'
                    )->ignore(
                        $locationId
                    ),
                ],

                'code' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'latitude' => [
                    'nullable',
                    'numeric',
                    'between:-90,90',
                ],

                'longitude' => [
                    'nullable',
                    'numeric',
                    'between:-180,180',
                ],

                'timezone' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'activity' => [
                    'nullable',
                    'boolean',
                ],

                'is_default' => [
                    'nullable',
                    'boolean',
                ],

                'sort' => [
                    'nullable',
                    'integer',
                    'min:0',
                ],

                /*
                 * Переводы.
                 */
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
                    'required',
                    'string',
                    'max:255',
                ],

                'translations.*.title_in' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'translations.*.title_from' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'translations.*.short' => [
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

                'translations.*.meta_keywords' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'translations.*.meta_desc' => [
                    'nullable',
                    'string',
                ],
            ]
            + $this->localeRules(
                $availableLocales
            );
    }

    /**
     * Пользовательские сообщения валидации.
     */
    public function messages(): array
    {
        return [
            'parent_id.integer' =>
                'ID родительской локации должен быть числом.',

            'parent_id.exists' =>
                'Указанная родительская локация не найдена.',

            'type.required' =>
                'Необходимо указать тип локации.',

            'type.string' =>
                'Тип локации должен быть строкой.',

            'type.max' =>
                'Тип локации не должен превышать 50 символов.',

            'slug.required' =>
                'Slug обязателен для заполнения.',

            'slug.max' =>
                'Slug не должен превышать 255 символов.',

            'slug.regex' =>
                'Slug может содержать только строчные латинские буквы, цифры и дефисы.',

            'slug.unique' =>
                'Локация с таким slug уже существует.',

            'code.max' =>
                'Код локации не должен превышать 100 символов.',

            'latitude.numeric' =>
                'Широта должна быть числом.',

            'latitude.between' =>
                'Широта должна находиться в диапазоне от -90 до 90.',

            'longitude.numeric' =>
                'Долгота должна быть числом.',

            'longitude.between' =>
                'Долгота должна находиться в диапазоне от -180 до 180.',

            'timezone.max' =>
                'Название часового пояса не должно превышать 100 символов.',

            'activity.boolean' =>
                'Поле активности должно быть логическим значением.',

            'is_default.boolean' =>
                'Поле локации по умолчанию должно быть логическим значением.',

            'sort.integer' =>
                'Поле сортировки должно быть числом.',

            'sort.min' =>
                'Поле сортировки не может быть меньше 0.',

            'translations.required' =>
                'Необходимо добавить хотя бы один перевод.',

            'translations.array' =>
                'Поле переводов должно быть массивом.',

            'translations.min' =>
                'Необходимо добавить хотя бы одну локаль перевода.',

            'translations.*.title.required' =>
                'Название локации обязательно для каждой добавленной локали.',

            'translations.*.title.max' =>
                'Название локации не должно превышать 255 символов.',

            'translations.*.title_in.max' =>
                'Форма названия "в локации" не должна превышать 255 символов.',

            'translations.*.title_from.max' =>
                'Форма названия "из локации" не должна превышать 255 символов.',

            'translations.*.short.max' =>
                'Краткое описание не должно превышать 255 символов.',

            'translations.*.meta_title.max' =>
                'Meta title не должен превышать 255 символов.',

            'translations.*.meta_keywords.max' =>
                'Meta keywords не должны превышать 255 символов.',
        ];
    }

    /**
     * Правила для доступных локалей.
     */
    protected function localeRules(
        array $availableLocales
    ): array {
        $rules = [];

        foreach (
            $availableLocales as $locale
        ) {
            $rules[
            "translations.$locale"
            ] = [
                'sometimes',
                'array',
            ];
        }

        return $rules;
    }

    /**
     * Нормализация nullable string.
     */
    protected function normalizeNullableString(
        mixed $value
    ): ?string {
        if (is_null($value)) {
            return null;
        }

        $value = trim(
            (string) $value
        );

        return $value === ''
            ? null
            : $value;
    }

    /**
     * Нормализация nullable text.
     */
    protected function normalizeNullableText(
        mixed $value
    ): ?string {
        if (is_null($value)) {
            return null;
        }

        $value = trim(
            (string) $value
        );

        return $value === ''
            ? null
            : $value;
    }
}
